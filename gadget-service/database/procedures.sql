-- Stored Procedures for Gadget Service Management System
-- Business Logic Enforcement and Atomic Operations

USE gadget_service;

-- ========================================
-- PROCEDURE: Generate Auto-Incremented Ticket Number
-- ========================================

DELIMITER //
CREATE PROCEDURE generate_ticket_number(OUT p_ticket_no VARCHAR(20))
BEGIN
    DECLARE v_date_prefix VARCHAR(8);
    DECLARE v_sequence INT;

    SET v_date_prefix = DATE_FORMAT(CURDATE(), '%Y%m%d');

    -- Get current sequence for today
    SELECT COALESCE(COUNT(*), 0) + 1 INTO v_sequence
    FROM tickets
    WHERE DATE(created_at) = CURDATE();

    -- Format: TK-YYYYMMDD-NNN
    SET p_ticket_no = CONCAT('TK-', v_date_prefix, '-', LPAD(v_sequence, 3, '0'));
END//
DELIMITER ;

-- ========================================
-- PROCEDURE: Generate Auto-Incremented Customer Code
-- ========================================

DELIMITER //
CREATE PROCEDURE generate_customer_code(OUT p_customer_code VARCHAR(20))
BEGIN
    DECLARE v_date_prefix VARCHAR(8);
    DECLARE v_sequence INT;

    SET v_date_prefix = DATE_FORMAT(CURDATE(), '%Y%m%d');

    -- Get current sequence for today
    SELECT COALESCE(COUNT(*), 0) + 1 INTO v_sequence
    FROM customers
    WHERE DATE(created_at) = CURDATE();

    -- Format: CUST-YYYYMMDD-NNN
    SET p_customer_code = CONCAT('CUST-', v_date_prefix, '-', LPAD(v_sequence, 3, '0'));
END//
DELIMITER ;

-- ========================================
-- PROCEDURE: Generate Auto-Incremented Payment Number
-- ========================================

DELIMITER //
CREATE PROCEDURE generate_payment_number(OUT p_payment_no VARCHAR(20))
BEGIN
    DECLARE v_date_prefix VARCHAR(8);
    DECLARE v_sequence INT;

    SET v_date_prefix = DATE_FORMAT(CURDATE(), '%Y%m%d');

    -- Get current sequence for today
    SELECT COALESCE(COUNT(*), 0) + 1 INTO v_sequence
    FROM payments
    WHERE DATE(payment_date) = CURDATE();

    -- Format: PAY-YYYYMMDD-NNN
    SET p_payment_no = CONCAT('PAY-', v_date_prefix, '-', LPAD(v_sequence, 3, '0'));
END//
DELIMITER ;

-- ========================================
-- PROCEDURE: Atomic Estimate Approval Process
-- ========================================

DELIMITER //
CREATE PROCEDURE approve_estimate(
    IN p_estimate_id INT,
    IN p_approved_by INT,
    IN p_approval_method VARCHAR(20)
)
BEGIN
    DECLARE EXIT HANDLER FOR SQLEXCEPTION
    BEGIN
        ROLLBACK;
        RESIGNAL;
    END;

    DECLARE v_ticket_id INT;
    DECLARE v_completion_days INT;

    START TRANSACTION;

    -- Validate estimate exists and is in sent_to_customer status
    IF NOT EXISTS (
        SELECT 1 FROM estimates
        WHERE id = p_estimate_id AND status = 'sent_to_customer'
    ) THEN
        SIGNAL SQLSTATE '45000' SET MESSAGE_TEXT = 'Estimate not found or not ready for approval';
    END IF;

    -- Get ticket ID and completion days from estimate
    SELECT ticket_id, estimated_completion_days INTO v_ticket_id, v_completion_days
    FROM estimates WHERE id = p_estimate_id;

    -- Update estimate status
    UPDATE estimates
    SET status = 'approved',
        customer_approval_date = CURRENT_TIMESTAMP,
        customer_approval_method = p_approval_method,
        updated_at = CURRENT_TIMESTAMP
    WHERE id = p_estimate_id;

    -- Update ticket status to REPAIRING
    UPDATE tickets
    SET current_status_code = 'REPAIRING',
        estimated_completion_date = DATE_ADD(CURRENT_DATE, INTERVAL v_completion_days DAY),
        updated_at = CURRENT_TIMESTAMP
    WHERE id = v_ticket_id;

    -- Log status change
    INSERT INTO ticket_status_history (
        ticket_id, from_status_code, to_status_code,
        changed_by, change_notes
    ) VALUES (
        v_ticket_id, 'ESTIMATE_APPROVED', 'REPAIRING',
        p_approved_by,
        CONCAT('Estimate ', p_estimate_id, ' approved via ', p_approval_method)
    );

    COMMIT;
END//
DELIMITER ;

-- ========================================
-- PROCEDURE: Complete Repair and Create Payment
-- ========================================

DELIMITER //
CREATE PROCEDURE complete_repair_and_create_payment(
    IN p_ticket_id INT,
    IN p_technician_id INT,
    IN p_payment_method VARCHAR(20),
    IN p_actual_labor_hours DECIMAL(5,2)
)
BEGIN
    DECLARE EXIT HANDLER FOR SQLEXCEPTION
    BEGIN
        ROLLBACK;
        RESIGNAL;
    END;

    DECLARE v_payment_no VARCHAR(20);
    DECLARE v_total_cost DECIMAL(10,2);
    DECLARE v_labor_cost DECIMAL(10,2);
    DECLARE v_parts_cost DECIMAL(10,2);

    START TRANSACTION;

    -- Get labor cost from estimates
    SELECT COALESCE(labor_cost, 0) INTO v_labor_cost
    FROM estimates
    WHERE ticket_id = p_ticket_id;

    -- Calculate total parts cost from repair parts
    SELECT COALESCE(SUM(line_total_cost), 0) INTO v_parts_cost
    FROM repair_parts rp
    JOIN repairs r ON rp.repair_id = r.id
    WHERE r.ticket_id = p_ticket_id;

    -- Calculate total cost
    SET v_total_cost = v_labor_cost + v_parts_cost;

    -- Generate payment number
    CALL generate_payment_number(v_payment_no);

    -- Update repair completion
    UPDATE repairs
    SET actual_end_time = CURRENT_TIMESTAMP,
        actual_labor_hours = p_actual_labor_hours,
        repair_status = 'completed',
        completion_notes = CONCAT('Repair completed by technician ', p_technician_id),
        updated_at = CURRENT_TIMESTAMP
    WHERE ticket_id = p_ticket_id;

    -- Update ticket status
    UPDATE tickets
    SET current_status_code = 'REPAIR_COMPLETED',
        actual_completion_date = CURRENT_DATE(),
        total_actual_cost = v_total_cost,
        updated_at = CURRENT_TIMESTAMP
    WHERE id = p_ticket_id;

    -- Create payment record
    INSERT INTO payments (
        ticket_id, payment_no, amount_paid, payment_method,
        payment_status, created_at
    ) VALUES (
        p_ticket_id, v_payment_no, v_total_cost, p_payment_method,
        'pending', CURRENT_TIMESTAMP
    );

    -- Log status change
    INSERT INTO ticket_status_history (
        ticket_id, from_status_code, to_status_code,
        changed_by, change_notes
    ) VALUES (
        p_ticket_id, 'REPAIRING', 'REPAIR_COMPLETED',
        p_technician_id,
        CONCAT('Repair completed. Total cost: ', v_total_cost)
    );

    COMMIT;
END//
DELIMITER ;

-- ========================================
-- PROCEDURE: Create New Ticket with Customer and Device
-- ========================================

DELIMITER //
CREATE PROCEDURE create_new_ticket(
    IN p_customer_id INT,
    IN p_brand_id INT,
    IN p_device_model_id INT,
    IN p_serial_number VARCHAR(100),
    IN p_imei_number VARCHAR(50),
    IN p_device_color VARCHAR(50),
    IN p_device_condition VARCHAR(20),
    IN p_complaint_description TEXT,
    IN p_customer_notes TEXT,
    IN p_priority_level VARCHAR(20),
    IN p_created_by INT,
    OUT p_ticket_id INT,
    OUT p_ticket_no VARCHAR(20)
)
BEGIN
    DECLARE EXIT HANDLER FOR SQLEXCEPTION
    BEGIN
        ROLLBACK;
        RESIGNAL;
    END;

    DECLARE v_device_id INT;
    DECLARE v_assigned_technician_id INT;

    START TRANSACTION;

    -- Generate ticket number
    CALL generate_ticket_number(p_ticket_no);

    -- Create device record
    INSERT INTO devices (
        customer_id, brand_id, device_model_id, serial_number,
        imei_number, device_color, device_condition
    ) VALUES (
        p_customer_id, p_brand_id, p_device_model_id, p_serial_number,
        p_imei_number, p_device_color, p_device_condition
    );

    SET v_device_id = LAST_INSERT_ID();

    -- Assign technician (round-robin - get least busy technician)
    SELECT id INTO v_assigned_technician_id
    FROM users u
    WHERE u.role_id = (SELECT id FROM roles WHERE role_name = 'teknisi')
      AND u.is_active = TRUE
    ORDER BY (
        SELECT COUNT(*) FROM tickets t
        WHERE t.assigned_technician_id = u.id
          AND t.current_status_code IN ('DIAGNOSING', 'REPAIRING')
    ) ASC
    LIMIT 1;

    -- Create ticket
    INSERT INTO tickets (
        ticket_no, customer_id, device_id, assigned_technician_id,
        current_status_code, priority_level, complaint_description,
        customer_notes, created_by
    ) VALUES (
        p_ticket_no, p_customer_id, v_device_id, v_assigned_technician_id,
        'CHECK_IN', p_priority_level, p_complaint_description,
        p_customer_notes, p_created_by
    );

    SET p_ticket_id = LAST_INSERT_ID();

    -- Log initial status
    INSERT INTO ticket_status_history (
        ticket_id, from_status_code, to_status_code,
        changed_by, change_notes
    ) VALUES (
        p_ticket_id, NULL, 'CHECK_IN',
        p_created_by,
        'Ticket created and device checked in'
    );

    COMMIT;
END//
DELIMITER ;

-- ========================================
-- PROCEDURE: Process Payment Confirmation
-- ========================================

DELIMITER //
CREATE PROCEDURE confirm_payment(
    IN p_payment_id INT,
    IN p_confirmed_by INT,
    OUT p_ticket_no VARCHAR(20)
)
BEGIN
    DECLARE EXIT HANDLER FOR SQLEXCEPTION
    BEGIN
        ROLLBACK;
        RESIGNAL;
    END;

    DECLARE v_ticket_id INT;
    DECLARE v_total_cost DECIMAL(10,2);
    DECLARE v_total_payments DECIMAL(10,2);

    START TRANSACTION;

    -- Get ticket and cost information
    SELECT ticket_id INTO v_ticket_id
    FROM payments
    WHERE id = p_payment_id;

    SELECT total_actual_cost INTO v_total_cost
    FROM tickets
    WHERE id = v_ticket_id;

    -- Update payment confirmation
    UPDATE payments
    SET payment_status = 'confirmed',
        confirmed_by = p_confirmed_by,
        confirmation_date = CURRENT_TIMESTAMP,
        updated_at = CURRENT_TIMESTAMP
    WHERE id = p_payment_id;

    -- Calculate total confirmed payments
    SELECT COALESCE(SUM(amount_paid), 0) INTO v_total_payments
    FROM payments
    WHERE ticket_id = v_ticket_id AND payment_status = 'confirmed';

    -- If fully paid, update ticket status
    IF v_total_payments >= v_total_cost THEN
        UPDATE tickets
        SET current_status_code = 'READY_PICKUP',
            updated_at = CURRENT_TIMESTAMP
        WHERE id = v_ticket_id;

        -- Log status change
        INSERT INTO ticket_status_history (
            ticket_id, from_status_code, to_status_code,
            changed_by, change_notes
        ) VALUES (
            v_ticket_id, 'REPAIR_COMPLETED', 'READY_PICKUP',
            p_confirmed_by,
            'Payment confirmed and device ready for pickup'
        );

        -- Create warranty record
        INSERT INTO warranties (
            ticket_id, warranty_type, warranty_duration_days,
            warranty_start_date, warranty_terms
        ) VALUES (
            v_ticket_id, 'both', 90,  -- 90 days default warranty
            CURDATE(),
            'Standard warranty covering parts and labor for 90 days from completion date'
        );
    END IF;

    -- Get ticket number for output
    SELECT ticket_no INTO p_ticket_no
    FROM tickets
    WHERE id = v_ticket_id;

    COMMIT;
END//
DELIMITER ;

-- ========================================
-- PROCEDURE: Create Warranty Claim
-- ========================================

DELIMITER //
CREATE PROCEDURE create_warranty_claim(
    IN p_warranty_id INT,
    IN p_ticket_id INT,
    IN p_claim_reason TEXT,
    IN p_created_by INT,
    OUT p_claim_id INT
)
BEGIN
    DECLARE EXIT HANDLER FOR SQLEXCEPTION
    BEGIN
        ROLLBACK;
        RESIGNAL;
    END;

    DECLARE v_warranty_end_date DATE;
    DECLARE v_original_ticket_id INT;

    START TRANSACTION;

    -- Validate warranty is still valid
    SELECT warranty_end_date, ticket_id INTO v_warranty_end_date, v_original_ticket_id
    FROM warranties
    WHERE id = p_warranty_id;

    IF CURDATE() > v_warranty_end_date THEN
        SIGNAL SQLSTATE '45000' SET MESSAGE_TEXT = 'Warranty has expired';
    END IF;

    -- Create warranty claim
    INSERT INTO warranty_claims (
        warranty_id, ticket_id, claim_reason, created_at
    ) VALUES (
        p_warranty_id, p_ticket_id, p_claim_reason, CURRENT_TIMESTAMP
    );

    SET p_claim_id = LAST_INSERT_ID();

    -- Log warranty claim in original ticket history
    INSERT INTO ticket_status_history (
        ticket_id, from_status_code, to_status_code,
        changed_by, change_notes
    ) VALUES (
        v_original_ticket_id, 'COMPLETED', 'WARRANTY_CLAIM',
        p_created_by,
        CONCAT('Warranty claim ', p_claim_id, ' created for new ticket ',
               (SELECT ticket_no FROM tickets WHERE id = p_ticket_id))
    );

    COMMIT;
END//
DELIMITER ;

-- ========================================
-- PROCEDURE: Add Stock (Purchase)
-- ========================================

DELIMITER //
CREATE PROCEDURE add_stock(
    IN p_part_id INT,
    IN p_quantity INT,
    IN p_unit_cost DECIMAL(10,2),
    IN p_reference_no VARCHAR(50),
    IN p_created_by INT
)
BEGIN
    DECLARE EXIT HANDLER FOR SQLEXCEPTION
    BEGIN
        ROLLBACK;
        RESIGNAL;
    END;

    START TRANSACTION;

    -- Update stock quantity
    UPDATE parts
    SET stock_qty = stock_qty + p_quantity,
        updated_at = CURRENT_TIMESTAMP
    WHERE id = p_part_id;

    -- Record stock mutation
    INSERT INTO stock_mutations (
        part_id, mutation_type, quantity, reference_type,
        reference_id, reference_no, created_by, notes
    ) VALUES (
        p_part_id, 'in', p_quantity, 'purchase',
        p_part_id, p_reference_no, p_created_by,
        CONCAT('Stock addition: ', p_quantity, ' units at ', p_unit_cost, ' each')
    );

    COMMIT;
END//
DELIMITER ;
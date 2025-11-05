-- Database Triggers for Gadget Service Management System
-- Automatic Stock Management and Audit Trail

USE gadget_service;

-- ========================================
-- TRIGGER: Automatic Stock Deduction on Repair Parts Usage
-- ========================================

DELIMITER //
CREATE TRIGGER trg_repair_parts_insert
AFTER INSERT ON repair_parts
FOR EACH ROW
BEGIN
    DECLARE current_stock INT;
    DECLARE min_stock INT;
    DECLARE ticket_no VARCHAR(20);

    -- Get ticket number for reference
    SELECT t.ticket_no INTO ticket_no
    FROM tickets t
    JOIN repairs r ON t.id = r.ticket_id
    WHERE r.id = NEW.repair_id;

    -- Update parts stock (ensure non-negative)
    UPDATE parts
    SET stock_qty = GREATEST(0, stock_qty - NEW.quantity_used),
        updated_at = CURRENT_TIMESTAMP
    WHERE id = NEW.part_id;

    -- Record stock mutation
    INSERT INTO stock_mutations (
        part_id, mutation_type, quantity, reference_type,
        reference_id, reference_no, created_by, notes
    ) VALUES (
        NEW.part_id, 'out', NEW.quantity_used, 'repair',
        NEW.repair_id, ticket_no,
        (SELECT technician_id FROM repairs WHERE id = NEW.repair_id),
        CONCAT('Used for repair ticket ', ticket_no)
    );

    -- Check stock level and warn if below minimum
    SELECT stock_qty, min_stock_level INTO current_stock, min_stock
    FROM parts WHERE id = NEW.part_id;

    IF current_stock < min_stock THEN
        -- Could insert notification record here
        SIGNAL SQLSTATE '01000' SET MESSAGE_TEXT = 'Warning: Stock level below minimum';
    END IF;
END//
DELIMITER ;

-- ========================================
-- TRIGGER: Stock Restoration on Repair Parts Deletion
-- ========================================

DELIMITER //
CREATE TRIGGER trg_repair_parts_delete
AFTER DELETE ON repair_parts
FOR EACH ROW
BEGIN
    DECLARE ticket_no VARCHAR(20);

    -- Get ticket number for reference
    SELECT t.ticket_no INTO ticket_no
    FROM tickets t
    JOIN repairs r ON t.id = r.ticket_id
    WHERE r.id = OLD.repair_id;

    -- Restore parts stock
    UPDATE parts
    SET stock_qty = stock_qty + OLD.quantity_used,
        updated_at = CURRENT_TIMESTAMP
    WHERE id = OLD.part_id;

    -- Record stock mutation (return)
    INSERT INTO stock_mutations (
        part_id, mutation_type, quantity, reference_type,
        reference_id, reference_no, created_by, notes
    ) VALUES (
        OLD.part_id, 'in', OLD.quantity_used, 'return',
        OLD.repair_id, ticket_no,
        (SELECT technician_id FROM repairs WHERE id = OLD.repair_id),
        CONCAT('Returned from repair ticket ', ticket_no)
    );
END//
DELIMITER ;

-- ========================================
-- TRIGGER: Automatic Ticket Status History Logging
-- ========================================

DELIMITER //
CREATE TRIGGER trg_ticket_status_history
AFTER UPDATE ON tickets
FOR EACH ROW
BEGIN
    IF NEW.current_status_code <> OLD.current_status_code THEN
        INSERT INTO ticket_status_history (
            ticket_id, from_status_code, to_status_code,
            changed_by, change_notes
        ) VALUES (
            NEW.id, OLD.current_status_code, NEW.current_status_code,
            NEW.assigned_technician_id,
            CONCAT('Status changed from ', OLD.current_status_code, ' to ', NEW.current_status_code)
        );
    END IF;
END//
DELIMITER ;

-- ========================================
-- TRIGGER: Update Customer Total Tickets Count
-- ========================================

DELIMITER //
CREATE TRIGGER trg_tickets_update_customer_count
AFTER INSERT ON tickets
FOR EACH ROW
BEGIN
    UPDATE customers
    SET total_tickets = total_tickets + 1,
        updated_at = CURRENT_TIMESTAMP
    WHERE id = NEW.customer_id;
END//
DELIMITER ;

DELIMITER //
CREATE TRIGGER trg_tickets_delete_customer_count
AFTER DELETE ON tickets
FOR EACH ROW
BEGIN
    UPDATE customers
    SET total_tickets = GREATEST(0, total_tickets - 1),
        updated_at = CURRENT_TIMESTAMP
    WHERE id = OLD.customer_id;
END//
DELIMITER ;

-- ========================================
-- TRIGGER: Estimate Items Total Parts Cost Update
-- ========================================

DELIMITER //
CREATE TRIGGER trg_estimate_items_insert
AFTER INSERT ON estimate_items
FOR EACH ROW
BEGIN
    UPDATE estimates
    SET total_parts_cost = (
        SELECT COALESCE(SUM(line_total), 0)
        FROM estimate_items
        WHERE estimate_id = NEW.estimate_id
    ),
    updated_at = CURRENT_TIMESTAMP
    WHERE id = NEW.estimate_id;
END//
DELIMITER ;

DELIMITER //
CREATE TRIGGER trg_estimate_items_update
AFTER UPDATE ON estimate_items
FOR EACH ROW
BEGIN
    UPDATE estimates
    SET total_parts_cost = (
        SELECT COALESCE(SUM(line_total), 0)
        FROM estimate_items
        WHERE estimate_id = NEW.estimate_id
    ),
    updated_at = CURRENT_TIMESTAMP
    WHERE id = NEW.estimate_id;
END//
DELIMITER ;

DELIMITER //
CREATE TRIGGER trg_estimate_items_delete
AFTER DELETE ON estimate_items
FOR EACH ROW
BEGIN
    UPDATE estimates
    SET total_parts_cost = (
        SELECT COALESCE(SUM(line_total), 0)
        FROM estimate_items
        WHERE estimate_id = OLD.estimate_id
    ),
    updated_at = CURRENT_TIMESTAMP
    WHERE id = OLD.estimate_id;
END//
DELIMITER ;

-- ========================================
-- TRIGGER: Prevent Negative Stock Update
-- ========================================

DELIMITER //
CREATE TRIGGER trg_parts_before_update
BEFORE UPDATE ON parts
FOR EACH ROW
BEGIN
    -- If stock is being decreased, ensure it won't go negative
    IF NEW.stock_qty < 0 THEN
        SET NEW.stock_qty = 0;
        SIGNAL SQLSTATE '45000' SET MESSAGE_TEXT = 'Stock quantity cannot be negative';
    END IF;
END//
DELIMITER ;

-- ========================================
-- TRIGGER: Automatic Ticket Cost Updates
-- ========================================

DELIMITER //
CREATE TRIGGER trg_estimates_update_ticket_cost
AFTER UPDATE ON estimates
FOR EACH ROW
BEGIN
    -- Update ticket estimated cost when estimate total changes
    IF NEW.total_estimated_cost <> OLD.total_estimated_cost THEN
        UPDATE tickets
        SET total_estimated_cost = NEW.total_estimated_cost,
            updated_at = CURRENT_TIMESTAMP
        WHERE id = NEW.ticket_id;
    END IF;
END//
DELIMITER ;

-- ========================================
-- TRIGGER: Payment Confirmation Status Updates
-- ========================================

DELIMITER //
CREATE TRIGGER trg_payments_after_update
AFTER UPDATE ON payments
FOR EACH ROW
BEGIN
    -- When payment is confirmed, potentially update ticket status
    IF NEW.payment_status = 'confirmed' AND OLD.payment_status <> 'confirmed' THEN
        -- Check if this is the final payment for the ticket
        DECLARE total_payments DECIMAL(10,2);
        DECLARE ticket_cost DECIMAL(10,2);

        SELECT COALESCE(SUM(amount_paid), 0) INTO total_payments
        FROM payments
        WHERE ticket_id = NEW.ticket_id AND payment_status = 'confirmed';

        SELECT total_actual_cost INTO ticket_cost
        FROM tickets
        WHERE id = NEW.ticket_id;

        -- If payments cover the cost, update ticket to READY_PICKUP
        IF total_payments >= ticket_cost THEN
            UPDATE tickets
            SET current_status_code = 'READY_PICKUP',
                updated_at = CURRENT_TIMESTAMP
            WHERE id = NEW.ticket_id AND current_status_code = 'REPAIR_COMPLETED';
        END IF;
    END IF;
END//
DELIMITER ;
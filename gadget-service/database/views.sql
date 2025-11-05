-- Reporting Views for Gadget Service Management System
-- Optimized for UI Performance

USE gadget_service;

-- ========================================
-- VIEW: Monthly Revenue Report
-- ========================================

CREATE VIEW v_monthly_revenue AS
SELECT
    DATE_FORMAT(payment_date, '%Y-%m') as month,
    DATE_FORMAT(payment_date, '%M %Y') as month_name,
    COUNT(*) as total_payments,
    SUM(amount_paid) as total_revenue,
    AVG(amount_paid) as avg_payment,
    MIN(amount_paid) as min_payment,
    MAX(amount_paid) as max_payment
FROM payments
WHERE payment_status = 'confirmed'
GROUP BY DATE_FORMAT(payment_date, '%Y-%m'), DATE_FORMAT(payment_date, '%M %Y')
ORDER BY month DESC;

-- ========================================
-- VIEW: Part Usage Statistics
-- ========================================

CREATE VIEW v_part_usage AS
SELECT
    p.id as part_id,
    p.part_sku,
    p.part_name,
    p.brand_id,
    b.brand_name,
    p.stock_qty,
    p.unit_price,
    COALESCE(SUM(rp.quantity_used), 0) as total_used,
    COALESCE(SUM(rp.line_total_cost), 0) as total_cost,
    COUNT(DISTINCT rp.repair_id) as repair_count,
    COALESCE(AVG(rp.unit_cost), p.unit_price) as avg_unit_cost,
    p.min_stock_level,
    CASE
        WHEN p.stock_qty <= p.min_stock_level THEN 'Low Stock'
        WHEN p.stock_qty <= (p.min_stock_level * 2) THEN 'Medium Stock'
        ELSE 'Good Stock'
    END as stock_status
FROM parts p
LEFT JOIN brands b ON p.brand_id = b.id
LEFT JOIN repair_parts rp ON p.id = rp.part_id
GROUP BY p.id, p.part_sku, p.part_name, p.brand_id, b.brand_name,
         p.stock_qty, p.unit_price, p.min_stock_level
ORDER BY total_used DESC, p.part_name;

-- ========================================
-- VIEW: Technician Productivity Report
-- ========================================

CREATE VIEW v_technician_productivity AS
SELECT
    u.id as technician_id,
    u.full_name,
    u.phone,
    COUNT(DISTINCT t.id) as total_tickets,
    COUNT(DISTINCT CASE WHEN t.current_status_code = 'COMPLETED' THEN t.id END) as completed_tickets,
    COUNT(DISTINCT CASE WHEN t.current_status_code IN ('DIAGNOSING', 'REPAIRING') THEN t.id END) as active_tickets,
    AVG(TIMESTAMPDIFF(HOUR, t.created_at, COALESCE(t.actual_completion_date, NOW()))) as avg_completion_hours,
    MIN(TIMESTAMPDIFF(HOUR, t.created_at, COALESCE(t.actual_completion_date, NOW()))) as min_completion_hours,
    MAX(TIMESTAMPDIFF(HOUR, t.created_at, COALESCE(t.actual_completion_date, NOW()))) as max_completion_hours,
    COALESCE(SUM(t.total_actual_cost), 0) as total_revenue_generated,
    COALESCE(AVG(t.total_actual_cost), 0) as avg_revenue_per_ticket,
    COUNT(DISTINCT d.id) as diagnostics_completed,
    COUNT(DISTINCT r.id) as repairs_completed
FROM users u
LEFT JOIN tickets t ON u.id = t.assigned_technician_id
LEFT JOIN diagnostics d ON t.id = d.ticket_id
LEFT JOIN repairs r ON t.id = r.ticket_id
WHERE u.role_id = (SELECT id FROM roles WHERE role_name = 'teknisi')
GROUP BY u.id, u.full_name, u.phone
ORDER BY completed_tickets DESC, total_revenue_generated DESC;

-- ========================================
-- VIEW: Ticket Turnaround Time Analysis
-- ========================================

CREATE VIEW v_ticket_turnaround_time AS
SELECT
    t.id,
    t.ticket_no,
    t.customer_id,
    c.full_name as customer_name,
    c.phone as customer_phone,
    t.device_id,
    b.brand_name,
    dm.model_name,
    t.priority_level,
    t.current_status_code,
    sr.status_name as current_status_name,
    t.created_at as check_in_time,
    t.actual_completion_date,
    TIMESTAMPDIFF(HOUR, t.created_at, COALESCE(t.actual_completion_date, NOW())) as turnaround_hours,
    TIMESTAMPDIFF(DAY, t.created_at, COALESCE(t.actual_completion_date, CURDATE())) as turnaround_days,
    t.total_estimated_cost,
    t.total_actual_cost,
    CASE
        WHEN t.current_status_code = 'COMPLETED' THEN 'Completed'
        WHEN TIMESTAMPDIFF(HOUR, t.created_at, NOW()) > 72 THEN 'Overdue'
        WHEN TIMESTAMPDIFF(HOUR, t.created_at, NOW()) > 48 THEN 'Critical'
        WHEN TIMESTAMPDIFF(HOUR, t.created_at, NOW()) > 24 THEN 'Attention Needed'
        ELSE 'On Track'
    END as urgency_status,
    u.full_name as assigned_technician
FROM tickets t
JOIN customers c ON t.customer_id = c.id
JOIN devices d ON t.device_id = d.id
JOIN brands b ON d.brand_id = b.id
JOIN device_models dm ON d.device_model_id = dm.id
JOIN status_refs sr ON t.current_status_code = sr.status_code
LEFT JOIN users u ON t.assigned_technician_id = u.id
ORDER BY t.created_at DESC;

-- ========================================
-- VIEW: Customer Summary Report
-- ========================================

CREATE VIEW v_customer_summary AS
SELECT
    c.id,
    c.customer_code,
    c.full_name,
    c.phone,
    c.email,
    c.total_tickets,
    COUNT(t.id) as actual_tickets,
    COUNT(CASE WHEN t.current_status_code = 'COMPLETED' THEN 1 END) as completed_tickets,
    COUNT(CASE WHEN t.current_status_code IN ('CHECK_IN', 'DIAGNOSING', 'REPAIRING') THEN 1 END) as active_tickets,
    COALESCE(SUM(t.total_actual_cost), 0) as total_spent,
    COALESCE(AVG(t.total_actual_cost), 0) as avg_ticket_cost,
    MAX(t.created_at) as last_service_date,
    AVG(TIMESTAMPDIFF(DAY, t.created_at, COALESCE(t.actual_completion_date, CURDATE()))) as avg_turnaround_days,
    COUNT(DISTINCT d.brand_id) as unique_brands_serviced,
    COUNT(DISTINCT w.id) as warranty_claims
FROM customers c
LEFT JOIN tickets t ON c.id = t.customer_id
LEFT JOIN devices d ON t.device_id = d.id
LEFT JOIN warranties w ON t.id = w.ticket_id
GROUP BY c.id, c.customer_code, c.full_name, c.phone, c.email, c.total_tickets
ORDER BY total_spent DESC, completed_tickets DESC;

-- ========================================
-- VIEW: Inventory Status Report
-- ========================================

CREATE VIEW v_inventory_status AS
SELECT
    p.id,
    p.part_sku,
    p.part_name,
    p.brand_id,
    b.brand_name,
    p.device_model_id,
    dm.model_name,
    p.stock_qty,
    p.min_stock_level,
    p.max_stock_level,
    p.unit_price,
    (p.stock_qty * p.unit_price) as total_value,
    p.is_active,
    CASE
        WHEN p.stock_qty = 0 THEN 'Out of Stock'
        WHEN p.stock_qty <= p.min_stock_level THEN 'Critical'
        WHEN p.stock_qty <= (p.min_stock_level * 1.5) THEN 'Low'
        WHEN p.stock_qty >= p.max_stock_level THEN 'Overstocked'
        ELSE 'Optimal'
    END as stock_level_status,
    COALESCE(smu.total_used_last_30_days, 0) as usage_last_30_days,
    CASE
        WHEN smu.total_used_last_30_days = 0 THEN NULL
        ELSE FLOOR(p.stock_qty / (smu.total_used_last_30_days / 30))
    END as days_of_stock_remaining,
    COALESCE(smc.total_cost_last_30_days, 0) as cost_used_last_30_days
FROM parts p
LEFT JOIN brands b ON p.brand_id = b.id
LEFT JOIN device_models dm ON p.device_model_id = dm.id
LEFT JOIN (
    SELECT
        rp.part_id,
        SUM(rp.quantity_used) as total_used_last_30_days,
        SUM(rp.line_total_cost) as total_cost_last_30_days
    FROM repair_parts rp
    JOIN repairs r ON rp.repair_id = r.id
    WHERE r.created_at >= DATE_SUB(CURDATE(), INTERVAL 30 DAY)
    GROUP BY rp.part_id
) smu ON p.id = smu.part_id
LEFT JOIN (
    SELECT
        rp.part_id,
        SUM(rp.line_total_cost) as total_cost_last_30_days
    FROM repair_parts rp
    JOIN repairs r ON rp.repair_id = r.id
    WHERE r.created_at >= DATE_SUB(CURDATE(), INTERVAL 30 DAY)
    GROUP BY rp.part_id
) smc ON p.id = smc.part_id
WHERE p.is_active = TRUE
ORDER BY
    CASE
        WHEN p.stock_qty = 0 THEN 1
        WHEN p.stock_qty <= p.min_stock_level THEN 2
        ELSE 3
    END,
    p.part_name;

-- ========================================
-- VIEW: Warranty Claims Report
-- ========================================

CREATE VIEW v_warranty_claims AS
SELECT
    wc.id as claim_id,
    wc.claim_status,
    wc.claim_reason,
    wc.created_at as claim_date,
    wc.approved_date,
    wc.approved_by,
    wc.resolution_details,
    w.id as warranty_id,
    w.warranty_type,
    w.warranty_start_date,
    w.warranty_end_date,
    w.ticket_id as original_ticket_id,
    t_original.ticket_no as original_ticket_no,
    wc.ticket_id as claim_ticket_id,
    t_claim.ticket_no as claim_ticket_no,
    c_original.full_name as original_customer,
    c_claim.full_name as claim_customer,
    TIMESTAMPDIFF(DAY, w.warranty_start_date, wc.created_at) as days_since_warranty_start,
    TIMESTAMPDIFF(DAY, wc.created_at, COALESCE(wc.approved_date, CURDATE())) as claim_processing_days,
    u_approver.full_name as approved_by_name
FROM warranty_claims wc
JOIN warranties w ON wc.warranty_id = w.id
JOIN tickets t_original ON w.ticket_id = t_original.id
JOIN tickets t_claim ON wc.ticket_id = t_claim.id
JOIN customers c_original ON t_original.customer_id = c_original.id
JOIN customers c_claim ON t_claim.customer_id = c_claim.id
LEFT JOIN users u_approver ON wc.approved_by = u_approver.id
ORDER BY wc.created_at DESC;

-- ========================================
-- VIEW: Daily Operations Summary
-- ========================================

CREATE VIEW v_daily_operations AS
SELECT
    DATE(t.created_at) as operation_date,
    COUNT(*) as new_tickets,
    COUNT(CASE WHEN t.current_status_code = 'COMPLETED' THEN 1 END) as completed_tickets,
    COUNT(CASE WHEN t.current_status_code IN ('DIAGNOSING', 'REPAIRING') THEN 1 END) as active_tickets,
    COALESCE(SUM(CASE WHEN p.payment_status = 'confirmed' THEN p.amount_paid END), 0) as daily_revenue,
    COUNT(CASE WHEN p.payment_status = 'confirmed' THEN 1 END) as confirmed_payments,
    COUNT(DISTINCT t.assigned_technician_id) as active_technicians,
    COUNT(DISTINCT t.customer_id) as unique_customers,
    COALESCE(AVG(TIMESTAMPDIFF(HOUR, t.created_at, COALESCE(t.actual_completion_date, NOW()))), 0) as avg_turnaround_hours
FROM tickets t
LEFT JOIN payments p ON t.id = p.ticket_id
WHERE DATE(t.created_at) >= DATE_SUB(CURDATE(), INTERVAL 30 DAY)
GROUP BY DATE(t.created_at)
ORDER BY operation_date DESC;
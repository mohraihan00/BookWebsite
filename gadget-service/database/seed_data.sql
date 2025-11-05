-- Initial Data Seeding for Gadget Service Management System
-- Master Data and Sample Records

USE gadget_service;

-- ========================================
-- INSERT: System Roles
-- ========================================

INSERT INTO roles (role_name, description) VALUES
('admin', 'System Administrator with full access'),
('cs', 'Customer Service - handles customer registration and ticket creation'),
('teknisi', 'Technician - performs diagnostics and repairs'),
('pelanggan', 'Customer - can view own tickets and approve estimates');

-- ========================================
-- INSERT: System Status References
-- ========================================

INSERT INTO status_refs (status_code, status_name, status_category, description, display_order) VALUES
-- Ticket statuses
('CHECK_IN', 'Device Check-in', 'ticket', 'Device received and initial ticket created', 1),
('DIAGNOSING', 'Under Diagnosis', 'ticket', 'Technician is diagnosing the device issues', 2),
('ESTIMATE_APPROVED', 'Estimate Approved', 'ticket', 'Customer has approved the repair estimate', 3),
('REPAIRING', 'Under Repair', 'ticket', 'Technician is actively repairing the device', 4),
('REPAIR_COMPLETED', 'Repair Completed', 'ticket', 'Repair work is completed, awaiting payment', 5),
('READY_PICKUP', 'Ready for Pickup', 'ticket', 'Device is ready for customer pickup', 6),
('COMPLETED', 'Completed', 'ticket', 'Device has been picked up by customer', 7),
('CANCELLED', 'Cancelled', 'ticket', 'Service ticket has been cancelled', 8),

-- Payment statuses
('PAYMENT_PENDING', 'Payment Pending', 'payment', 'Payment is awaiting confirmation', 1),
('PAYMENT_CONFIRMED', 'Payment Confirmed', 'payment', 'Payment has been confirmed', 2),
('PAYMENT_FAILED', 'Payment Failed', 'payment', 'Payment transaction has failed', 3),
('PAYMENT_REFUNDED', 'Payment Refunded', 'payment', 'Payment has been refunded', 4),

-- Warranty statuses
('WARRANTY_ACTIVE', 'Warranty Active', 'warranty', 'Device is under warranty coverage', 1),
('WARRANTY_EXPIRED', 'Warranty Expired', 'warranty', 'Warranty period has ended', 2),
('WARRANTY_CLAIMED', 'Warranty Claimed', 'warranty', 'Warranty has been claimed', 3),
('WARRANTY_VOID', 'Warranty Void', 'warranty', 'Warranty has been voided', 4);

-- ========================================
-- INSERT: Popular Device Brands
-- ========================================

INSERT INTO brands (brand_name, description) VALUES
('Apple', 'Apple Inc. devices including iPhone, iPad, MacBook'),
('Samsung', 'Samsung Electronics devices including Galaxy series'),
('Xiaomi', 'Xiaomi Corporation smartphones and electronics'),
('Oppo', 'Oppo smartphones and accessories'),
('Vivo', 'Vivo mobile phones and accessories'),
('Realme', 'Realme smartphones and devices'),
('Huawei', 'Huawei Technologies devices'),
('Asus', 'ASUS computers and smartphones'),
('Lenovo', 'Lenovo computers, laptops, and mobile devices'),
('Dell', 'Dell computers and laptops'),
('HP', 'HP computers, laptops, and printers'),
('Acer', 'Acer computers and laptops'),
('Microsoft', 'Microsoft Surface devices and accessories'),
('LG', 'LG Electronics devices'),
('Sony', 'Sony mobile devices and electronics'),
('OnePlus', 'OnePlus smartphones'),
('Motorola', 'Motorola mobile devices'),
('Nokia', 'Nokia phones and telecommunications'),
('Google', 'Google Pixel devices'),
('Other', 'Other brands not listed');

-- ========================================
-- INSERT: Common Device Models
-- ========================================

-- iPhone models
INSERT INTO device_models (brand_id, model_name, model_number, device_type) VALUES
((SELECT id FROM brands WHERE brand_name = 'Apple'), 'iPhone 15 Pro', 'A3108', 'smartphone'),
((SELECT id FROM brands WHERE brand_name = 'Apple'), 'iPhone 15', 'A3094', 'smartphone'),
((SELECT id FROM brands WHERE brand_name = 'Apple'), 'iPhone 14 Pro', 'A2889', 'smartphone'),
((SELECT id FROM brands WHERE brand_name = 'Apple'), 'iPhone 14', 'A2884', 'smartphone'),
((SELECT id FROM brands WHERE brand_name = 'Apple'), 'iPhone 13', 'A2483', 'smartphone'),
((SELECT id FROM brands WHERE brand_name = 'Apple'), 'iPhone 12', 'A2404', 'smartphone'),
((SELECT id FROM brands WHERE brand_name = 'Apple'), 'MacBook Pro 14"', 'MK1E3', 'laptop'),
((SELECT id FROM brands WHERE brand_name = 'Apple'), 'MacBook Air M2', 'MLE73', 'laptop'),
((SELECT id FROM brands WHERE brand_name = 'Apple'), 'iPad Pro 11"', 'MXQT2', 'tablet');

-- Samsung models
INSERT INTO device_models (brand_id, model_name, model_number, device_type) VALUES
((SELECT id FROM brands WHERE brand_name = 'Samsung'), 'Galaxy S24 Ultra', 'SM-S928B', 'smartphone'),
((SELECT id FROM brands WHERE brand_name = 'Samsung'), 'Galaxy S24', 'SM-S921B', 'smartphone'),
((SELECT id FROM brands WHERE brand_name = 'Samsung'), 'Galaxy S23 Ultra', 'SM-S918B', 'smartphone'),
((SELECT id FROM brands WHERE brand_name = 'Samsung'), 'Galaxy Tab S9', 'SM-X710', 'tablet'),
((SELECT id FROM brands WHERE brand_name = 'Samsung'), 'Galaxy Book3 Pro', 'NP960XGK', 'laptop');

-- Xiaomi models
INSERT INTO device_models (brand_id, model_name, model_number, device_type) VALUES
((SELECT id FROM brands WHERE brand_name = 'Xiaomi'), 'Redmi Note 13 Pro', '2312DRA50G', 'smartphone'),
((SELECT id FROM brands WHERE brand_name = 'Xiaomi'), 'Xiaomi 13 Pro', '2210132G', 'smartphone'),
((SELECT id FROM brands WHERE brand_name = 'Xiaomi'), 'Poco X5 Pro', '23049PCD8G', 'smartphone');

-- Other popular models
INSERT INTO device_models (brand_id, model_name, model_number, device_type) VALUES
((SELECT id FROM brands WHERE brand_name = 'Oppo'), 'Oppo Find X6 Pro', 'PHU110', 'smartphone'),
((SELECT id FROM brands WHERE brand_name = 'Vivo'), 'Vivo X90 Pro', 'V2227', 'smartphone'),
((SELECT id FROM brands WHERE brand_name = 'OnePlus'), 'OnePlus 12', 'CPH2583', 'smartphone'),
((SELECT id FROM brands WHERE brand_name = 'Google'), 'Pixel 8 Pro', 'GA02864', 'smartphone'),
((SELECT id FROM brands WHERE brand_name = 'Asus'), 'ROG Phone 8', 'AI2401', 'smartphone'),
((SELECT id FROM brands WHERE brand_name = 'Asus'), 'ZenBook Pro', 'UX535', 'laptop'),
((SELECT id FROM brands WHERE brand_name = 'Lenovo'), 'ThinkPad X1 Carbon', 'Gen11', 'laptop'),
((SELECT id FROM brands WHERE brand_name = 'Dell'), 'XPS 13', '9330', 'laptop');

-- ========================================
-- INSERT: Common Spare Parts
-- ========================================

INSERT INTO parts (part_sku, part_name, description, brand_id, device_model_id, unit_price, stock_qty, min_stock_level) VALUES
-- Universal parts
('BAT-001', 'Battery Replacement Universal', 'High capacity replacement battery for smartphones', NULL, NULL, 45.00, 25, 10),
('SCR-001', 'Screen Protector Tempered Glass', '9H tempered glass screen protector', NULL, NULL, 8.50, 100, 30),
('CHG-001', 'USB Type-C Charging Cable', 'Fast charging USB-C cable 1.5m', NULL, NULL, 12.00, 50, 15),
('CHG-002', 'Lightning Charging Cable', 'Original MFi certified lightning cable 1.5m', NULL, NULL, 15.00, 40, 12),

-- iPhone parts
('AAP-BAT-15P', 'iPhone 15 Pro Battery', 'Original battery for iPhone 15 Pro', (SELECT id FROM brands WHERE brand_name = 'Apple'), (SELECT id FROM device_models WHERE model_name = 'iPhone 15 Pro'), 65.00, 15, 8),
('AAP-SCR-15P', 'iPhone 15 Pro Screen', 'OLED display assembly for iPhone 15 Pro', (SELECT id FROM brands WHERE brand_name = 'Apple'), (SELECT id FROM device_models WHERE model_name = 'iPhone 15 Pro'), 280.00, 8, 5),
('AAP-CAM-15P', 'iPhone 15 Pro Camera', 'Rear camera assembly for iPhone 15 Pro', (SELECT id FROM brands WHERE brand_name = 'Apple'), (SELECT id FROM device_models WHERE model_name = 'iPhone 15 Pro'), 120.00, 10, 6),

-- Samsung parts
('SAM-BAT-S24U', 'Galaxy S24 Ultra Battery', 'Original battery for Galaxy S24 Ultra', (SELECT id FROM brands WHERE brand_name = 'Samsung'), (SELECT id FROM device_models WHERE model_name = 'Galaxy S24 Ultra'), 55.00, 12, 7),
('SAM-SCR-S24U', 'Galaxy S24 Ultra Screen', 'Dynamic AMOLED display for S24 Ultra', (SELECT id FROM brands WHERE brand_name = 'Samsung'), (SELECT id FROM device_models WHERE model_name = 'Galaxy S24 Ultra'), 320.00, 6, 4),
('SAM-PEN-S24U', 'Galaxy S24 Ultra S Pen', 'Replacement S Pen stylus', (SELECT id FROM brands WHERE brand_name = 'Samsung'), (SELECT id FROM device_models WHERE model_name = 'Galaxy S24 Ultra'), 35.00, 20, 10),

-- Common repair tools and materials
('TOOL-001', 'Screen Opening Toolkit', 'Professional screen removal tools set', NULL, NULL, 25.00, 15, 5),
('TOOL-002', 'Precision Screwdriver Set', 'Magnetic precision screwdriver set 45 in 1', NULL, NULL, 18.00, 10, 4),
('GLUE-001', 'B-7000 Adhesive', 'Professional grade adhesive for screen assembly', NULL, NULL, 6.50, 30, 10);

-- ========================================
-- INSERT: Default Admin User
-- ========================================

-- Note: In production, use proper password hashing
-- Default password: admin123 (should be changed immediately)
INSERT INTO users (role_id, username, password_hash, email, full_name, phone) VALUES
((SELECT id FROM roles WHERE role_name = 'admin'), 'admin', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'admin@gadgetservice.com', 'System Administrator', '+62812345678');

-- ========================================
-- INSERT: Sample Customer Service User
-- ========================================

-- Default password: cs123
INSERT INTO users (role_id, username, password_hash, email, full_name, phone) VALUES
((SELECT id FROM roles WHERE role_name = 'cs'), 'customer_service', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'cs@gadgetservice.com', 'Customer Service Staff', '+62823456789');

-- ========================================
-- INSERT: Sample Technician Users
-- ========================================

-- Default password: tech123
INSERT INTO users (role_id, username, password_hash, email, full_name, phone) VALUES
((SELECT id FROM roles WHERE role_name = 'teknisi'), 'technician1', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'tech1@gadgetservice.com', 'Senior Technician - John Doe', '+62834567890'),
((SELECT id FROM roles WHERE role_name = 'teknisi'), 'technician2', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'tech2@gadgetservice.com', 'Technician - Jane Smith', '+62845678901');

-- ========================================
-- INSERT: Sample Customers
-- ========================================

INSERT INTO customers (customer_code, full_name, phone, email, address) VALUES
('CUST-20241101-001', 'Budi Santoso', '+62811112222', 'budi@email.com', 'Jl. Sudirman No. 123, Jakarta Pusat'),
('CUST-20241101-002', 'Siti Nurhaliza', '+62812223333', 'siti@email.com', 'Jl. Thamrin No. 456, Jakarta Selatan'),
('CUST-20241101-003', 'Ahmad Wijaya', '+62813334444', 'ahmad@email.com', 'Jl. Gatot Subroto No. 789, Jakarta Barat'),
('CUST-20241101-004', 'Dewi Lestari', '+62814445555', 'dewi@email.com', 'Jl. Rasuna Said No. 321, Jakarta Selatan'),
('CUST-20241101-005', 'Rudi Hermawan', '+62815556666', 'rudi@email.com', 'Jl. Menteng Raya No. 654, Jakarta Pusat');

-- ========================================
-- INSERT: Sample Devices (linked to customers)
-- ========================================

INSERT INTO devices (customer_id, brand_id, device_model_id, serial_number, imei_number, device_color, device_condition, physical_damage_notes) VALUES
(1, (SELECT id FROM brands WHERE brand_name = 'Apple'), (SELECT id FROM device_models WHERE model_name = 'iPhone 15 Pro'), 'F2JYXZQNM', '351273811234567', 'Natural Titanium', 'good', 'Minor scratches on back cover'),
(2, (SELECT id FROM brands WHERE brand_name = 'Samsung'), (SELECT id FROM device_models WHERE model_name = 'Galaxy S24 Ultra'), 'R3CTGHJKL', '351273812345678', 'Phantom Black', 'excellent', 'No visible damage'),
(3, (SELECT id FROM brands WHERE brand_name = 'Xiaomi'), (SELECT id FROM device_models WHERE model_name = 'Redmi Note 13 Pro'), 'S4VBNMOPQ', '351273813456789', 'Midnight Black', 'fair', 'Screen has minor cracks, battery drain issue'),
(4, (SELECT id FROM brands WHERE brand_name = 'Oppo'), (SELECT id FROM device_models WHERE model_name = 'Oppo Find X6 Pro'), 'T5WXYZABC', '351273814567890', 'Mars Orange', 'good', 'Charging port issues'),
(5, (SELECT id FROM brands WHERE brand_name = 'Google'), (SELECT id FROM device_models WHERE model_name = 'Pixel 8 Pro'), 'U6QRSTUVW', '351273815678901', 'Bay Blue', 'excellent', 'Software issues');

-- Update customer total_tickets count (triggers will handle this going forward)
UPDATE customers SET total_tickets = 0 WHERE id IN (1,2,3,4,5);

-- ========================================
-- COMPLETION MESSAGE
-- ========================================

SELECT 'Database seeding completed successfully!' as message,
       (SELECT COUNT(*) FROM roles) as roles_created,
       (SELECT COUNT(*) FROM status_refs) as statuses_created,
       (SELECT COUNT(*) FROM brands) as brands_created,
       (SELECT COUNT(*) FROM device_models) as models_created,
       (SELECT COUNT(*) FROM parts) as parts_created,
       (SELECT COUNT(*) FROM users) as users_created,
       (SELECT COUNT(*) FROM customers) as customers_created,
       (SELECT COUNT(*) FROM devices) as devices_created;
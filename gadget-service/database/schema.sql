-- Gadget Service Management System Database
-- 3NF Compliant Database Schema
-- Engine: InnoDB
-- Charset: utf8mb4

-- Create database
CREATE DATABASE IF NOT EXISTS gadget_service
CHARACTER SET utf8mb4
COLLATE utf8mb4_unicode_ci;

USE gadget_service;

-- ========================================
-- MASTER DATA TABLES (Static Reference Data)
-- ========================================

-- Table: roles
CREATE TABLE roles (
    id INT PRIMARY KEY AUTO_INCREMENT,
    role_name VARCHAR(50) UNIQUE NOT NULL COMMENT 'admin, cs, teknisi, pelanggan',
    description TEXT,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Table: users
CREATE TABLE users (
    id INT PRIMARY KEY AUTO_INCREMENT,
    role_id INT NOT NULL,
    username VARCHAR(50) UNIQUE NOT NULL,
    password_hash VARCHAR(255) NOT NULL,
    email VARCHAR(100) UNIQUE NOT NULL,
    full_name VARCHAR(100) NOT NULL,
    phone VARCHAR(20),
    is_active BOOLEAN DEFAULT TRUE,
    last_login TIMESTAMP NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (role_id) REFERENCES roles(id) ON DELETE RESTRICT,
    INDEX idx_username (username),
    INDEX idx_email (email)
) ENGINE=InnoDB CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Table: brands
CREATE TABLE brands (
    id INT PRIMARY KEY AUTO_INCREMENT,
    brand_name VARCHAR(100) UNIQUE NOT NULL,
    description TEXT,
    is_active BOOLEAN DEFAULT TRUE,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    INDEX idx_brand_name (brand_name)
) ENGINE=InnoDB CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Table: device_models
CREATE TABLE device_models (
    id INT PRIMARY KEY AUTO_INCREMENT,
    brand_id INT NOT NULL,
    model_name VARCHAR(100) NOT NULL,
    model_number VARCHAR(50) UNIQUE,
    device_type ENUM('smartphone', 'laptop', 'tablet', 'other'),
    description TEXT,
    is_active BOOLEAN DEFAULT TRUE,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (brand_id) REFERENCES brands(id) ON DELETE RESTRICT,
    UNIQUE KEY uk_brand_model (brand_id, model_name),
    INDEX idx_model_name (model_name),
    INDEX idx_device_type (device_type)
) ENGINE=InnoDB CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Table: parts
CREATE TABLE parts (
    id INT PRIMARY KEY AUTO_INCREMENT,
    part_sku VARCHAR(50) UNIQUE NOT NULL,
    part_name VARCHAR(200) NOT NULL,
    description TEXT,
    brand_id INT NULL,
    device_model_id INT NULL,
    unit_price DECIMAL(10,2) NOT NULL DEFAULT 0.00,
    stock_qty INT NOT NULL DEFAULT 0,
    min_stock_level INT NOT NULL DEFAULT 5,
    max_stock_level INT NOT NULL DEFAULT 100,
    is_active BOOLEAN DEFAULT TRUE,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (brand_id) REFERENCES brands(id) ON DELETE SET NULL,
    FOREIGN KEY (device_model_id) REFERENCES device_models(id) ON DELETE SET NULL,
    INDEX idx_sku (part_sku),
    INDEX idx_part_name (part_name),
    INDEX idx_stock_level (stock_qty),
    CONSTRAINT chk_stock_non_negative CHECK (stock_qty >= 0)
) ENGINE=InnoDB CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Table: status_refs
CREATE TABLE status_refs (
    id INT PRIMARY KEY AUTO_INCREMENT,
    status_code VARCHAR(20) UNIQUE NOT NULL COMMENT 'CHECK_IN, DIAGNOSING, ESTIMATE_APPROVED, REPAIRING, REPAIR_COMPLETED, READY_PICKUP, COMPLETED, CANCELLED',
    status_name VARCHAR(100) NOT NULL,
    status_category ENUM('ticket', 'payment', 'warranty') COMMENT 'for categorization',
    description TEXT,
    display_order INT DEFAULT 0,
    is_active BOOLEAN DEFAULT TRUE,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    INDEX idx_status_code (status_code),
    INDEX idx_category (status_category)
) ENGINE=InnoDB CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ========================================
-- OPERATIONAL DATA TABLES (Transactional)
-- ========================================

-- Table: customers
CREATE TABLE customers (
    id INT PRIMARY KEY AUTO_INCREMENT,
    customer_code VARCHAR(20) UNIQUE NOT NULL COMMENT 'Auto-generated: CUST-YYYYMMDD-NNN',
    full_name VARCHAR(100) NOT NULL,
    phone VARCHAR(20) NOT NULL,
    email VARCHAR(100),
    address TEXT,
    id_card_number VARCHAR(50),
    notes TEXT,
    is_active BOOLEAN DEFAULT TRUE,
    total_tickets INT DEFAULT 0,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    INDEX idx_customer_code (customer_code),
    INDEX idx_phone (phone),
    INDEX idx_email (email),
    INDEX idx_full_name (full_name)
) ENGINE=InnoDB CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Table: devices
CREATE TABLE devices (
    id INT PRIMARY KEY AUTO_INCREMENT,
    customer_id INT NOT NULL,
    brand_id INT NOT NULL,
    device_model_id INT NOT NULL,
    serial_number VARCHAR(100),
    imei_number VARCHAR(50),
    device_color VARCHAR(50),
    device_condition ENUM('excellent', 'good', 'fair', 'poor'),
    password_pattern VARCHAR(100) COMMENT 'For unlocking device',
    accessories_list TEXT COMMENT 'JSON: ["charger", "case", "etc"]',
    physical_damage_notes TEXT,
    is_under_warranty BOOLEAN DEFAULT FALSE,
    warranty_expiry DATE NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (customer_id) REFERENCES customers(id) ON DELETE CASCADE,
    FOREIGN KEY (brand_id) REFERENCES brands(id) ON DELETE RESTRICT,
    FOREIGN KEY (device_model_id) REFERENCES device_models(id) ON DELETE RESTRICT,
    UNIQUE KEY uk_customer_serial (customer_id, serial_number),
    UNIQUE KEY uk_customer_imei (customer_id, imei_number),
    INDEX idx_serial_imei (serial_number, imei_number)
) ENGINE=InnoDB CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Table: tickets
CREATE TABLE tickets (
    id INT PRIMARY KEY AUTO_INCREMENT,
    ticket_no VARCHAR(20) UNIQUE NOT NULL COMMENT 'Auto-generated: TK-YYYYMMDD-NNN',
    customer_id INT NOT NULL,
    device_id INT NOT NULL,
    assigned_technician_id INT NULL,
    current_status_code VARCHAR(20) NOT NULL,
    priority_level ENUM('low', 'normal', 'high', 'urgent') DEFAULT 'normal',
    complaint_description TEXT NOT NULL,
    customer_notes TEXT,
    technician_notes TEXT,
    estimated_completion_date DATE NULL,
    actual_completion_date DATE NULL,
    total_estimated_cost DECIMAL(10,2) DEFAULT 0.00,
    total_actual_cost DECIMAL(10,2) DEFAULT 0.00,
    created_by INT NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (customer_id) REFERENCES customers(id) ON DELETE RESTRICT,
    FOREIGN KEY (device_id) REFERENCES devices(id) ON DELETE RESTRICT,
    FOREIGN KEY (assigned_technician_id) REFERENCES users(id) ON DELETE SET NULL,
    FOREIGN KEY (current_status_code) REFERENCES status_refs(status_code) ON DELETE RESTRICT,
    FOREIGN KEY (created_by) REFERENCES users(id) ON DELETE RESTRICT,
    INDEX idx_ticket_no (ticket_no),
    INDEX idx_customer_id (customer_id),
    INDEX idx_status_code (current_status_code),
    INDEX idx_technician_id (assigned_technician_id),
    INDEX idx_created_date (DATE(created_at))
) ENGINE=InnoDB CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Table: ticket_status_history
CREATE TABLE ticket_status_history (
    id INT PRIMARY KEY AUTO_INCREMENT,
    ticket_id INT NOT NULL,
    from_status_code VARCHAR(20) NULL,
    to_status_code VARCHAR(20) NOT NULL,
    changed_by INT NOT NULL,
    change_notes TEXT,
    changed_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (ticket_id) REFERENCES tickets(id) ON DELETE CASCADE,
    FOREIGN KEY (from_status_code) REFERENCES status_refs(status_code) ON DELETE SET NULL,
    FOREIGN KEY (to_status_code) REFERENCES status_refs(status_code) ON DELETE RESTRICT,
    FOREIGN KEY (changed_by) REFERENCES users(id) ON DELETE RESTRICT,
    INDEX idx_ticket_id (ticket_id),
    INDEX idx_status_change (to_status_code, changed_at)
) ENGINE=InnoDB CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Table: diagnostics
CREATE TABLE diagnostics (
    id INT PRIMARY KEY AUTO_INCREMENT,
    ticket_id INT NOT NULL UNIQUE,
    technician_id INT NOT NULL,
    diagnostic_findings TEXT NOT NULL,
    issues_found TEXT NOT NULL COMMENT 'JSON array of identified issues',
    recommended_actions TEXT,
    estimated_repair_time INT COMMENT 'in hours',
    estimated_difficulty_level ENUM('easy', 'medium', 'hard', 'expert'),
    diagnostic_images TEXT COMMENT 'JSON array of image paths',
    diagnostic_completed_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (ticket_id) REFERENCES tickets(id) ON DELETE CASCADE,
    FOREIGN KEY (technician_id) REFERENCES users(id) ON DELETE RESTRICT
) ENGINE=InnoDB CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Table: estimates
CREATE TABLE estimates (
    id INT PRIMARY KEY AUTO_INCREMENT,
    ticket_id INT NOT NULL UNIQUE,
    technician_id INT NOT NULL,
    labor_cost DECIMAL(10,2) NOT NULL DEFAULT 0.00,
    total_parts_cost DECIMAL(10,2) NOT NULL DEFAULT 0.00,
    total_estimated_cost DECIMAL(10,2) NOT NULL GENERATED ALWAYS AS (labor_cost + total_parts_cost) STORED,
    estimated_completion_days INT NOT NULL DEFAULT 1,
    estimate_notes TEXT,
    status ENUM('draft', 'sent_to_customer', 'approved', 'rejected', 'expired') DEFAULT 'draft',
    customer_approval_date TIMESTAMP NULL,
    customer_approval_method ENUM('email', 'sms', 'whatsapp', 'in_person') NULL,
    valid_until TIMESTAMP NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (ticket_id) REFERENCES tickets(id) ON DELETE CASCADE,
    FOREIGN KEY (technician_id) REFERENCES users(id) ON DELETE RESTRICT,
    INDEX idx_ticket_id (ticket_id),
    INDEX idx_status (status)
) ENGINE=InnoDB CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Table: estimate_items
CREATE TABLE estimate_items (
    id INT PRIMARY KEY AUTO_INCREMENT,
    estimate_id INT NOT NULL,
    part_id INT NOT NULL,
    quantity INT NOT NULL DEFAULT 1,
    unit_price DECIMAL(10,2) NOT NULL,
    line_total DECIMAL(10,2) NOT NULL GENERATED ALWAYS AS (quantity * unit_price) STORED,
    item_notes TEXT,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (estimate_id) REFERENCES estimates(id) ON DELETE CASCADE,
    FOREIGN KEY (part_id) REFERENCES parts(id) ON DELETE RESTRICT,
    INDEX idx_estimate_id (estimate_id),
    INDEX idx_part_id (part_id),
    CONSTRAINT chk_quantity_positive CHECK (quantity > 0)
) ENGINE=InnoDB CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Table: repairs
CREATE TABLE repairs (
    id INT PRIMARY KEY AUTO_INCREMENT,
    ticket_id INT NOT NULL UNIQUE,
    technician_id INT NOT NULL,
    actual_start_time TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    actual_end_time TIMESTAMP NULL,
    actual_labor_hours DECIMAL(5,2),
    repair_description TEXT NOT NULL,
    repair_actions_performed TEXT COMMENT 'JSON array of actions',
    repair_images_before TEXT COMMENT 'JSON array of before images',
    repair_images_after TEXT COMMENT 'JSON array of after images',
    repair_status ENUM('in_progress', 'completed', 'failed', 'cancelled') DEFAULT 'in_progress',
    completion_notes TEXT,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (ticket_id) REFERENCES tickets(id) ON DELETE CASCADE,
    FOREIGN KEY (technician_id) REFERENCES users(id) ON DELETE RESTRICT,
    INDEX idx_ticket_id (ticket_id),
    INDEX idx_technician_id (technician_id),
    INDEX idx_status (repair_status)
) ENGINE=InnoDB CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Table: repair_parts
CREATE TABLE repair_parts (
    id INT PRIMARY KEY AUTO_INCREMENT,
    repair_id INT NOT NULL,
    part_id INT NOT NULL,
    quantity_used INT NOT NULL DEFAULT 1,
    unit_cost DECIMAL(10,2) NOT NULL,
    line_total_cost DECIMAL(10,2) NOT NULL GENERATED ALWAYS AS (quantity_used * unit_cost) STORED,
    usage_notes TEXT,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (repair_id) REFERENCES repairs(id) ON DELETE CASCADE,
    FOREIGN KEY (part_id) REFERENCES parts(id) ON DELETE RESTRICT,
    INDEX idx_repair_id (repair_id),
    INDEX idx_part_id (part_id),
    CONSTRAINT chk_quantity_used_positive CHECK (quantity_used > 0)
) ENGINE=InnoDB CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Table: stock_mutations
CREATE TABLE stock_mutations (
    id INT PRIMARY KEY AUTO_INCREMENT,
    part_id INT NOT NULL,
    mutation_type ENUM('in', 'out') NOT NULL,
    quantity INT NOT NULL,
    reference_type ENUM('purchase', 'repair', 'adjustment', 'return') NOT NULL,
    reference_id INT NOT NULL COMMENT 'ID from related table',
    reference_no VARCHAR(50) COMMENT 'Ticket number, PO number, etc',
    mutation_date TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    created_by INT NOT NULL,
    notes TEXT,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (part_id) REFERENCES parts(id) ON DELETE RESTRICT,
    FOREIGN KEY (created_by) REFERENCES users(id) ON DELETE RESTRICT,
    INDEX idx_part_id (part_id),
    INDEX idx_mutation_date (mutation_date),
    INDEX idx_reference (reference_type, reference_id)
) ENGINE=InnoDB CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Table: payments
CREATE TABLE payments (
    id INT PRIMARY KEY AUTO_INCREMENT,
    ticket_id INT NOT NULL,
    payment_no VARCHAR(20) UNIQUE NOT NULL COMMENT 'Auto-generated: PAY-YYYYMMDD-NNN',
    amount_paid DECIMAL(10,2) NOT NULL,
    payment_method ENUM('cash', 'transfer', 'card', 'ewallet') NOT NULL,
    payment_status ENUM('pending', 'confirmed', 'failed', 'refunded') DEFAULT 'pending',
    payment_date TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    confirmed_by INT NULL,
    confirmation_date TIMESTAMP NULL,
    payment_notes TEXT,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (ticket_id) REFERENCES tickets(id) ON DELETE RESTRICT,
    FOREIGN KEY (confirmed_by) REFERENCES users(id) ON DELETE SET NULL,
    INDEX idx_ticket_id (ticket_id),
    INDEX idx_payment_no (payment_no),
    INDEX idx_payment_date (payment_date),
    INDEX idx_status (payment_status)
) ENGINE=InnoDB CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Table: warranties
CREATE TABLE warranties (
    id INT PRIMARY KEY AUTO_INCREMENT,
    ticket_id INT NOT NULL UNIQUE,
    warranty_type ENUM('parts', 'labor', 'both') NOT NULL,
    warranty_duration_days INT NOT NULL,
    warranty_start_date DATE NOT NULL,
    warranty_end_date DATE NOT NULL GENERATED ALWAYS AS (DATE_ADD(warranty_start_date, INTERVAL warranty_duration_days DAY)) STORED,
    warranty_terms TEXT,
    coverage_details TEXT COMMENT 'JSON: what\'s covered',
    exclusion_details TEXT COMMENT 'JSON: what\'s not covered',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (ticket_id) REFERENCES tickets(id) ON DELETE CASCADE,
    INDEX idx_ticket_id (ticket_id),
    INDEX idx_warranty_period (warranty_start_date, warranty_end_date),
    CONSTRAINT chk_warranty_duration_positive CHECK (warranty_duration_days > 0)
) ENGINE=InnoDB CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Table: warranty_claims
CREATE TABLE warranty_claims (
    id INT PRIMARY KEY AUTO_INCREMENT,
    warranty_id INT NOT NULL,
    ticket_id INT NOT NULL COMMENT 'New ticket for warranty claim',
    claim_reason TEXT NOT NULL,
    claim_status ENUM('submitted', 'approved', 'rejected', 'completed') DEFAULT 'submitted',
    resolution_details TEXT,
    approved_by INT NULL,
    approved_date TIMESTAMP NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (warranty_id) REFERENCES warranties(id) ON DELETE RESTRICT,
    FOREIGN KEY (ticket_id) REFERENCES tickets(id) ON DELETE RESTRICT,
    FOREIGN KEY (approved_by) REFERENCES users(id) ON DELETE SET NULL,
    INDEX idx_warranty_id (warranty_id),
    INDEX idx_ticket_id (ticket_id),
    INDEX idx_status (claim_status)
) ENGINE=InnoDB CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
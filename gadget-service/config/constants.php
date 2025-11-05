<?php
/**
 * Gadget Service Management System - Constants
 *
 * This file contains system constants and enums
 * for the application.
 */

// Prevent direct access
if (!defined('GADGET_SERVICE')) {
    exit('Direct access to this file is not allowed.');
}

// ========================================
// USER ROLES
// ========================================

define('ROLE_ADMIN', 1);
define('ROLE_CUSTOMER_SERVICE', 2);
define('ROLE_TECHNICIAN', 3);
define('ROLE_CUSTOMER', 4);

// Role names
$USER_ROLES = [
    ROLE_ADMIN => 'admin',
    ROLE_CUSTOMER_SERVICE => 'cs',
    ROLE_TECHNICIAN => 'teknisi',
    ROLE_CUSTOMER => 'pelanggan'
];

// Role display names
$ROLE_DISPLAY_NAMES = [
    ROLE_ADMIN => 'Administrator',
    ROLE_CUSTOMER_SERVICE => 'Customer Service',
    ROLE_TECHNICIAN => 'Technician',
    ROLE_CUSTOMER => 'Customer'
];

// ========================================
// TICKET STATUSES
// ========================================

define('STATUS_CHECK_IN', 'CHECK_IN');
define('STATUS_DIAGNOSING', 'DIAGNOSING');
define('STATUS_ESTIMATE_APPROVED', 'ESTIMATE_APPROVED');
define('STATUS_REPAIRING', 'REPAIRING');
define('STATUS_REPAIR_COMPLETED', 'REPAIR_COMPLETED');
define('STATUS_READY_PICKUP', 'READY_PICKUP');
define('STATUS_COMPLETED', 'COMPLETED');
define('STATUS_CANCELLED', 'CANCELLED');

// Status display names
$TICKET_STATUSES = [
    STATUS_CHECK_IN => 'Check-in',
    STATUS_DIAGNOSING => 'Diagnosing',
    STATUS_ESTIMATE_APPROVED => 'Estimate Approved',
    STATUS_REPAIRING => 'Under Repair',
    STATUS_REPAIR_COMPLETED => 'Repair Completed',
    STATUS_READY_PICKUP => 'Ready for Pickup',
    STATUS_COMPLETED => 'Completed',
    STATUS_CANCELLED => 'Cancelled'
];

// Status colors for UI
$STATUS_COLORS = [
    STATUS_CHECK_IN => 'primary',
    STATUS_DIAGNOSING => 'info',
    STATUS_ESTIMATE_APPROVED => 'warning',
    STATUS_REPAIRING => 'secondary',
    STATUS_REPAIR_COMPLETED => 'success',
    STATUS_READY_PICKUP => 'warning',
    STATUS_COMPLETED => 'success',
    STATUS_CANCELLED => 'danger'
];

// ========================================
// PAYMENT STATUSES
// ========================================

define('PAYMENT_PENDING', 'pending');
define('PAYMENT_CONFIRMED', 'confirmed');
define('PAYMENT_FAILED', 'failed');
define('PAYMENT_REFUNDED', 'refunded');

// Payment method types
$PAYMENT_METHODS = [
    'cash' => 'Cash',
    'transfer' => 'Bank Transfer',
    'card' => 'Credit/Debit Card',
    'ewallet' => 'E-Wallet'
];

// ========================================
// WARRANTY TYPES
// ========================================

define('WARRANTY_PARTS', 'parts');
define('WARRANTY_LABOR', 'labor');
define('WARRANTY_BOTH', 'both');

$WARRANTY_TYPES = [
    WARRANTY_PARTS => 'Parts Only',
    WARRANTY_LABOR => 'Labor Only',
    WARRANTY_BOTH => 'Parts & Labor'
];

// ========================================
// DEVICE CONDITIONS
// ========================================

$DEVICE_CONDITIONS = [
    'excellent' => 'Excellent',
    'good' => 'Good',
    'fair' => 'Fair',
    'poor' => 'Poor'
];

// ========================================
// DEVICE TYPES
// ========================================

$DEVICE_TYPES = [
    'smartphone' => 'Smartphone',
    'laptop' => 'Laptop',
    'tablet' => 'Tablet',
    'other' => 'Other'
];

// ========================================
// PRIORITY LEVELS
// ========================================

$PRIORITY_LEVELS = [
    'low' => 'Low',
    'normal' => 'Normal',
    'high' => 'High',
    'urgent' => 'Urgent'
];

$PRIORITY_COLORS = [
    'low' => 'success',
    'normal' => 'info',
    'high' => 'warning',
    'urgent' => 'danger'
];

// ========================================
// ESTIMATE STATUSES
// ========================================

$ESTIMATE_STATUSES = [
    'draft' => 'Draft',
    'sent_to_customer' => 'Sent to Customer',
    'approved' => 'Approved',
    'rejected' => 'Rejected',
    'expired' => 'Expired'
];

$ESTIMATE_STATUS_COLORS = [
    'draft' => 'secondary',
    'sent_to_customer' => 'info',
    'approved' => 'success',
    'rejected' => 'danger',
    'expired' => 'warning'
];

// ========================================
// REPAIR STATUSES
// ========================================

$REPAIR_STATUSES = [
    'in_progress' => 'In Progress',
    'completed' => 'Completed',
    'failed' => 'Failed',
    'cancelled' => 'Cancelled'
];

$REPAIR_STATUS_COLORS = [
    'in_progress' => 'info',
    'completed' => 'success',
    'failed' => 'danger',
    'cancelled' => 'secondary'
];

// ========================================
// WARRANTY CLAIM STATUSES
// ========================================

$WARRANTY_CLAIM_STATUSES = [
    'submitted' => 'Submitted',
    'approved' => 'Approved',
    'rejected' => 'Rejected',
    'completed' => 'Completed'
];

$WARRANTY_CLAIM_COLORS = [
    'submitted' => 'info',
    'approved' => 'success',
    'rejected' => 'danger',
    'completed' => 'success'
];

// ========================================
// CUSTOMER APPROVAL METHODS
// ========================================

$APPROVAL_METHODS = [
    'email' => 'Email',
    'sms' => 'SMS',
    'whatsapp' => 'WhatsApp',
    'in_person' => 'In Person'
];

// ========================================
// REPAIR DIFFICULTY LEVELS
// ========================================

$DIFFICULTY_LEVELS = [
    'easy' => 'Easy',
    'medium' => 'Medium',
    'hard' => 'Hard',
    'expert' => 'Expert'
];

$DIFFICULTY_COLORS = [
    'easy' => 'success',
    'medium' => 'info',
    'hard' => 'warning',
    'expert' => 'danger'
];

// ========================================
// STOCK MUTATION TYPES
// ========================================

$MUTATION_TYPES = [
    'in' => 'Stock In',
    'out' => 'Stock Out'
];

$REFERENCE_TYPES = [
    'purchase' => 'Purchase Order',
    'repair' => 'Repair Usage',
    'adjustment' => 'Stock Adjustment',
    'return' => 'Return/Refund'
];

// ========================================
// NOTIFICATION TYPES
// ========================================

define('NOTIFICATION_TICKET_CREATED', 'ticket_created');
define('NOTIFICATION_ESTIMATE_READY', 'estimate_ready');
define('NOTIFICATION_ESTIMATE_APPROVED', 'estimate_approved');
define('NOTIFICATION_REPAIR_COMPLETED', 'repair_completed');
define('NOTIFICATION_READY_PICKUP', 'ready_pickup');
define('NOTIFICATION_PAYMENT_CONFIRMED', 'payment_confirmed');
define('NOTIFICATION_LOW_STOCK', 'low_stock');

// ========================================
// FILE UPLOAD CONSTANTS
// ========================================

define('MAX_IMAGE_WIDTH', 1920);
define('MAX_IMAGE_HEIGHT', 1080);
define('IMAGE_QUALITY', 85);

// Allowed file extensions
$ALLOWED_IMAGE_EXTENSIONS = ['jpg', 'jpeg', 'png', 'gif', 'webp'];
$ALLOWED_DOCUMENT_EXTENSIONS = ['pdf', 'doc', 'docx'];

// ========================================
// VALIDATION REGEX PATTERNS
// ========================================

define('REGEX_PHONE', '/^[\+]?[0-9]{10,15}$/');
define('REGEX_EMAIL', '/^[a-zA-Z0-9._%+-]+@[a-zA-Z0-9.-]+\.[a-zA-Z]{2,}$/');
define('REGEX_TICKET_NO', '/^TK-\d{8}-\d{3}$/');
define('REGEX_CUSTOMER_CODE', '/^CUST-\d{8}-\d{3}$/');
define('REGEX_PAYMENT_NO', '/^PAY-\d{8}-\d{3}$/');
define('REGEX_PART_SKU', '/^[A-Z0-9-]{3,20}$/');
define('REGEX_SERIAL_NUMBER', '/^[A-Z0-9-]{6,30}$/');
define('REGEX_IMEI', '/^\d{15}$/');

// ========================================
// SYSTEM MESSAGES
// ========================================

$SUCCESS_MESSAGES = [
    'login_success' => 'Login successful. Welcome back!',
    'logout_success' => 'You have been logged out successfully.',
    'ticket_created' => 'Ticket has been created successfully.',
    'ticket_updated' => 'Ticket has been updated successfully.',
    'customer_created' => 'Customer has been registered successfully.',
    'customer_updated' => 'Customer information has been updated.',
    'estimate_created' => 'Estimate has been created and sent to customer.',
    'estimate_approved' => 'Estimate has been approved. Repair work will begin.',
    'repair_completed' => 'Repair work has been completed successfully.',
    'payment_confirmed' => 'Payment has been confirmed successfully.',
    'warranty_created' => 'Warranty has been created for this device.',
    'parts_added' => 'Parts have been added to inventory.',
    'parts_updated' => 'Parts information has been updated.',
    'profile_updated' => 'Your profile has been updated successfully.'
];

$ERROR_MESSAGES = [
    'login_failed' => 'Invalid username or password.',
    'access_denied' => 'Access denied. You do not have permission to perform this action.',
    'session_expired' => 'Your session has expired. Please login again.',
    'invalid_request' => 'Invalid request. Please try again.',
    'database_error' => 'A database error occurred. Please contact administrator.',
    'file_upload_error' => 'File upload failed. Please check file size and format.',
    'invalid_file_type' => 'Invalid file type. Please upload allowed file types only.',
    'file_too_large' => 'File size exceeds maximum allowed limit.',
    'ticket_not_found' => 'Ticket not found or has been deleted.',
    'customer_not_found' => 'Customer not found or has been deleted.',
    'estimate_not_found' => 'Estimate not found or has been deleted.',
    'insufficient_stock' => 'Insufficient stock. Only %s items available.',
    'invalid_status_transition' => 'Invalid status transition.',
    'duplicate_record' => 'A record with this information already exists.',
    'required_field_missing' => 'Required field(s) are missing: %s',
    'invalid_email_format' => 'Please enter a valid email address.',
    'invalid_phone_format' => 'Please enter a valid phone number.',
    'password_mismatch' => 'Passwords do not match.',
    'weak_password' => 'Password does not meet security requirements.',
    'invalid_credentials' => 'Invalid username or password provided.',
    'account_locked' => 'Account has been locked due to multiple failed login attempts.',
    'maintenance_mode' => 'System is currently under maintenance. Please try again later.'
];

$WARNING_MESSAGES = [
    'unsaved_changes' => 'You have unsaved changes. Are you sure you want to leave?',
    'delete_confirmation' => 'Are you sure you want to delete this record? This action cannot be undone.',
    'low_stock_warning' => 'Stock level is low for item: %s. Only %s units remaining.',
    'duplicate_customer' => 'A customer with similar information already exists. Please check before creating.',
    'ticket_already_assigned' => 'This ticket is already assigned to another technician.',
    'estimate_expired' => 'This estimate has expired. Please create a new estimate.',
    'warranty_expired' => 'Warranty has expired for this device.',
    'payment_pending' => 'Payment is still pending for this ticket.'
];

// ========================================
// SYSTEM LIMITS
// ========================================

define('MAX_LOGIN_ATTEMPTS', 5);
define('LOGIN_LOCKOUT_DURATION', 900); // 15 minutes
define('SESSION_TIMEOUT', 28800); // 8 hours
define('PASSWORD_RESET_TIMEOUT', 3600); // 1 hour
define('TICKET_AUTO_TIMEOUT', 2592000); // 30 days
define('ESTIMATE_VALIDITY_DAYS', 7);

// ========================================
// EXPORT/IMPORT SETTINGS
// ========================================

define('EXPORT_FORMATS', ['csv', 'xlsx', 'pdf']);
define('IMPORT_FORMATS', ['csv', 'xlsx']);
define('MAX_EXPORT_ROWS', 10000);
define('MAX_IMPORT_SIZE', 10485760); // 10MB

// ========================================
// API CONSTANTS
// ========================================

define('API_VERSION', '1.0');
define('API_RATE_LIMIT', 1000); // requests per hour
define('API_TIMEOUT', 30); // seconds
define('API_PAGINATION_LIMIT', 100);

// ========================================
// EMAIL TEMPLATES
// ========================================

define('EMAIL_TEMPLATE_TICKET_CREATED', 'ticket_created');
define('EMAIL_TEMPLATE_ESTIMATE_READY', 'estimate_ready');
define('EMAIL_TEMPLATE_REPAIR_COMPLETED', 'repair_completed');
define('EMAIL_TEMPLATE_READY_PICKUP', 'ready_pickup');
define('EMAIL_TEMPLATE_WARRANTY_CREATED', 'warranty_created');

?>
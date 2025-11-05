<?php
/**
 * Gadget Service Management System - Configuration
 *
 * This file contains system-wide configuration settings
 * including database connection, paths, and security settings.
 */

// Prevent direct access
if (!defined('GADGET_SERVICE')) {
    exit('Direct access to this file is not allowed.');
}

// ========================================
// APPLICATION SETTINGS
// ========================================

// Application metadata
define('APP_NAME', 'Gadget Service Management System');
define('APP_VERSION', '1.0.0');
define('APP_DESCRIPTION', 'Professional gadget repair service management system');

// Debug settings
define('DEBUG_MODE', true); // Set to false in production
define('ERROR_REPORTING', E_ALL);
define('DISPLAY_ERRORS', true);

// Timezone settings
date_default_timezone_set('Asia/Jakarta');

// ========================================
// DATABASE CONFIGURATION
// ========================================

define('DB_HOST', 'localhost');
define('DB_NAME', 'gadget_service');
define('DB_USER', 'root');
define('DB_PASS', '');
define('DB_CHARSET', 'utf8mb4');

// Database connection settings
define('DB_OPTIONS', [
    PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
    PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
    PDO::ATTR_EMULATE_PREPARES => false,
    PDO::MYSQL_ATTR_INIT_COMMAND => "SET NAMES utf8mb4 COLLATE utf8mb4_unicode_ci"
]);

// ========================================
// PATH CONFIGURATION
// ========================================

// Base paths
define('ROOT_PATH', dirname(__DIR__));
define('BASE_URL', 'http://localhost/gadget-service'); // Update this for production
define('ASSETS_URL', BASE_URL . '/assets');
define('UPLOADS_URL', BASE_URL . '/uploads');

// Directory paths
define('CONFIG_PATH', ROOT_PATH . '/config');
define('INCLUDES_PATH', ROOT_PATH . '/includes');
define('PAGES_PATH', ROOT_PATH . '/pages');
define('API_PATH', ROOT_PATH . '/api');
define('UPLOADS_PATH', ROOT_PATH . '/uploads');
define('TEMPLATES_PATH', ROOT_PATH . '/templates');
define('ASSETS_PATH', ROOT_PATH . '/assets');

// Upload directories
define('UPLOAD_IMAGES_PATH', UPLOADS_PATH . '/images');
define('UPLOAD_DOCUMENTS_PATH', UPLOADS_PATH . '/documents');
define('UPLOAD_REPAIR_IMAGES_PATH', UPLOAD_IMAGES_PATH . '/repairs');
define('UPLOAD_DIAGNOSTIC_IMAGES_PATH', UPLOAD_IMAGES_PATH . '/diagnostics');

// ========================================
// SECURITY CONFIGURATION
// ========================================

// Session settings
define('SESSION_NAME', 'gadget_service_session');
define('SESSION_LIFETIME', 8 * 60 * 60); // 8 hours in seconds
define('SESSION_PATH', '/');
define('SESSION_DOMAIN', '');
define('SESSION_SECURE', false); // Set to true if using HTTPS
define('SESSION_HTTPONLY', true);

// Password settings
define('PASSWORD_MIN_LENGTH', 8);
define('PASSWORD_REQUIRE_UPPERCASE', true);
define('PASSWORD_REQUIRE_LOWERCASE', true);
define('PASSWORD_REQUIRE_NUMBER', true);
define('PASSWORD_REQUIRE_SPECIAL', true);

// CSRF protection
define('CSRF_TOKEN_NAME', 'csrf_token');
define('CSRF_TOKEN_EXPIRY', 3600); // 1 hour

// Rate limiting
define('LOGIN_ATTEMPT_LIMIT', 5);
define('LOGIN_LOCKOUT_TIME', 15 * 60); // 15 minutes

// File upload security
define('MAX_FILE_SIZE', 5 * 1024 * 1024); // 5MB
define('ALLOWED_IMAGE_TYPES', ['image/jpeg', 'image/png', 'image/gif', 'image/webp']);
define('ALLOWED_DOCUMENT_TYPES', ['application/pdf', 'application/msword', 'application/vnd.openxmlformats-officedocument.wordprocessingml.document']);

// ========================================
// BUSINESS RULES CONFIGURATION
// ========================================

// Ticket numbering
define('TICKET_PREFIX', 'TK');
define('CUSTOMER_PREFIX', 'CUST');
define('PAYMENT_PREFIX', 'PAY');

// Warranty settings
define('DEFAULT_WARRANTY_DAYS', 90); // 3 months default warranty
define('WARRANTY_PARTS_DAYS', 90);
define('WARRANTY_LABOR_DAYS', 30);

// Stock settings
define('LOW_STOCK_THRESHOLD', 0.3); // 30% of min stock level triggers warning
define('AUTO_REORDER_THRESHOLD', 0.1); // 10% triggers auto-reorder suggestion

// Pricing settings
define('LABOR_RATE_PER_HOUR', 50.00); // Default labor rate
define('DIAGNOSTIC_FEE', 25.00); // Base diagnostic fee

// ========================================
// EMAIL CONFIGURATION (for notifications)
// ========================================

define('SMTP_HOST', 'localhost');
define('SMTP_PORT', 587);
define('SMTP_USERNAME', '');
define('SMTP_PASSWORD', '');
define('SMTP_ENCRYPTION', 'tls'); // or 'ssl'
define('EMAIL_FROM_NAME', APP_NAME);
define('EMAIL_FROM_ADDRESS', 'noreply@gadgetservice.com');

// ========================================
// SYSTEM LIMITS AND THRESHOLDS
// ========================================

define('MAX_TICKETS_PER_DAY', 100);
define('MAX_CUSTOMERS_PER_DAY', 50);
define('MAX_UPLOAD_PER_DAY', 50);

// Pagination settings
define('ITEMS_PER_PAGE', 20);
define('MAX_ITEMS_PER_PAGE', 100);

// ========================================
// LOGGING CONFIGURATION
// ========================================

define('LOG_ENABLED', true);
define('LOG_FILE_PATH', ROOT_PATH . '/logs/app.log');
define('LOG_MAX_SIZE', 10 * 1024 * 1024); // 10MB
define('LOG_BACKUP_COUNT', 5);

// Log levels
define('LOG_LEVEL_ERROR', 1);
define('LOG_LEVEL_WARNING', 2);
define('LOG_LEVEL_INFO', 3);
define('LOG_LEVEL_DEBUG', 4);
define('LOG_CURRENT_LEVEL', LOG_LEVEL_DEBUG); // Set to lower level in production

// ========================================
// BACKUP CONFIGURATION
// ========================================

define('BACKUP_ENABLED', true);
define('BACKUP_PATH', ROOT_PATH . '/backups');
define('BACKUP_SCHEDULE', '0 2 * * *'); // Daily at 2 AM (cron format)
define('BACKUP_RETENTION_DAYS', 30);
define('BACKUP_INCLUDE_UPLOADS', true);

// ========================================
// API CONFIGURATION
// ========================================

define('API_ENABLED', true);
define('API_RATE_LIMIT', 100); // requests per hour per user
define('API_TIMEOUT', 30); // seconds
define('API_VERSION', 'v1');

// ========================================
// CUSTOMIZATION SETTINGS
// ========================================

// Company information
define('COMPANY_NAME', 'Gadget Service Center');
define('COMPANY_ADDRESS', 'Jl. Teknologi No. 123, Jakarta, Indonesia');
define('COMPANY_PHONE', '+6221-1234-5678');
define('COMPANY_EMAIL', 'info@gadgetservice.com');
define('COMPANY_WEBSITE', 'https://gadgetservice.com');

// Tax settings
define('TAX_RATE', 0.11); // 11% tax rate
define('TAX_ENABLED', true);

// Currency settings
define('CURRENCY_CODE', 'IDR');
define('CURRENCY_SYMBOL', 'Rp');
define('CURRENCY_DECIMAL_PLACES', 0);

// ========================================
// DEVELOPMENT/DEBUGGING SETTINGS
// ========================================

// Enable query logging in debug mode
if (DEBUG_MODE) {
    define('SAVE_QUERIES', true);
    define('SHOW_QUERY_TIMES', true);
} else {
    define('SAVE_QUERIES', false);
    define('SHOW_QUERY_TIMES', false);
}

// Maintenance mode
define('MAINTENANCE_MODE', false);
define('MAINTENANCE_MESSAGE', 'System is currently under maintenance. Please try again later.');

// ========================================
// ERROR HANDLING
// ========================================

// Custom error handler for development
if (DEBUG_MODE) {
    set_error_handler(function($severity, $message, $file, $line) {
        if (!(error_reporting() & $severity)) {
            return;
        }

        $error_type = match ($severity) {
            E_ERROR => 'Error',
            E_WARNING => 'Warning',
            E_PARSE => 'Parse Error',
            E_NOTICE => 'Notice',
            E_CORE_ERROR => 'Core Error',
            E_CORE_WARNING => 'Core Warning',
            E_COMPILE_ERROR => 'Compile Error',
            E_COMPILE_WARNING => 'Compile Warning',
            E_USER_ERROR => 'User Error',
            E_USER_WARNING => 'User Warning',
            E_USER_NOTICE => 'User Notice',
            E_STRICT => 'Strict Notice',
            E_RECOVERABLE_ERROR => 'Recoverable Error',
            E_DEPRECATED => 'Deprecated',
            E_USER_DEPRECATED => 'User Deprecated',
            default => 'Unknown Error'
        };

        error_log("[$error_type] $message in $file on line $line");

        if (DISPLAY_ERRORS) {
            echo "<div style='background: #f8d7da; color: #721c24; padding: 10px; margin: 10px; border: 1px solid #f5c6cb; border-radius: 4px;'>";
            echo "<strong>$error_type:</strong> $message<br>";
            echo "<em>File:</em> $file<br>";
            echo "<em>Line:</em> $line";
            echo "</div>";
        }
    });
}

// ========================================
// INITIALIZATION
// ========================================

// Start secure session
ini_set('session.cookie_httponly', 1);
ini_set('session.use_only_cookies', 1);
ini_set('session.cookie_samesite', 'Strict');

// Set error reporting
if (DEBUG_MODE) {
    error_reporting(ERROR_REPORTING);
    ini_set('display_errors', 1);
} else {
    error_reporting(0);
    ini_set('display_errors', 0);
}

// Set internal encoding
mb_internal_encoding('UTF-8');

// Include constants file if exists
if (file_exists(CONFIG_PATH . '/constants.php')) {
    require_once CONFIG_PATH . '/constants.php';
}

?>
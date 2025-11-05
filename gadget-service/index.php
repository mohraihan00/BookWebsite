<?php
/**
 * Gadget Service Management System - Main Entry Point
 *
 * This is the main entry point for the application.
 * All requests are routed through this file.
 */

// Define application constant
define('GADGET_SERVICE', true);

// Load configuration
require_once __DIR__ . '/config/config.php';

// Handle maintenance mode
if (MAINTENANCE_MODE) {
    http_response_code(503);
    ?>
    <!DOCTYPE html>
    <html lang="en">
    <head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <title>Maintenance - <?php echo APP_NAME; ?></title>
        <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    </head>
    <body class="bg-light">
        <div class="container vh-100 d-flex align-items-center justify-content-center">
            <div class="text-center">
                <div class="mb-4">
                    <i class="bi bi-tools display-1 text-primary"></i>
                </div>
                <h1 class="h3 mb-3">System Under Maintenance</h1>
                <p class="text-muted"><?php echo htmlspecialchars(MAINTENANCE_MESSAGE); ?></p>
                <div class="spinner-border text-primary" role="status">
                    <span class="visually-hidden">Loading...</span>
                </div>
            </div>
        </div>
    </body>
    </html>
    <?php
    exit;
}

// Initialize authentication
require_once __DIR__ . '/includes/auth.php';
$auth = Auth::getInstance();

// Check for auto-login from remember me token
if (!$auth->isLoggedIn()) {
    $auth->autoLogin();
}

// Load utility functions
require_once __DIR__ . '/includes/functions.php';

// Validate CSRF token for POST requests
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    validateCSRF();
}

// Get current page
$page = $_GET['page'] ?? 'dashboard';
$action = $_GET['action'] ?? '';

// Define page routing
$publicPages = [
    'login' => '/pages/auth/login.php',
    'logout' => '/pages/auth/logout.php',
    'forgot-password' => '/pages/auth/forgot_password.php',
    'reset-password' => '/pages/auth/reset_password.php'
];

$protectedPages = [
    'dashboard' => '/pages/auth/dashboard.php',
    'profile' => '/pages/auth/profile.php',
    'customers' => '/pages/customers/list.php',
    'customer-create' => '/pages/customers/create.php',
    'customer-view' => '/pages/customers/view.php',
    'customer-edit' => '/pages/customers/edit.php',
    'tickets' => '/pages/tickets/list.php',
    'ticket-create' => '/pages/tickets/create.php',
    'ticket-view' => '/pages/tickets/view.php',
    'ticket-diagnostic' => '/pages/tickets/diagnostic.php',
    'ticket-estimate' => '/pages/tickets/estimate.php',
    'ticket-repair' => '/pages/tickets/repair.php',
    'ticket-payment' => '/pages/tickets/payment.php',
    'inventory-parts' => '/pages/inventory/parts.php',
    'inventory-stock' => '/pages/inventory/stock.php',
    'inventory-purchase' => '/pages/inventory/purchase.php',
    'reports-revenue' => '/pages/reports/revenue.php',
    'reports-productivity' => '/pages/reports/productivity.php',
    'reports-inventory' => '/pages/reports/inventory.php',
    'admin-users' => '/pages/admin/users.php',
    'admin-settings' => '/pages/admin/settings.php',
    'admin-backup' => '/pages/admin/backup.php'
];

// Handle special pages
if ($page === 'logout') {
    require_once __DIR__ . '/pages/auth/logout.php';
    exit;
}

// Check if page requires login
if (!$auth->isLoggedIn() && !array_key_exists($page, $publicPages)) {
    $_SESSION['redirect_after_login'] = $_SERVER['REQUEST_URI'];
    redirect('/pages/auth/login.php');
}

// Route to appropriate page
$pageFile = '';

if (array_key_exists($page, $publicPages)) {
    $pageFile = __DIR__ . $publicPages[$page];
} elseif (array_key_exists($page, $protectedPages)) {
    $pageFile = __DIR__ . $protectedPages[$page];
} else {
    // Default to dashboard for authenticated users
    if ($auth->isLoggedIn()) {
        $pageFile = __DIR__ . '/pages/auth/dashboard.php';
    } else {
        $pageFile = __DIR__ . '/pages/auth/login.php';
    }
}

// Check if page file exists
if (!file_exists($pageFile)) {
    http_response_code(404);
    ?>
    <!DOCTYPE html>
    <html lang="en">
    <head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <title>Page Not Found - <?php echo APP_NAME; ?></title>
        <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
        <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.0/font/bootstrap-icons.css">
    </head>
    <body class="bg-light">
        <div class="container vh-100 d-flex align-items-center justify-content-center">
            <div class="text-center">
                <div class="mb-4">
                    <i class="bi bi-exclamation-triangle display-1 text-warning"></i>
                </div>
                <h1 class="h3 mb-3">Page Not Found</h1>
                <p class="text-muted mb-4">The page you are looking for does not exist.</p>
                <a href="<?php echo baseUrl('index.php'); ?>" class="btn btn-primary">
                    <i class="bi bi-house"></i> Go to Dashboard
                </a>
            </div>
        </div>
    </body>
    </html>
    <?php
    exit;
}

// Load the page
require_once $pageFile;
?>
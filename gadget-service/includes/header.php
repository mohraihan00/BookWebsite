<?php
/**
 * Gadget Service Management System - Header
 *
 * This file contains the HTML header, navigation bar,
 * and common meta tags for all pages.
 */

// Prevent direct access
if (!defined('GADGET_SERVICE')) {
    exit('Direct access to this file is not allowed.');
}

$auth = Auth::getInstance();
$currentUser = $auth->getCurrentUser();
$currentPage = getCurrentPage();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo isset($pageTitle) ? htmlspecialchars($pageTitle) . ' - ' : ''; ?><?php echo APP_NAME; ?></title>
    <meta name="description" content="<?php echo isset($pageDescription) ? htmlspecialchars($pageDescription) : APP_DESCRIPTION; ?>">
    <meta name="keywords" content="gadget service, repair management, customer service, inventory">
    <meta name="author" content="<?php echo COMPANY_NAME; ?>">

    <!-- Favicon -->
    <link rel="icon" type="image/x-icon" href="<?php echo assetsUrl('img/favicon.ico'); ?>">

    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">

    <!-- Bootstrap Icons -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.0/font/bootstrap-icons.css">

    <!-- Custom CSS -->
    <link rel="stylesheet" href="<?php echo assetsUrl('css/custom.css'); ?>">

    <!-- Datepicker CSS -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/flatpickr/dist/flatpickr.min.css">

    <!-- Select2 CSS -->
    <link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/select2-bootstrap-5-theme@1.3.0/dist/select2-bootstrap-5-theme.min.css" rel="stylesheet">

    <!-- DataTables CSS -->
    <link href="https://cdn.datatables.net/1.13.6/css/dataTables.bootstrap5.min.css" rel="stylesheet">
    <link href="https://cdn.datatables.net/buttons/2.4.2/css/buttons.bootstrap5.min.css" rel="stylesheet">

    <!-- Chart.js -->
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>

    <!-- Custom styles for this page -->
    <?php if (isset($pageStyles)): ?>
        <style>
            <?php echo $pageStyles; ?>
        </style>
    <?php endif; ?>
</head>
<body class="bg-light">
    <!-- Top Navigation Bar -->
    <nav class="navbar navbar-expand-lg navbar-dark bg-primary sticky-top">
        <div class="container-fluid">
            <!-- Brand -->
            <a class="navbar-brand d-flex align-items-center" href="<?php echo baseUrl('index.php'); ?>">
                <i class="bi bi-tools me-2"></i>
                <span class="fw-bold"><?php echo APP_NAME; ?></span>
            </a>

            <!-- Mobile toggle button -->
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
                <span class="navbar-toggler-icon"></span>
            </button>

            <!-- Navbar content -->
            <div class="collapse navbar-collapse" id="navbarNav">
                <ul class="navbar-nav me-auto">
                    <?php if ($auth->isLoggedIn()): ?>
                        <!-- Main navigation items -->
                        <li class="nav-item">
                            <a class="nav-link <?php echo $currentPage === 'dashboard' ? 'active' : ''; ?>"
                               href="<?php echo baseUrl('index.php?page=dashboard'); ?>">
                                <i class="bi bi-speedometer2"></i> Dashboard
                            </a>
                        </li>

                        <?php if ($auth->hasPermission('ticket.view')): ?>
                        <li class="nav-item dropdown">
                            <a class="nav-link dropdown-toggle <?php echo in_array($currentPage, ['tickets', 'ticket-create', 'ticket-view', 'ticket-diagnostic', 'ticket-estimate', 'ticket-repair', 'ticket-payment']) ? 'active' : ''; ?>"
                               href="#" id="ticketsDropdown" role="button" data-bs-toggle="dropdown">
                                <i class="bi bi-clipboard-check"></i> Tickets
                            </a>
                            <ul class="dropdown-menu">
                                <li><a class="dropdown-item" href="<?php echo baseUrl('index.php?page=tickets'); ?>">
                                    <i class="bi bi-list"></i> All Tickets
                                </a></li>
                                <?php if ($auth->hasPermission('ticket.create')): ?>
                                <li><a class="dropdown-item" href="<?php echo baseUrl('index.php?page=ticket-create'); ?>">
                                    <i class="bi bi-plus-circle"></i> New Ticket
                                </a></li>
                                <?php endif; ?>
                            </ul>
                        </li>
                        <?php endif; ?>

                        <?php if ($auth->hasPermission('customer.view')): ?>
                        <li class="nav-item dropdown">
                            <a class="nav-link dropdown-toggle <?php echo in_array($currentPage, ['customers', 'customer-create', 'customer-view', 'customer-edit']) ? 'active' : ''; ?>"
                               href="#" id="customersDropdown" role="button" data-bs-toggle="dropdown">
                                <i class="bi bi-people"></i> Customers
                            </a>
                            <ul class="dropdown-menu">
                                <li><a class="dropdown-item" href="<?php echo baseUrl('index.php?page=customers'); ?>">
                                    <i class="bi bi-list"></i> All Customers
                                </a></li>
                                <?php if ($auth->hasPermission('customer.create')): ?>
                                <li><a class="dropdown-item" href="<?php echo baseUrl('index.php?page=customer-create'); ?>">
                                    <i class="bi bi-plus-circle"></i> New Customer
                                </a></li>
                                <?php endif; ?>
                            </ul>
                        </li>
                        <?php endif; ?>

                        <?php if ($auth->hasPermission('parts.view') || $auth->hasPermission('inventory.view')): ?>
                        <li class="nav-item dropdown">
                            <a class="nav-link dropdown-toggle <?php echo in_array($currentPage, ['inventory-parts', 'inventory-stock', 'inventory-purchase']) ? 'active' : ''; ?>"
                               href="#" id="inventoryDropdown" role="button" data-bs-toggle="dropdown">
                                <i class="bi bi-box-seam"></i> Inventory
                            </a>
                            <ul class="dropdown-menu">
                                <li><a class="dropdown-item" href="<?php echo baseUrl('index.php?page=inventory-parts'); ?>">
                                    <i class="bi bi-tags"></i> Parts Management
                                </a></li>
                                <li><a class="dropdown-item" href="<?php echo baseUrl('index.php?page=inventory-stock'); ?>">
                                    <i class="bi bi-bar-chart"></i> Stock Status
                                </a></li>
                                <?php if ($auth->hasPermission('inventory.manage')): ?>
                                <li><a class="dropdown-item" href="<?php echo baseUrl('index.php?page=inventory-purchase'); ?>">
                                    <i class="bi bi-cart-plus"></i> Purchase Orders
                                </a></li>
                                <?php endif; ?>
                            </ul>
                        </li>
                        <?php endif; ?>

                        <?php if ($auth->hasPermission('report.view')): ?>
                        <li class="nav-item dropdown">
                            <a class="nav-link dropdown-toggle <?php echo in_array($currentPage, ['reports-revenue', 'reports-productivity', 'reports-inventory']) ? 'active' : ''; ?>"
                               href="#" id="reportsDropdown" role="button" data-bs-toggle="dropdown">
                                <i class="bi bi-graph-up"></i> Reports
                            </a>
                            <ul class="dropdown-menu">
                                <li><a class="dropdown-item" href="<?php echo baseUrl('index.php?page=reports-revenue'); ?>">
                                    <i class="bi bi-currency-dollar"></i> Revenue Reports
                                </a></li>
                                <li><a class="dropdown-item" href="<?php echo baseUrl('index.php?page=reports-productivity'); ?>">
                                    <i class="bi bi-person-workspace"></i> Productivity
                                </a></li>
                                <li><a class="dropdown-item" href="<?php echo baseUrl('index.php?page=reports-inventory'); ?>">
                                    <i class="bi bi-box"></i> Inventory Reports
                                </a></li>
                            </ul>
                        </li>
                        <?php endif; ?>

                        <?php if ($auth->hasRole('admin')): ?>
                        <li class="nav-item dropdown">
                            <a class="nav-link dropdown-toggle <?php echo in_array($currentPage, ['admin-users', 'admin-settings', 'admin-backup']) ? 'active' : ''; ?>"
                               href="#" id="adminDropdown" role="button" data-bs-toggle="dropdown">
                                <i class="bi bi-gear"></i> Administration
                            </a>
                            <ul class="dropdown-menu">
                                <li><a class="dropdown-item" href="<?php echo baseUrl('index.php?page=admin-users'); ?>">
                                    <i class="bi bi-people-fill"></i> User Management
                                </a></li>
                                <li><a class="dropdown-item" href="<?php echo baseUrl('index.php?page=admin-settings'); ?>">
                                    <i class="bi bi-gear-fill"></i> System Settings
                                </a></li>
                                <li><a class="dropdown-item" href="<?php echo baseUrl('index.php?page=admin-backup'); ?>">
                                    <i class="bi bi-cloud-download"></i> Backup & Restore
                                </a></li>
                            </ul>
                        </li>
                        <?php endif; ?>
                    <?php endif; ?>
                </ul>

                <!-- Right side items -->
                <ul class="navbar-nav">
                    <?php if ($auth->isLoggedIn()): ?>
                        <!-- Notifications -->
                        <li class="nav-item dropdown">
                            <a class="nav-link dropdown-toggle" href="#" id="notificationDropdown" role="button" data-bs-toggle="dropdown">
                                <i class="bi bi-bell"></i>
                                <span class="badge bg-danger ms-1" id="notificationCount">0</span>
                            </a>
                            <ul class="dropdown-menu dropdown-menu-end" style="min-width: 300px;">
                                <li class="dropdown-header">Notifications</li>
                                <li><hr class="dropdown-divider"></li>
                                <li><a class="dropdown-item text-center text-muted" href="#">No new notifications</a></li>
                            </ul>
                        </li>

                        <!-- User menu -->
                        <li class="nav-item dropdown">
                            <a class="nav-link dropdown-toggle" href="#" id="userDropdown" role="button" data-bs-toggle="dropdown">
                                <i class="bi bi-person-circle"></i>
                                <span class="d-none d-lg-inline"><?php echo htmlspecialchars($currentUser['full_name']); ?></span>
                            </a>
                            <ul class="dropdown-menu dropdown-menu-end">
                                <li class="dropdown-header">
                                    <div class="fw-bold"><?php echo htmlspecialchars($currentUser['full_name']); ?></div>
                                    <small class="text-muted"><?php echo htmlspecialchars($currentUser['role_name']); ?></small>
                                </li>
                                <li><hr class="dropdown-divider"></li>
                                <li><a class="dropdown-item" href="<?php echo baseUrl('index.php?page=profile'); ?>">
                                    <i class="bi bi-person"></i> Profile
                                </a></li>
                                <li><a class="dropdown-item" href="#" onclick="changePassword()">
                                    <i class="bi bi-key"></i> Change Password
                                </a></li>
                                <li><hr class="dropdown-divider"></li>
                                <li><a class="dropdown-item text-danger" href="<?php echo baseUrl('index.php?page=logout'); ?>">
                                    <i class="bi bi-box-arrow-right"></i> Logout
                                </a></li>
                            </ul>
                        </li>
                    <?php else: ?>
                        <li class="nav-item">
                            <a class="nav-link" href="<?php echo baseUrl('index.php?page=login'); ?>">
                                <i class="bi bi-box-arrow-in-right"></i> Login
                            </a>
                        </li>
                    <?php endif; ?>
                </ul>
            </div>
        </div>
    </nav>

    <!-- Flash Messages -->
    <div class="container-fluid mt-3">
        <?php displayFlashMessages(); ?>
    </div>

    <!-- Breadcrumb -->
    <?php if (isset($breadcrumb) && !empty($breadcrumb)): ?>
    <nav class="container-fluid" aria-label="breadcrumb">
        <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="<?php echo baseUrl('index.php'); ?>">Home</a></li>
            <?php foreach ($breadcrumb as $key => $value): ?>
                <?php if ($key === array_key_last($breadcrumb)): ?>
                    <li class="breadcrumb-item active" aria-current="page"><?php echo htmlspecialchars($value); ?></li>
                <?php else: ?>
                    <li class="breadcrumb-item"><a href="<?php echo htmlspecialchars($key); ?>"><?php echo htmlspecialchars($value); ?></a></li>
                <?php endif; ?>
            <?php endforeach; ?>
        </ol>
    </nav>
    <?php endif; ?>

    <!-- Main content area -->
    <div class="container-fluid">
        <div class="row">
            <?php if ($auth->isLoggedIn() && !in_array($currentPage, ['login', 'forgot-password', 'reset-password'])): ?>
                <!-- Sidebar (for desktop) -->
                <nav class="col-md-3 col-lg-2 d-md-block bg-light sidebar collapse">
                    <div class="position-sticky pt-3">
                        <?php require_once __DIR__ . '/sidebar.php'; ?>
                    </div>
                </nav>

                <!-- Main content -->
                <main class="col-md-9 ms-sm-auto col-lg-10 px-md-4">
            <?php else: ?>
                <!-- Full width content for login/public pages -->
                <main class="col-12">
            <?php endif; ?>

<!-- Continue with content in individual pages -->
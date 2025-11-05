<?php
/**
 * Gadget Service Management System - Sidebar
 *
 * This file contains the sidebar navigation menu
 * for authenticated users.
 */

// Prevent direct access
if (!defined('GADGET_SERVICE')) {
    exit('Direct access to this file is not allowed.');
}

$auth = Auth::getInstance();
$currentPage = getCurrentPage();
$currentUser = $auth->getCurrentUser();
?>

<ul class="nav flex-column">
    <!-- Dashboard -->
    <li class="nav-item">
        <a class="nav-link <?php echo $currentPage === 'dashboard' ? 'active' : ''; ?>"
           href="<?php echo baseUrl('index.php?page=dashboard'); ?>">
            <i class="bi bi-speedometer2"></i>
            <span>Dashboard</span>
        </a>
    </li>

    <!-- Quick Stats -->
    <li class="nav-item mt-3">
        <div class="sidebar-heading">Quick Actions</div>
    </li>

    <?php if ($auth->hasPermission('ticket.create')): ?>
    <li class="nav-item">
        <a class="nav-link" href="<?php echo baseUrl('index.php?page=ticket-create'); ?>">
            <i class="bi bi-plus-circle text-success"></i>
            <span>New Ticket</span>
        </a>
    </li>
    <?php endif; ?>

    <?php if ($auth->hasPermission('customer.create')): ?>
    <li class="nav-item">
        <a class="nav-link" href="<?php echo baseUrl('index.php?page=customer-create'); ?>">
            <i class="bi bi-person-plus text-info"></i>
            <span>New Customer</span>
        </a>
    </li>
    <?php endif; ?>

    <!-- Ticket Management -->
    <?php if ($auth->hasPermission('ticket.view')): ?>
    <li class="nav-item mt-3">
        <div class="sidebar-heading">Tickets</div>
    </li>

    <li class="nav-item">
        <a class="nav-link <?php echo $currentPage === 'tickets' ? 'active' : ''; ?>"
           href="<?php echo baseUrl('index.php?page=tickets'); ?>">
            <i class="bi bi-list"></i>
            <span>All Tickets</span>
            <?php if (isset($ticketStats['total'])): ?>
                <span class="badge bg-secondary ms-auto"><?php echo $ticketStats['total']; ?></span>
            <?php endif; ?>
        </a>
    </li>

    <?php if ($auth->hasRole('teknisi')): ?>
    <li class="nav-item">
        <a class="nav-link <?php echo $currentPage === 'my-tickets' ? 'active' : ''; ?>"
           href="<?php echo baseUrl('index.php?page=my-tickets'); ?>">
            <i class="bi bi-person-workspace"></i>
            <span>My Tickets</span>
            <?php if (isset($myTicketStats['active'])): ?>
                <span class="badge bg-warning ms-auto"><?php echo $myTicketStats['active']; ?></span>
            <?php endif; ?>
        </a>
    </li>
    <?php endif; ?>

    <?php if ($auth->hasRole('cs')): ?>
    <li class="nav-item">
        <a class="nav-link <?php echo $currentPage === 'awaiting-diagnosis' ? 'active' : ''; ?>"
           href="<?php echo baseUrl('index.php?page=awaiting-diagnosis'); ?>">
            <i class="bi bi-clock-history"></i>
            <span>Awaiting Diagnosis</span>
        </a>
    </li>
    <?php endif; ?>

    <li class="nav-item">
        <a class="nav-link <?php echo $currentPage === 'ticket-queue' ? 'active' : ''; ?>"
           href="<?php echo baseUrl('index.php?page=ticket-queue'); ?>">
            <i class="bi bi-activity"></i>
            <span>Today's Queue</span>
        </a>
    </li>
    <?php endif; ?>

    <!-- Customer Management -->
    <?php if ($auth->hasPermission('customer.view')): ?>
    <li class="nav-item mt-3">
        <div class="sidebar-heading">Customers</div>
    </li>

    <li class="nav-item">
        <a class="nav-link <?php echo $currentPage === 'customers' ? 'active' : ''; ?>"
           href="<?php echo baseUrl('index.php?page=customers'); ?>">
            <i class="bi bi-people"></i>
            <span>All Customers</span>
        </a>
    </li>

    <li class="nav-item">
        <a class="nav-link <?php echo $currentPage === 'recent-customers' ? 'active' : ''; ?>"
           href="<?php echo baseUrl('index.php?page=recent-customers'); ?>">
            <i class="bi bi-clock"></i>
            <span>Recent Customers</span>
        </a>
    </li>
    <?php endif; ?>

    <!-- Inventory Management -->
    <?php if ($auth->hasPermission('parts.view') || $auth->hasPermission('inventory.view')): ?>
    <li class="nav-item mt-3">
        <div class="sidebar-heading">Inventory</div>
    </li>

    <li class="nav-item">
        <a class="nav-link <?php echo $currentPage === 'inventory-parts' ? 'active' : ''; ?>"
           href="<?php echo baseUrl('index.php?page=inventory-parts'); ?>">
            <i class="bi bi-tags"></i>
            <span>Parts Catalog</span>
        </a>
    </li>

    <li class="nav-item">
        <a class="nav-link <?php echo $currentPage === 'inventory-stock' ? 'active' : ''; ?>"
           href="<?php echo baseUrl('index.php?page=inventory-stock'); ?>">
            <i class="bi bi-bar-chart"></i>
            <span>Stock Status</span>
            <?php if (isset($lowStockCount) && $lowStockCount > 0): ?>
                <span class="badge bg-danger ms-auto"><?php echo $lowStockCount; ?></span>
            <?php endif; ?>
        </a>
    </li>

    <?php if ($auth->hasPermission('inventory.manage')): ?>
    <li class="nav-item">
        <a class="nav-link <?php echo $currentPage === 'inventory-purchase' ? 'active' : ''; ?>"
           href="<?php echo baseUrl('index.php?page=inventory-purchase'); ?>">
            <i class="bi bi-cart-plus"></i>
            <span>Purchase Orders</span>
        </a>
    </li>
    <?php endif; ?>

    <li class="nav-item">
        <a class="nav-link <?php echo $currentPage === 'stock-alerts' ? 'active' : ''; ?>"
           href="<?php echo baseUrl('index.php?page=stock-alerts'); ?>">
            <i class="bi bi-exclamation-triangle"></i>
            <span>Stock Alerts</span>
        </a>
    </li>
    <?php endif; ?>

    <!-- Reports -->
    <?php if ($auth->hasPermission('report.view')): ?>
    <li class="nav-item mt-3">
        <div class="sidebar-heading">Reports</div>
    </li>

    <li class="nav-item">
        <a class="nav-link <?php echo $currentPage === 'reports-revenue' ? 'active' : ''; ?>"
           href="<?php echo baseUrl('index.php?page=reports-revenue'); ?>">
            <i class="bi bi-currency-dollar"></i>
            <span>Revenue</span>
        </a>
    </li>

    <li class="nav-item">
        <a class="nav-link <?php echo $currentPage === 'reports-productivity' ? 'active' : ''; ?>"
           href="<?php echo baseUrl('index.php?page=reports-productivity'); ?>">
            <i class="bi bi-graph-up"></i>
            <span>Productivity</span>
        </a>
    </li>

    <li class="nav-item">
        <a class="nav-link <?php echo $currentPage === 'reports-inventory' ? 'active' : ''; ?>"
           href="<?php echo baseUrl('index.php?page=reports-inventory'); ?>">
            <i class="bi bi-box-seam"></i>
            <span>Inventory</span>
        </a>
    </li>

    <li class="nav-item">
        <a class="nav-link <?php echo $currentPage === 'reports-turnaround' ? 'active' : ''; ?>"
           href="<?php echo baseUrl('index.php?page=reports-turnaround'); ?>">
            <i class="bi bi-stopwatch"></i>
            <span>Turnaround Time</span>
        </a>
    </li>
    <?php endif; ?>

    <!-- Administration -->
    <?php if ($auth->hasRole('admin')): ?>
    <li class="nav-item mt-3">
        <div class="sidebar-heading">Administration</div>
    </li>

    <li class="nav-item">
        <a class="nav-link <?php echo $currentPage === 'admin-users' ? 'active' : ''; ?>"
           href="<?php echo baseUrl('index.php?page=admin-users'); ?>">
            <i class="bi bi-people-fill"></i>
            <span>Users</span>
        </a>
    </li>

    <li class="nav-item">
        <a class="nav-link <?php echo $currentPage === 'admin-settings' ? 'active' : ''; ?>"
           href="<?php echo baseUrl('index.php?page=admin-settings'); ?>">
            <i class="bi bi-gear-fill"></i>
            <span>Settings</span>
        </a>
    </li>

    <li class="nav-item">
        <a class="nav-link <?php echo $currentPage === 'admin-backup' ? 'active' : ''; ?>"
           href="<?php echo baseUrl('index.php?page=admin-backup'); ?>">
            <i class="bi bi-cloud-download"></i>
            <span>Backup</span>
        </a>
    </li>

    <li class="nav-item">
        <a class="nav-link <?php echo $currentPage === 'admin-logs' ? 'active' : ''; ?>"
           href="<?php echo baseUrl('index.php?page=admin-logs'); ?>">
            <i class="bi bi-file-text"></i>
            <span>System Logs</span>
        </a>
    </li>
    <?php endif; ?>

    <!-- Customer Portal -->
    <?php if ($auth->hasRole('pelanggan')): ?>
    <li class="nav-item mt-3">
        <div class="sidebar-heading">My Account</div>
    </li>

    <li class="nav-item">
        <a class="nav-link <?php echo $currentPage === 'my-tickets' ? 'active' : ''; ?>"
           href="<?php echo baseUrl('index.php?page=my-tickets'); ?>">
            <i class="bi bi-clipboard-check"></i>
            <span>My Tickets</span>
        </a>
    </li>

    <li class="nav-item">
        <a class="nav-link <?php echo $currentPage === 'my-warranties' ? 'active' : ''; ?>"
           href="<?php echo baseUrl('index.php?page=my-warranties'); ?>">
            <i class="bi bi-shield-check"></i>
            <span>Warranties</span>
        </a>
    </li>

    <li class="nav-item">
        <a class="nav-link <?php echo $currentPage === 'profile' ? 'active' : ''; ?>"
           href="<?php echo baseUrl('index.php?page=profile'); ?>">
            <i class="bi bi-person"></i>
            <span>Profile</span>
        </a>
    </li>
    <?php endif; ?>
</ul>

<hr class="sidebar-divider">

<!-- Quick Info -->
<div class="sidebar-info">
    <div class="small text-muted">
        <div class="mb-2">
            <strong>System Status:</strong>
            <span class="badge bg-success">Online</span>
        </div>
        <div class="mb-2">
            <strong>Version:</strong> <?php echo APP_VERSION; ?>
        </div>
        <div class="mb-2">
            <strong>Logged in as:</strong><br>
            <?php echo htmlspecialchars($currentUser['full_name']); ?><br>
            <small class="text-capitalize"><?php echo htmlspecialchars($currentUser['role_name']); ?></small>
        </div>
        <div class="mb-2">
            <strong>Last login:</strong><br>
            <small><?php echo formatDateTime($currentUser['last_login']); ?></small>
        </div>
    </div>
</div>

<style>
.sidebar-heading {
    font-size: .75rem;
    text-transform: uppercase;
    font-weight: 800;
    color: #6c757d;
    padding: 0.5rem 1rem;
    margin-top: 1rem;
}

.sidebar .nav-link {
    font-weight: 500;
    color: #333;
    padding: 0.5rem 1rem;
    border-radius: 0.25rem;
    margin: 0.125rem 0;
    display: flex;
    align-items: center;
}

.sidebar .nav-link:hover {
    color: #007bff;
    background-color: #f8f9fa;
}

.sidebar .nav-link.active {
    color: #fff;
    background-color: #007bff;
}

.sidebar .nav-link i {
    margin-right: 0.5rem;
    width: 16px;
    text-align: center;
}

.sidebar-info {
    padding: 1rem;
    background-color: #f8f9fa;
    border-radius: 0.25rem;
    margin: 1rem;
}

@media (max-width: 767.98px) {
    .sidebar {
        position: fixed;
        top: 56px;
        bottom: 0;
        left: -100%;
        z-index: 1000;
        width: 280px;
        transition: left 0.3s ease;
    }

    .sidebar.show {
        left: 0;
    }

    .sidebar-overlay {
        position: fixed;
        top: 56px;
        left: 0;
        right: 0;
        bottom: 0;
        background-color: rgba(0, 0, 0, 0.5);
        z-index: 999;
        display: none;
    }

    .sidebar-overlay.show {
        display: block;
    }
}
</style>
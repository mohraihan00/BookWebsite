<?php
/**
 * Gadget Service Management System - Footer
 *
 * This file contains the HTML footer and closing tags.
 */

// Prevent direct access
if (!defined('GADGET_SERVICE')) {
    exit('Direct access to this file is not allowed.');
}
?>

            <!-- Close main content -->
            </main>
        </div>
    </div>

    <!-- Footer -->
    <footer class="bg-dark text-light mt-5 py-4">
        <div class="container-fluid">
            <div class="row">
                <div class="col-md-4">
                    <h5 class="mb-3"><?php echo APP_NAME; ?></h5>
                    <p class="text-muted">
                        Professional gadget service management system for efficient repair operations,
                        customer management, and inventory control.
                    </p>
                    <div class="mt-3">
                        <strong>Version:</strong> <?php echo APP_VERSION; ?>
                    </div>
                </div>

                <div class="col-md-4">
                    <h5 class="mb-3">Quick Links</h5>
                    <ul class="list-unstyled">
                        <li class="mb-2">
                            <a href="<?php echo baseUrl('index.php?page=dashboard'); ?>" class="text-muted text-decoration-none">
                                <i class="bi bi-speedometer2"></i> Dashboard
                            </a>
                        </li>
                        <li class="mb-2">
                            <a href="<?php echo baseUrl('index.php?page=tickets'); ?>" class="text-muted text-decoration-none">
                                <i class="bi bi-clipboard-check"></i> Tickets
                            </a>
                        </li>
                        <li class="mb-2">
                            <a href="<?php echo baseUrl('index.php?page=customers'); ?>" class="text-muted text-decoration-none">
                                <i class="bi bi-people"></i> Customers
                            </a>
                        </li>
                        <li class="mb-2">
                            <a href="<?php echo baseUrl('index.php?page=inventory-stock'); ?>" class="text-muted text-decoration-none">
                                <i class="bi bi-box-seam"></i> Inventory
                            </a>
                        </li>
                    </ul>
                </div>

                <div class="col-md-4">
                    <h5 class="mb-3">Support</h5>
                    <ul class="list-unstyled">
                        <li class="mb-2">
                            <a href="#" class="text-muted text-decoration-none" onclick="showHelp()">
                                <i class="bi bi-question-circle"></i> Help Center
                            </a>
                        </li>
                        <li class="mb-2">
                            <a href="#" class="text-muted text-decoration-none" onclick="showDocumentation()">
                                <i class="bi bi-book"></i> Documentation
                            </a>
                        </li>
                        <li class="mb-2">
                            <a href="mailto:<?php echo htmlspecialchars(COMPANY_EMAIL); ?>" class="text-muted text-decoration-none">
                                <i class="bi bi-envelope"></i> Contact Support
                            </a>
                        </li>
                        <li class="mb-2">
                            <a href="#" class="text-muted text-decoration-none" onclick="reportIssue()">
                                <i class="bi bi-bug"></i> Report Issue
                            </a>
                        </li>
                    </ul>
                </div>
            </div>

            <hr class="border-secondary">

            <div class="row align-items-center">
                <div class="col-md-6">
                    <p class="text-muted mb-0">
                        &copy; <?php echo date('Y'); ?> <?php echo COMPANY_NAME; ?>. All rights reserved.
                    </p>
                </div>
                <div class="col-md-6 text-md-end">
                    <small class="text-muted">
                        <i class="bi bi-geo-alt"></i> <?php echo htmlspecialchars(COMPANY_ADDRESS); ?><br>
                        <i class="bi bi-telephone"></i> <?php echo htmlspecialchars(COMPANY_PHONE); ?> |
                        <i class="bi bi-envelope"></i> <?php echo htmlspecialchars(COMPANY_EMAIL); ?>
                    </small>
                </div>
            </div>
        </div>
    </footer>

    <!-- Change Password Modal -->
    <div class="modal fade" id="changePasswordModal" tabindex="-1">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Change Password</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <form id="changePasswordForm">
                    <div class="modal-body">
                        <div class="mb-3">
                            <label for="currentPassword" class="form-label">Current Password</label>
                            <input type="password" class="form-control" id="currentPassword" name="current_password" required>
                        </div>
                        <div class="mb-3">
                            <label for="newPassword" class="form-label">New Password</label>
                            <input type="password" class="form-control" id="newPassword" name="new_password" required>
                            <div class="form-text">
                                Password must be at least <?php echo PASSWORD_MIN_LENGTH; ?> characters long
                                <?php if (PASSWORD_REQUIRE_UPPERCASE) echo 'and contain uppercase letter'; ?>
                                <?php if (PASSWORD_REQUIRE_LOWERCASE) echo 'and contain lowercase letter'; ?>
                                <?php if (PASSWORD_REQUIRE_NUMBER) echo 'and contain number'; ?>
                                <?php if (PASSWORD_REQUIRE_SPECIAL) echo 'and contain special character'; ?>.
                            </div>
                        </div>
                        <div class="mb-3">
                            <label for="confirmPassword" class="form-label">Confirm New Password</label>
                            <input type="password" class="form-control" id="confirmPassword" name="confirm_password" required>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                        <button type="submit" class="btn btn-primary">Change Password</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- Loading Overlay -->
    <div id="loadingOverlay" class="loading-overlay" style="display: none;">
        <div class="loading-spinner">
            <div class="spinner-border text-primary" role="status">
                <span class="visually-hidden">Loading...</span>
            </div>
            <div class="mt-2">Please wait...</div>
        </div>
    </div>

    <!-- Bootstrap JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>

    <!-- jQuery -->
    <script src="https://code.jquery.com/jquery-3.7.0.min.js"></script>

    <!-- Select2 JS -->
    <script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>

    <!-- DataTables JS -->
    <script src="https://cdn.datatables.net/1.13.6/js/jquery.dataTables.min.js"></script>
    <script src="https://cdn.datatables.net/1.13.6/js/dataTables.bootstrap5.min.js"></script>
    <script src="https://cdn.datatables.net/buttons/2.4.2/js/dataTables.buttons.min.js"></script>
    <script src="https://cdn.datatables.net/buttons/2.4.2/js/buttons.bootstrap5.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jszip/3.10.1/jszip.min.js"></script>
    <script src="https://cdn.datatables.net/buttons/2.4.2/js/buttons.html5.min.js"></script>

    <!-- Flatpickr Datepicker -->
    <script src="https://cdn.jsdelivr.net/npm/flatpickr"></script>

    <!-- Chart.js -->
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>

    <!-- Bootstrap Notify -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap-notify@3.1.3/bootstrap-notify.min.js"></script>

    <!-- jQuery Validation -->
    <script src="https://cdn.jsdelivr.net/npm/jquery-validation@1.19.5/dist/jquery.validate.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/jquery-validation@1.19.5/dist/localization/messages_en.min.js"></script>

    <!-- Custom JavaScript -->
    <script src="<?php echo assetsUrl('js/app.js'); ?>"></script>

    <!-- Page-specific scripts -->
    <?php if (isset($pageScripts)): ?>
        <script>
            <?php echo $pageScripts; ?>
        </script>
    <?php endif; ?>

    <script>
        // Global functions
        function changePassword() {
            $('#changePasswordModal').modal('show');
        }

        function showHelp() {
            window.open('<?php echo baseUrl('help/index.php'); ?>', '_blank', 'width=800,height=600,scrollbars=yes');
        }

        function showDocumentation() {
            window.open('<?php echo baseUrl('docs/index.php'); ?>', '_blank', 'width=800,height=600,scrollbars=yes');
        }

        function reportIssue() {
            window.open('<?php echo baseUrl('support/issue.php'); ?>', '_blank', 'width=600,height=400,scrollbars=yes');
        }

        function showLoading() {
            $('#loadingOverlay').show();
        }

        function hideLoading() {
            $('#loadingOverlay').hide();
        }

        // Change password form handler
        $('#changePasswordForm').on('submit', function(e) {
            e.preventDefault();

            const formData = new FormData(this);

            // Validate passwords match
            const newPassword = formData.get('new_password');
            const confirmPassword = formData.get('confirm_password');

            if (newPassword !== confirmPassword) {
                showNotification('Passwords do not match', 'error');
                return;
            }

            showLoading();

            fetch('<?php echo baseUrl('api/auth.php'); ?>', {
                method: 'POST',
                body: formData
            })
            .then(response => response.json())
            .then(data => {
                hideLoading();
                if (data.success) {
                    showNotification('Password changed successfully', 'success');
                    $('#changePasswordModal').modal('hide');
                    this.reset();
                } else {
                    showNotification(data.message || 'Failed to change password', 'error');
                }
            })
            .catch(error => {
                hideLoading();
                showNotification('An error occurred while changing password', 'error');
                console.error('Error:', error);
            });
        });

        // Initialize tooltips
        var tooltipTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="tooltip"]'));
        var tooltipList = tooltipTriggerList.map(function (tooltipTriggerEl) {
            return new bootstrap.Tooltip(tooltipTriggerEl);
        });

        // Initialize popovers
        var popoverTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="popover"]'));
        var popoverList = popoverTriggerList.map(function (popoverTriggerEl) {
            return new bootstrap.Popover(popoverTriggerEl);
        });

        // Auto-refresh notifications
        function refreshNotifications() {
            fetch('<?php echo baseUrl('api/notifications.php'); ?>')
                .then(response => response.json())
                .then(data => {
                    if (data.count > 0) {
                        $('#notificationCount').text(data.count);
                        $('#notificationCount').removeClass('d-none');
                    } else {
                        $('#notificationCount').addClass('d-none');
                    }
                })
                .catch(error => console.error('Error fetching notifications:', error));
        }

        // Refresh notifications every 30 seconds
        setInterval(refreshNotifications, 30000);
        refreshNotifications(); // Initial load

        // Session timeout warning
        let sessionTimeout;

        function resetSessionTimeout() {
            clearTimeout(sessionTimeout);
            sessionTimeout = setTimeout(function() {
                if (confirm('Your session is about to expire. Click OK to extend your session.')) {
                    fetch('<?php echo baseUrl('api/keepalive.php'); ?>');
                    resetSessionTimeout();
                } else {
                    window.location.href = '<?php echo baseUrl('index.php?page=logout'); ?>';
                }
            }, <?php echo SESSION_TIMEOUT - 300000; ?>); // Show warning 5 minutes before timeout
        }

        // Reset timeout on user activity
        $(document).on('click keypress scroll', resetSessionTimeout);
        resetSessionTimeout();

        // Global error handler
        window.addEventListener('error', function(e) {
            console.error('JavaScript error:', e.error);
            // Log errors to server in production
            if (!<?php echo DEBUG_MODE ? 'true' : 'false'; ?>) {
                fetch('<?php echo baseUrl('api/log_error.php'); ?>', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                    },
                    body: JSON.stringify({
                        message: e.error.message,
                        filename: e.filename,
                        lineno: e.lineno,
                        colno: e.colno,
                        stack: e.error.stack
                    })
                });
            }
        });

        // Global AJAX error handler
        $(document).ajaxError(function(event, xhr, settings, error) {
            console.error('AJAX error:', error, xhr.responseText);
            if (xhr.status === 401) {
                // Session expired
                window.location.href = '<?php echo baseUrl('index.php?page=login'); ?>';
            } else if (xhr.status >= 500) {
                showNotification('Server error occurred. Please try again later.', 'error');
            }
        });

        // Performance monitoring
        if (window.performance && window.performance.timing) {
            window.addEventListener('load', function() {
                const loadTime = window.performance.timing.loadEventEnd - window.performance.timing.navigationStart;
                console.log('Page load time:', loadTime + 'ms');

                // Log slow pages
                if (loadTime > 3000) {
                    logMessage('Slow page load: ' + window.location.href + ' took ' + loadTime + 'ms', 'WARNING');
                }
            });
        }
    </script>

    </body>
    </html>
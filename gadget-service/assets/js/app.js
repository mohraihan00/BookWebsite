/**
 * Gadget Service Management System - Main JavaScript
 *
 * Main application JavaScript file with common functions,
 * utilities, and event handlers.
 */

// Global namespace
window.GadgetService = window.GadgetService || {};

// ========================================
   CONFIGURATION
   ========================================

GadgetService.Config = {
    baseUrl: window.BASE_URL || '/',
    assetsUrl: window.ASSETS_URL || '/assets',
    uploadsUrl: window.UPLOADS_URL || '/uploads',
    csrfToken: document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') || '',
    apiTimeout: 30000,
    notificationTimeout: 5000
};

// ========================================
   UTILITY FUNCTIONS
   ========================================

GadgetService.Utils = {
    /**
     * Format currency amount
     */
    formatCurrency: function(amount, symbol = 'Rp') {
        return symbol + Number(amount).toLocaleString('id-ID', {
            minimumFractionDigits: 0,
            maximumFractionDigits: 0
        });
    },

    /**
     * Format date
     */
    formatDate: function(date, format = 'DD MMM YYYY') {
        if (!date) return '-';
        return moment(date).format(format);
    },

    /**
     * Format date time
     */
    formatDateTime: function(datetime, format = 'DD MMM YYYY HH:mm') {
        if (!datetime) return '-';
        return moment(datetime).format(format);
    },

    /**
     * Time ago helper
     */
    timeAgo: function(datetime) {
        if (!datetime) return '-';
        return moment(datetime).fromNow();
    },

    /**
     * Debounce function
     */
    debounce: function(func, wait, immediate) {
        let timeout;
        return function executedFunction() {
            const context = this;
            const args = arguments;
            const later = function() {
                timeout = null;
                if (!immediate) func.apply(context, args);
            };
            const callNow = immediate && !timeout;
            clearTimeout(timeout);
            timeout = setTimeout(later, wait);
            if (callNow) func.apply(context, args);
        };
    },

    /**
     * Throttle function
     */
    throttle: function(func, limit) {
        let inThrottle;
        return function() {
            const args = arguments;
            const context = this;
            if (!inThrottle) {
                func.apply(context, args);
                inThrottle = true;
                setTimeout(() => inThrottle = false, limit);
            }
        };
    },

    /**
     * Generate slug
     */
    slugify: function(text) {
        return text
            .toString()
            .toLowerCase()
            .trim()
            .replace(/\s+/g, '-')
            .replace(/[^\w\-]+/g, '')
            .replace(/\-\-+/g, '-')
            .replace(/^-+/, '')
            .replace(/-+$/, '');
    },

    /**
     * Copy to clipboard
     */
    copyToClipboard: function(text, element) {
        if (navigator.clipboard && window.isSecureContext) {
            return navigator.clipboard.writeText(text).then(() => {
                this.showNotification('Copied to clipboard!', 'success');
            });
        } else {
            // Fallback for older browsers
            const textArea = document.createElement('textarea');
            textArea.value = text;
            textArea.style.position = 'fixed';
            textArea.style.left = '-999999px';
            textArea.style.top = '-999999px';
            document.body.appendChild(textArea);
            textArea.focus();
            textArea.select();
            return new Promise((resolve, reject) => {
                document.execCommand('copy') ? resolve() : reject();
                textArea.remove();
                this.showNotification('Copied to clipboard!', 'success');
            });
        }
    },

    /**
     * Confirm dialog
     */
    confirm: function(message, callback, title = 'Confirm Action') {
        if (window.Swal) {
            Swal.fire({
                title: title,
                text: message,
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#3085d6',
                cancelButtonColor: '#d33',
                confirmButtonText: 'Yes, proceed!',
                cancelButtonText: 'Cancel'
            }).then((result) => {
                if (result.isConfirmed && callback) {
                    callback();
                }
            });
        } else {
            if (confirm(message) && callback) {
                callback();
            }
        }
    },

    /**
     * Parse URL parameters
     */
    getUrlParams: function() {
        const params = {};
        window.location.search.substring(1).split('&').forEach(param => {
            const [key, value] = param.split('=');
            if (key) {
                params[decodeURIComponent(key)] = decodeURIComponent(value || '');
            }
        });
        return params;
    },

    /**
     * Update URL parameter
     */
    updateUrlParam: function(key, value) {
        const url = new URL(window.location);
        if (value === null || value === '') {
            url.searchParams.delete(key);
        } else {
            url.searchParams.set(key, value);
        }
        window.history.replaceState({}, '', url);
    }
};

// ========================================
   NOTIFICATION SYSTEM
   ========================================

GadgetService.Notification = {
    /**
     * Show notification
     */
    show: function(message, type = 'info', options = {}) {
        const defaults = {
            delay: GadgetService.Config.notificationTimeout,
            allow_dismiss: true,
            placement: { from: 'top', align: 'right' },
            animate: { enter: 'animated fadeInDown', exit: 'animated fadeOutUp' }
        };

        const settings = Object.assign({}, defaults, options);

        if (window.$.notify) {
            $.notify(message, {
                type: type,
                delay: settings.delay,
                allow_dismiss: settings.allow_dismiss,
                placement: settings.placement,
                animate: settings.animate,
                template: `
                    <div data-notify="container" class="alert alert-{0}" role="alert">
                        <button type="button" aria-hidden="true" class="btn-close" data-notify="dismiss"></button>
                        <span data-notify="icon"></span>
                        <span data-notify="title">{1}</span>
                        <span data-notify="message">{2}</span>
                        <div class="progress" data-notify="progressbar">
                            <div class="progress-bar progress-bar-{0}" role="progressbar" aria-valuenow="0" aria-valuemin="0" aria-valuemax="100" style="width: 0%;"></div>
                        </div>
                        <a href="{3}" target="{4}" data-notify="url"></a>
                    </div>
                `
            });
        } else {
            // Fallback to console
            console.log(`[${type.toUpperCase()}] ${message}`);
            alert(message);
        }
    },

    /**
     * Show success notification
     */
    success: function(message, options) {
        this.show(message, 'success', options);
    },

    /**
     * Show error notification
     */
    error: function(message, options) {
        this.show(message, 'danger', options);
    },

    /**
     * Show warning notification
     */
    warning: function(message, options) {
        this.show(message, 'warning', options);
    },

    /**
     * Show info notification
     */
    info: function(message, options) {
        this.show(message, 'info', options);
    }
};

// ========================================
   API SERVICE
   ========================================

GadgetService.API = {
    /**
     * Make API request
     */
    request: function(endpoint, options = {}) {
        const defaults = {
            method: 'GET',
            headers: {
                'Content-Type': 'application/json',
                'X-Requested-With': 'XMLHttpRequest'
            },
            timeout: GadgetService.Config.apiTimeout
        };

        const config = Object.assign({}, defaults, options);

        // Add CSRF token if available
        if (GadgetService.Config.csrfToken) {
            config.headers['X-CSRF-Token'] = GadgetService.Config.csrfToken;
        }

        // Handle FormData (file uploads)
        if (config.body instanceof FormData) {
            delete config.headers['Content-Type'];
        }

        return fetch(GadgetService.Config.baseUrl + 'api/' + endpoint, config)
            .then(this.handleResponse)
            .catch(this.handleError);
    },

    /**
     * Handle API response
     */
    handleResponse: function(response) {
        if (!response.ok) {
            if (response.status === 401) {
                window.location.href = GadgetService.Config.baseUrl + 'index.php?page=login';
                return Promise.reject(new Error('Unauthorized'));
            }
            return response.json().then(data => {
                throw new Error(data.message || 'Request failed');
            });
        }

        const contentType = response.headers.get('content-type');
        if (contentType && contentType.includes('application/json')) {
            return response.json();
        } else {
            return response.text();
        }
    },

    /**
     * Handle API errors
     */
    handleError: function(error) {
        console.error('API Error:', error);
        GadgetService.Notification.error(error.message || 'An error occurred');
        throw error;
    },

    /**
     * GET request
     */
    get: function(endpoint, params = {}) {
        const url = new URLSearchParams(params).toString();
        const fullUrl = url ? `${endpoint}?${url}` : endpoint;
        return this.request(fullUrl);
    },

    /**
     * POST request
     */
    post: function(endpoint, data = {}) {
        return this.request(endpoint, {
            method: 'POST',
            body: JSON.stringify(data)
        });
    },

    /**
     * PUT request
     */
    put: function(endpoint, data = {}) {
        return this.request(endpoint, {
            method: 'PUT',
            body: JSON.stringify(data)
        });
    },

    /**
     * DELETE request
     */
    delete: function(endpoint) {
        return this.request(endpoint, {
            method: 'DELETE'
        });
    },

    /**
     * Upload file
     */
    upload: function(endpoint, file, additionalData = {}) {
        const formData = new FormData();
        formData.append('file', file);

        // Add additional form data
        Object.keys(additionalData).forEach(key => {
            formData.append(key, additionalData[key]);
        });

        return this.request(endpoint, {
            method: 'POST',
            body: formData
        });
    }
};

// ========================================
   FORM HELPERS
   ========================================

GadgetService.Form = {
    /**
     * Serialize form to object
     */
    serialize: function(form) {
        const formData = new FormData(form);
        const object = {};

        formData.forEach((value, key) => {
            if (object[key]) {
                if (!Array.isArray(object[key])) {
                    object[key] = [object[key]];
                }
                object[key].push(value);
            } else {
                object[key] = value;
            }
        });

        return object;
    },

    /**
     * Reset form
     */
    reset: function(form) {
        form.reset();

        // Clear validation errors
        const errors = form.querySelectorAll('.is-invalid');
        errors.forEach(element => {
            element.classList.remove('is-invalid');
        });

        const errorMessages = form.querySelectorAll('.invalid-feedback');
        errorMessages.forEach(element => {
            element.remove();
        });
    },

    /**
     * Validate form
     */
    validate: function(form, rules = {}) {
        let isValid = true;
        const errors = {};

        // Custom validation
        Object.keys(rules).forEach(fieldName => {
            const field = form.querySelector(`[name="${fieldName}"]`);
            const fieldRules = rules[fieldName];
            const value = field?.value?.trim();

            if (fieldRules.required && !value) {
                errors[fieldName] = `${fieldName} is required`;
                isValid = false;
            }

            if (value && fieldRules.email && !this.isValidEmail(value)) {
                errors[fieldName] = 'Please enter a valid email address';
                isValid = false;
            }

            if (value && fieldRules.phone && !this.isValidPhone(value)) {
                errors[fieldName] = 'Please enter a valid phone number';
                isValid = false;
            }

            if (value && fieldRules.minLength && value.length < fieldRules.minLength) {
                errors[fieldName] = `Minimum length is ${fieldRules.minLength} characters`;
                isValid = false;
            }

            if (value && fieldRules.maxLength && value.length > fieldRules.maxLength) {
                errors[fieldName] = `Maximum length is ${fieldRules.maxLength} characters`;
                isValid = false;
            }
        });

        // Display errors
        this.showErrors(form, errors);

        return isValid;
    },

    /**
     * Show form errors
     */
    showErrors: function(form, errors) {
        // Clear existing errors
        this.clearErrors(form);

        Object.keys(errors).forEach(fieldName => {
            const field = form.querySelector(`[name="${fieldName}"]`);
            if (field) {
                field.classList.add('is-invalid');

                const errorElement = document.createElement('div');
                errorElement.className = 'invalid-feedback';
                errorElement.textContent = errors[fieldName];

                field.parentNode.appendChild(errorElement);
            }
        });
    },

    /**
     * Clear form errors
     */
    clearErrors: function(form) {
        const invalidFields = form.querySelectorAll('.is-invalid');
        invalidFields.forEach(field => {
            field.classList.remove('is-invalid');
        });

        const errorMessages = form.querySelectorAll('.invalid-feedback');
        errorMessages.forEach(message => {
            message.remove();
        });
    },

    /**
     * Validate email
     */
    isValidEmail: function(email) {
        return /^[^\s@]+@[^\s@]+\.[^\s@]+$/.test(email);
    },

    /**
     * Validate phone
     */
    isValidPhone: function(phone) {
        return /^[\+]?[0-9]{10,15}$/.test(phone.replace(/[\s\-\(\)]/g, ''));
    }
};

// ========================================
   DATA TABLES HELPERS
   ========================================

GadgetService.DataTable = {
    /**
     * Initialize data table
     */
    init: function(selector, options = {}) {
        const defaults = {
            responsive: true,
            pageLength: 25,
            dom: 'Bfrtip',
            buttons: [
                'pageLength',
                {
                    extend: 'excel',
                    text: '<i class="bi bi-file-earmark-excel"></i> Excel',
                    className: 'btn btn-sm btn-success'
                },
                {
                    extend: 'pdf',
                    text: '<i class="bi bi-file-earmark-pdf"></i> PDF',
                    className: 'btn btn-sm btn-danger'
                },
                {
                    extend: 'print',
                    text: '<i class="bi bi-printer"></i> Print',
                    className: 'btn btn-sm btn-info'
                }
            ],
            language: {
                search: 'Search:',
                lengthMenu: 'Show _MENU_ entries',
                info: 'Showing _START_ to _END_ of _TOTAL_ entries',
                paginate: {
                    first: 'First',
                    last: 'Last',
                    next: 'Next',
                    previous: 'Previous'
                },
                emptyTable: 'No data available in table',
                zeroRecords: 'No matching records found'
            }
        };

        const settings = Object.assign({}, defaults, options);

        return $(selector).DataTable(settings);
    },

    /**
     * Create AJAX data table
     */
    createAjax: function(selector, url, options = {}) {
        const defaults = {
            ajax: {
                url: GadgetService.Config.baseUrl + url,
                type: 'GET',
                data: function(d) {
                    // Add custom parameters
                    return d;
                },
                error: function(xhr, error, thrown) {
                    GadgetService.Notification.error('Error loading data');
                }
            },
            processing: true,
            serverSide: true
        };

        const settings = Object.assign({}, defaults, options);
        return this.init(selector, settings);
    }
};

// ========================================
   MODAL HELPERS
   ========================================

GadgetService.Modal = {
    /**
     * Show modal
     */
    show: function(selector, options = {}) {
        const modal = new bootstrap.Modal(document.querySelector(selector), options);
        modal.show();
        return modal;
    },

    /**
     * Hide modal
     */
    hide: function(selector) {
        const modal = bootstrap.Modal.getInstance(document.querySelector(selector));
        if (modal) {
            modal.hide();
        }
    },

    /**
     * Create dynamic modal
     */
    create: function(title, content, options = {}) {
        const modalId = 'dynamic-modal-' + Date.now();
        const modalHtml = `
            <div class="modal fade" id="${modalId}" tabindex="-1">
                <div class="modal-dialog ${options.size || ''}">
                    <div class="modal-content">
                        <div class="modal-header">
                            <h5 class="modal-title">${title}</h5>
                            <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                        </div>
                        <div class="modal-body">
                            ${content}
                        </div>
                        ${options.footer ? `<div class="modal-footer">${options.footer}</div>` : ''}
                    </div>
                </div>
            </div>
        `;

        document.body.insertAdjacentHTML('beforeend', modalHtml);

        const modal = this.show('#' + modalId);

        // Remove modal from DOM when hidden
        document.getElementById(modalId).addEventListener('hidden.bs.modal', function() {
            this.remove();
        });

        return modal;
    }
};

// ========================================
   LOADING STATES
   ========================================

GadgetService.Loading = {
    /**
     * Show loading overlay
     */
    show: function(message = 'Loading...') {
        const overlay = document.getElementById('loadingOverlay');
        if (overlay) {
            overlay.querySelector('.loading-spinner div:last-child').textContent = message;
            overlay.style.display = 'flex';
        }
    },

    /**
     * Hide loading overlay
     */
    hide: function() {
        const overlay = document.getElementById('loadingOverlay');
        if (overlay) {
            overlay.style.display = 'none';
        }
    },

    /**
     * Show loading on button
     */
    button: function(button, loadingText = 'Loading...') {
        if (!button.dataset.originalText) {
            button.dataset.originalText = button.innerHTML;
        }
        button.innerHTML = `<span class="spinner-border spinner-border-sm me-2"></span>${loadingText}`;
        button.disabled = true;
    },

    /**
     * Hide loading on button
     */
    buttonReset: function(button) {
        if (button.dataset.originalText) {
            button.innerHTML = button.dataset.originalText;
            delete button.dataset.originalText;
        }
        button.disabled = false;
    }
};

// ========================================
   SEARCH AND FILTERING
   ========================================

GadgetService.Search = {
    /**
     * Live search
     */
    live: function(inputSelector, targetSelector, options = {}) {
        const input = document.querySelector(inputSelector);
        const targets = document.querySelectorAll(targetSelector);

        if (!input || !targets.length) return;

        const defaults = {
            minChars: 2,
            highlight: true,
            caseSensitive: false
        };

        const settings = Object.assign({}, defaults, options);

        input.addEventListener('input', GadgetService.Utils.debounce(function(e) {
            const query = e.target.value.trim();

            targets.forEach(target => {
                const text = settings.caseSensitive ? target.textContent : target.textContent.toLowerCase();
                const search = settings.caseSensitive ? query : query.toLowerCase();

                if (query.length < settings.minChars || text.includes(search)) {
                    target.style.display = '';

                    if (settings.highlight && query.length >= settings.minChars) {
                        // Add highlight logic here if needed
                    }
                } else {
                    target.style.display = 'none';
                }
            });
        }, 300));
    },

    /**
     * Filter table
     */
    filterTable: function(inputSelector, tableSelector) {
        const input = document.querySelector(inputSelector);
        const table = document.querySelector(tableSelector);

        if (!input || !table) return;

        input.addEventListener('input', GadgetService.Utils.debounce(function(e) {
            const query = e.target.value.toLowerCase();
            const rows = table.querySelectorAll('tbody tr');

            rows.forEach(row => {
                const text = row.textContent.toLowerCase();
                row.style.display = text.includes(query) ? '' : 'none';
            });
        }, 300));
    }
};

// ========================================
   INITIALIZATION
   ========================================

GadgetService.init = function() {
    // Initialize tooltips
    const tooltipTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="tooltip"]'));
    tooltipTriggerList.map(function (tooltipTriggerEl) {
        return new bootstrap.Tooltip(tooltipTriggerEl);
    });

    // Initialize popovers
    const popoverTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="popover"]'));
    popoverTriggerList.map(function (popoverTriggerEl) {
        return new bootstrap.Popover(popoverTriggerEl);
    });

    // Initialize datepickers
    document.querySelectorAll('.datepicker').forEach(function(element) {
        flatpickr(element, {
            dateFormat: 'Y-m-d',
            locale: {
                firstDayOfWeek: 1,
                weekdays: {
                    shorthand: ['Sun', 'Mon', 'Tue', 'Wed', 'Thu', 'Fri', 'Sat'],
                    longhand: ['Sunday', 'Monday', 'Tuesday', 'Wednesday', 'Thursday', 'Friday', 'Saturday']
                },
                months: {
                    shorthand: ['Jan', 'Feb', 'Mar', 'Apr', 'May', 'Jun', 'Jul', 'Aug', 'Sep', 'Oct', 'Nov', 'Dec'],
                    longhand: ['January', 'February', 'March', 'April', 'May', 'June', 'July', 'August', 'September', 'October', 'November', 'December']
                }
            }
        });
    });

    // Initialize datetime pickers
    document.querySelectorAll('.datetimepicker').forEach(function(element) {
        flatpickr(element, {
            enableTime: true,
            dateFormat: 'Y-m-d H:i',
            time_24hr: true
        });
    });

    // Initialize select2
    $('.select2').select2({
        theme: 'bootstrap-5',
        width: '100%'
    });

    // Initialize confirm dialogs
    document.querySelectorAll('[data-confirm]').forEach(function(element) {
        element.addEventListener('click', function(e) {
            const message = this.dataset.confirm;
            if (message && !confirm(message)) {
                e.preventDefault();
                return false;
            }
        });
    });

    // Auto-hide alerts after 5 seconds
    setTimeout(function() {
        document.querySelectorAll('.alert.fade').forEach(function(alert) {
            const bsAlert = new bootstrap.Alert(alert);
            bsAlert.close();
        });
    }, 5000);

    // Handle AJAX errors globally
    window.addEventListener('unhandledrejection', function(event) {
        if (event.reason instanceof Error) {
            GadgetService.Notification.error(event.reason.message);
        }
    });

    console.log('Gadget Service Management System initialized');
};

// ========================================
   GLOBAL FUNCTIONS (for backward compatibility)
   ========================================

// Make functions available globally for inline scripts
window.showNotification = GadgetService.Notification.show.bind(GadgetService.Notification);
window.showLoading = GadgetService.Loading.show.bind(GadgetService.Loading);
window.hideLoading = GadgetService.Loading.hide.bind(GadgetService.Loading);
window.formatCurrency = GadgetService.Utils.formatCurrency.bind(GadgetService.Utils);
window.formatDate = GadgetService.Utils.formatDate.bind(GadgetService.Utils);
window.confirmAction = GadgetService.Utils.confirm.bind(GadgetService.Utils);

// Initialize when DOM is ready
document.addEventListener('DOMContentLoaded', GadgetService.init);

// Export for module systems
if (typeof module !== 'undefined' && module.exports) {
    module.exports = GadgetService;
}
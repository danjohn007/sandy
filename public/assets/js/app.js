/**
 * Sandy Beauty Nails - JavaScript Application
 */

// Global app object
const SandyApp = {
    // Configuration
    config: {
        apiBaseUrl: '/api',
        dateFormat: 'Y-m-d',
        timeFormat: 'H:i:s'
    },

    // Utility functions
    utils: {
        // Format phone number
        formatPhone: function(value) {
            const digits = value.replace(/\D/g, '');
            if (digits.length >= 6) {
                return digits.substring(0,3) + '-' + digits.substring(3,6) + '-' + digits.substring(6,10);
            } else if (digits.length >= 3) {
                return digits.substring(0,3) + '-' + digits.substring(3);
            }
            return digits;
        },

        // Format time for display
        formatTime: function(time24) {
            const [hours, minutes] = time24.split(':');
            const hour = parseInt(hours);
            const ampm = hour >= 12 ? 'PM' : 'AM';
            const hour12 = hour % 12 || 12;
            return `${hour12}:${minutes} ${ampm}`;
        },

        // Show loading state
        showLoading: function(element) {
            if (element) {
                element.style.display = 'block';
                element.classList.add('show');
            }
        },

        // Hide loading state
        hideLoading: function(element) {
            if (element) {
                element.style.display = 'none';
                element.classList.remove('show');
            }
        },

        // Show alert message
        showAlert: function(message, type = 'info', container = document.body) {
            const alertHtml = `
                <div class="alert alert-${type} alert-dismissible fade show" role="alert">
                    ${message}
                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                </div>
            `;
            
            const alertElement = document.createElement('div');
            alertElement.innerHTML = alertHtml;
            container.insertBefore(alertElement.firstElementChild, container.firstChild);
            
            // Auto-dismiss after 5 seconds
            setTimeout(function() {
                const alert = container.querySelector('.alert');
                if (alert) {
                    alert.remove();
                }
            }, 5000);
        },

        // Debounce function
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
        }
    },

    // API functions
    api: {
        // Generic API call
        call: async function(endpoint, options = {}) {
            const url = SandyApp.config.apiBaseUrl + endpoint;
            const defaultOptions = {
                headers: {
                    'Content-Type': 'application/json',
                    'X-Requested-With': 'XMLHttpRequest'
                }
            };
            
            const finalOptions = Object.assign(defaultOptions, options);
            
            try {
                const response = await fetch(url, finalOptions);
                return await response.json();
            } catch (error) {
                console.error('API call failed:', error);
                throw error;
            }
        },

        // Get available time slots
        getAvailableSlots: async function(date, serviceId = null) {
            const params = new URLSearchParams();
            params.append('date', date);
            if (serviceId) params.append('service_id', serviceId);
            
            return await this.call(`/available-slots?${params}`);
        },

        // Get client by phone
        getClientByPhone: async function(phone) {
            return await this.call(`/client?phone=${encodeURIComponent(phone)}`);
        },

        // Validate time slot
        validateSlot: async function(date, time, manicuristId = null) {
            return await this.call('/validate-slot', {
                method: 'POST',
                body: JSON.stringify({
                    date: date,
                    time: time,
                    manicurist_id: manicuristId
                })
            });
        }
    },

    // Initialize application
    init: function() {
        console.log('Sandy Beauty Nails App initialized');
        
        // Initialize phone formatting
        this.initPhoneFormatting();
        
        // Initialize form validation
        this.initFormValidation();
        
        // Initialize tooltips and popovers
        this.initBootstrapComponents();
        
        // Initialize admin features if on admin pages
        if (window.location.pathname.startsWith('/admin')) {
            this.initAdminFeatures();
        }
    },

    // Initialize phone number formatting
    initPhoneFormatting: function() {
        document.querySelectorAll('input[type="tel"]').forEach(input => {
            input.addEventListener('input', function(e) {
                e.target.value = SandyApp.utils.formatPhone(e.target.value);
            });
        });
    },

    // Initialize form validation
    initFormValidation: function() {
        document.querySelectorAll('form').forEach(form => {
            form.addEventListener('submit', function(e) {
                if (!form.checkValidity()) {
                    e.preventDefault();
                    e.stopPropagation();
                }
                form.classList.add('was-validated');
            });
        });
    },

    // Initialize Bootstrap components
    initBootstrapComponents: function() {
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
    },

    // Initialize admin-specific features
    initAdminFeatures: function() {
        console.log('Initializing admin features');
        
        // Auto-refresh dashboard data every 5 minutes
        if (window.location.pathname === '/admin/dashboard') {
            setInterval(() => {
                // Could implement auto-refresh here
            }, 300000); // 5 minutes
        }

        // Initialize status update handlers
        this.initStatusUpdaters();
    },

    // Initialize status update functionality
    initStatusUpdaters: function() {
        document.querySelectorAll('.status-updater').forEach(select => {
            select.addEventListener('change', async function() {
                const appointmentId = this.dataset.appointmentId;
                const newStatus = this.value;
                
                try {
                    const response = await fetch(`/admin/appointments/${appointmentId}/status`, {
                        method: 'PUT',
                        headers: {
                            'Content-Type': 'application/json',
                            'X-Requested-With': 'XMLHttpRequest'
                        },
                        body: JSON.stringify({ status: newStatus })
                    });
                    
                    const result = await response.json();
                    
                    if (result.success) {
                        SandyApp.utils.showAlert('Estado actualizado correctamente', 'success');
                        // Update UI if needed
                        location.reload();
                    } else {
                        SandyApp.utils.showAlert('Error al actualizar el estado', 'danger');
                    }
                } catch (error) {
                    console.error('Error updating status:', error);
                    SandyApp.utils.showAlert('Error de conexión', 'danger');
                }
            });
        });
    }
};

// Initialize app when DOM is loaded
document.addEventListener('DOMContentLoaded', function() {
    SandyApp.init();
});

// Make SandyApp globally available
window.SandyApp = SandyApp;
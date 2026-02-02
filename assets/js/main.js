// Main JavaScript for Alpharide Rental

// Global configuration
const API_BASE_URL = '/api/';

// Utility Functions
const utils = {
    // Format currency to IDR
    formatCurrency: function(amount) {
        return new Intl.NumberFormat('id-ID', {
            style: 'currency',
            currency: 'IDR',
            minimumFractionDigits: 0
        }).format(amount);
    },
    
    // Format date
    formatDate: function(dateString) {
        const options = { year: 'numeric', month: 'long', day: 'numeric' };
        return new Date(dateString).toLocaleDateString('id-ID', options);
    },
    
    // Show alert
    showAlert: function(message, type = 'info') {
        const alertDiv = document.createElement('div');
        alertDiv.className = `alert alert-${type}`;
        alertDiv.textContent = message;
        
        document.body.insertBefore(alertDiv, document.body.firstChild);
        
        setTimeout(() => {
            alertDiv.remove();
        }, 3000);
    },
    
    // AJAX request helper
    ajax: async function(url, method = 'GET', data = null) {
        const options = {
            method: method,
            headers: {
                'Content-Type': 'application/json'
            }
        };
        
        if (data && method !== 'GET') {
            options.body = JSON.stringify(data);
        }
        
        try {
            const response = await fetch(url, options);
            return await response.json();
        } catch (error) {
            console.error('AJAX Error:', error);
            return { success: false, message: 'Network error' };
        }
    }
};

// API Functions
const API = {
    // Mobil API
    mobil: {
        getAll: () => utils.ajax(API_BASE_URL + 'mobil.php'),
        getById: (id) => utils.ajax(API_BASE_URL + 'mobil.php?id=' + id),
        create: (data) => utils.ajax(API_BASE_URL + 'mobil.php', 'POST', data),
        update: (data) => utils.ajax(API_BASE_URL + 'mobil.php', 'PUT', data),
        delete: (id) => utils.ajax(API_BASE_URL + 'mobil.php?id=' + id, 'DELETE')
    },
    
    // Transaksi API
    transaksi: {
        getAll: () => utils.ajax(API_BASE_URL + 'transaksi.php'),
        getById: (id) => utils.ajax(API_BASE_URL + 'transaksi.php?id=' + id),
        getByUser: (userId) => utils.ajax(API_BASE_URL + 'transaksi.php?user_id=' + userId),
        create: (data) => utils.ajax(API_BASE_URL + 'transaksi.php', 'POST', data),
        updateStatus: (data) => utils.ajax(API_BASE_URL + 'transaksi.php', 'PUT', data)
    },
    
    // User API
    user: {
        getAll: () => utils.ajax(API_BASE_URL + 'user.php'),
        getById: (id) => utils.ajax(API_BASE_URL + 'user.php?id=' + id),
        register: (data) => utils.ajax(API_BASE_URL + 'user.php', 'POST', data),
        update: (data) => utils.ajax(API_BASE_URL + 'user.php', 'PUT', data),
        delete: (id) => utils.ajax(API_BASE_URL + 'user.php?id=' + id, 'DELETE')
    }
};

// Initialize on page load
document.addEventListener('DOMContentLoaded', function() {
    // Add smooth scrolling
    document.querySelectorAll('a[href^="#"]').forEach(anchor => {
        anchor.addEventListener('click', function (e) {
            e.preventDefault();
            const target = document.querySelector(this.getAttribute('href'));
            if (target) {
                target.scrollIntoView({
                    behavior: 'smooth',
                    block: 'start'
                });
            }
        });
    });
    
    // Initialize forms
    initializeForms();
    
    // Initialize modals
    initializeModals();
});

// Form initialization
function initializeForms() {
    const forms = document.querySelectorAll('form[data-ajax]');
    forms.forEach(form => {
        form.addEventListener('submit', async function(e) {
            e.preventDefault();
            
            const formData = new FormData(this);
            const data = Object.fromEntries(formData);
            
            const url = this.getAttribute('action');
            const method = this.getAttribute('method') || 'POST';
            
            const response = await utils.ajax(url, method, data);
            
            if (response.success) {
                utils.showAlert(response.message, 'success');
                if (this.hasAttribute('data-redirect')) {
                    setTimeout(() => {
                        window.location.href = this.getAttribute('data-redirect');
                    }, 1000);
                }
            } else {
                utils.showAlert(response.message, 'error');
            }
        });
    });
}

// Modal initialization
function initializeModals() {
    // Close modal on outside click
    document.querySelectorAll('.modal').forEach(modal => {
        modal.addEventListener('click', function(e) {
            if (e.target === this) {
                this.classList.remove('show');
            }
        });
    });
    
    // Close button
    document.querySelectorAll('.close-modal').forEach(btn => {
        btn.addEventListener('click', function() {
            this.closest('.modal').classList.remove('show');
        });
    });
}

// Export for use in other scripts
window.AlpharideAPI = API;
window.AlpharideUtils = utils;
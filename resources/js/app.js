// public/js/app.js

/**
 * Main JavaScript file for DonorTrack
 * Handles navigation, interactions, and dynamic content loading
 */

document.addEventListener('DOMContentLoaded', function() {
    
    // ===== MOBILE NAVIGATION =====
    initMobileNav();
    
    // ===== SMOOTH SCROLLING =====
    initSmoothScroll();
    
    // ===== FORM VALIDATION =====
    initFormValidation();
    
    // ===== BARANGAY MAP INTERACTIONS =====
    if (document.getElementById('barangayMap')) {
        initBarangayMap();
    }
    
    // ===== QR SCANNER MODAL =====
    if (document.getElementById('scanQRBtn')) {
        initQRScanner();
    }
    
});

/**
 * Initialize mobile navigation toggle
 */
function initMobileNav() {
    const hamburger = document.getElementById('hamburger');
    const navMenu = document.getElementById('navMenu');
    
    if (hamburger && navMenu) {
        hamburger.addEventListener('click', function() {
            navMenu.classList.toggle('active');
            hamburger.classList.toggle('active');
        });
        
        // Close menu when clicking outside
        document.addEventListener('click', function(e) {
            if (!hamburger.contains(e.target) && !navMenu.contains(e.target)) {
                navMenu.classList.remove('active');
                hamburger.classList.remove('active');
            }
        });
        
        // Close menu when clicking on a link
        const navLinks = navMenu.querySelectorAll('a');
        navLinks.forEach(link => {
            link.addEventListener('click', function() {
                navMenu.classList.remove('active');
                hamburger.classList.remove('active');
            });
        });
    }
}

/**
 * Initialize smooth scrolling for anchor links
 */
function initSmoothScroll() {
    const links = document.querySelectorAll('a[href^="#"]');
    
    links.forEach(link => {
        link.addEventListener('click', function(e) {
            const href = this.getAttribute('href');
            
            // Check if it's a valid anchor (not just #)
            if (href !== '#') {
                const target = document.querySelector(href);
                
                if (target) {
                    e.preventDefault();
                    target.scrollIntoView({
                        behavior: 'smooth',
                        block: 'start'
                    });
                }
            }
        });
    });
}

/**
 * Initialize form validation
 */
function initFormValidation() {
    const forms = document.querySelectorAll('form');
    
    forms.forEach(form => {
        form.addEventListener('submit', function(e) {
            const inputs = form.querySelectorAll('input[required]');
            let isValid = true;
            
            inputs.forEach(input => {
                if (!input.value.trim()) {
                    isValid = false;
                    input.classList.add('error');
                    showInputError(input, 'This field is required');
                } else {
                    input.classList.remove('error');
                    removeInputError(input);
                }
                
                // Email validation
                if (input.type === 'email' && input.value.trim()) {
                    if (!isValidEmail(input.value)) {
                        isValid = false;
                        input.classList.add('error');
                        showInputError(input, 'Please enter a valid email address');
                    }
                }
                
                // Password confirmation
                if (input.id === 'password_confirmation') {
                    const password = form.querySelector('#reg_password');
                    if (password && input.value !== password.value) {
                        isValid = false;
                        input.classList.add('error');
                        showInputError(input, 'Passwords do not match');
                    }
                }
            });
            
            if (!isValid) {
                e.preventDefault();
            }
        });
        
        // Real-time validation on input
        const inputs = form.querySelectorAll('input');
        inputs.forEach(input => {
            input.addEventListener('input', function() {
                if (this.classList.contains('error')) {
                    this.classList.remove('error');
                    removeInputError(this);
                }
            });
        });
    });
}

/**
 * Show input error message
 */
function showInputError(input, message) {
    removeInputError(input);
    
    const errorDiv = document.createElement('div');
    errorDiv.className = 'input-error';
    errorDiv.textContent = message;
    errorDiv.style.color = '#ef4444';
    errorDiv.style.fontSize = '0.875rem';
    errorDiv.style.marginTop = '0.25rem';
    
    input.parentNode.appendChild(errorDiv);
}

/**
 * Remove input error message
 */
function removeInputError(input) {
    const errorDiv = input.parentNode.querySelector('.input-error');
    if (errorDiv) {
        errorDiv.remove();
    }
}

/**
 * Validate email format
 */
function isValidEmail(email) {
    const emailRegex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
    return emailRegex.test(email);
}

/**
 * Initialize barangay map interactions
 * This will handle map markers and sidebar interactions
 * Data will be fetched from BARANGAYS and DONATIONS tables via backend
 */
function initBarangayMap() {
    const map = document.getElementById('barangayMap');
    
    // TODO: When backend is ready, fetch barangay data
    // Endpoint: GET /api/barangays
    // Returns: barangays with latitude, longitude, disaster_status
    
    // Mock data structure for reference
    const mockBarangays = [
        { id: 'B001', name: 'Lahug', latitude: 10.3333, longitude: 123.9000, status: 'active' },
        { id: 'B002', name: 'Apas', latitude: 10.3400, longitude: 123.9100, status: 'active' },
        // ... more barangays
    ];
    
    // Create markers dynamically
    mockBarangays.forEach(barangay => {
        const marker = createMarker(barangay);
        map.appendChild(marker);
        
        // Add click handler to show barangay details
        marker.addEventListener('click', function() {
            showBarangayDetails(barangay.id);
        });
    });
    
    // Activity list click handlers
    const activityItems = document.querySelectorAll('.activity-item');
    activityItems.forEach(item => {
        item.addEventListener('click', function() {
            const barangayName = this.textContent.trim();
            // TODO: Filter or highlight corresponding marker on map
            highlightBarangay(barangayName);
        });
    });
}

/**
 * Create a map marker element
 */
function createMarker(barangay) {
    const marker = document.createElement('div');
    marker.className = 'map-marker';
    marker.style.position = 'absolute';
    
    // Calculate position (simplified - real implementation would use proper map projection)
    const x = ((barangay.longitude - 123.85) * 1000) + 100;
    const y = ((10.35 - barangay.latitude) * 1000) + 100;
    
    marker.style.left = x + 'px';
    marker.style.top = y + 'px';
    marker.style.width = '20px';
    marker.style.height = '20px';
    marker.style.borderRadius = '50%';
    marker.style.cursor = 'pointer';
    marker.style.transition = 'transform 0.3s';
    
    // Set marker color based on status
    const statusColors = {
        'active': '#10b981',
        'pending': '#f59e0b',
        'completed': '#9ca3af'
    };
    marker.style.backgroundColor = statusColors[barangay.status] || '#9ca3af';
    marker.style.border = '3px solid white';
    marker.style.boxShadow = '0 2px 4px rgba(0,0,0,0.3)';
    
    // Hover effect
    marker.addEventListener('mouseenter', function() {
        this.style.transform = 'scale(1.3)';
    });
    
    marker.addEventListener('mouseleave', function() {
        this.style.transform = 'scale(1)';
    });
    
    return marker;
}

/**
 * Show barangay details
 * Fetches donation data for specific barangay from DONATIONS table
 */
function showBarangayDetails(barangayId) {
    // TODO: When backend is ready
    // Endpoint: GET /api/barangays/{id}/details
    // Returns: barangay info, recent donations, resource requests
    
    console.log('Showing details for barangay:', barangayId);
    
    // Mock implementation - show alert
    alert('Barangay details will be shown here when backend is connected');
}

/**
 * Highlight barangay on map
 */
function highlightBarangay(barangayName) {
    console.log('Highlighting barangay:', barangayName);
    // TODO: Add visual highlight to corresponding marker
}

/**
 * Initialize QR Scanner Modal
 */
function initQRScanner() {
    const scanBtn = document.getElementById('scanQRBtn');
    const modal = document.getElementById('qrModal');
    const closeBtn = modal ? modal.querySelector('.close') : null;
    
    if (scanBtn && modal) {
        scanBtn.addEventListener('click', function() {
            modal.style.display = 'flex';
            modal.classList.add('active');
            startQRScanner();
        });
        
        if (closeBtn) {
            closeBtn.addEventListener('click', function() {
                modal.style.display = 'none';
                modal.classList.remove('active');
                stopQRScanner();
            });
        }
        
        // Close modal when clicking outside
        modal.addEventListener('click', function(e) {
            if (e.target === modal) {
                modal.style.display = 'none';
                modal.classList.remove('active');
                stopQRScanner();
            }
        });
    }
}

/**
 * Start QR code scanner
 * Uses device camera to scan QR codes
 */
function startQRScanner() {
    // TODO: Implement QR scanner using html5-qrcode library
    // This is a placeholder implementation
    
    const qrReader = document.getElementById('qrReader');
    if (qrReader) {
        qrReader.innerHTML = '<p style="text-align: center; padding: 2rem;">QR Scanner will be initialized here.<br>Requires html5-qrcode library.</p>';
        
        // When QR code is scanned, the donation code will be extracted
        // Then redirect to tracking page with the code
        // Example: window.location.href = '/track-donation?code=' + scannedCode;
    }
}

/**
 * Stop QR code scanner
 */
function stopQRScanner() {
    // TODO: Stop camera and cleanup
    console.log('QR Scanner stopped');
}

/**
 * Load donation statistics
 * Fetches aggregate data from DONATIONS table
 */
function loadDonationStats() {
    // TODO: When backend is ready
    // Endpoint: GET /api/statistics
    // Returns: total_donations, total_barangays, total_amount
    
    // Update the stat cards on homepage
    console.log('Loading donation statistics...');
}

/**
 * Load active fundraisers
 * Fetches from RESOURCE_REQUESTS table with status='open'
 */
function loadActiveFundraisers() {
    // TODO: When backend is ready
    // Endpoint: GET /api/fundraisers/active
    // Returns: list of active fundraisers with progress
    
    console.log('Loading active fundraisers...');
}

/**
 * Track donation by code
 * Searches DONATIONS table by donation_id or blockchain_tx_hash
 */
function trackDonation(code) {
    // TODO: When backend is ready
    // Endpoint: GET /api/donations/track/{code}
    // Returns: donation details, status, blockchain confirmation
    
    console.log('Tracking donation:', code);
}

/**
 * Format currency to Philippine Peso
 */
function formatCurrency(amount) {
    return new Intl.NumberFormat('en-PH', {
        style: 'currency',
        currency: 'PHP'
    }).format(amount);
}

/**
 * Format date to readable format
 */
function formatDate(dateString) {
    const date = new Date(dateString);
    return date.toLocaleDateString('en-PH', {
        year: 'numeric',
        month: 'long',
        day: 'numeric'
    });
}

/**
 * Calculate days remaining
 */
function daysRemaining(endDate) {
    const end = new Date(endDate);
    const now = new Date();
    const diff = end - now;
    const days = Math.ceil(diff / (1000 * 60 * 60 * 24));
    return days > 0 ? days : 0;
}

/**
 * Calculate fundraiser progress percentage
 */
function calculateProgress(current, goal) {
    return Math.min(Math.round((current / goal) * 100), 100);
}

/**
 * Show loading spinner
 */
function showLoading(element) {
    if (element) {
        element.innerHTML = '<div class="loading-spinner"></div>';
    }
}

/**
 * Hide loading spinner
 */
function hideLoading(element) {
    if (element) {
        const spinner = element.querySelector('.loading-spinner');
        if (spinner) {
            spinner.remove();
        }
    }
}

/**
 * Show toast notification
 */
function showToast(message, type = 'info') {
    const toast = document.createElement('div');
    toast.className = `toast toast-${type}`;
    toast.textContent = message;
    toast.style.position = 'fixed';
    toast.style.bottom = '20px';
    toast.style.right = '20px';
    toast.style.padding = '1rem 1.5rem';
    toast.style.borderRadius = '0.5rem';
    toast.style.color = 'white';
    toast.style.fontWeight = '600';
    toast.style.zIndex = '9999';
    toast.style.animation = 'slideIn 0.3s ease-out';
    
    const colors = {
        'success': '#10b981',
        'error': '#ef4444',
        'warning': '#f59e0b',
        'info': '#3b82f6'
    };
    toast.style.backgroundColor = colors[type] || colors.info;
    
    document.body.appendChild(toast);
    
    setTimeout(() => {
        toast.style.animation = 'slideOut 0.3s ease-in';
        setTimeout(() => toast.remove(), 300);
    }, 3000);
}

/**
 * Copy to clipboard
 */
function copyToClipboard(text) {
    navigator.clipboard.writeText(text).then(() => {
        showToast('Copied to clipboard!', 'success');
    }).catch(() => {
        showToast('Failed to copy', 'error');
    });
}

/**
 * Debounce function for search inputs
 */
function debounce(func, wait) {
    let timeout;
    return function executedFunction(...args) {
        const later = () => {
            clearTimeout(timeout);
            func(...args);
        };
        clearTimeout(timeout);
        timeout = setTimeout(later, wait);
    };
}

// Export functions for use in other files
if (typeof module !== 'undefined' && module.exports) {
    module.exports = {
        formatCurrency,
        formatDate,
        daysRemaining,
        calculateProgress,
        showToast,
        copyToClipboard,
        debounce
    };
}
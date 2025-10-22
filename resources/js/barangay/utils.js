/**
 * Utility Functions for Barangay Dashboard
 * =========================================
 * Core utilities, API helpers, and formatting functions
 */

// CSRF Token
const csrfToken = document.querySelector('meta[name="csrf-token"]').content;

/**
 * Fetch API wrapper with CSRF token and error handling
 */
async function fetchAPI(url, options = {}) {
    try {
        const response = await fetch(url, {
            ...options,
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': csrfToken,
                ...options.headers
            }
        });

        if (!response.ok) {
            throw new Error(`HTTP ${response.status}: ${response.statusText}`);
        }

        return await response.json();
    } catch (error) {
        console.error(`API Error (${url}):`, error);
        throw error;
    }
}

/**
 * Format category name for display
 */
function formatCategory(category) {
    const categories = {
        'food': 'Food',
        'water': 'Water',
        'medical': 'Medical Supplies',
        'shelter': 'Shelter Materials',
        'clothing': 'Clothing',
        'other': 'Other'
    };
    return categories[category] || category;
}

/**
 * Format status for display
 */
function formatStatus(status) {
    return status ? status.replace(/_/g, ' ').replace(/\b\w/g, l => l.toUpperCase()) : 'Unknown';
}

/**
 * Format date
 */
function formatDate(dateString) {
    if (!dateString) return 'Unknown date';
    const date = new Date(dateString);
    return date.toLocaleDateString('en-US', { year: 'numeric', month: 'short', day: 'numeric' });
}

/**
 * Format date (short version)
 */
function formatDateShort(dateString) {
    if (!dateString) return 'Unknown date';
    const date = new Date(dateString);
    return date.toLocaleDateString('en-US', { month: 'short', day: 'numeric', year: 'numeric' });
}

/**
 * Get urgency badge classes
 */
function getUrgencyBadge(urgency) {
    const badges = {
        'low': 'bg-gray-100 text-gray-700',
        'medium': 'bg-blue-100 text-blue-700',
        'high': 'bg-orange-100 text-orange-700',
        'critical': 'bg-red-100 text-red-700'
    };
    return badges[urgency] || 'bg-gray-100 text-gray-700';
}

/**
 * Get need status badge classes
 */
function getNeedStatusBadge(status) {
    const badges = {
        'pending': 'bg-yellow-100 text-yellow-700',
        'fulfilled': 'bg-green-100 text-green-700',
        'partially_fulfilled': 'bg-blue-100 text-blue-700'
    };
    return badges[status] || 'bg-gray-100 text-gray-700';
}

/**
 * Get distribution status badge classes
 */
function getDistributionStatusBadge(status) {
    const badges = {
        'pending': 'bg-yellow-100 text-yellow-700',
        'partially_distributed': 'bg-blue-100 text-blue-700',
        'fully_distributed': 'bg-green-100 text-green-700'
    };
    return badges[status] || 'bg-gray-100 text-gray-700';
}

/**
 * Get status badge HTML
 */
function getStatusBadge(status) {
    const badges = {
        'pending': '<span class="px-3 py-1 bg-yellow-100 text-yellow-700 rounded-full text-xs font-bold"><i class="fas fa-clock mr-1"></i>Pending</span>',
        'accepted': '<span class="px-3 py-1 bg-green-100 text-green-700 rounded-full text-xs font-bold"><i class="fas fa-check-circle mr-1"></i>Accepted</span>',
        'rejected': '<span class="px-3 py-1 bg-red-100 text-red-700 rounded-full text-xs font-bold"><i class="fas fa-times-circle mr-1"></i>Rejected</span>',
        'completed': '<span class="px-3 py-1 bg-blue-100 text-blue-700 rounded-full text-xs font-bold"><i class="fas fa-flag-checkered mr-1"></i>Completed</span>',
        'cancelled': '<span class="px-3 py-1 bg-gray-100 text-gray-700 rounded-full text-xs font-bold"><i class="fas fa-ban mr-1"></i>Cancelled</span>'
    };
    return badges[status] || `<span class="px-3 py-1 bg-gray-100 text-gray-700 rounded-full text-xs font-bold">${status}</span>`;
}

/**
 * Get urgency color classes
 */
function getUrgencyColor(urgency) {
    const colors = {
        'low': 'bg-gray-100 text-gray-700',
        'medium': 'bg-blue-100 text-blue-700',
        'high': 'bg-orange-100 text-orange-700',
        'critical': 'bg-red-100 text-red-700'
    };
    return colors[urgency] || 'bg-gray-100 text-gray-700';
}

/**
 * Get status color classes
 */
function getStatusColor(status) {
    const colors = {
        'pending': 'bg-yellow-100 text-yellow-700',
        'accepted': 'bg-green-100 text-green-700',
        'rejected': 'bg-red-100 text-red-700',
        'completed': 'bg-blue-100 text-blue-700',
        'cancelled': 'bg-gray-100 text-gray-700'
    };
    return colors[status] || 'bg-gray-100 text-gray-700';
}

/**
 * Get status icon classes
 */
function getStatusIcon(status) {
    const icons = {
        'pending': 'fas fa-clock',
        'accepted': 'fas fa-check-circle',
        'rejected': 'fas fa-times-circle',
        'completed': 'fas fa-flag-checkered',
        'cancelled': 'fas fa-ban'
    };
    return icons[status] || 'fas fa-question-circle';
}

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="<?php echo e(csrf_token()); ?>">
    <title>City Dashboard (LDRRMO)</title>

    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/leaflet/1.9.4/leaflet.min.css" />
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>

    <style>
        .tab-content { display: none; }
        .tab-content.active { display: block; }
        .tab-btn.active {
            border-bottom: 3px solid #1D4ED8;
            color: #1D4ED8;
        }
        #cityMap { height: 600px; }
        .error-message {
            background-color: #FEE2E2;
            color: #991B1B;
            padding: 1rem;
            border-radius: 0.5rem;
            margin: 1rem 0;
        }
    </style>
</head>
<body class="bg-gray-50">

    <!-- Top Header -->
    <div class="bg-[#1D4ED8] text-white px-6 py-4 flex justify-between items-center">
        <div>
            <h1 class="text-2xl font-bold">City Dashboard (LDRRMO)</h1>
            <p class="text-sm text-blue-100">Cebu City Disaster Management / Public Works</p>
        </div>
        <div class="flex items-center gap-4">
            <div class="text-right">
                <p class="text-sm text-blue-100">Logged in as LDRRMO</p>
                <p class="font-medium"><?php echo e(session('user_name', 'Admin')); ?></p>
            </div>
            <form action="<?php echo e(route('logout')); ?>" method="POST">
                <?php echo csrf_field(); ?>
                <button type="submit" class="bg-white/20 hover:bg-white/30 px-4 py-2 rounded text-sm transition">
                    <i class="fas fa-sign-out-alt mr-1"></i> Logout
                </button>
            </form>
        </div>
    </div>

    <div class="max-w-7xl mx-auto px-6 py-6">

        <!-- Statistics Cards -->
        <div class="grid grid-cols-1 md:grid-cols-6 gap-6 mb-6">
            <div class="bg-white rounded-xl p-6 shadow-sm">
                <div class="flex justify-between items-start">
                    <div>
                        <p class="text-sm text-gray-600">Total Donations</p>
                        <h3 class="text-3xl font-bold text-gray-800" id="totalDonations">₱0</h3>
                    </div>
                    <div class="w-12 h-12 bg-green-100 rounded-lg flex items-center justify-center">
                        <i class="fas fa-dollar-sign text-green-600 text-xl"></i>
                    </div>
                </div>
                <p class="text-xs text-green-600 mt-2"><i class="fas fa-check"></i> Verified</p>
            </div>

            <div class="bg-white rounded-xl p-6 shadow-sm">
                <div class="flex justify-between items-start">
                    <div>
                        <p class="text-sm text-gray-600">Online Donations</p>
                        <h3 class="text-2xl font-bold text-gray-800" id="onlineDonations">₱0</h3>
                    </div>
                    <div class="w-12 h-12 bg-purple-100 rounded-lg flex items-center justify-center">
                        <i class="fas fa-wallet text-purple-600 text-xl"></i>
                    </div>
                </div>
                <p class="text-xs text-purple-600 mt-2"><i class="fas fa-link"></i> Blockchain</p>
            </div>

            <div class="bg-white rounded-xl p-6 shadow-sm">
                <div class="flex justify-between items-start">
                    <div>
                        <p class="text-sm text-gray-600">Affected Families</p>
                        <h3 class="text-3xl font-bold text-gray-800" id="affectedFamilies">0</h3>
                    </div>
                    <div class="w-12 h-12 bg-red-100 rounded-lg flex items-center justify-center">
                        <i class="fas fa-users text-red-600 text-xl"></i>
                    </div>
                </div>
                <p class="text-xs text-gray-600 mt-2" id="affectedBarangays">0 Barangays</p>
            </div>

            <div class="bg-white rounded-xl p-6 shadow-sm">
                <div class="flex justify-between items-start">
                    <div>
                        <p class="text-sm text-gray-600">Active Fundraisers</p>
                        <h3 class="text-3xl font-bold text-gray-800" id="activeFundraisers">0</h3>
                    </div>
                    <div class="w-12 h-12 bg-blue-100 rounded-lg flex items-center justify-center">
                        <i class="fas fa-hand-holding-heart text-blue-600 text-xl"></i>
                    </div>
                </div>
            </div>

            <div class="bg-white rounded-xl p-6 shadow-sm">
                <div class="flex justify-between items-start">
                    <div>
                        <p class="text-sm text-gray-600">Critical Barangays</p>
                        <h3 class="text-3xl font-bold text-gray-800" id="criticalBarangays">0</h3>
                    </div>
                    <div class="w-12 h-12 bg-orange-100 rounded-lg flex items-center justify-center">
                        <i class="fas fa-exclamation-triangle text-orange-600 text-xl"></i>
                    </div>
                </div>
            </div>

            <div class="bg-white rounded-xl p-6 shadow-sm">
                <div class="flex justify-between items-start">
                    <div>
                        <p class="text-sm text-gray-600">Total Donors</p>
                        <h3 class="text-3xl font-bold text-gray-800" id="totalDonors">0</h3>
                    </div>
                    <div class="w-12 h-12 bg-teal-100 rounded-lg flex items-center justify-center">
                        <i class="fas fa-users text-teal-600 text-xl"></i>
                    </div>
                </div>
                <p class="text-xs text-teal-600 mt-2" id="blockchainVerified">0 blockchain verified</p>
            </div>
        </div>

        <!-- Tabs Navigation -->
        <div class="bg-white rounded-t-xl shadow-sm border-b">
            <div class="flex gap-2 px-6">
                <button onclick="switchTab('map', event)" class="tab-btn active px-6 py-4 font-medium text-gray-700 transition">
                    <i class="fas fa-map mr-2"></i> Map View
                </button>
                <button onclick="switchTab('resources', event)" class="tab-btn px-6 py-4 font-medium text-gray-700 transition">
                    <i class="fas fa-handshake mr-2"></i> Resource Needs
                </button>
                <button onclick="switchTab('analytics', event)" class="tab-btn px-6 py-4 font-medium text-gray-700 transition">
                    <i class="fas fa-chart-bar mr-2"></i> Analytics
                </button>
                <button onclick="switchTab('fundraisers', event)" class="tab-btn px-6 py-4 font-medium text-gray-700 transition">
                    <i class="fas fa-donate mr-2"></i> Fundraisers
                </button>
                <button onclick="switchTab('barangays', event)" class="tab-btn px-6 py-4 font-medium text-gray-700 transition">
                    <i class="fas fa-list mr-2"></i> Barangays
                </button>
            </div>
        </div>

        <!-- TAB 1: Interactive Map -->
        <div id="map-tab" class="tab-content active bg-white rounded-b-xl shadow-sm p-6">
            <h2 class="text-2xl font-bold mb-4">Interactive Cebu Map</h2>
            <div id="cityMap" class="rounded-lg border"></div>
            <div class="mt-4 flex gap-4">
                <div class="flex items-center gap-2">
                    <div class="w-4 h-4 rounded-full bg-green-500"></div>
                    <span class="text-sm">Safe</span>
                </div>
                <div class="flex items-center gap-2">
                    <div class="w-4 h-4 rounded-full bg-yellow-500"></div>
                    <span class="text-sm">Warning</span>
                </div>
                <div class="flex items-center gap-2">
                    <div class="w-4 h-4 rounded-full bg-orange-500"></div>
                    <span class="text-sm">Critical</span>
                </div>
                <div class="flex items-center gap-2">
                    <div class="w-4 h-4 rounded-full bg-red-500"></div>
                    <span class="text-sm">Emergency</span>
                </div>
            </div>
        </div>

<!-- TAB 2: Resource Needs -->
<div id="resources-tab" class="tab-content bg-white rounded-b-xl shadow-sm p-6">
    <div class="flex justify-between items-center mb-6">
        <div>
            <h2 class="text-2xl font-bold">City-Wide Resource Needs</h2>
            <p class="text-gray-600 text-sm">Coordinate and match resource requests across all barangays</p>
        </div>
    </div>

    <!-- Filter Tabs -->
    <div class="mb-6 border-b border-gray-200">
        <nav class="flex space-x-8">
            <button onclick="filterResourceNeeds('all')" id="resource-tab-all" class="resource-filter-btn active pb-4 px-1 border-b-2 font-medium text-sm text-blue-600 border-blue-600">
                All Requests
                <span id="resource-count-all" class="ml-2 px-2 py-0.5 bg-gray-200 rounded-full text-xs">0</span>
            </button>
            <button onclick="filterResourceNeeds('pending')" id="resource-tab-pending" class="resource-filter-btn pb-4 px-1 border-b-2 font-medium text-sm text-gray-500 border-transparent">
                Pending Verification
                <span id="resource-count-pending" class="ml-2 px-2 py-0.5 bg-yellow-100 text-yellow-800 rounded-full text-xs">0</span>
            </button>
            <button onclick="filterResourceNeeds('verified')" id="resource-tab-verified" class="resource-filter-btn pb-4 px-1 border-b-2 font-medium text-sm text-gray-500 border-transparent">
                Verified Requests
                <span id="resource-count-verified" class="ml-2 px-2 py-0.5 bg-green-100 text-green-800 rounded-full text-xs">0</span>
            </button>
        </nav>
    </div>

    <div id="resourceNeedsList" class="space-y-4">
        <div class="text-center py-8 text-gray-500">
            <i class="fas fa-spinner fa-spin text-3xl mb-2"></i>
            <p>Loading resource needs...</p>
        </div>
    </div>
</div>

<!-- Add this Verification Modal before the closing body tag (around line 870) -->
<!-- Add this right after the Suggested Matches Modal -->

<!-- Verification Modal -->
<div id="verificationModal" class="hidden fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50">
    <div class="bg-white rounded-lg p-6 max-w-md w-full mx-4">
        <h3 class="text-lg font-semibold mb-4" id="verificationModalTitle">Verify Resource Request</h3>
        <div id="verificationModalContent"></div>
        <div class="mt-6 flex justify-end space-x-3">
            <button onclick="closeVerificationModal()" class="px-4 py-2 bg-gray-200 text-gray-700 rounded hover:bg-gray-300">Cancel</button>
            <button id="rejectBtn" onclick="performReject()" class="hidden px-4 py-2 bg-red-600 text-white rounded hover:bg-red-700">Reject</button>
            <button id="verifyBtn" onclick="performVerify()" class="hidden px-4 py-2 bg-green-600 text-white rounded hover:bg-green-700">Verify</button>
        </div>
    </div>
</div>
        <!-- TAB 3: Analytics -->
        <div id="analytics-tab" class="tab-content bg-white rounded-b-xl shadow-sm p-6">
            <h2 class="text-2xl font-bold mb-6">Analytics</h2>

            <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
                <div class="bg-white border rounded-lg p-6">
                    <h3 class="text-lg font-semibold mb-4">Donations by Barangay</h3>
                    <canvas id="donationsChart"></canvas>
                </div>

                <div class="bg-white border rounded-lg p-6">
                    <h3 class="text-lg font-semibold mb-4">Disaster Status Distribution</h3>
                    <canvas id="statusChart"></canvas>
                </div>

                <div class="bg-white border rounded-lg p-6">
                    <h3 class="text-lg font-semibold mb-4">Payment Method Distribution</h3>
                    <canvas id="paymentMethodChart"></canvas>
                </div>

                <div class="bg-white border rounded-lg p-6">
                    <h3 class="text-lg font-semibold mb-4">Affected Families by Barangay</h3>
                    <canvas id="familiesChart"></canvas>
                </div>
            </div>
        </div>

        <!-- TAB 4: Fundraisers -->
        <div id="fundraisers-tab" class="tab-content bg-white rounded-b-xl shadow-sm p-6">
            <div class="flex justify-between items-center mb-6">
                <h2 class="text-2xl font-bold">Active Fundraisers</h2>
                <button class="px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition">
                    <i class="fas fa-plus mr-2"></i> Create Fundraiser
                </button>
            </div>

            <div id="fundraisersList" class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                <div class="text-center py-8 text-gray-500">
                    <i class="fas fa-spinner fa-spin text-3xl mb-2"></i>
                    <p>Loading fundraisers...</p>
                </div>
            </div>
        </div>

        <!-- TAB 5: Barangays Comparison -->
        <div id="barangays-tab" class="tab-content bg-white rounded-b-xl shadow-sm p-6">
            <h2 class="text-2xl font-bold mb-6">Barangay Comparison</h2>

            <div class="overflow-x-auto">
                <table class="w-full">
                    <thead class="bg-gray-50 border-b">
                        <tr>
                            <th class="px-4 py-3 text-left text-sm font-semibold text-gray-700">Barangay</th>
                            <th class="px-4 py-3 text-left text-sm font-semibold text-gray-700">Status</th>
                            <th class="px-4 py-3 text-left text-sm font-semibold text-gray-700">Affected Families</th>
                            <th class="px-4 py-3 text-left text-sm font-semibold text-gray-700">Total Donations</th>
                            <th class="px-4 py-3 text-left text-sm font-semibold text-gray-700">Online</th>
                            <th class="px-4 py-3 text-left text-sm font-semibold text-gray-700">Physical</th>
                            <th class="px-4 py-3 text-left text-sm font-semibold text-gray-700">Blockchain %</th>
                            <th class="px-4 py-3 text-left text-sm font-semibold text-gray-700">Urgent Needs</th>
                        </tr>
                    </thead>
                    <tbody id="barangaysTableBody">
                        <tr>
                            <td colspan="8" class="px-4 py-8 text-center text-gray-500">
                                <i class="fas fa-spinner fa-spin text-3xl mb-2"></i>
                                <p>Loading barangays...</p>
                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>

    </div>

    <!-- Suggested Matches Modal -->
    <div id="suggestedMatchesModal" class="hidden fixed inset-0 bg-black bg-opacity-50 z-50 flex items-center justify-center p-4">
        <div class="bg-white rounded-2xl shadow-2xl max-w-3xl w-full max-h-[90vh] overflow-hidden">
            <!-- Modal Header -->
            <div class="bg-gradient-to-r from-indigo-600 to-purple-600 px-6 py-4 flex justify-between items-center">
                <h3 class="text-xl font-bold text-white flex items-center gap-2">
                    <i class="fas fa-star"></i>
                    Suggested Matches
                </h3>
                <button onclick="closeMatchModal()" class="text-white hover:text-gray-200 transition">
                    <i class="fas fa-times text-2xl"></i>
                </button>
            </div>

            <!-- Modal Body -->
            <div id="matchesModalBody" class="p-6 overflow-y-auto max-h-[calc(90vh-80px)]">
                <div class="text-center py-12">
                    <i class="fas fa-spinner fa-spin text-4xl text-indigo-600 mb-4"></i>
                    <p class="text-gray-600">Finding available donations that match this resource need</p>
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdnjs.cloudflare.com/ajax/libs/leaflet/1.9.4/leaflet.min.js"></script>

  <script>
// ============================================
// UTILITY FUNCTIONS
// ============================================

const csrfToken = document.querySelector('meta[name="csrf-token"]')?.content || '';

function escapeHtml(text) {
    if (text == null) return '';
    const div = document.createElement('div');
    div.textContent = String(text);
    return div.innerHTML;
}

function formatCurrency(amount) {
    return '₱' + (Number(amount) || 0).toLocaleString();
}

function formatNumber(num) {
    return (Number(num) || 0).toLocaleString();
}

function showError(containerId, message) {
    const container = document.getElementById(containerId);
    if (!container) return;

    container.innerHTML = `
        <div class="text-center py-8">
            <div class="error-message inline-block">
                <i class="fas fa-exclamation-triangle text-2xl mb-2"></i>
                <p class="font-medium">${escapeHtml(message)}</p>
                <button onclick="location.reload()" class="mt-2 px-4 py-2 bg-red-600 text-white rounded hover:bg-red-700 transition">
                    Retry
                </button>
            </div>
        </div>
    `;
}

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

// ============================================
// STATE MANAGEMENT
// ============================================

let cityMap = null;
let donationsChart = null;
let statusChart = null;
let familiesChart = null;
let paymentMethodChart = null;
let loadedTabs = { map: false, resources: false, analytics: false, fundraisers: false, barangays: false };

// Resource Needs Verification State
let currentResourceFilter = 'all';
let allResourceNeeds = [];
let currentVerificationNeedId = null;

// ============================================
// TAB SWITCHING
// ============================================

function switchTab(tabName, event) {
    document.querySelectorAll('.tab-content').forEach(tab => tab.classList.remove('active'));
    document.querySelectorAll('.tab-btn').forEach(btn => btn.classList.remove('active'));

    const targetTab = document.getElementById(tabName + '-tab');
    let targetBtn = null;
    
    if (event && event.currentTarget) {
        targetBtn = event.currentTarget;
    } else {
        targetBtn = document.querySelector(`button[onclick*="switchTab('${tabName}'"]`);
    }

    if (targetTab) {
        targetTab.classList.add('active');
    }
    
    if (targetBtn) {
        targetBtn.classList.add('active');
    }

    if (!loadedTabs[tabName]) {
        loadedTabs[tabName] = true;

        switch(tabName) {
            case 'map':
                loadMapData();
                break;
            case 'resources':
                loadResourceNeeds();
                break;
            case 'analytics':
                loadAnalytics();
                break;
            case 'fundraisers':
                loadFundraisers();
                break;
            case 'barangays':
                loadBarangaysComparison();
                break;
        }
    }
}

// ============================================
// OVERVIEW STATISTICS
// ============================================

async function loadOverview() {
    try {
        const stats = await fetchAPI('/api/ldrrmo/overview');

        document.getElementById('totalDonations').textContent = formatCurrency(stats.total_donations);
        document.getElementById('onlineDonations').textContent = formatCurrency(stats.online_donations);
        document.getElementById('affectedFamilies').textContent = formatNumber(stats.total_affected_families);
        document.getElementById('affectedBarangays').textContent = formatNumber(stats.affected_barangays) + ' Barangays';
        document.getElementById('activeFundraisers').textContent = formatNumber(stats.active_fundraisers);
        document.getElementById('criticalBarangays').textContent = formatNumber(stats.critical_barangays);
        document.getElementById('totalDonors').textContent = formatNumber(stats.total_donors);
        document.getElementById('blockchainVerified').textContent = '0 blockchain verified';
    } catch (error) {
        console.error('Error loading overview:', error);
    }
}

// ============================================
// MAP FUNCTIONALITY
// ============================================

function initMap() {
    const mapContainer = document.getElementById('cityMap');
    if (!mapContainer) {
        console.error('Map container not found');
        return false;
    }

    if (cityMap) {
        console.warn('Map already initialized');
        return true;
    }

    try {
        cityMap = L.map('cityMap').setView([10.3157, 123.8854], 12);
        L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
            attribution: '© OpenStreetMap contributors',
            maxZoom: 18
        }).addTo(cityMap);

        return true;
    } catch (error) {
        console.error('Error initializing map:', error);
        showError('cityMap', 'Failed to initialize map');
        return false;
    }
}

async function loadMapData() {
    if (!cityMap && !initMap()) {
        return;
    }

    try {
        const barangays = await fetchAPI('/api/ldrrmo/barangays-map');

        barangays.forEach(barangay => {
            const colorMap = {
                'safe': '#10b981',
                'warning': '#eab308',
                'critical': '#f97316',
                'emergency': '#ef4444'
            };
            const color = colorMap[barangay.status] || '#9ca3af';

            const marker = L.circleMarker([barangay.lat, barangay.lng], {
                radius: 8,
                fillColor: color,
                color: '#fff',
                weight: 2,
                opacity: 1,
                fillOpacity: 0.8
            }).addTo(cityMap);

            const urgentNeedsList = Array.isArray(barangay.urgent_needs)
                ? barangay.urgent_needs.map(escapeHtml).join(', ')
                : '';

            marker.bindPopup(`
                <div style="min-width: 200px;">
                    <strong style="font-size: 16px;">${escapeHtml(barangay.name)}</strong><br>
                    <span style="color: ${color}; font-weight: 600;">${escapeHtml(String(barangay.status).toUpperCase())}</span><br>
                    <strong>Affected:</strong> ${escapeHtml(formatNumber(barangay.affected_families))} families<br>
                    ${urgentNeedsList ? '<strong>Needs:</strong> ' + urgentNeedsList : ''}
                </div>
            `);
        });
    } catch (error) {
        console.error('Error loading map data:', error);
        showError('cityMap', 'Failed to load map data. Please refresh.');
    }
}

// ============================================
// RESOURCE NEEDS & VERIFICATION
// ============================================

async function loadResourceNeeds() {
    const container = document.getElementById('resourceNeedsList');
    if (!container) {
        console.error('resourceNeedsList container not found!');
        return;
    }
    
    container.innerHTML = '<div class="text-center py-8"><i class="fas fa-spinner fa-spin text-3xl text-gray-400"></i><p class="mt-2 text-gray-600">Loading resource needs...</p></div>';

    try {
        console.log('Loading resource needs with filter:', currentResourceFilter);
        const response = await fetchAPI(`/api/ldrrmo/resource-needs?filter=${currentResourceFilter}`);
        
        console.log('Resource needs response:', response);
        allResourceNeeds = response;
        
        if (!response || response.length === 0) {
            container.innerHTML = `
                <div class="text-center py-12">
                    <i class="fas fa-inbox text-6xl text-gray-300 mb-4"></i>
                    <h3 class="text-xl font-semibold text-gray-800 mb-2">No Resource Needs Found</h3>
                    <p class="text-gray-600">No ${currentResourceFilter === 'all' ? '' : currentResourceFilter} resource needs at this time</p>
                </div>
            `;
            updateResourceCounts();
            return;
        }

        container.innerHTML = response.map(need => {
            const urgencyColors = {
                'critical': 'border-red-300 bg-red-50',
                'high': 'border-orange-300 bg-orange-50',
                'medium': 'border-yellow-300 bg-yellow-50',
                'low': 'border-blue-300 bg-blue-50'
            };

            const urgencyBadgeColors = {
                'critical': 'bg-red-100 text-red-800',
                'high': 'bg-orange-100 text-orange-800',
                'medium': 'bg-yellow-100 text-yellow-800',
                'low': 'bg-blue-100 text-blue-800'
            };

            const borderClass = urgencyColors[need.urgency] || urgencyColors['low'];
            const badgeClass = urgencyBadgeColors[need.urgency] || urgencyBadgeColors['low'];
            
            const verificationBadge = getVerificationBadgeHTML(need.verification_status || 'pending');
            const actionButtons = getActionButtonsHTML(need);

            return `
                <div class="border-2 ${borderClass} rounded-xl p-6 hover:shadow-lg transition-all">
                    <div class="flex items-start justify-between gap-4">
                        <div class="flex-1">
                            <div class="flex items-center gap-3 mb-3 flex-wrap">
                                <h3 class="text-xl font-bold text-gray-900">${escapeHtml(need.barangay_name)}</h3>
                                <span class="px-3 py-1 ${badgeClass} text-xs font-bold rounded-full uppercase">
                                    ${escapeHtml(need.urgency)}
                                </span>
                                ${verificationBadge}
                                <span class="px-3 py-1 bg-gray-100 text-gray-700 text-xs font-semibold rounded-full">
                                    ${escapeHtml(need.category)}
                                </span>
                            </div>
                            
                            <p class="text-gray-700 mb-4">${escapeHtml(need.description)}</p>
                            
                            <div class="grid grid-cols-2 gap-4 text-sm">
                                <div>
                                    <span class="text-gray-600">Quantity:</span>
                                    <strong class="ml-2 text-gray-900">${escapeHtml(need.quantity)}</strong>
                                </div>
                                <div>
                                    <span class="text-gray-600">Affected Families:</span>
                                    <strong class="ml-2 text-gray-900">${escapeHtml(need.affected_families || 0)}</strong>
                                </div>
                            </div>

                            ${need.verification_status === 'rejected' && need.rejection_reason ? `
                                <div class="mt-3 p-3 bg-red-50 border border-red-200 rounded">
                                    <p class="text-sm text-red-800"><strong>Rejection Reason:</strong> ${escapeHtml(need.rejection_reason)}</p>
                                </div>
                            ` : ''}
                        </div>

                        <div class="flex flex-col gap-3">
                            ${actionButtons}
                        </div>
                    </div>
                </div>
            `;
        }).join('');

        updateResourceCounts();

    } catch (error) {
        console.error('Error loading resource needs:', error);
        container.innerHTML = `
            <div class="text-center py-8">
                <div class="inline-block bg-red-50 border border-red-200 rounded-lg p-6">
                    <i class="fas fa-exclamation-triangle text-4xl text-red-500 mb-2"></i>
                    <p class="text-red-600 font-semibold">Failed to load resource needs</p>
                    <p class="text-sm text-red-500 mt-2">${error.message}</p>
                    <button onclick="loadResourceNeeds()" class="mt-4 px-4 py-2 bg-red-600 text-white rounded hover:bg-red-700">
                        Try Again
                    </button>
                </div>
            </div>
        `;
    }
}

function getVerificationBadgeHTML(status) {
    const badges = {
        'pending': 'bg-yellow-100 text-yellow-800 border border-yellow-300',
        'verified': 'bg-green-100 text-green-800 border border-green-300',
        'rejected': 'bg-red-100 text-red-800 border border-red-300'
    };
    
    const texts = {
        'pending': '⏳ Pending',
        'verified': '✓ Verified',
        'rejected': '✗ Rejected'
    };
    
    const badgeClass = badges[status] || badges['pending'];
    const badgeText = texts[status] || status;
    
    return `<span class="px-3 py-1 ${badgeClass} text-xs font-bold rounded-full">${badgeText}</span>`;
}

function getActionButtonsHTML(need) {
    const verificationStatus = need.verification_status || 'pending';
    
    if (verificationStatus === 'pending') {
        return `
            <button onclick="openVerificationModal(${need.id}, 'verify')" 
                class="px-4 py-2 bg-green-600 text-white text-sm rounded hover:bg-green-700 whitespace-nowrap flex items-center gap-2">
                <i class="fas fa-check"></i> Verify
            </button>
            <button onclick="openVerificationModal(${need.id}, 'reject')" 
                class="px-4 py-2 bg-red-600 text-white text-sm rounded hover:bg-red-700 whitespace-nowrap flex items-center gap-2">
                <i class="fas fa-times"></i> Reject
            </button>
        `;
    } else if (verificationStatus === 'verified') {
        return `
            <span class="px-4 py-2 bg-red-100 text-red-700 text-sm font-semibold rounded-lg">
                ACTIVE
            </span>
            <button onclick="findMatches(${need.id})" 
                class="px-6 py-3 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition-all flex items-center gap-2 font-semibold shadow-md hover:shadow-lg">
                <i class="fas fa-search"></i> Look for Match
            </button>
            <button onclick="revertVerificationStatus(${need.id})" 
                class="px-4 py-2 bg-yellow-600 text-white text-sm rounded hover:bg-yellow-700 whitespace-nowrap flex items-center gap-2">
                <i class="fas fa-undo"></i> Revert
            </button>
        `;
    } else {
        return `
            <button onclick="revertVerificationStatus(${need.id})" 
                class="px-4 py-2 bg-yellow-600 text-white text-sm rounded hover:bg-yellow-700 whitespace-nowrap flex items-center gap-2">
                <i class="fas fa-undo"></i> Revert to Pending
            </button>
        `;
    }
}

function filterResourceNeeds(filter) {
    console.log('Filtering by:', filter);
    currentResourceFilter = filter;
    
    document.querySelectorAll('.resource-filter-btn').forEach(btn => {
        btn.classList.remove('active', 'text-blue-600', 'border-blue-600');
        btn.classList.add('text-gray-500', 'border-transparent');
    });
    
    const activeTab = document.getElementById(`resource-tab-${filter}`);
    if (activeTab) {
        activeTab.classList.add('active', 'text-blue-600', 'border-blue-600');
        activeTab.classList.remove('text-gray-500', 'border-transparent');
    }
    
    loadResourceNeeds();
}

async function updateResourceCounts() {
    try {
        const allNeeds = await fetchAPI('/api/ldrrmo/resource-needs?filter=all');
        
        const counts = {
            all: allNeeds.length,
            pending: allNeeds.filter(n => (n.verification_status || 'pending') === 'pending').length,
            verified: allNeeds.filter(n => n.verification_status === 'verified').length
        };
        
        console.log('Counts:', counts);
        
        const countAll = document.getElementById('resource-count-all');
        const countPending = document.getElementById('resource-count-pending');
        const countVerified = document.getElementById('resource-count-verified');
        
        if (countAll) countAll.textContent = counts.all;
        if (countPending) countPending.textContent = counts.pending;
        if (countVerified) countVerified.textContent = counts.verified;
        
    } catch (err) {
        console.error('Error updating counts:', err);
    }
}

function openVerificationModal(needId, action) {
    currentVerificationNeedId = needId;
    const need = allResourceNeeds.find(n => n.id === needId);
    const modal = document.getElementById('verificationModal');
    const modalTitle = document.getElementById('verificationModalTitle');
    const content = document.getElementById('verificationModalContent');
    const verifyBtn = document.getElementById('verifyBtn');
    const rejectBtn = document.getElementById('rejectBtn');
    
    if (!need) {
        alert('Error: Resource need not found');
        return;
    }
    
    if (action === 'reject') {
        modalTitle.textContent = 'Reject Resource Request';
        content.innerHTML = `
            <p class="text-gray-700 mb-4">Please provide a reason for rejecting this request:</p>
            <div class="bg-gray-50 p-3 rounded mb-3">
                <p><strong>Barangay:</strong> ${escapeHtml(need.barangay_name)}</p>
                <p><strong>Category:</strong> ${escapeHtml(need.category)}</p>
                <p><strong>Quantity:</strong> ${escapeHtml(need.quantity)}</p>
            </div>
            <textarea id="rejectionReason" class="w-full border rounded p-2 h-24 focus:ring-2 focus:ring-red-500 focus:border-red-500" placeholder="Enter rejection reason (required)..."></textarea>
        `;
        verifyBtn.classList.add('hidden');
        rejectBtn.classList.remove('hidden');
    } else {
        modalTitle.textContent = 'Verify Resource Request';
        content.innerHTML = `
            <p class="text-gray-700 mb-4">Are you sure you want to verify this resource request?</p>
            <div class="bg-gray-50 p-3 rounded">
                <p><strong>Barangay:</strong> ${escapeHtml(need.barangay_name)}</p>
                <p><strong>Category:</strong> ${escapeHtml(need.category)}</p>
                <p><strong>Quantity:</strong> ${escapeHtml(need.quantity)}</p>
                <p><strong>Urgency:</strong> <span class="px-2 py-1 bg-orange-100 text-orange-800 text-xs rounded">${escapeHtml(need.urgency)}</span></p>
            </div>
        `;
        verifyBtn.classList.remove('hidden');
        rejectBtn.classList.add('hidden');
    }
    
    modal.classList.remove('hidden');
}

function closeVerificationModal() {
    document.getElementById('verificationModal').classList.add('hidden');
    currentVerificationNeedId = null;
}

async function performVerify() {
    if (!currentVerificationNeedId) return;
    
    try {
        const response = await fetchAPI(`/api/ldrrmo/resource-needs/${currentVerificationNeedId}/verify`, {
            method: 'POST',
            body: JSON.stringify({ action: 'verify' })
        });

        if (response.success) {
            alert('✓ Resource need verified successfully!');
            closeVerificationModal();
            loadResourceNeeds();
        } else {
            alert('Error: ' + response.message);
        }
    } catch (error) {
        console.error('Error verifying need:', error);
        alert('Failed to verify resource need. Please try again.');
    }
}

async function performReject() {
    if (!currentVerificationNeedId) return;
    
    const reason = document.getElementById('rejectionReason')?.value.trim();
    if (!reason) {
        alert('Please provide a rejection reason');
        return;
    }
    
    try {
        const response = await fetchAPI(`/api/ldrrmo/resource-needs/${currentVerificationNeedId}/verify`, {
            method: 'POST',
            body: JSON.stringify({
                action: 'reject',
                rejection_reason: reason
            })
        });

        if (response.success) {
            alert('✗ Resource need rejected');
            closeVerificationModal();
            loadResourceNeeds();
        } else {
            alert('Error: ' + response.message);
        }
    } catch (error) {
        console.error('Error rejecting need:', error);
        alert('Failed to reject resource need. Please try again.');
    }
}

async function revertVerificationStatus(needId) {
    if (!confirm('Are you sure you want to revert this to pending status?')) return;
    
    try {
        const response = await fetchAPI(`/api/ldrrmo/resource-needs/${needId}/revert`, {
            method: 'POST'
        });

        if (response.success) {
            alert('↺ Verification status reverted to pending');
            loadResourceNeeds();
        } else {
            alert('Error: ' + response.message);
        }
    } catch (error) {
        console.error('Error reverting verification:', error);
        alert('Failed to revert verification. Please try again.');
    }
}

// ============================================
// MATCHING FUNCTIONALITY
// ============================================

async function findMatches(needId) {
    document.getElementById('suggestedMatchesModal').classList.remove('hidden');
    const modalBody = document.getElementById('matchesModalBody');
    
    modalBody.innerHTML = `
        <div class="text-center py-12">
            <i class="fas fa-spinner fa-spin text-4xl text-indigo-600 mb-4"></i>
            <p class="text-gray-600">Searching for available donations...</p>
        </div>
    `;

    try {
        const data = await fetchAPI(`/api/ldrrmo/find-matches/${needId}`, { method: 'POST' });
        
        if (data.success) {
            displayMatches(data.need, data.matches);
        } else {
            modalBody.innerHTML = `
                <div class="text-center py-8">
                    <i class="fas fa-exclamation-circle text-4xl text-red-500 mb-4"></i>
                    <p class="text-red-600 font-semibold">Error finding matches</p>
                </div>
            `;
        }
    } catch (error) {
        console.error('Error finding matches:', error);
        modalBody.innerHTML = `
            <div class="text-center py-8">
                <i class="fas fa-exclamation-circle text-4xl text-red-500 mb-4"></i>
                <p class="text-red-600 font-semibold">Failed to load matches. Please try again.</p>
            </div>
        `;
    }
}

function displayMatches(need, matches) {
    const modalBody = document.getElementById('matchesModalBody');
    
    let html = `
        <div class="bg-blue-50 border-2 border-blue-200 rounded-xl p-4 mb-6">
            <div class="flex items-start gap-4">
                <div class="w-12 h-12 bg-blue-600 rounded-lg flex items-center justify-center flex-shrink-0">
                    <i class="fas fa-home text-white text-xl"></i>
                </div>
                <div class="flex-1">
                    <h4 class="text-lg font-bold text-gray-900 mb-3">
                        <i class="fas fa-search text-blue-600 mr-2"></i>Looking for: ${escapeHtml(need.item_name)}
                    </h4>
                    <div class="grid grid-cols-2 gap-3 text-sm">
                        <div>
                            <span class="text-gray-600">Barangay:</span>
                            <strong class="ml-2 text-gray-900">${escapeHtml(need.barangay.name)}</strong>
                        </div>
                        <div>
                            <span class="text-gray-600">Quantity Needed:</span>
                            <strong class="ml-2 text-gray-900">${escapeHtml(need.quantity)} units</strong>
                        </div>
                        <div>
                            <span class="text-gray-600">Urgency:</span>
                            <span class="ml-2 px-2 py-1 bg-red-100 text-red-700 text-xs font-bold rounded uppercase">
                                ${escapeHtml(need.urgency)}
                            </span>
                        </div>
                        <div>
                            <span class="text-gray-600">Affected Families:</span>
                            <strong class="ml-2 text-gray-900">${escapeHtml(need.affected_families)}</strong>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <hr class="my-6 border-dashed border-2 border-gray-300">
    `;

    if (matches.length === 0) {
        html += `
            <div class="text-center py-12">
                <i class="fas fa-box-open text-8xl text-gray-300 mb-6"></i>
                <h4 class="text-2xl font-bold text-gray-800 mb-2">No Current Matches Available</h4>
                <p class="text-gray-600">No barangays currently have "${escapeHtml(need.item_name)}" available for donation</p>
            </div>
        `;
    } else {
        html += matches.map(match => {
            const score = Math.round(match.match_score);
            const scoreColor = score >= 70 ? 'text-green-600 border-green-500' : 
                            score >= 50 ? 'text-yellow-600 border-yellow-500' : 
                            'text-red-600 border-red-500';

            return `
                <div class="border-2 ${scoreColor} rounded-xl p-5 mb-4 hover:shadow-xl transition-all">
                    <div class="flex items-center gap-6">
                        <div class="text-center flex-shrink-0">
                            <div class="text-5xl font-black ${scoreColor}">${score}%</div>
                            <div class="text-xs text-gray-500 font-semibold mt-1">MATCH</div>
                        </div>

                        <div class="flex-1">
                            <h5 class="text-lg font-bold text-gray-900 mb-2">
                                <i class="fas fa-building text-blue-600 mr-2"></i>
                                ${escapeHtml(match.barangay.name)}
                            </h5>
                            <p class="text-gray-700 mb-2">
                                <strong>Available:</strong> ${escapeHtml(match.donation.item_name)}
                                <span class="ml-2 px-2 py-1 bg-gray-200 text-gray-800 text-xs font-semibold rounded">
                                    ${escapeHtml(match.donation.quantity)} units
                                </span>
                            </p>
                            ${match.can_fulfill ? 
                                '<span class="inline-flex items-center gap-1 px-3 py-1 bg-green-100 text-green-700 text-xs font-bold rounded-full"><i class="fas fa-check-circle"></i> Can fully fulfill request</span>' :
                                '<span class="inline-flex items-center gap-1 px-3 py-1 bg-yellow-100 text-yellow-700 text-xs font-bold rounded-full"><i class="fas fa-exclamation-triangle"></i> Partial fulfillment only</span>'
                            }
                        </div>

                        <div class="flex flex-col gap-2 flex-shrink-0">
                            <button onclick="viewMatchDetails(${need.id}, ${match.donation.id})" 
                                    class="px-4 py-2 bg-blue-100 text-blue-700 rounded-lg hover:bg-blue-200 transition text-sm font-semibold">
                                <i class="fas fa-info-circle mr-1"></i> View Details
                            </button>
                            <button onclick="contactBarangay('${match.barangay.id}', '${escapeHtml(match.barangay.name)}')" 
                                    class="px-4 py-2 bg-green-600 text-white rounded-lg hover:bg-green-700 transition text-sm font-semibold">
                                <i class="fas fa-phone mr-1"></i> Contact
                            </button>
                        </div>
                    </div>
                </div>
            `;
        }).join('');
    }

    modalBody.innerHTML = html;
}

function closeMatchModal() {
    document.getElementById('suggestedMatchesModal').classList.add('hidden');
}

function viewMatchDetails(needId, donationId) {
    alert('📋 Detailed Match Information\n\nThis would display:\n• Transfer logistics\n• Distance between barangays\n• Detailed item comparison\n• Confirmation and tracking options');
}

async function contactBarangay(barangayId, barangayName) {
    try {
        const data = await fetchAPI(`/api/ldrrmo/barangay-contact/${barangayId}`);
        
        if (data.success) {
            const contact = data.contact_info;
            alert(`📞 Contact Information\n\nBarangay: ${contact.name}\nContact Person: ${contact.contact_person}\nPhone: ${contact.phone}\nEmail: ${contact.email}`);
        }
    } catch (error) {
        alert('Unable to retrieve contact information. Please try again.');
    }
}

// ============================================
// ANALYTICS CHARTS
// ============================================

async function loadAnalytics() {
    await new Promise(resolve => setTimeout(resolve, 100));

    try {
        const data = await fetchAPI('/api/ldrrmo/analytics');

        const donationsCtx = document.getElementById('donationsChart');
        if (donationsCtx) {
            if (donationsChart) {
                donationsChart.destroy();
                donationsChart = null;
            }

            donationsChart = new Chart(donationsCtx, {
                type: 'bar',
                data: {
                    labels: (data.donations_by_barangay || []).map(b => b.name),
                    datasets: [{
                        label: 'Total Donations (₱)',
                        data: (data.donations_by_barangay || []).map(b => Number(b.total_donations) || 0),
                        backgroundColor: '#3B82F6'
                    }]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: true,
                    scales: {
                        y: { beginAtZero: true }
                    }
                }
            });
        }

        const statusCtx = document.getElementById('statusChart');
        if (statusCtx) {
            if (statusChart) {
                statusChart.destroy();
                statusChart = null;
            }

            const statusData = data.disaster_status_distribution || {};
            statusChart = new Chart(statusCtx, {
                type: 'pie',
                data: {
                    labels: Object.keys(statusData).map(s => String(s).toUpperCase()),
                    datasets: [{
                        data: Object.values(statusData),
                        backgroundColor: ['#10B981', '#EAB308', '#F97316', '#EF4444']
                    }]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: true
                }
            });
        }

        const paymentCtx = document.getElementById('paymentMethodChart');
        if (paymentCtx && data.payment_method_distribution) {
            if (paymentMethodChart) {
                paymentMethodChart.destroy();
                paymentMethodChart = null;
            }

            paymentMethodChart = new Chart(paymentCtx, {
                type: 'doughnut',
                data: {
                    labels: (data.payment_method_distribution || []).map(p => String(p.payment_method).toUpperCase()),
                    datasets: [{
                        data: (data.payment_method_distribution || []).map(p => Number(p.total) || 0),
                        backgroundColor: ['#9333EA', '#3B82F6', '#F97316', '#10B981', '#6B7280']
                    }]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: true,
                    plugins: {
                        legend: {
                            position: 'bottom'
                        }
                    }
                }
            });
        }

        const familiesCtx = document.getElementById('familiesChart');
        if (familiesCtx) {
            if (familiesChart) {
                familiesChart.destroy();
                familiesChart = null;
            }

            familiesChart = new Chart(familiesCtx, {
                type: 'bar',
                data: {
                    labels: (data.affected_families_by_barangay || []).map(b => b.name),
                    datasets: [{
                        label: 'Affected Families',
                        data: (data.affected_families_by_barangay || []).map(b => Number(b.affected_families) || 0),
                        backgroundColor: '#EF4444'
                    }]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: true,
                    scales: {
                        y: { beginAtZero: true }
                    }
                }
            });
        }
    } catch (error) {
        console.error('Error loading analytics:', error);
        showError('analytics-tab', 'Failed to load analytics data. Please try again.');
    }
}

// ============================================
// FUNDRAISERS
// ============================================

async function loadFundraisers() {
    const container = document.getElementById('fundraisersList');
    if (!container) return;

    try {
        const fundraisers = await fetchAPI('/api/ldrrmo/fundraisers');

        if (!Array.isArray(fundraisers) || fundraisers.length === 0) {
            container.innerHTML = '<div class="col-span-3 text-center py-8 text-gray-500">No active fundraisers</div>';
            return;
        }

        container.innerHTML = fundraisers.map(f => {
            const urgentNeeds = Array.isArray(f.urgent_needs) ? f.urgent_needs : [];
            const progress = Number(f.progress) || 0;

            return `
                <div class="border rounded-lg p-6 hover:shadow-md transition">
                    <div class="flex justify-between items-start mb-4">
                        <h3 class="text-lg font-semibold">${escapeHtml(f.title)}</h3>
                        <span class="px-2 py-1 bg-red-100 text-red-700 text-xs rounded">${escapeHtml(f.severity)}</span>
                    </div>
                    <p class="text-sm text-gray-600 mb-2">
                        <i class="fas fa-map-marker-alt mr-1"></i>${escapeHtml(f.barangay)}
                    </p>
                    <p class="text-sm text-gray-500 mb-4">${escapeHtml(f.description)}</p>

                    <div class="mb-4">
                        <div class="flex justify-between text-sm mb-1">
                            <span class="font-semibold">${formatCurrency(f.raised)}</span>
                            <span class="text-gray-600">${formatCurrency(f.goal)}</span>
                        </div>
                        <div class="w-full bg-gray-200 rounded-full h-2">
                            <div class="bg-green-600 h-2 rounded-full" style="width: ${Math.min(progress, 100)}%"></div>
                        </div>
                        <p class="text-xs text-gray-600 mt-1">${progress}% funded</p>
                    </div>

                    <div class="flex justify-between text-sm text-gray-600 mb-3">
                        <span><i class="fas fa-users"></i> ${formatNumber(f.donors_count)} donors</span>
                        <span><i class="fas fa-calendar"></i> ${formatNumber(f.days_active)} days</span>
                    </div>

                    ${urgentNeeds.length > 0 ? `
                        <div class="border-t pt-3">
                            <p class="text-xs text-gray-600 mb-2">Urgent Needs:</p>
                            <div class="flex flex-wrap gap-1">
                                ${urgentNeeds.map(need => `
                                    <span class="px-2 py-1 bg-blue-100 text-blue-700 text-xs rounded">
                                        ${escapeHtml(need.type || need)}
                                    </span>
                                `).join('')}
                            </div>
                        </div>
                    ` : ''}
                </div>
            `;
        }).join('');
    } catch (error) {
        console.error('Error loading fundraisers:', error);
        showError('fundraisersList', 'Failed to load fundraisers. Please try again.');
    }
}

// ============================================
// BARANGAYS COMPARISON
// ============================================

async function loadBarangaysComparison() {
    const tbody = document.getElementById('barangaysTableBody');
    if (!tbody) return;

    try {
        const barangays = await fetchAPI('/api/ldrrmo/barangays-comparison');

        if (!Array.isArray(barangays) || barangays.length === 0) {
            tbody.innerHTML = `
                <tr>
                    <td colspan="8" class="px-4 py-8 text-center text-gray-500">
                        No barangay data available
                    </td>
                </tr>
            `;
            return;
        }

        tbody.innerHTML = barangays.map(b => {
            const urgentNeeds = Array.isArray(b.urgent_needs) ? b.urgent_needs : 
                               Array.isArray(b.resource_needs) ? b.resource_needs : [];
            const blockchainRate = Number(b.blockchain_verification_rate) || 0;

            return `
                <tr class="border-b hover:bg-gray-50">
                    <td class="px-4 py-3 font-medium">${escapeHtml(b.name)}</td>
                    <td class="px-4 py-3">
                        <span class="px-2 py-1 text-xs rounded ${getStatusBadge(b.status)}">
                            ${escapeHtml(String(b.status).toUpperCase())}
                        </span>
                    </td>
                    <td class="px-4 py-3">${formatNumber(b.affected_families)}</td>
                    <td class="px-4 py-3 font-semibold">${formatCurrency(b.donations_received)}</td>
                    <td class="px-4 py-3 text-purple-600">${formatCurrency(b.online_donations)}</td>
                    <td class="px-4 py-3 text-green-600">${formatCurrency(b.physical_donations)}</td>
                    <td class="px-4 py-3">
                        <div class="flex items-center gap-2">
                            <span class="text-sm">${blockchainRate}%</span>
                            ${blockchainRate >= 80 ? '<i class="fas fa-check-circle text-green-500"></i>' :
                              blockchainRate >= 50 ? '<i class="fas fa-check-circle text-yellow-500"></i>' :
                              '<i class="fas fa-exclamation-circle text-gray-400"></i>'}
                        </div>
                    </td>
                    <td class="px-4 py-3">
                        ${urgentNeeds.length > 0 ? urgentNeeds.map(need =>
                            `<span class="px-2 py-1 bg-blue-100 text-blue-700 text-xs rounded mr-1 mb-1 inline-block">${escapeHtml(need)}</span>`
                        ).join('') : '<span class="text-gray-400 text-sm">None</span>'}
                    </td>
                </tr>
            `;
        }).join('');
    } catch (error) {
        console.error('Error loading barangays:', error);
        tbody.innerHTML = `
            <tr>
                <td colspan="8" class="px-4 py-8 text-center text-red-500">
                    Failed to load barangay data. Please refresh the page.
                </td>
            </tr>
        `;
    }
}

function getStatusBadge(status) {
    const badges = {
        'safe': 'bg-green-100 text-green-700',
        'warning': 'bg-yellow-100 text-yellow-700',
        'critical': 'bg-orange-100 text-orange-700',
        'emergency': 'bg-red-100 text-red-700'
    };
    return badges[status] || 'bg-gray-100 text-gray-700';
}

// ============================================
// EVENT LISTENERS
// ============================================

document.addEventListener('click', function(e) {
    const modal = document.getElementById('suggestedMatchesModal');
    if (e.target === modal) {
        closeMatchModal();
    }
    
    const verificationModal = document.getElementById('verificationModal');
    if (e.target === verificationModal) {
        closeVerificationModal();
    }
});

// ============================================
// INITIALIZATION
// ============================================

document.addEventListener('DOMContentLoaded', function() {
    console.log('LDRRMO Dashboard initializing...');

    loadOverview();

    if (initMap()) {
        loadMapData();
        loadedTabs.map = true;
    }

    setInterval(loadOverview, 5 * 60 * 1000);

    console.log('LDRRMO Dashboard initialized successfully');
    console.log('✅ Resource Needs Verification system loaded');
});

// ============================================
// ERROR HANDLING
// ============================================

window.addEventListener('error', function(event) {
    console.error('Global error:', event.error);
});

window.addEventListener('unhandledrejection', function(event) {
    console.error('Unhandled promise rejection:', event.reason);
});
</script>
</body>
</html><?php /**PATH C:\Users\janlo\Git\BayanihanCebu\resources\views/UserDashboards/citydashboard.blade.php ENDPATH**/ ?>
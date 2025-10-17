<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
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
                <p class="font-medium">{{ session('user_name', 'Admin') }}</p>
            </div>
            <form action="{{ route('logout') }}" method="POST">
                @csrf
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

        <!-- TAB 2: Analytics -->
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

        <!-- TAB 3: Fundraisers -->
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

        <!-- TAB 4: Barangays Comparison -->
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

    <script src="https://cdnjs.cloudflare.com/ajax/libs/leaflet/1.9.4/leaflet.min.js"></script>

    <script>
        // ============================================
        // UTILITY FUNCTIONS
        // ============================================

        const csrfToken = document.querySelector('meta[name="csrf-token"]')?.content || '';

        // HTML escape to prevent XSS
        function escapeHtml(text) {
            if (text == null) return '';
            const div = document.createElement('div');
            div.textContent = String(text);
            return div.innerHTML;
        }

        // Safe number formatting
        function formatCurrency(amount) {
            return '₱' + (Number(amount) || 0).toLocaleString();
        }

        // Safe number formatting without currency
        function formatNumber(num) {
            return (Number(num) || 0).toLocaleString();
        }

        // Show error message in container
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

        // API fetch with error handling
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
        let loadedTabs = { map: false, analytics: false, fundraisers: false, barangays: false };

        // ============================================
        // TAB SWITCHING
        // ============================================

        function switchTab(tabName, event) {
            if (!event) return;

            // Update UI
            document.querySelectorAll('.tab-content').forEach(tab => tab.classList.remove('active'));
            document.querySelectorAll('.tab-btn').forEach(btn => btn.classList.remove('active'));

            const targetTab = document.getElementById(tabName + '-tab');
            const targetBtn = event.currentTarget;

            if (targetTab && targetBtn) {
                targetTab.classList.add('active');
                targetBtn.classList.add('active');
            }

            // Load data for the tab (only once)
            if (!loadedTabs[tabName]) {
                loadedTabs[tabName] = true;

                switch(tabName) {
                    case 'map':
                        loadMapData();
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
                document.getElementById('blockchainVerified').textContent = formatNumber(stats.blockchain_verified) + ' blockchain verified';
            } catch (error) {
                console.error('Error loading overview:', error);
                // Show error indicator but don't block the page
                document.querySelectorAll('[id$="Donations"], [id$="Families"], [id$="Fundraisers"]').forEach(el => {
                    el.textContent = 'Error';
                    el.classList.add('text-red-500');
                });
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
        // ANALYTICS CHARTS
        // ============================================

        async function loadAnalytics() {
            // Small delay to ensure canvas elements are rendered
            await new Promise(resolve => setTimeout(resolve, 100));

            try {
                const data = await fetchAPI('/api/ldrrmo/analytics');

                // Donations Chart
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

                // Status Distribution Chart
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

                // Payment Method Distribution Chart
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

                // Families Chart
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
                    const urgentNeeds = Array.isArray(b.urgent_needs) ? b.urgent_needs : [];
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
                showError('barangaysTableBody', 'Failed to load barangay data. Please try again.');
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
        // INITIALIZATION
        // ============================================

        document.addEventListener('DOMContentLoaded', function() {
            console.log('LDRRMO Dashboard initializing...');

            // Load overview stats immediately
            loadOverview();

            // Initialize map (visible on load)
            if (initMap()) {
                loadMapData();
                loadedTabs.map = true;
            }

            // Set up periodic refresh for overview stats (every 5 minutes)
            setInterval(loadOverview, 5 * 60 * 1000);

            console.log('LDRRMO Dashboard initialized successfully');
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

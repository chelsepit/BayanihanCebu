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

            <div class="flex items-center gap-4">
    <!-- Notification Bell -->
    <div class="relative">
        <button id="notification-bell" 
                onclick="toggleNotifications()" 
                class="relative p-2 text-gray-600 hover:text-indigo-600 transition">
            <i class="fas fa-bell text-xl"></i>
            <!-- Unread Badge -->
            <span id="notification-badge" 
                  class="hidden absolute -top-1 -right-1 bg-red-500 text-white text-xs font-bold rounded-full h-5 w-5 flex items-center justify-center">
                0
            </span>
        </button>

        <!-- Notifications Dropdown -->
        <div id="notifications-dropdown" 
             class="hidden absolute right-0 mt-2 w-96 bg-white rounded-lg shadow-xl border z-50 max-h-96 overflow-hidden">
            
            <!-- Header -->
            <div class="px-4 py-3 border-b bg-gray-50 flex items-center justify-between">
                <h3 class="font-bold text-gray-900">Notifications</h3>
                <div class="flex items-center gap-2">
                    <span id="notification-count" class="text-xs text-gray-500">0 unread</span>
                    <button onclick="markAllAsRead()" 
                            class="text-xs text-indigo-600 hover:text-indigo-700 font-semibold">
                        Mark all read
                    </button>
                </div>
            </div>

            <!-- Filter Tabs -->
            <div class="flex border-b bg-gray-50">
                <button onclick="filterNotifications('all')" 
                        id="notif-filter-all"
                        class="flex-1 px-4 py-2 text-sm font-semibold border-b-2 border-indigo-600 text-indigo-600">
                    All
                </button>
                <button onclick="filterNotifications('match_request')" 
                        id="notif-filter-match_request"
                        class="flex-1 px-4 py-2 text-sm font-semibold border-b-2 border-transparent text-gray-600 hover:text-indigo-600">
                    Matches
                </button>
                <button onclick="filterNotifications('match_accepted')" 
                        id="notif-filter-match_accepted"
                        class="flex-1 px-4 py-2 text-sm font-semibold border-b-2 border-transparent text-gray-600 hover:text-indigo-600">
                    Accepted
                </button>
                <button onclick="filterNotifications('message')" 
                        id="notif-filter-message"
                        class="flex-1 px-4 py-2 text-sm font-semibold border-b-2 border-transparent text-gray-600 hover:text-indigo-600">
                    Messages
                </button>
            </div>

            <!-- Notifications List -->
            <div id="notifications-list" class="overflow-y-auto max-h-80">
                <div class="text-center py-8 text-gray-500">
                    <i class="fas fa-spinner fa-spin text-2xl mb-2"></i>
                    <p class="text-sm">Loading notifications...</p>
                </div>
            </div>

            <!-- Footer -->
            <div class="px-4 py-3 border-t bg-gray-50 text-center">
                <button onclick="viewAllNotifications()" 
                        class="text-sm text-indigo-600 hover:text-indigo-700 font-semibold">
                    View All Notifications
                </button>
            </div>
        </div>
    </div>

    <!-- User Info & Logout (existing) -->
    <div class="flex items-center gap-3">
        <!-- Your existing user dropdown here -->
    </div>
</div>
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
                <button onclick="showTab('my-matches')" 
                class="tab-button px-6 py-3 text-gray-600 hover:text-indigo-600 hover:border-indigo-600 border-b-2 border-transparent transition font-semibold"
                data-tab="my-matches">
                     <i class="fas fa-handshake mr-2"></i>My Matches
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

            <div id="resourceNeedsList" class="space-y-4">
                <div class="text-center py-8 text-gray-500">
                    <i class="fas fa-spinner fa-spin text-3xl mb-2"></i>
                    <p>Loading resource needs...</p>
                </div>
            </div>
        </div>

        <div id="my-matches-tab" class="tab-content bg-white rounded-b-xl shadow-sm p-6">
    <div class="flex justify-between items-center mb-6">
        <h2 class="text-2xl font-bold">My Initiated Matches</h2>


        <!-- Filter Tabs -->
        <div class="flex gap-2">
            <button onclick="filterMyMatches('all')" 
                    id="my-matches-filter-all"
                    class="px-4 py-2 rounded-lg font-semibold text-sm transition bg-indigo-600 text-white">
                All (<span id="my-matches-count-all">0</span>)
            </button>
            <button onclick="filterMyMatches('pending')" 
                    id="my-matches-filter-pending"
                    class="px-4 py-2 rounded-lg font-semibold text-sm transition bg-gray-200 text-gray-700 hover:bg-gray-300">
                Pending (<span id="my-matches-count-pending">0</span>)
            </button>
            <button onclick="filterMyMatches('accepted')" 
                    id="my-matches-filter-accepted"
                    class="px-4 py-2 rounded-lg font-semibold text-sm transition bg-gray-200 text-gray-700 hover:bg-gray-300">
                Accepted (<span id="my-matches-count-accepted">0</span>)
            </button>
            <button onclick="filterMyMatches('completed')" 
                    id="my-matches-filter-completed"
                    class="px-4 py-2 rounded-lg font-semibold text-sm transition bg-gray-200 text-gray-700 hover:bg-gray-300">
                Completed (<span id="my-matches-count-completed">0</span>)
            </button>
            <button onclick="filterMyMatches('rejected')" 
                    id="my-matches-filter-rejected"
                    class="px-4 py-2 rounded-lg font-semibold text-sm transition bg-gray-200 text-gray-700 hover:bg-gray-300">
                Rejected (<span id="my-matches-count-rejected">0</span>)
            </button>
        </div>
    </div>

    <!-- Statistics Cards -->
    <div class="grid grid-cols-1 md:grid-cols-4 gap-4 mb-6">
        <div class="bg-gradient-to-br from-blue-500 to-blue-600 rounded-lg p-4 text-white">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-blue-100 text-sm">Total Matches</p>
                    <p class="text-3xl font-bold" id="stats-total-matches">0</p>
                </div>
                <i class="fas fa-handshake text-4xl opacity-20"></i>
            </div>
        </div>

        <div class="bg-gradient-to-br from-yellow-500 to-yellow-600 rounded-lg p-4 text-white">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-yellow-100 text-sm">Pending Response</p>
                    <p class="text-3xl font-bold" id="stats-pending-matches">0</p>
                </div>
                <i class="fas fa-clock text-4xl opacity-20"></i>
            </div>
        </div>

        <div class="bg-gradient-to-br from-green-500 to-green-600 rounded-lg p-4 text-white">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-green-100 text-sm">Accepted</p>
                    <p class="text-3xl font-bold" id="stats-accepted-matches">0</p>
                </div>
                <i class="fas fa-check-circle text-4xl opacity-20"></i>
            </div>
        </div>

        <div class="bg-gradient-to-br from-purple-500 to-purple-600 rounded-lg p-4 text-white">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-purple-100 text-sm">Success Rate</p>
                    <p class="text-3xl font-bold" id="stats-success-rate">0%</p>
                </div>
                <i class="fas fa-chart-line text-4xl opacity-20"></i>
            </div>
        </div>
    </div>

    <!-- Matches List -->
    <div id="my-matches-list" class="space-y-4">
        <div class="text-center py-8 text-gray-500">
            <i class="fas fa-spinner fa-spin text-3xl mb-2"></i>
            <p>Loading matches...</p>
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

        // ============================================
        // TAB SWITCHING
        // ============================================

        function switchTab(tabName, event) {
            document.querySelectorAll('.tab-content').forEach(tab => tab.classList.remove('active'));
            document.querySelectorAll('.tab-btn, .tab-button').forEach(btn => btn.classList.remove('active'));

            const targetTab = document.getElementById(tabName + '-tab');
            let targetBtn = null;

            if (event && event.currentTarget) {
                targetBtn = event.currentTarget;
            } else {
                targetBtn = document.querySelector(`button[onclick*="switchTab('${tabName}'"]`) ||
                            document.querySelector(`button[onclick*="showTab('${tabName}'"]`) ||
                            document.querySelector(`button[data-tab="${tabName}"]`);
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
                    case 'my-matches':
                        loadMyMatches();
                        break;
                }
            }
        }

        // Alias for compatibility
        function showTab(tabName) {
            switchTab(tabName, null);
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

            // Build needs list from resource_needs table
            let needsHtml = '';
            if (barangay.status !== 'safe' && barangay.resource_needs && barangay.resource_needs.length > 0) {
                needsHtml = '<div class="mt-2"><strong>Resource Needs:</strong><ul class="mt-1 text-sm">';
                
                // Group by urgency or show top 3
                barangay.resource_needs.slice(0, 3).forEach(need => {
                    const urgencyBadge = need.urgency === 'critical' ? '🔴' : 
                                       need.urgency === 'high' ? '🟠' : 
                                       need.urgency === 'medium' ? '🟡' : '🔵';
                    needsHtml += `<li>${urgencyBadge} ${escapeHtml(need.category)}: ${escapeHtml(need.quantity)}</li>`;
                });
                
                if (barangay.resource_needs.length > 3) {
                    needsHtml += `<li class="text-gray-600">...and ${barangay.resource_needs.length - 3} more</li>`;
                }
                needsHtml += '</ul></div>';
            }

            marker.bindPopup(`
                <div style="min-width: 250px;">
                    <strong style="font-size: 16px;">${escapeHtml(barangay.name)}</strong><br>
                    <span class="px-2 py-1 text-xs rounded" style="background-color: ${color}20; color: ${color}; font-weight: 600;">
                        ${escapeHtml(String(barangay.status).toUpperCase())}
                    </span><br>
                    <div class="mt-2">
                        <strong>Affected:</strong> ${formatNumber(barangay.affected_families)} families
                    </div>
                    ${needsHtml}
                </div>
            `);
        });
    } catch (error) {
        console.error('Error loading map data:', error);
        showError('cityMap', 'Failed to load map data. Please refresh.');
    }
}

        // ============================================
        // RESOURCE NEEDS & MATCHING
        // ============================================
        let currentResourceNeeds = [];
        let currentResourceNeedsFilter = 'all';

        async function loadResourceNeeds() {
            const container = document.getElementById('resourceNeedsList');
            container.innerHTML = '<div class="text-center py-8"><i class="fas fa-spinner fa-spin text-3xl text-gray-400"></i><p class="mt-2 text-gray-600">Loading resource needs...</p></div>';

            try {
                const filter = currentResourceNeedsFilter || 'all';
                const response = await fetchAPI('/api/ldrrmo/resource-needs');

                currentResourceNeeds = response;

                if (!response || response.length === 0) {
                    container.innerHTML = `
                        <div class="text-center py-12">
                            <i class="fas fa-check-circle text-6xl text-green-500 mb-4"></i>
                            <h3 class="text-xl font-semibold text-gray-800 mb-2">All Needs Fulfilled</h3>
                            <p class="text-gray-600">No active resource needs at this time</p>
                        </div>
                    `;
                    return;
                }

                container.innerHTML = response.map(need => {
                    const urgencyColors = {
                        'critical': 'bg-red-100 text-red-800 border-red-300',
                        'high': 'bg-orange-100 text-orange-800 border-orange-300',
                        'medium': 'bg-yellow-100 text-yellow-800 border-yellow-300',
                        'low': 'bg-blue-100 text-blue-800 border-blue-300'
                    };

                    const urgencyClass = urgencyColors[need.urgency] || urgencyColors['low'];

                    return `
                        <div class="border-2 ${urgencyClass} rounded-xl p-6 hover:shadow-lg transition-all">
                            <div class="flex items-start justify-between gap-4">
                                <div class="flex-1">
                                    <div class="flex items-center gap-3 mb-3">
                                        <h3 class="text-xl font-bold text-gray-900">${escapeHtml(need.barangay_name)}</h3>
                                        <span class="px-3 py-1 ${urgencyClass} text-xs font-bold rounded-full uppercase border">
                                            ${escapeHtml(need.urgency)}
                                        </span>
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
                                            <strong class="ml-2 text-gray-900">${escapeHtml(need.affected_families)}</strong>
                                        </div>
                                    </div>
                                </div>

                                <div class="flex flex-col items-end gap-3">
                                    <span class="px-4 py-2 bg-red-100 text-red-700 text-sm font-semibold rounded-lg">
                                        ACTIVE
                                    </span>
                                    <button onclick="findMatches(${need.id})" 
                                            class="px-6 py-3 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition-all flex items-center gap-2 font-semibold shadow-md hover:shadow-lg">
                                        <i class="fas fa-search"></i>
                                        Look for Match
                                    </button>
                                </div>
                            </div>
                        </div>
                    `;
                }).join('');

            } catch (error) {
                console.error('Error loading resource needs:', error);
                showError('resourceNeedsList', 'Failed to load resource needs. Please try again.');
            }
        }

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
    
    if (!matches || matches.length === 0) {
        modalBody.innerHTML = `
            <div class="text-center py-12">
                <i class="fas fa-times-circle text-6xl text-gray-400 mb-4"></i>
                <h3 class="text-xl font-semibold text-gray-700 mb-2">No Matches Found</h3>
                <p class="text-gray-500">No available donations match this resource need at the moment.</p>
            </div>
        `;
        return;
    }

    const escapeHtml = (text) => {
        const div = document.createElement('div');
        div.textContent = text;
        return div.innerHTML;
    };

    const html = `
        <div class="mb-6 bg-blue-50 border border-blue-200 rounded-lg p-4">
            <h3 class="font-bold text-lg text-blue-900 mb-2">Resource Need Details</h3>
            <div class="grid grid-cols-2 gap-4 text-sm">
                <div>
                    <span class="font-semibold text-gray-700">Barangay:</span>
                    <span class="text-gray-900">${escapeHtml(need.barangay.name)}</span>
                </div>
                <div>
                    <span class="font-semibold text-gray-700">Category:</span>
                    <span class="text-gray-900">${escapeHtml(need.category)}</span>
                </div>
                <div>
                    <span class="font-semibold text-gray-700">Quantity Needed:</span>
                    <span class="text-gray-900">${escapeHtml(need.quantity)}</span>
                </div>
                <div>
                    <span class="font-semibold text-gray-700">Urgency:</span>
                    <span class="px-2 py-1 rounded-full text-xs font-bold ${
                        need.urgency === 'critical' ? 'bg-red-100 text-red-700' :
                        need.urgency === 'high' ? 'bg-orange-100 text-orange-700' :
                        need.urgency === 'medium' ? 'bg-yellow-100 text-yellow-700' :
                        'bg-green-100 text-green-700'
                    }">${escapeHtml(need.urgency.toUpperCase())}</span>
                </div>
            </div>
        </div>

        <h3 class="font-bold text-lg mb-4">Suggested Matches (${matches.length})</h3>
        
        ${matches.map(match => `
            <div class="border rounded-lg p-4 mb-4 hover:shadow-md transition">
                <div class="flex items-start justify-between gap-4">
                    <div class="flex-1">
                        <div class="flex items-center gap-2 mb-2">
                            <span class="inline-flex items-center gap-1 px-3 py-1 bg-indigo-100 text-indigo-700 text-xs font-bold rounded-full">
                                <i class="fas fa-chart-line"></i> Match Score: ${match.match_score}%
                            </span>
                            <span class="inline-flex items-center gap-1 px-3 py-1 bg-purple-100 text-purple-700 text-xs font-bold rounded-full">
                                <i class="fas fa-map-marker-alt"></i> ${escapeHtml(match.barangay.name)}
                            </span>
                        </div>
                        
                        <p class="font-semibold text-gray-900 mb-1">
                            <i class="fas fa-box mr-1 text-gray-500"></i>
                            ${escapeHtml(match.donation.items_description)}
                        </p>
                        <p class="text-sm text-gray-600 mb-2">
                            <i class="fas fa-cubes mr-1"></i>
                            Available: <span class="font-semibold">${escapeHtml(match.donation.quantity)}</span>
                        </p>

                        <div class="flex items-center gap-2 text-xs text-gray-500 mb-2">
                            <span><i class="fas fa-user mr-1"></i>${escapeHtml(match.donation.donor_name)}</span>
                            <span><i class="fas fa-calendar mr-1"></i>${match.donation.recorded_at}</span>
                        </div>

                        <div class="mt-2">
                            ${match.can_fully_fulfill ? 
                                '<span class="inline-flex items-center gap-1 px-3 py-1 bg-green-100 text-green-700 text-xs font-bold rounded-full"><i class="fas fa-check-circle"></i> Can fully fulfill request</span>' :
                                '<span class="inline-flex items-center gap-1 px-3 py-1 bg-yellow-100 text-yellow-700 text-xs font-bold rounded-full"><i class="fas fa-exclamation-triangle"></i> Partial fulfillment only</span>'
                            }
                        </div>
                    </div>

                    <div class="flex flex-col gap-2 flex-shrink-0">
                        <button onclick="viewMatchDetails(${need.id}, ${match.donation.id})" 
                                class="px-4 py-2 bg-blue-100 text-blue-700 rounded-lg hover:bg-blue-200 transition text-sm font-semibold">
                            <i class="fas fa-info-circle mr-1"></i> View Details
                        </button>
                        
                        <!-- ✅ UPDATED: Now initiates match request -->
                        <button onclick="contactBarangay(${need.id}, ${match.donation.id}, '${match.barangay.id}', '${escapeHtml(match.barangay.name)}', ${match.match_score}, ${match.can_fully_fulfill})" 
                                class="px-4 py-2 bg-green-600 text-white rounded-lg hover:bg-green-700 transition text-sm font-semibold">
                            <i class="fas fa-handshake mr-1"></i> Initiate Match
                        </button>
                    </div>
                </div>
            </div>
        `).join('')}
    `;

    modalBody.innerHTML = html;
}

        function closeMatchModal() {
            document.getElementById('suggestedMatchesModal').classList.add('hidden');
        }

        function viewMatchDetails(needId, donationId) {
            alert('📋 Detailed Match Information\n\nThis would display:\n• Transfer logistics\n• Distance between barangays\n• Detailed item comparison\n• Confirmation and tracking options');
        }
let currentMyMatchesFilter = 'all';
let allMyMatches = [];

async function loadMyMatches() {
    try {
        const data = await fetchAPI(`/api/ldrrmo/matches?status=${currentMyMatchesFilter}`);
        
        allMyMatches = data;
        
        // Update counts
        updateMyMatchesCounts(data);
        
        // Display matches
        displayMyMatches(data);
        
        // Load statistics
        loadMatchStatistics();
        
    } catch (error) {
        console.error('Error loading matches:', error);
        document.getElementById('my-matches-list').innerHTML = `
            <div class="text-center py-8 text-red-500">
                <i class="fas fa-exclamation-circle text-3xl mb-2"></i>
                <p>Failed to load matches. Please try again.</p>
            </div>
        `;
    }
}

function updateMyMatchesCounts(matches) {
    // Count by status
    const counts = {
        all: matches.length,
        pending: matches.filter(m => m.status === 'pending').length,
        accepted: matches.filter(m => m.status === 'accepted').length,
        completed: matches.filter(m => m.status === 'completed').length,
        rejected: matches.filter(m => m.status === 'rejected').length
    };
    
    // Update count badges
    Object.keys(counts).forEach(status => {
        const countEl = document.getElementById(`my-matches-count-${status}`);
        if (countEl) countEl.textContent = counts[status];
    });
}

function displayMyMatches(matches) {
    const container = document.getElementById('my-matches-list');
    
    if (!matches || matches.length === 0) {
        container.innerHTML = `
            <div class="text-center py-12">
                <i class="fas fa-inbox text-6xl text-gray-300 mb-4"></i>
                <h3 class="text-xl font-semibold text-gray-700 mb-2">No Matches Found</h3>
                <p class="text-gray-500">
                    ${currentMyMatchesFilter === 'all' 
                        ? 'You haven\'t initiated any matches yet.' 
                        : `No ${currentMyMatchesFilter} matches.`}
                </p>
            </div>
        `;
        return;
    }
    
    const html = matches.map(match => `
        <div class="border rounded-lg p-5 hover:shadow-md transition bg-white">
            <div class="flex items-start justify-between mb-4">
                <div class="flex-1">
                    <div class="flex items-center gap-2 mb-2">
                        <span class="px-3 py-1 rounded-full text-xs font-bold ${getStatusColor(match.status)}">
                            <i class="${getStatusIcon(match.status)} mr-1"></i>
                            ${match.status_label}
                        </span>
                        <span class="text-xs text-gray-500">
                            <i class="fas fa-clock mr-1"></i>
                            Initiated: ${match.initiated_at}
                        </span>
                        ${match.responded_at ? `
                            <span class="text-xs text-gray-500">
                                <i class="fas fa-reply mr-1"></i>
                                Responded: ${match.responded_at}
                            </span>
                        ` : ''}
                    </div>
                    
                    <h3 class="text-lg font-bold text-gray-900 mb-2">
                        Match #${match.id}
                        ${match.match_score ? `
                            <span class="text-sm font-normal text-indigo-600">
                                (Score: ${match.match_score}%)
                            </span>
                        ` : ''}
                    </h3>
                </div>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mb-4">
                <!-- Requesting Side -->
                <div class="bg-blue-50 border border-blue-200 rounded-lg p-4">
                    <div class="flex items-center gap-2 mb-2">
                        <i class="fas fa-exclamation-circle text-blue-600"></i>
                        <h4 class="font-semibold text-blue-900">Requesting Barangay</h4>
                    </div>
                    <p class="text-sm font-bold text-gray-900">${match.requesting_barangay.name}</p>
                    <p class="text-sm text-gray-700 mt-2">
                        <span class="font-semibold">Needs:</span> ${match.resource_need.category}
                    </p>
                    <p class="text-sm text-gray-700">
                        <span class="font-semibold">Quantity:</span> ${match.resource_need.quantity}
                    </p>
                    <p class="text-sm">
                        <span class="font-semibold">Urgency:</span>
                        <span class="px-2 py-1 rounded-full text-xs font-bold ${getUrgencyColor(match.resource_need.urgency)}">
                            ${match.resource_need.urgency.toUpperCase()}
                        </span>
                    </p>
                </div>

                <!-- Donating Side -->
                <div class="bg-green-50 border border-green-200 rounded-lg p-4">
                    <div class="flex items-center gap-2 mb-2">
                        <i class="fas fa-hands-helping text-green-600"></i>
                        <h4 class="font-semibold text-green-900">Donating Barangay</h4>
                    </div>
                    <p class="text-sm font-bold text-gray-900">${match.donating_barangay.name}</p>
                    <p class="text-sm text-gray-700 mt-2">
                        <span class="font-semibold">Items:</span> ${match.physical_donation.items}
                    </p>
                    <p class="text-sm text-gray-700">
                        <span class="font-semibold">Available:</span> ${match.physical_donation.quantity}
                    </p>
                    ${match.can_fully_fulfill ? 
                        '<span class="inline-flex items-center gap-1 px-2 py-1 bg-green-100 text-green-700 text-xs font-bold rounded-full mt-1"><i class="fas fa-check"></i> Can Fulfill</span>' :
                        '<span class="inline-flex items-center gap-1 px-2 py-1 bg-yellow-100 text-yellow-700 text-xs font-bold rounded-full mt-1"><i class="fas fa-exclamation-triangle"></i> Partial</span>'
                    }
                </div>
            </div>

            ${match.response_message ? `
                <div class="bg-gray-50 border border-gray-200 rounded-lg p-3 mb-3">
                    <p class="text-xs text-gray-600 mb-1">
                        <i class="fas fa-comment mr-1"></i>
                        Response Message:
                    </p>
                    <p class="text-sm text-gray-800">"${match.response_message}"</p>
                </div>
            ` : ''}

            <div class="flex gap-2 justify-end">
                ${match.status === 'pending' ? `
                    <button onclick="cancelMatch(${match.id})" 
                            class="px-4 py-2 bg-red-100 text-red-700 rounded-lg hover:bg-red-200 transition text-sm font-semibold">
                        <i class="fas fa-times mr-1"></i> Cancel Request
                    </button>
                ` : ''}
                
                ${match.has_conversation ? `
                    <button onclick="viewConversation(${match.id})" 
                            class="px-4 py-2 bg-indigo-600 text-white rounded-lg hover:bg-indigo-700 transition text-sm font-semibold">
                        <i class="fas fa-comments mr-1"></i> View Conversation
                    </button>
                ` : ''}
            </div>
        </div>
    `).join('');
    
    container.innerHTML = html;
}

async function loadMatchStatistics() {
    try {
        const stats = await fetchAPI('/api/ldrrmo/matches/statistics');
        
        document.getElementById('stats-total-matches').textContent = stats.total_matches || 0;
        document.getElementById('stats-pending-matches').textContent = stats.pending_matches || 0;
        document.getElementById('stats-accepted-matches').textContent = stats.accepted_matches || 0;
        document.getElementById('stats-success-rate').textContent = (stats.success_rate || 0) + '%';
        
    } catch (error) {
        console.error('Error loading statistics:', error);
    }
}

function filterMyMatches(status) {
    currentMyMatchesFilter = status;
    
    // Update active button
    document.querySelectorAll('[id^="my-matches-filter-"]').forEach(btn => {
        btn.classList.remove('bg-indigo-600', 'text-white');
        btn.classList.add('bg-gray-200', 'text-gray-700');
    });
    document.getElementById(`my-matches-filter-${status}`).classList.remove('bg-gray-200', 'text-gray-700');
    document.getElementById(`my-matches-filter-${status}`).classList.add('bg-indigo-600', 'text-white');
    
    // Reload matches
    loadMyMatches();
}

async function cancelMatch(matchId) {
    if (!confirm('Are you sure you want to cancel this match request?')) return;
    
    try {
        const response = await fetchAPI(`/api/ldrrmo/matches/${matchId}/cancel`, {
            method: 'POST'
        });
        
        if (response.success) {
            alert('✅ Match request cancelled successfully');
            loadMyMatches();
        } else {
            alert('❌ Error: ' + response.message);
        }
    } catch (error) {
        console.error('Error cancelling match:', error);
        alert('Failed to cancel match. Please try again.');
    }
}

function viewConversation(matchId) {
    alert('🔜 Conversation feature coming next!\n\nYou will be able to view the conversation between both barangays here.');
    // This will be implemented in the next part
}

// Helper functions
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

function getUrgencyColor(urgency) {
    const colors = {
        'critical': 'bg-red-100 text-red-700',
        'high': 'bg-orange-100 text-orange-700',
        'medium': 'bg-yellow-100 text-yellow-700',
        'low': 'bg-green-100 text-green-700'
    };
    return colors[urgency] || 'bg-gray-100 text-gray-700';
}

// Load matches when tab is shown
document.addEventListener('DOMContentLoaded', function() {
    // Add event listener to tab button if it exists
    const myMatchesTab = document.querySelector('[data-tab="my-matches"]');
    if (myMatchesTab) {
        myMatchesTab.addEventListener('click', function() {
            loadMyMatches();
        });
    }
});

async function contactBarangay(needId, donationId, barangayId, barangayName, matchScore, canFullyFulfill) {
    // Show confirmation modal
    const confirmed = confirm(
        `🤝 Initiate Match Request\n\n` +
        `You are about to connect:\n` +
        `• Requesting Barangay: (with this need)\n` +
        `• Donating Barangay: ${barangayName}\n\n` +
        `Match Score: ${matchScore}%\n` +
        `Can Fully Fulfill: ${canFullyFulfill ? 'Yes ✅' : 'Partial ⚠️'}\n\n` +
        `Both barangays will be notified. Continue?`
    );
    
    if (!confirmed) return;

    try {
        // Get the need details to extract quantity
        const needData = currentResourceNeeds.find(n => n.id === needId);
        
        const response = await fetchAPI('/api/ldrrmo/matches/initiate', {
            method: 'POST',
            body: JSON.stringify({
                resource_need_id: needId,
                physical_donation_id: donationId,
                match_score: matchScore,
                quantity_requested: needData?.quantity || '',
                can_fully_fulfill: canFullyFulfill
            })
        });

        if (response.success) {
            alert(
                `✅ Match Request Sent!\n\n` +
                `Match ID: ${response.data.match_id}\n` +
                `Status: ${response.data.status}\n\n` +
                `Both barangays have been notified:\n` +
                `• ${response.data.requesting_barangay} (FYI)\n` +
                `• ${response.data.donating_barangay} (Action Required)\n\n` +
                `You can track this match in the "My Matches" tab.`
            );
            
            // Close the modal and refresh
            closeMatchModal();
            
            // Refresh the resource needs list
            loadResourceNeeds();
            
            // TODO: Update notification bell count
            // loadNotifications();
        } else {
            alert('❌ Error: ' + response.message);
        }
    } catch (error) {
        console.error('Error initiating match:', error);
        alert('Failed to initiate match request. Please try again.');
    }
}
        document.addEventListener('click', function(e) {
            const modal = document.getElementById('suggestedMatchesModal');
            if (e.target === modal) {
                closeMatchModal();
            }
        });

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
        // INITIALIZATION
        // ============================================

        document.addEventListener('DOMContentLoaded', function() {
            console.log('LDRRMO Dashboard initializing...');

            loadOverview();

            if (initMap()) {
                loadMapData();
                loadedTabs.map = true;
            }

            // Auto-refresh overview statistics every 30 seconds
            setInterval(loadOverview, 30 * 1000);

            // Auto-refresh map data every 30 seconds
            setInterval(() => {
                if (loadedTabs.map && cityMap) {
                    loadMapData();
                }
            }, 30 * 1000);

            // Auto-refresh resource needs if tab is active
            setInterval(() => {
                if (loadedTabs.resources) {
                    loadResourceNeeds();
                }
            }, 30 * 1000);

            console.log('LDRRMO Dashboard initialized successfully with real-time polling');
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

        let currentNotificationFilter = 'all';
let allNotifications = [];
let notificationDropdownOpen = false;

// Initialize notifications on page load
document.addEventListener('DOMContentLoaded', function() {
    loadNotifications();
    
    // Poll for new notifications every 30 seconds
    setInterval(loadNotifications, 30000);
    
    // Close dropdown when clicking outside
    document.addEventListener('click', function(event) {
        const dropdown = document.getElementById('notifications-dropdown');
        const bell = document.getElementById('notification-bell');
        
        if (notificationDropdownOpen && 
            !dropdown.contains(event.target) && 
            !bell.contains(event.target)) {
            closeNotifications();
        }
    });
});

async function loadNotifications() {
    try {
        const data = await fetchAPI('/api/notifications');
        allNotifications = data;
        
        // Update unread count
        updateUnreadCount();
        
        // If dropdown is open, display notifications
        if (notificationDropdownOpen) {
            displayNotifications();
        }
    } catch (error) {
        console.error('Error loading notifications:', error);
    }
}

async function updateUnreadCount() {
    try {
        const data = await fetchAPI('/api/notifications/unread-count');
        const count = data.count || 0;
        
        const badge = document.getElementById('notification-badge');
        if (count > 0) {
            badge.classList.remove('hidden');
            badge.textContent = count > 99 ? '99+' : count;
        } else {
            badge.classList.add('hidden');
        }
        
        // Update count text
        document.getElementById('notification-count').textContent = 
            count === 0 ? 'No unread' : `${count} unread`;
            
    } catch (error) {
        console.error('Error updating unread count:', error);
    }
}

function toggleNotifications() {
    const dropdown = document.getElementById('notifications-dropdown');
    
    if (notificationDropdownOpen) {
        closeNotifications();
    } else {
        dropdown.classList.remove('hidden');
        notificationDropdownOpen = true;
        displayNotifications();
    }
}

function closeNotifications() {
    document.getElementById('notifications-dropdown').classList.add('hidden');
    notificationDropdownOpen = false;
}

function displayNotifications() {
    const container = document.getElementById('notifications-list');
    
    // Filter notifications
    let filtered = allNotifications;
    if (currentNotificationFilter !== 'all') {
        filtered = allNotifications.filter(n => n.type === currentNotificationFilter);
    }
    
    if (!filtered || filtered.length === 0) {
        container.innerHTML = `
            <div class="text-center py-8 text-gray-400">
                <i class="fas fa-bell-slash text-3xl mb-2"></i>
                <p class="text-sm">No notifications</p>
            </div>
        `;
        return;
    }
    
    const html = filtered.map(notif => `
        <div onclick="handleNotificationClick(${notif.id}, '${notif.action_url || ''}')" 
             class="px-4 py-3 border-b hover:bg-gray-50 cursor-pointer transition ${notif.is_read ? 'opacity-60' : 'bg-blue-50'}">
            
            <div class="flex items-start gap-3">
                <!-- Icon -->
                <div class="flex-shrink-0 mt-1">
                    <div class="w-10 h-10 rounded-full flex items-center justify-center ${getNotificationIconBg(notif.type)}">
                        <i class="${getNotificationIcon(notif.type)} ${getNotificationIconColor(notif.type)}"></i>
                    </div>
                </div>
                
                <!-- Content -->
                <div class="flex-1 min-w-0">
                    <div class="flex items-start justify-between gap-2 mb-1">
                        <h4 class="font-semibold text-sm text-gray-900 ${!notif.is_read ? 'font-bold' : ''}">
                            ${notif.title}
                        </h4>
                        ${!notif.is_read ? '<span class="w-2 h-2 bg-blue-600 rounded-full"></span>' : ''}
                    </div>
                    
                    <p class="text-xs text-gray-600 mb-2">${notif.message}</p>
                    
                    <div class="flex items-center justify-between">
                        <span class="text-xs text-gray-400">
                            <i class="fas fa-clock mr-1"></i>
                            ${notif.created_at}
                        </span>
                        
                        ${notif.action_url ? `
                            <span class="text-xs text-indigo-600 font-semibold">
                                Click to view <i class="fas fa-arrow-right ml-1"></i>
                            </span>
                        ` : ''}
                    </div>
                </div>
            </div>
        </div>
    `).join('');
    
    container.innerHTML = html;
}

async function handleNotificationClick(notificationId, actionUrl) {
    try {
        // Mark as read
        await fetchAPI(`/api/notifications/${notificationId}/read`, {
            method: 'POST'
        });
        
        // Update counts
        await loadNotifications();
        
        // Navigate if there's an action URL
        if (actionUrl) {
            closeNotifications();
            
            // Parse action (e.g., "view-match-123" or "view-conversation-5")
            if (actionUrl.includes('view-match-')) {
                const matchId = actionUrl.replace('view-match-', '');
                // Go to My Matches tab and highlight this match
                showTab('my-matches');
                // TODO: Scroll to and highlight the match
            } else if (actionUrl.includes('view-conversation-')) {
                const matchId = actionUrl.replace('view-conversation-', '');
                viewConversation(matchId);
            }
        }
        
    } catch (error) {
        console.error('Error handling notification click:', error);
    }
}

async function markAllAsRead() {
    try {
        await fetchAPI('/api/notifications/mark-all-read', {
            method: 'POST'
        });
        
        await loadNotifications();
        displayNotifications();
        
    } catch (error) {
        console.error('Error marking all as read:', error);
    }
}

function filterNotifications(type) {
    currentNotificationFilter = type;
    
    // Update active button
    document.querySelectorAll('[id^="notif-filter-"]').forEach(btn => {
        btn.classList.remove('border-indigo-600', 'text-indigo-600');
        btn.classList.add('border-transparent', 'text-gray-600');
    });
    
    const activeBtn = document.getElementById(`notif-filter-${type}`);
    activeBtn.classList.remove('border-transparent', 'text-gray-600');
    activeBtn.classList.add('border-indigo-600', 'text-indigo-600');
    
    displayNotifications();
}

function viewAllNotifications() {
    closeNotifications();
    // TODO: Navigate to a full notifications page if you create one
    alert('📋 Full notifications page coming soon!\n\nFor now, all notifications are shown in the dropdown.');
}

// Helper functions
function getNotificationIcon(type) {
    const icons = {
        'match_request': 'fas fa-handshake',
        'match_accepted': 'fas fa-check-circle',
        'match_rejected': 'fas fa-times-circle',
        'match_completed': 'fas fa-flag-checkered',
        'message': 'fas fa-comment',
        'system': 'fas fa-info-circle'
    };
    return icons[type] || 'fas fa-bell';
}

function getNotificationIconBg(type) {
    const colors = {
        'match_request': 'bg-blue-100',
        'match_accepted': 'bg-green-100',
        'match_rejected': 'bg-red-100',
        'match_completed': 'bg-purple-100',
        'message': 'bg-yellow-100',
        'system': 'bg-gray-100'
    };
    return colors[type] || 'bg-gray-100';
}

function getNotificationIconColor(type) {
    const colors = {
        'match_request': 'text-blue-600',
        'match_accepted': 'text-green-600',
        'match_rejected': 'text-red-600',
        'match_completed': 'text-purple-600',
        'message': 'text-yellow-600',
        'system': 'text-gray-600'
    };
    return colors[type] || 'text-gray-600';
}

console.log('✅ Notification system loaded');
    </script>
</body>
</html><?php /**PATH C:\Users\Judd\BayanihanCebuBackEnd\resources\views/UserDashboards/citydashboard.blade.php ENDPATH**/ ?>
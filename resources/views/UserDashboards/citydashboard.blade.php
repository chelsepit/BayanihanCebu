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
                <p class="font-medium">{{ session('user_name') }}</p>
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
        <div class="grid grid-cols-1 md:grid-cols-4 gap-6 mb-6">
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
        </div>

        <!-- Tabs Navigation -->
        <div class="bg-white rounded-t-xl shadow-sm border-b">
            <div class="flex gap-2 px-6">
                <button onclick="switchTab('map')" class="tab-btn active px-6 py-4 font-medium text-gray-700 transition">
                    <i class="fas fa-map mr-2"></i> Map View
                </button>
                <button onclick="switchTab('analytics')" class="tab-btn px-6 py-4 font-medium text-gray-700 transition">
                    <i class="fas fa-chart-bar mr-2"></i> Analytics
                </button>
                <button onclick="switchTab('fundraisers')" class="tab-btn px-6 py-4 font-medium text-gray-700 transition">
                    <i class="fas fa-donate mr-2"></i> Fundraisers
                </button>
                <button onclick="switchTab('barangays')" class="tab-btn px-6 py-4 font-medium text-gray-700 transition">
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
                <!-- Donations by Barangay Chart -->
                <div class="bg-white border rounded-lg p-6">
                    <h3 class="text-lg font-semibold mb-4">Donations by Barangay</h3>
                    <canvas id="donationsChart"></canvas>
                </div>

                <!-- Disaster Status Distribution -->
                <div class="bg-white border rounded-lg p-6">
                    <h3 class="text-lg font-semibold mb-4">Disaster Status Distribution</h3>
                    <canvas id="statusChart"></canvas>
                </div>

                <!-- Affected Families -->
                <div class="bg-white border rounded-lg p-6 lg:col-span-2">
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
                            <th class="px-4 py-3 text-left text-sm font-semibold text-gray-700">Donations Received</th>
                            <th class="px-4 py-3 text-left text-sm font-semibold text-gray-700">Urgent Needs</th>
                            <th class="px-4 py-3 text-left text-sm font-semibold text-gray-700">Actions</th>
                        </tr>
                    </thead>
                    <tbody id="barangaysTableBody">
                        <tr>
                            <td colspan="6" class="px-4 py-8 text-center text-gray-500">
                                <i class="fas fa-spinner fa-spin text-3xl mb-2"></i>
                                <p>Loading barangays...</p>
                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>

    </div>

    <!-- Leaflet JS -->
    <script src="https://cdnjs.cloudflare.com/ajax/libs/leaflet/1.9.4/leaflet.min.js"></script>

    <script>
        const csrfToken = document.querySelector('meta[name="csrf-token"]').content;
        let cityMap, donationsChart, statusChart, familiesChart;

        // Tab switching
        function switchTab(tabName) {
            document.querySelectorAll('.tab-content').forEach(tab => tab.classList.remove('active'));
            document.querySelectorAll('.tab-btn').forEach(btn => btn.classList.remove('active'));
            
            document.getElementById(tabName + '-tab').classList.add('active');
            event.target.classList.add('active');
            
            // Load data for specific tabs
            if (tabName === 'map') loadMapData();
            if (tabName === 'analytics') loadAnalytics();
            if (tabName === 'fundraisers') loadFundraisers();
            if (tabName === 'barangays') loadBarangaysComparison();
        }

        // Load overview stats
        async function loadOverview() {
            try {
                const response = await fetch('/api/ldrrmo/overview');
                const stats = await response.json();
                
                document.getElementById('totalDonations').textContent = 
                    '₱' + stats.total_donations.toLocaleString();
                document.getElementById('affectedFamilies').textContent = 
                    stats.total_affected_families.toLocaleString();
                document.getElementById('affectedBarangays').textContent = 
                    stats.affected_barangays + ' Barangays';
                document.getElementById('activeFundraisers').textContent = 
                    stats.active_fundraisers;
                document.getElementById('criticalBarangays').textContent = 
                    stats.critical_barangays;
            } catch (error) {
                console.error('Error loading overview:', error);
            }
        }

        // Initialize map
        function initMap() {
            cityMap = L.map('cityMap').setView([10.3157, 123.8854], 12);
            L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
                attribution: '© OpenStreetMap'
            }).addTo(cityMap);
        }

        // Load map data
        async function loadMapData() {
            if (!cityMap) initMap();
            
            try {
                const response = await fetch('/api/ldrrmo/barangays-map');
                const barangays = await response.json();
                
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
                    
                    marker.bindPopup(`
                        <div style="min-width: 200px;">
                            <strong style="font-size: 16px;">${barangay.name}</strong><br>
                            <span style="color: ${color}; font-weight: 600;">${barangay.status.toUpperCase()}</span><br>
                            <strong>Affected:</strong> ${barangay.affected_families} families<br>
                            ${barangay.urgent_needs.length > 0 ? 
                                '<strong>Needs:</strong> ' + barangay.urgent_needs.join(', ') : ''}
                        </div>
                    `);
                });
            } catch (error) {
                console.error('Error loading map data:', error);
            }
        }

        // Load analytics
        async function loadAnalytics() {
            try {
                const response = await fetch('/api/ldrrmo/analytics');
                const data = await response.json();
                
                // Donations Chart
                if (donationsChart) donationsChart.destroy();
                const donationsCtx = document.getElementById('donationsChart');
                if (donationsCtx) {
                    donationsChart = new Chart(donationsCtx, {
                        type: 'bar',
                        data: {
                            labels: data.donations_by_barangay.map(b => b.name),
                            datasets: [{
                                label: 'Donations (₱)',
                                data: data.donations_by_barangay.map(b => b.total_donations),
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
                if (statusChart) statusChart.destroy();
                const statusCtx = document.getElementById('statusChart');
                if (statusCtx) {
                    statusChart = new Chart(statusCtx, {
                        type: 'pie',
                        data: {
                            labels: Object.keys(data.disaster_status_distribution).map(s => s.toUpperCase()),
                            datasets: [{
                                data: Object.values(data.disaster_status_distribution),
                                backgroundColor: ['#10B981', '#EAB308', '#F97316', '#EF4444']
                            }]
                        },
                        options: { responsive: true, maintainAspectRatio: true }
                    });
                }
                
                // Families Chart
                if (familiesChart) familiesChart.destroy();
                const familiesCtx = document.getElementById('familiesChart');
                if (familiesCtx) {
                    familiesChart = new Chart(familiesCtx, {
                        type: 'bar',
                        data: {
                            labels: data.affected_families_by_barangay.map(b => b.name),
                            datasets: [{
                                label: 'Affected Families',
                                data: data.affected_families_by_barangay.map(b => b.affected_families),
                                backgroundColor: '#EF4444'
                            }]
                        },
                        options: { 
                            responsive: true,
                            scales: {
                                y: { beginAtZero: true }
                            }
                        }
                    });
                }
            } catch (error) {
                console.error('Error loading analytics:', error);
            }
        }

        // Load fundraisers
        async function loadFundraisers() {
            try {
                const response = await fetch('/api/ldrrmo/fundraisers');
                const fundraisers = await response.json();
                
                const container = document.getElementById('fundraisersList');
                if (fundraisers.length === 0) {
                    container.innerHTML = '<div class="col-span-3 text-center py-8 text-gray-500">No active fundraisers</div>';
                    return;
                }
                
                container.innerHTML = fundraisers.map(f => `
                    <div class="border rounded-lg p-6 hover:shadow-md transition">
                        <div class="flex justify-between items-start mb-4">
                            <h3 class="text-lg font-semibold">${f.title}</h3>
                            <span class="px-2 py-1 bg-red-100 text-red-700 text-xs rounded">${f.severity}</span>
                        </div>
                        <p class="text-sm text-gray-600 mb-2"><i class="fas fa-map-marker-alt mr-1"></i>${f.barangay}</p>
                        <p class="text-sm text-gray-500 mb-4">${f.description}</p>
                        
                        <div class="mb-4">
                            <div class="flex justify-between text-sm mb-1">
                                <span class="font-semibold">₱${f.raised.toLocaleString()}</span>
                                <span class="text-gray-600">₱${f.goal.toLocaleString()}</span>
                            </div>
                            <div class="w-full bg-gray-200 rounded-full h-2">
                                <div class="bg-green-600 h-2 rounded-full" style="width: ${f.progress}%"></div>
                            </div>
                            <p class="text-xs text-gray-600 mt-1">${f.progress}% funded</p>
                        </div>
                        
                        <div class="flex justify-between text-sm text-gray-600 mb-3">
                            <span><i class="fas fa-users"></i> ${f.donors_count} donors</span>
                            <span><i class="fas fa-calendar"></i> ${f.days_active} days</span>
                        </div>
                        
                        ${f.urgent_needs.length > 0 ? `
                            <div class="border-t pt-3">
                                <p class="text-xs text-gray-600 mb-2">Urgent Needs:</p>
                                <div class="flex flex-wrap gap-1">
                                    ${f.urgent_needs.map(need => `
                                        <span class="px-2 py-1 bg-blue-100 text-blue-700 text-xs rounded">
                                            ${need.type}
                                        </span>
                                    `).join('')}
                                </div>
                            </div>
                        ` : ''}
                    </div>
                `).join('');
            } catch (error) {
                console.error('Error loading fundraisers:', error);
            }
        }

        // Load barangays comparison
        async function loadBarangaysComparison() {
            try {
                const response = await fetch('/api/ldrrmo/barangays-comparison');
                const barangays = await response.json();
                
                const tbody = document.getElementById('barangaysTableBody');
                tbody.innerHTML = barangays.map(b => `
                    <tr class="border-b hover:bg-gray-50">
                        <td class="px-4 py-3 font-medium">${b.name}</td>
                        <td class="px-4 py-3">
                            <span class="px-2 py-1 text-xs rounded ${getStatusBadge(b.status)}">
                                ${b.status.toUpperCase()}
                            </span>
                        </td>
                        <td class="px-4 py-3">${b.affected_families}</td>
                        <td class="px-4 py-3">₱${b.donations_received.toLocaleString()}</td>
                        <td class="px-4 py-3">
                            ${b.urgent_needs.map(need => 
                                `<span class="px-2 py-1 bg-blue-100 text-blue-700 text-xs rounded mr-1">${need}</span>`
                            ).join('')}
                            ${b.urgent_needs.length === 0 ? '<span class="text-gray-400 text-sm">None</span>' : ''}
                        </td>
                        <td class="px-4 py-3">
                            <button onclick="viewBarangay('${b.barangay_id}')" class="text-blue-600 hover:underline text-sm">
                                <i class="fas fa-eye mr-1"></i> View
                            </button>
                        </td>
                    </tr>
                `).join('');
            } catch (error) {
                console.error('Error loading barangays:', error);
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

        function viewBarangay(id) {
            // Navigate to detailed view
            window.location.href = `/api/ldrrmo/barangays/${id}`;
        }

        // Initialize on page load
        document.addEventListener('DOMContentLoaded', function() {
            loadOverview();
            initMap();
            loadMapData();
        });
    </script>
</body>
</html>
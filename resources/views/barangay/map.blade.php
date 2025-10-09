<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Barangay Map - DonorTrack</title>
    
    <!-- Leaflet CSS -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/leaflet/1.9.4/leaflet.min.css" />
    
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, Oxygen, Ubuntu, Cantarell, sans-serif;
            background: #f3f4f6;
        }

        .map-container {
            display: grid;
            grid-template-columns: 1fr 350px;
            gap: 20px;
            padding: 20px;
            height: calc(100vh - 80px);
        }

        .map-wrapper {
            background: white;
            border-radius: 12px;
            box-shadow: 0 1px 3px rgba(0,0,0,0.1);
            overflow: hidden;
            position: relative;
        }

        #map {
            width: 100%;
            height: 100%;
            min-height: 600px;
        }

        .map-legend {
            position: absolute;
            bottom: 30px;
            right: 10px;
            background: white;
            padding: 15px;
            border-radius: 8px;
            box-shadow: 0 2px 8px rgba(0,0,0,0.15);
            z-index: 1000;
        }

        .legend-title {
            font-weight: 600;
            margin-bottom: 10px;
            font-size: 14px;
            color: #1f2937;
        }

        .legend-item {
            display: flex;
            align-items: center;
            gap: 8px;
            margin-bottom: 8px;
            font-size: 13px;
            color: #4b5563;
        }

        .legend-item:last-child {
            margin-bottom: 0;
        }

        .marker-dot {
            width: 16px;
            height: 16px;
            border-radius: 50%;
            border: 2px solid white;
            box-shadow: 0 1px 3px rgba(0,0,0,0.3);
        }

        .marker-dot.active { background: #10b981; }
        .marker-dot.pending { background: #f59e0b; }
        .marker-dot.completed { background: #6b7280; }
        .marker-dot.none { background: #d1d5db; }

        .sidebar {
            display: flex;
            flex-direction: column;
            gap: 20px;
            overflow-y: auto;
        }

        .sidebar-card {
            background: white;
            border-radius: 12px;
            padding: 20px;
            box-shadow: 0 1px 3px rgba(0,0,0,0.1);
        }

        .sidebar-title {
            font-size: 18px;
            font-weight: 600;
            color: #1f2937;
            margin-bottom: 15px;
            display: flex;
            align-items: center;
            gap: 8px;
        }

        .activity-list {
            list-style: none;
        }

        .activity-item {
            padding: 12px;
            border-bottom: 1px solid #f3f4f6;
            display: flex;
            justify-content: space-between;
            align-items: center;
            transition: background 0.2s;
        }

        .activity-item:last-child {
            border-bottom: none;
        }

        .activity-item:hover {
            background: #f9fafb;
        }

        .badge {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            min-width: 32px;
            height: 24px;
            padding: 0 8px;
            border-radius: 12px;
            font-size: 12px;
            font-weight: 600;
        }

        .badge-blue {
            background: #dbeafe;
            color: #1e40af;
        }

        .badge-green {
            background: #d1fae5;
            color: #065f46;
        }

        .stats-grid {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 15px;
        }

        .stat-box {
            text-align: center;
            padding: 15px;
            background: #f9fafb;
            border-radius: 8px;
        }

        .stat-number {
            font-size: 24px;
            font-weight: 700;
            color: #1f2937;
            margin-bottom: 4px;
        }

        .stat-label {
            font-size: 12px;
            color: #6b7280;
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }

        /* Custom Leaflet Popup Styles */
        .leaflet-popup-content-wrapper {
            border-radius: 8px;
            padding: 0;
        }

        .leaflet-popup-content {
            margin: 0;
            min-width: 250px;
        }

        .popup-header {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            padding: 15px;
            border-radius: 8px 8px 0 0;
        }

        .popup-title {
            font-size: 16px;
            font-weight: 600;
            margin-bottom: 4px;
        }

        .popup-subtitle {
            font-size: 12px;
            opacity: 0.9;
        }

        .popup-body {
            padding: 15px;
        }

        .popup-stat {
            display: flex;
            justify-content: space-between;
            padding: 8px 0;
            border-bottom: 1px solid #f3f4f6;
        }

        .popup-stat:last-child {
            border-bottom: none;
        }

        .popup-label {
            color: #6b7280;
            font-size: 13px;
        }

        .popup-value {
            color: #1f2937;
            font-weight: 600;
            font-size: 13px;
        }

        .status-badge {
            display: inline-block;
            padding: 4px 10px;
            border-radius: 12px;
            font-size: 11px;
            font-weight: 600;
            text-transform: uppercase;
        }

        .status-active {
            background: #d1fae5;
            color: #065f46;
        }

        .status-pending {
            background: #fed7aa;
            color: #92400e;
        }

        .status-completed {
            background: #e5e7eb;
            color: #374151;
        }

        .popup-button {
            display: block;
            width: 100%;
            padding: 10px;
            margin-top: 10px;
            background: #667eea;
            color: white;
            text-align: center;
            border-radius: 6px;
            text-decoration: none;
            font-size: 13px;
            font-weight: 500;
            transition: background 0.2s;
        }

        .popup-button:hover {
            background: #5568d3;
        }

        @media (max-width: 1024px) {
            .map-container {
                grid-template-columns: 1fr;
            }
        }
    </style>
</head>
<body>
    <div class="map-container">
        <div class="map-wrapper">
            <div id="map"></div>
            <div class="map-legend">
                <div class="legend-title">Status Legend</div>
                <div class="legend-item">
                    <div class="marker-dot active"></div>
                    <span>Active Distribution</span>
                </div>
                <div class="legend-item">
                    <div class="marker-dot pending"></div>
                    <span>Pending Source</span>
                </div>
                <div class="legend-item">
                    <div class="marker-dot completed"></div>
                    <span>Completed</span>
                </div>
                <div class="legend-item">
                    <div class="marker-dot none"></div>
                    <span>No Donations</span>
                </div>
            </div>
        </div>

        <div class="sidebar">
            <div class="sidebar-card">
                <div class="sidebar-title">
                    <span>📊</span> Recent Activity
                </div>
                <ul class="activity-list" id="recentActivity">
                    <li style="text-align: center; color: #9ca3af; padding: 20px;">
                        Loading...
                    </li>
                </ul>
            </div>

            <div class="sidebar-card">
                <div class="sidebar-title">
                    <span>📈</span> Impact Overview
                </div>
                <div class="stats-grid" id="impactStats">
                    <div class="stat-box">
                        <div class="stat-number" id="totalDonations">-</div>
                        <div class="stat-label">Donations</div>
                    </div>
                    <div class="stat-box">
                        <div class="stat-number" id="totalBarangays">-</div>
                        <div class="stat-label">Barangays</div>
                    </div>
                    <div class="stat-box">
                        <div class="stat-number" id="totalAmount">-</div>
                        <div class="stat-label">Total Amount</div>
                    </div>
                    <div class="stat-box">
                        <div class="stat-number" id="familiesServed">-</div>
                        <div class="stat-label">Families</div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Leaflet JS -->
    <script src="https://cdnjs.cloudflare.com/ajax/libs/leaflet/1.9.4/leaflet.min.js"></script>
    
    <script>
        // Initialize map centered on Cebu City
        const map = L.map('map').setView([10.3157, 123.8854], 12);

        // Add OpenStreetMap tile layer
        L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
            attribution: '© <a href="https://www.openstreetmap.org/copyright">OpenStreetMap</a> contributors',
            maxZoom: 19,
        }).addTo(map);

        // Custom marker icon function
        function createCustomIcon(status) {
            const colors = {
                active: '#10b981',
                pending: '#f59e0b',
                completed: '#6b7280',
                no_donations: '#d1d5db'
            };

            return L.divIcon({
                className: 'custom-marker-icon',
                html: `<div style="
                    background-color: ${colors[status]};
                    width: 30px;
                    height: 30px;
                    border-radius: 50% 50% 50% 0;
                    transform: rotate(-45deg);
                    border: 3px solid white;
                    box-shadow: 0 3px 10px rgba(0,0,0,0.4);
                "></div>`,
                iconSize: [30, 30],
                iconAnchor: [15, 30],
                popupAnchor: [0, -30]
            });
        }

        // Format currency
        function formatCurrency(amount) {
            return '₱' + parseFloat(amount).toLocaleString('en-PH', {
                minimumFractionDigits: 0,
                maximumFractionDigits: 0
            });
        }

        // Fetch and display map data
        fetch('/api/barangay-map-data')
            .then(response => response.json())
            .then(data => {
                // Add markers for each barangay
                data.barangays.forEach(barangay => {
                    const marker = L.marker([barangay.lat, barangay.lng], {
                        icon: createCustomIcon(barangay.status)
                    }).addTo(map);

                    // Create popup content
                    const popupContent = `
                        <div class="popup-header">
                            <div class="popup-title">${barangay.name}</div>
                            <div class="popup-subtitle">${barangay.city}</div>
                        </div>
                        <div class="popup-body">
                            <div class="popup-stat">
                                <span class="popup-label">Status:</span>
                                <span class="status-badge status-${barangay.status}">
                                    ${barangay.status.replace('_', ' ')}
                                </span>
                            </div>
                            <div class="popup-stat">
                                <span class="popup-label">Donations:</span>
                                <span class="popup-value">${barangay.donations}</span>
                            </div>
                            <div class="popup-stat">
                                <span class="popup-label">Total Amount:</span>
                                <span class="popup-value">${formatCurrency(barangay.total_amount)}</span>
                            </div>
                            <a href="/barangay/${barangay.barangay_id}" class="popup-button">
                                View Details →
                            </a>
                        </div>
                    `;

                    marker.bindPopup(popupContent, {
                        maxWidth: 300,
                        className: 'custom-popup'
                    });
                });

                // Update recent activity
                const activityList = document.getElementById('recentActivity');
                activityList.innerHTML = data.recent_activity.map(activity => `
                    <li class="activity-item">
                        <span>${activity.name}</span>
                        <span class="badge badge-blue">${activity.donation_count}</span>
                    </li>
                `).join('');

                // Update statistics
                document.getElementById('totalDonations').textContent = data.stats.total_donations;
                document.getElementById('totalBarangays').textContent = data.stats.total_barangays;
                document.getElementById('totalAmount').textContent = formatCurrency(data.stats.total_amount);
                document.getElementById('familiesServed').textContent = data.stats.families_served || '0';
            })
            .catch(error => {
                console.error('Error loading map data:', error);
                document.getElementById('recentActivity').innerHTML = 
                    '<li style="text-align: center; color: #ef4444; padding: 20px;">Error loading data</li>';
            });
    </script>
</body>
</html>
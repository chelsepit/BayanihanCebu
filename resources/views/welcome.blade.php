<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>DonorTrack - Transparent Donation Tracking for Cebu</title>
    
    <!-- Leaflet CSS -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/leaflet/1.9.4/leaflet.min.css" />
    
    @vite(['resources/css/app.css', 'resources/js/app.js'])

    <style>
        /* Map specific styles */
        .barangay-section {
            padding: 60px 0;
            background: #f9fafb;
        }

        .section-title {
            font-size: 36px;
            font-weight: 700;
            text-align: center;
            margin-bottom: 12px;
            color: #1f2937;
        }

        .section-subtitle {
            text-align: center;
            color: #6b7280;
            font-size: 18px;
            margin-bottom: 40px;
        }

        .map-container {
            display: grid;
            grid-template-columns: 1fr 350px;
            gap: 20px;
            max-width: 1400px;
            margin: 0 auto;
        }

        .map-wrapper {
            background: white;
            border-radius: 12px;
            box-shadow: 0 1px 3px rgba(0,0,0,0.1);
            overflow: hidden;
            position: relative;
            height: 600px;
        }

        #barangayMap {
            width: 100%;
            height: 100%;
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

        .marker {
            width: 16px;
            height: 16px;
            border-radius: 50%;
            border: 2px solid white;
            box-shadow: 0 1px 3px rgba(0,0,0,0.3);
            display: inline-block;
        }

        .marker-green { background: #10b981; }
        .marker-orange { background: #f59e0b; }
        .marker-gray { background: #6b7280; }
        .marker-light { background: #d1d5db; }

        .map-sidebar {
            display: flex;
            flex-direction: column;
            gap: 20px;
        }

        .sidebar-section {
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
            padding: 0;
            margin: 0;
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

        .impact-stats {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 15px;
        }

        .impact-item {
            text-align: center;
            padding: 15px;
            background: #f9fafb;
            border-radius: 8px;
        }

        .impact-number {
            font-size: 24px;
            font-weight: 700;
            color: #1f2937;
            margin-bottom: 4px;
        }

        .impact-label {
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

        .status-no_donations {
            background: #f3f4f6;
            color: #6b7280;
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

            .map-wrapper {
                height: 500px;
            }
        }
    </style>
</head>
<body>
    {{-- Navigation --}}
    <nav class="navbar">
        <div class="container">
            <div class="nav-brand">
                <img src="{{ asset('images/logo.png') }}" alt="DonorTrack" class="logo">
                <span class="brand-name">DonorTrack</span>
            </div>
            <ul class="nav-menu" id="navMenu">
                <li><a href="#home">Home</a></li>
                <li><a href="#map">Barangay Map</a></li>
                <li><a href="{{ route('login') }}" class="btn-login">Login</a></li>
            </ul>
            <button class="hamburger" id="hamburger">
                <span></span>
                <span></span>
                <span></span>
            </button>
        </div>
    </nav>

    {{-- Hero Section --}}
    <section class="hero" id="home">
        <div class="hero-overlay"></div>
        <div class="container">
            <div class="hero-content">
                <h1 class="hero-title">Barangays helping barangays.</h1>
                <p class="hero-subtitle">Connecting communities through transparent aid, shared resources, and trust.</p>
                <div class="hero-buttons">
                    <a href="{{ route('donation.track') }}" class="btn btn-primary">
                        <span class="icon">🔍</span> Track Your Donation
                    </a>
                    <a href="#map" class="btn btn-secondary">
                        <span class="icon">🗺️</span> View Barangay Map
                    </a>
                </div>
            </div>

            {{-- Statistics Cards --}}
            <div class="stats-grid">
                <div class="stat-card">
                    <div class="stat-number" id="heroTotalDonations">-</div>
                    <div class="stat-label">Donations Tracked</div>
                </div>
                <div class="stat-card">
                    <div class="stat-number" id="heroTotalBarangays">-</div>
                    <div class="stat-label">Barangays Served</div>
                </div>
                <div class="stat-card">
                    <div class="stat-number" id="heroTotalAmount">-</div>
                    <div class="stat-label">Total Impact</div>
                </div>
            </div>
        </div>
    </section>

    {{-- Barangay Map Section --}}
    <section class="barangay-section" id="map">
        <div class="container">
            <h2 class="section-title">Cebu City Barangays</h2>
            <p class="section-subtitle">Click on any barangay to view detailed donation tracking and distribution information</p>

            <div class="map-container">
                <div class="map-wrapper">
                    <div id="barangayMap"></div>
                    <div class="map-legend">
                        <div class="legend-title">Status Legend</div>
                        <div class="legend-item">
                            <span class="marker marker-green"></span> Active Distribution
                        </div>
                        <div class="legend-item">
                            <span class="marker marker-orange"></span> Pending Source
                        </div>
                        <div class="legend-item">
                            <span class="marker marker-gray"></span> Completed
                        </div>
                        <div class="legend-item">
                            <span class="marker marker-light"></span> No Donations
                        </div>
                    </div>
                </div>

                {{-- Sidebar with Recent Activity --}}
                <div class="map-sidebar">
                    <div class="sidebar-section">
                        <h3 class="sidebar-title">
                            <span class="icon">📊</span> Recent Activity
                        </h3>
                        <ul class="activity-list" id="recentActivity">
                            <li style="text-align: center; color: #9ca3af; padding: 20px;">
                                Loading...
                            </li>
                        </ul>
                    </div>

                    <div class="sidebar-section">
                        <h3 class="sidebar-title">
                            <span class="icon">📈</span> Impact Overview
                        </h3>
                        <div class="impact-stats">
                            <div class="impact-item">
                                <div class="impact-number" id="familiesServed">-</div>
                                <div class="impact-label">Families Served</div>
                            </div>
                            <div class="impact-item">
                                <div class="impact-number" id="totalDonations">-</div>
                                <div class="impact-label">Total Donations</div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

<!-- Live Disaster Map Section -->
<div class="container mx-auto px-4 py-8 max-w-7xl">
    
    <!-- Header Section -->
    <div class="text-center mb-8">
        <h2 class="text-3xl font-bold text-gray-800 mb-2">Live Disaster Map of Cebu</h2>
        <p class="text-gray-600">Real-time status of barangays across Cebu City</p>
        
        <!-- Legend -->
        <div class="flex justify-center items-center gap-4 mt-4 flex-wrap">
            <div class="flex items-center gap-2">
                <span class="w-3 h-3 rounded-full bg-green-500"></span>
                <span class="text-sm text-gray-700">Safe</span>
            </div>
            <div class="flex items-center gap-2">
                <span class="w-3 h-3 rounded-full bg-yellow-500"></span>
                <span class="text-sm text-gray-700">Warning</span>
            </div>
            <div class="flex items-center gap-2">
                <span class="w-3 h-3 rounded-full bg-orange-500"></span>
                <span class="text-sm text-gray-700">Critical</span>
            </div>
            <div class="flex items-center gap-2">
                <span class="w-3 h-3 rounded-full bg-red-500"></span>
                <span class="text-sm text-gray-700">Emergency</span>
            </div>
        </div>
    </div>

    <!-- Success/Error Messages -->
    @if(session('success'))
        <div class="mb-6 bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded relative" role="alert">
            <span class="block sm:inline">{{ session('success') }}</span>
        </div>
    @endif

    @if(session('error'))
        <div class="mb-6 bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded relative" role="alert">
            <span class="block sm:inline">{{ session('error') }}</span>
        </div>
    @endif

    <!-- Disaster Cards Grid -->
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6 mb-12">
        
        @foreach($barangays as $barangay)
            @php
                $disaster = $barangay->currentDisaster;
                $statusColors = [
                    'safe' => 'green',
                    'warning' => 'yellow',
                    'critical' => 'orange',
                    'emergency' => 'red',
                ];
                $color = $statusColors[$barangay->status] ?? 'gray';
            @endphp

            <div class="bg-white rounded-lg shadow-md border border-gray-200 overflow-hidden hover:shadow-lg transition-shadow">
                <div class="p-5">
                    <div class="flex items-center justify-between mb-4">
                        <h3 class="text-lg font-semibold text-gray-800 flex items-center gap-2">
                            <svg class="w-5 h-5 text-gray-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"/>
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"/>
                            </svg>
                            {{ $barangay->name }}
                        </h3>
                        <span class="px-3 py-1 bg-{{ $color }}-100 text-{{ $color }}-800 text-xs font-semibold rounded-full">
                            {{ ucfirst($barangay->status) }}
                        </span>
                    </div>

                    @if($disaster)
                        <!-- Disaster Information -->
                        <div class="space-y-2 mb-4">
                            <div class="flex justify-between text-sm">
                                <span class="text-gray-600">Affected Families:</span>
                                <span class="font-semibold text-gray-800">{{ number_format($disaster->affected_families) }}</span>
                            </div>
                            <div class="flex justify-between text-sm">
                                <span class="text-gray-600">Donations Received:</span>
                                <span class="font-semibold text-green-600">₱{{ number_format($disaster->total_donations, 2) }}</span>
                            </div>
                        </div>

                        @if($disaster->urgentNeeds->count() > 0)
                            <div class="mb-4">
                                <p class="text-xs text-gray-500 mb-2">Urgent Needs:</p>
                                <div class="flex gap-2 flex-wrap">
                                    @foreach($disaster->urgentNeeds as $need)
                                        <span class="px-2 py-1 bg-gray-100 text-gray-700 text-xs rounded">
                                            {{ ucfirst($need->type) }}
                                        </span>
                                    @endforeach
                                </div>
                            </div>
                        @endif

                        <a href="{{ route('disaster.donate', $disaster->id) }}" class="block w-full bg-red-600 hover:bg-red-700 text-white font-semibold py-2 px-4 rounded transition-colors text-center">
                            Donate to {{ $barangay->name }}
                        </a>
                    @else
                        <!-- Safe Status -->
                        <p class="text-gray-600 text-sm mb-4">All clear - no active disasters</p>
                    @endif
                </div>
            </div>
        @endforeach

    </div>

    <!-- Track Your Donation Section -->
    <div class="bg-gradient-to-r from-blue-50 to-indigo-50 rounded-lg shadow-md p-8 mb-12">
        <div class="text-center max-w-2xl mx-auto">
            <h3 class="text-2xl font-bold text-gray-800 mb-2">Track Your Donation</h3>
            <p class="text-gray-600 mb-6">Enter your tracking code to see the status and distribution details of your donation</p>
            
            <form action="{{ route('donation.track') }}" method="POST" class="flex gap-3 max-w-md mx-auto">
                @csrf
                <input 
                    type="text" 
                    name="tracking_code"
                    placeholder="Enter Tracking Code (e.g., 1015-1430-48231)" 
                    class="flex-1 px-4 py-3 rounded-lg border border-gray-300 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent"
                    required
                >
                <button type="submit" class="bg-blue-600 hover:bg-blue-700 text-white font-semibold px-6 py-3 rounded-lg transition-colors whitespace-nowrap">
                    Track
                </button>
            </form>
        </div>
    </div>

    <!-- Why Trust BayanihanCebu Section -->
    <div class="text-center mb-8">
        <h3 class="text-2xl font-bold text-gray-800 mb-8">Why Trust BayanihanCebu?</h3>
        
        <div class="grid grid-cols-1 md:grid-cols-3 gap-8">
            
            <!-- Blockchain Verified -->
            <div class="bg-white rounded-lg shadow-md p-8 border border-gray-200 hover:shadow-lg transition-shadow">
                <div class="flex justify-center mb-4">
                    <div class="w-16 h-16 bg-green-100 rounded-full flex items-center justify-center">
                        <svg class="w-8 h-8 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"/>
                        </svg>
                    </div>
                </div>
                <h4 class="text-xl font-semibold text-gray-800 mb-3">Blockchain Verified</h4>
                <p class="text-gray-600">Every transaction is recorded on the Lisk Blockchain for complete transparency</p>
            </div>

            <!-- Direct to Barangays -->
            <div class="bg-white rounded-lg shadow-md p-8 border border-gray-200 hover:shadow-lg transition-shadow">
                <div class="flex justify-center mb-4">
                    <div class="w-16 h-16 bg-blue-100 rounded-full flex items-center justify-center">
                        <svg class="w-8 h-8 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"/>
                        </svg>
                    </div>
                </div>
                <h4 class="text-xl font-semibold text-gray-800 mb-3">Direct to Barangays</h4>
                <p class="text-gray-600">Donations go directly to affected communities, managed by local BDRMC officers</p>
            </div>

            <!-- Real-Time Tracking -->
            <div class="bg-white rounded-lg shadow-md p-8 border border-gray-200 hover:shadow-lg transition-shadow">
                <div class="flex justify-center mb-4">
                    <div class="w-16 h-16 bg-purple-100 rounded-full flex items-center justify-center">
                        <svg class="w-8 h-8 text-purple-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 7h8m0 0v8m0-8l-8 8-4-4-6 6"/>
                        </svg>
                    </div>
                </div>
                <h4 class="text-xl font-semibold text-gray-800 mb-3">Real-Time Tracking</h4>
                <p class="text-gray-600">See exactly how your donation is being used with live updates and receipts</p>
            </div>

        </div>
    </div>

</div>

<style>
/* Optional: Add smooth hover animations */
.hover\:shadow-lg {
    transition: box-shadow 0.3s ease-in-out;
}

/* Optional: Add pulse animation for emergency cards */
@keyframes pulse-emergency {
    0%, 100% {
        opacity: 1;
    }
    50% {
        opacity: 0.8;
    }
}

/* You can add this class to emergency status badges if you want them to pulse */
.pulse-emergency {
    animation: pulse-emergency 2s cubic-bezier(0.4, 0, 0.6, 1) infinite;
}
</style>



    {{-- Footer --}}
    <footer class="footer">
        <div class="container">
            <div class="footer-grid">
                <div class="footer-col">
                    <h4>About</h4>
                    <p>DonorTrack is a blockchain-based transparent donation platform, ensuring accountability and impact.</p>
                </div>
                <div class="footer-col">
                    <h4>Quick Links</h4>
                    <ul>
                       <li><a href="#map">Barangay Map</a></li>
                        <li><a href="{{ route('donation.track') }}">Track Donation</a></li>
                        <li><a href="{{ route('fundraisers') }}">Fundraisers</a></li>
                    </ul>
                </div>
                <div class="footer-col">
                    <h4>Resources</h4>
                    <ul>
                        <li><a href="#">Help Center</a></li>
                        <li><a href="#">Privacy Policy</a></li>
                        <li><a href="#">Terms of Service</a></li>
                    </ul>
                </div>
                <div class="footer-col">
                    <h4>Connect</h4>
                    <p>Email: info@donortrack.ph</p>
                    <p>Phone: +63 32 123 4567</p>
                </div>
            </div>
            <div class="footer-bottom">
                <p>&copy; 2025 DonorTrack. All rights reserved.</p>
            </div>
        </div>
    </footer>

    <!-- Leaflet JS -->
    <script src="https://cdnjs.cloudflare.com/ajax/libs/leaflet/1.9.4/leaflet.min.js"></script>
    
    <script>
        // Initialize map centered on Cebu City
        const map = L.map('barangayMap').setView([10.3157, 123.8854], 12);

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

        // Format large numbers
        function formatNumber(num) {
            if (num >= 1000000) {
                return '₱' + (num / 1000000).toFixed(1) + 'M';
            } else if (num >= 1000) {
                return '₱' + (num / 1000).toFixed(1) + 'K';
            }
            return formatCurrency(num);
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
                if (data.recent_activity.length > 0) {
                    activityList.innerHTML = data.recent_activity.map(activity => `
                        <li class="activity-item">
                            <span>${activity.name}</span>
                            <span class="badge badge-blue">${activity.donation_count}</span>
                        </li>
                    `).join('');
                } else {
                    activityList.innerHTML = '<li style="text-align: center; color: #9ca3af; padding: 20px;">No recent activity</li>';
                }

                // Update statistics in sidebar
                document.getElementById('totalDonations').textContent = data.stats.total_donations;
                document.getElementById('familiesServed').textContent = data.stats.families_served || '0';

                // Update hero statistics
                document.getElementById('heroTotalDonations').textContent = data.stats.total_donations.toLocaleString();
                document.getElementById('heroTotalBarangays').textContent = data.stats.total_barangays;
                document.getElementById('heroTotalAmount').textContent = formatNumber(data.stats.total_amount);
            })
            .catch(error => {
                console.error('Error loading map data:', error);
                document.getElementById('recentActivity').innerHTML = 
                    '<li style="text-align: center; color: #ef4444; padding: 20px;">Error loading data</li>';
            });

        // Smooth scroll for anchor links
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
    </script>

    <script src="{{ asset('js/app.js') }}"></script>
</body>
</html>

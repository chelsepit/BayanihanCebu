<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>BayanihanCebu - Transparent Disaster Relief for Cebu</title>
    
    <!-- Leaflet CSS -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/leaflet/1.9.4/leaflet.min.css" />
    
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <script src="{{ asset('js/simple-realtime.js') }}"></script>
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, 'Helvetica Neue', Arial, sans-serif;
            line-height: 1.6;
            color: #1f2937;
        }

        .container {
            max-width: 1200px;
            margin: 0 auto;
            padding: 0 20px;
        }

        /* Hero Section */
        .hero-section {
            background: linear-gradient(135deg, #1e40af 0%, #3b82f6 100%);
            color: white;
            padding: 60px 20px 80px;
            position: relative;
        }

        .hero-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 60px;
            max-width: 1200px;
            margin-left: auto;
            margin-right: auto;
        }

        .hero-logo {
            display: flex;
            align-items: center;
            gap: 12px;
        }

        .logo-icon {
            width: 40px;
            height: 40px;
            background: rgba(255, 255, 255, 0.2);
            border-radius: 8px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 24px;
        }

        .logo-text h1 {
            font-size: 24px;
            font-weight: 700;
            margin: 0;
        }

        .logo-text p {
            font-size: 13px;
            opacity: 0.9;
            margin: 0;
        }

        .sign-in-btn {
            background: white;
            color: #1e40af;
            padding: 10px 24px;
            border-radius: 8px;
            text-decoration: none;
            font-weight: 600;
            font-size: 14px;
            transition: all 0.3s;
        }

        .sign-in-btn:hover {
            background: #f0f9ff;
            transform: translateY(-2px);
        }

        .hero-content {
            max-width: 1200px;
            margin: 0 auto;
        }

        .hero-main {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 40px;
            align-items: center;
            margin-bottom: 60px;
        }

        .hero-text h2 {
            font-size: 42px;
            font-weight: 700;
            line-height: 1.2;
            margin-bottom: 20px;
        }

        .hero-text p {
            font-size: 18px;
            opacity: 0.95;
            margin-bottom: 30px;
        }

        .hero-buttons {
            display: flex;
            gap: 15px;
        }

        .btn {
            padding: 14px 28px;
            border-radius: 8px;
            text-decoration: none;
            font-weight: 600;
            font-size: 15px;
            display: inline-flex;
            align-items: center;
            gap: 8px;
            transition: all 0.3s;
        }

        .btn-donate {
            background: #ef4444;
            color: white;
        }

        .btn-donate:hover {
            background: #dc2626;
            transform: translateY(-2px);
        }

        .btn-track {
            background: rgba(255, 255, 255, 0.2);
            color: white;
            border: 2px solid white;
        }

        .btn-track:hover {
            background: white;
            color: #1e40af;
        }

        .hero-image {
            position: relative;
        }

        .hero-image img {
            width: 100%;
            border-radius: 12px;
            box-shadow: 0 20px 40px rgba(0, 0, 0, 0.2);
        }

        .blockchain-badge {
            position: absolute;
            top: 20px;
            right: 20px;
            background: #10b981;
            color: white;
            padding: 8px 16px;
            border-radius: 20px;
            font-size: 13px;
            font-weight: 600;
            display: flex;
            align-items: center;
            gap: 6px;
        }

        /* Stats Grid */
        .stats-grid {
            display: grid;
            grid-template-columns: repeat(4, 1fr);
            gap: 30px;
            max-width: 1200px;
            margin: 0 auto;
        }

        .stat-card {
            background: rgba(255, 255, 255, 0.15);
            backdrop-filter: blur(10px);
            padding: 30px 20px;
            border-radius: 12px;
            text-align: center;
            border: 1px solid rgba(255, 255, 255, 0.2);
        }

        .stat-number {
            font-size: 36px;
            font-weight: 700;
            margin-bottom: 8px;
        }

        .stat-label {
            font-size: 14px;
            opacity: 0.9;
        }

        .verified-badge {
            display: inline-flex;
            align-items: center;
            gap: 4px;
            background: #10b981;
            color: white;
            padding: 4px 10px;
            border-radius: 12px;
            font-size: 11px;
            font-weight: 600;
            margin-top: 5px;
        }

        /* Map Section */
        .map-section {
              padding: 80px 20px;
                background: #f9fafb;
                position: relative; 
}

        .section-header {
            text-align: center;
            margin-bottom: 30px;
        }

        .section-header h2 {
            font-size: 36px;
            font-weight: 700;
            color: #1f2937;
            margin-bottom: 10px;
            text-align: center
        }

        .section-header p {
            font-size: 18px;
            color: #6b7280;
            text-align: center
        }

        .status-legend {
            display: flex;
            justify-content: center;
            gap: 25px;
            margin-bottom: 40px;
        }

        .legend-item {
            display: flex;
            align-items: center;
            gap: 8px;
            font-size: 14px;
            color: #4b5563;
        }

        .legend-dot {
            width: 14px;
            height: 14px;
            border-radius: 50%;
            border: 2px solid white;
            box-shadow: 0 1px 3px rgba(0,0,0,0.2);
        }

        .legend-dot.safe { background: #10b981; }
        .legend-dot.warning { background: #f59e0b; }
        .legend-dot.critical { background: #f97316; }
        .legend-dot.emergency { background: #ef4444; }

        /* Map Container with Summary Panel - Optimized layout */
        .map-container {
            max-width: 1400px;
            margin: 30px auto 60px;
            display: grid;
            grid-template-columns: 1fr 360px;
            gap: 24px;
            height: 830px;
            align-items: start;
            position: relative;  
            z-index: 1;
        }

        .map-wrapper {
        background: white;
        border-radius: 12px;
        box-shadow: 0 4px 20px rgba(0,0,0,0.1);
        overflow: hidden;
        position: relative;
        height: 100%;
        z-index: 1;
        }

        #barangayMap {
            width: 100%;
            height: 100%;
        }

        /* Map Legend */
        .map-legend-overlay {
            position: absolute;
            bottom: 20px;
            left: 20px;
            background: white;
            padding: 15px 20px;
            border-radius: 10px;
            box-shadow: 0 4px 12px rgba(0,0,0,0.15);
            z-index: 1000;
        }

        .legend-title {
            font-weight: 700;
            margin-bottom: 12px;
            font-size: 14px;
            color: #1f2937;
        }

        .legend-items {
            display: flex;
            gap: 15px;
        }

        /* Summary Panel - No scrolling, fit content properly */
        .summary-panel {
               display: flex;
                flex-direction: column;
                gap: 20px;
                overflow: visible;
                height: 100%;
                position: relative;
                z-index: 2;
        }

        .summary-card {
            background: white;
            border-radius: 12px;
            padding: 20px;
            box-shadow: 0 1px 3px rgba(0,0,0,0.1);
        }

        .card-title {
            font-size: 18px;
            font-weight: 700;
            color: #1f2937;
            margin-bottom: 15px;
            display: flex;
            align-items: center;
            gap: 8px;
        }

        .card-icon {
            width: 32px;
            height: 32px;
            background: linear-gradient(135deg, #3b82f6 0%, #2563eb 100%);
            border-radius: 8px;
            display: flex;
            align-items: center;
            justify-content: center;
            color: white;
            font-size: 16px;
        }

        /* Stats Grid in Summary */
        .stats-grid-summary {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 12px;
        }

        .stat-box {
            text-align: center;
            padding: 15px 10px;
            background: #f9fafb;
            border-radius: 8px;
            border: 1px solid #e5e7eb;
        }

        .stat-box .stat-number {
            font-size: 24px;
            font-weight: 700;
            color: #1f2937;
            margin-bottom: 4px;
        }

        .stat-box .stat-label {
            font-size: 11px;
            color: #6b7280;
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }

        /* Barangay List in Summary - Adjusted height for better fit */
        .barangay-list {
            display: flex;
            flex-direction: column;
            gap: 10px;
            max-height: 280px;
            overflow-y: auto;
        }

        /* Scrollbar styling for barangay list */
        .barangay-list::-webkit-scrollbar {
            width: 6px;
        }

        .barangay-list::-webkit-scrollbar-track {
            background: #f1f1f1;
            border-radius: 10px;
        }

        .barangay-list::-webkit-scrollbar-thumb {
            background: #cbd5e1;
            border-radius: 10px;
        }

        .barangay-list::-webkit-scrollbar-thumb:hover {
            background: #94a3b8;
        }

        .barangay-item {
            padding: 12px;
            border: 1px solid #e5e7eb;
            border-radius: 8px;
            display: flex;
            justify-content: space-between;
            align-items: center;
            transition: all 0.3s;
            cursor: pointer;
        }

        .barangay-item:hover {
            border-color: #3b82f6;
            box-shadow: 0 2px 8px rgba(59, 130, 246, 0.1);
            transform: translateX(2px);
        }

        .barangay-info-summary {
            flex: 1;
        }

        .barangay-name-summary {
            font-weight: 600;
            color: #1f2937;
            margin-bottom: 4px;
            font-size: 14px;
        }

        .barangay-meta {
            font-size: 12px;
            color: #6b7280;
        }

        /* Quick Actions */
        .quick-actions {
            display: flex;
            flex-direction: column;
            gap: 10px;
        }

        .action-btn {
            width: 100%;
            padding: 12px;
            border-radius: 8px;
            font-weight: 600;
            font-size: 14px;
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 8px;
            transition: all 0.3s;
            border: none;
            cursor: pointer;
            text-decoration: none;
        }

        .action-btn-primary {
            background: #ef4444;
            color: white;
        }

        .action-btn-primary:hover {
            background: #dc2626;
            transform: translateY(-2px);
            box-shadow: 0 4px 12px rgba(239, 68, 68, 0.3);
        }

        .action-btn-secondary {
            background: #3b82f6;
            color: white;
        }

        .action-btn-secondary:hover {
            background: #2563eb;
            transform: translateY(-2px);
            box-shadow: 0 4px 12px rgba(59, 130, 246, 0.3);
        }

        /* Barangay Cards Grid - BELOW THE MAP */
        .barangay-cards {
            display: grid;
            grid-template-columns: repeat(3, 1fr);
            gap: 20px;
            max-width: 1200px;
            margin: 80px auto 0px;
            padding: 0 20px;
            clear: both;
            position: relative;
            z-index: 0;
        }

        .barangay-card {
            background: white;
            border-radius: 12px;
            padding: 20px;
            box-shadow: 0 1px 3px rgba(0,0,0,0.1);
            transition: all 0.3s;
            position: relative; 
            z-index: 3; 
        }

        .barangay-card:hover {
            box-shadow: 0 4px 12px rgba(0,0,0,0.15);
            transform: translateY(-2px);
        }

        .barangay-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 15px;
        }

        .barangay-name {
            font-size: 18px;
            font-weight: 600;
            color: #1f2937;
        }

        .status-badge {
            padding: 4px 12px;
            border-radius: 12px;
            font-size: 11px;
            font-weight: 600;
            text-transform: uppercase;
        }

        .status-badge.safe {
            background: #d1fae5;
            color: #065f46;
        }

        .status-badge.warning {
            background: #fef3c7;
            color: #92400e;
        }

        .status-badge.critical {
            background: #fed7aa;
            color: #9a3412;
        }

        .status-badge.emergency {
            background: #fee2e2;
            color: #991b1b;
        }

        .barangay-info {
            color: #6b7280;
            font-size: 14px;
            margin-bottom: 15px;
        }

        .barangay-stats {
            display: flex;
            justify-content: space-between;
            padding: 12px 0;
            border-top: 1px solid #f3f4f6;
            border-bottom: 1px solid #f3f4f6;
            margin-bottom: 12px;
        }

        .stat-item {
            text-align: center;
        }

        .stat-item-label {
            font-size: 11px;
            color: #9ca3af;
            text-transform: uppercase;
            margin-bottom: 4px;
        }

        .stat-item-value {
            font-size: 16px;
            font-weight: 700;
            color: #1f2937;
        }

        .urgent-needs {
            margin-bottom: 15px;
        }

        .urgent-needs-label {
            font-size: 12px;
            color: #6b7280;
            margin-bottom: 6px;
        }

        .needs-tags {
            display: flex;
            gap: 6px;
            flex-wrap: wrap;
        }

        .need-tag {
            background: #fef3c7;
            color: #92400e;
            padding: 4px 10px;
            border-radius: 12px;
            font-size: 12px;
            font-weight: 600;
        }

        .donate-btn {
            width: 100%;
            background: #ef4444;
            color: white;
            padding: 12px;
            border-radius: 8px;
            border: none;
            font-weight: 600;
            font-size: 14px;
            cursor: pointer;
            transition: all 0.3s;
        }

        .donate-btn:hover {
            background: #dc2626;
            transform: translateY(-2px);
        }

        /* Track Donation Section */
        .track-section {
            padding: 80px 20px;
            background: white;
        }

        .track-container {
            max-width: 700px;
            margin: 0 auto;
            text-align: center;
        }

        .track-container h2 {
            font-size: 32px;
            font-weight: 700;
            color: #1f2937;
            margin-bottom: 12px;
        }

        .track-container p {
            font-size: 16px;
            color: #6b7280;
            margin-bottom: 30px;
        }

        .track-form {
            display: flex;
            gap: 12px;
            max-width: 600px;
            margin: 0 auto;
        }

        .track-input {
            flex: 1;
            padding: 14px 20px;
            border: 2px solid #e5e7eb;
            border-radius: 8px;
            font-size: 15px;
            transition: all 0.3s;
        }

        .track-input:focus {
            outline: none;
            border-color: #3b82f6;
        }

        .track-btn {
            background: #3b82f6;
            color: white;
            padding: 14px 32px;
            border-radius: 8px;
            border: none;
            font-weight: 600;
            font-size: 15px;
            cursor: pointer;
            transition: all 0.3s;
            display: flex;
            align-items: center;
            gap: 8px;
        }

        .track-btn:hover {
            background: #2563eb;
        }

        /* Trust Section */
        .trust-section {
            padding: 80px 20px;
            background: #f9fafb;
        }

        .trust-container {
            max-width: 1200px;
            margin: 0 auto;
        }

        .trust-header {
            text-align: center;
            margin-bottom: 60px;
        }

        .trust-header h2 {
            font-size: 36px;
            font-weight: 700;
            color: #1f2937;
        }

        .trust-grid {
            display: grid;
            grid-template-columns: repeat(3, 1fr);
            gap: 30px;
        }

        .trust-card {
            background: white;
            padding: 40px 30px;
            border-radius: 12px;
            text-align: center;
            box-shadow: 0 1px 3px rgba(0,0,0,0.1);
            transition: all 0.3s;
        }

        .trust-card:hover {
            box-shadow: 0 4px 12px rgba(0,0,0,0.15);
            transform: translateY(-4px);
        }

        .trust-icon {
            width: 64px;
            height: 64px;
            background: linear-gradient(135deg, #10b981 0%, #059669 100%);
            border-radius: 16px;
            display: flex;
            align-items: center;
            justify-content: center;
            margin: 0 auto 20px;
            font-size: 32px;
        }

        .trust-icon.icon-users {
            background: linear-gradient(135deg, #3b82f6 0%, #2563eb 100%);
        }

        .trust-icon.icon-chart {
            background: linear-gradient(135deg, #f59e0b 0%, #d97706 100%);
        }

        .trust-card h3 {
            font-size: 22px;
            font-weight: 700;
            color: #1f2937;
            margin-bottom: 12px;
        }

        .trust-card p {
            font-size: 15px;
            color: #6b7280;
            line-height: 1.6;
        }

        /* Responsive */
        @media (max-width: 968px) {
            .hero-main {
                grid-template-columns: 1fr;
            }

            .stats-grid {
                grid-template-columns: repeat(2, 1fr);
            }

            .barangay-cards {
                  grid-template-columns: repeat(2, 1fr);
                   padding: 0 40px;  
            }

            .trust-grid {
                grid-template-columns: 1fr;
            }

            .hero-text h2 {
                font-size: 32px;
            }

            .map-container {
                height: 500px;
                 margin-bottom: 40px; 
            }
        }

        @media (max-width: 640px) {
            .hero-header {
                flex-direction: column;
                gap: 20px;
            }

            .stats-grid {
                grid-template-columns: 1fr;
               
            }

            .barangay-cards {
                grid-template-columns: 1fr;
                 padding: 200px 20px;
            }

            .track-form {
                flex-direction: column;
            }

            .hero-buttons {
                flex-direction: column;
            }

            .map-container {
              height: 400px;
        grid-template-columns: 1fr; 
        margin-bottom: 30px;
            }
            .summary-panel {
        height: auto; 
    } 
        } {
            .header-content {
                flex-direction: column;
                gap: 15px;
            }

            .stats-grid {
                grid-template-columns: 1fr;
            }

            .legend-items {
                flex-direction: column;
                gap: 8px;
            }
        }

        /* Leaflet Popup Customization */
        .leaflet-popup-content-wrapper {
            padding: 0;
            overflow: hidden;
        }

        .leaflet-popup-content {
            margin: 0;
            max-height: 500px;
            overflow-y: auto;
        }

        /* Custom scrollbar for popup */
        .leaflet-popup-content::-webkit-scrollbar {
            width: 6px;
        }

        .leaflet-popup-content::-webkit-scrollbar-track {
            background: #f1f1f1;
            border-radius: 10px;
        }

        .leaflet-popup-content::-webkit-scrollbar-thumb {
            background: #888;
            border-radius: 10px;
        }

        .leaflet-popup-content::-webkit-scrollbar-thumb:hover {
            background: #555;
        }
    </style>
</head>
<body>

    {{-- Hero Section --}}
    <section class="hero-section">
        <div class="hero-header">
            <div class="hero-logo">
                <div class="logo-icon">🛡️</div>
                <div class="logo-text">
                    <h1>BayanihanCebu</h1>
                    <p>Philippines Disaster Relief</p>
                </div>
            </div>
            <a href="{{ route('login') }}" class="sign-in-btn">Sign In</a>
        </div>

        <div class="hero-content">
            <div class="hero-main">
                <div class="hero-text">
                    <h2>Transparent Disaster Relief for Cebu</h2>
                    <p>Every donation is tracked on the blockchain. Every peso reaches those in need. Join us in building a more transparent and efficient disaster relief system.</p>
                    <div class="hero-buttons">
                        <a href="#donate" class="btn btn-donate">
                            ❤️ Donate Now
                        </a>
                        <a href="#track" class="btn btn-track">
                            🔍 Track Donation
                        </a>
                    </div>
                </div>
                <div class="hero-image">
                    <img src="https://images.unsplash.com/photo-1559027615-cd4628902d4a?w=600&h=400&fit=crop" alt="Helping Hands">
                    <div class="blockchain-badge">
                        ✓ Blockchain Verified
                    </div>
                </div>
            </div>

            <div class="stats-grid">
                <div class="stat-card">
                    <div class="stat-number" id="heroTotalDonations">₱0</div>
                    <div class="stat-label">Total Donations</div>
                    <div class="verified-badge">✓ Verified</div>
                </div>
                <div class="stat-card">
                    <div class="stat-number" id="heroAffectedFamilies">0</div>
                    <div class="stat-label">Families Affected</div>
                </div>
                <div class="stat-card">
                    <div class="stat-number" id="heroVerifiedTransactions">0</div>
                    <div class="stat-label">Verified Transactions</div>
                </div>
                <div class="stat-card">
                    <div class="stat-number" id="heroActiveFundraisers">0</div>
                    <div class="stat-label">Active Fundraisers</div>
                </div>
            </div>
        </div>
    </section>

    {{-- Map Section --}}
    <section class="map-section" id="map">
        <div class="section-header">
            <h2>Live Disaster Map of Cebu</h2>
            <p>Real-time status of barangays across Cebu City</p>
        </div>

        {{-- Legend removed from top - now only showing in map overlay --}}

        {{-- Map Container with Summary Panel (Version 1 Style) --}}
        <div class="map-container">
            {{-- Interactive Map --}}
            <div class="map-wrapper">
                <div id="barangayMap"></div>
                
                {{-- Map Legend Overlay - Updated to match urgency levels --}}
                <div class="map-legend-overlay">
                    <div class="legend-title">📍 Urgency Levels</div>
                    <div class="legend-items">
                        <div class="legend-item">
                            <span class="legend-dot" style="background: #3b82f6;"></span>
                            <span>Low</span>
                        </div>
                        <div class="legend-item">
                            <span class="legend-dot" style="background: #f59e0b;"></span>
                            <span>Medium</span>
                        </div>
                        <div class="legend-item">
                            <span class="legend-dot" style="background: #f97316;"></span>
                            <span>High</span>
                        </div>
                        <div class="legend-item">
                            <span class="legend-dot" style="background: #ef4444;"></span>
                            <span>Critical</span>
                        </div>
                    </div>
                </div>
            </div>

            {{-- Summary Panel --}}
            <div class="summary-panel">
                
                {{-- City Statistics --}}
                <div class="summary-card">
                    <div class="card-title">
                        <div class="card-icon">📊</div>
                        City Overview
                    </div>
                    <div class="stats-grid-summary">
                        <div class="stat-box">
                            <div class="stat-number" id="totalDonations">₱0</div>
                            <div class="stat-label">Total Donations</div>
                        </div>
                        <div class="stat-box">
                            <div class="stat-number" id="affectedFamilies">0</div>
                            <div class="stat-label">Affected Families</div>
                        </div>
                        <div class="stat-box">
                            <div class="stat-number" id="affectedBarangays">0</div>
                            <div class="stat-label">Barangays Affected</div>
                        </div>
                        <div class="stat-box">
                            <div class="stat-number" id="totalDonors">0</div>
                            <div class="stat-label">Donors</div>
                        </div>
                    </div>
                </div>

                {{-- Affected Barangays List --}}
                <div class="summary-card">
                    <div class="card-title">
                        <div class="card-icon">⚠️</div>
                        Barangays Needing Help
                    </div>
                    <div class="barangay-list" id="barangayList">
                        <div style="text-align: center; padding: 20px; color: #6b7280;">
                            <i class="fas fa-spinner fa-spin"></i> Loading...
                        </div>
                    </div>
                </div>

                {{-- Quick Actions --}}
                <div class="summary-card">
                    <div class="card-title">
                        <div class="card-icon">⚡</div>
                        Quick Actions
                    </div>
                    <div class="quick-actions">
                        <a href="#donate" class="action-btn action-btn-primary">
                            ❤️ Make a Donation
                        </a>
                        <a href="#track" class="action-btn action-btn-secondary">
                            🔍 Track My Donation
                        </a>
                    </div>
                </div>

            </div>
        </div>

        {{-- Barangay Cards BELOW the Map --}}
        <div class="barangay-cards">
            @foreach($barangays as $barangay)
                <div class="barangay-card">
                    <div class="barangay-header">
                        <div class="barangay-name">📍 {{ $barangay->name }}</div>
                        <div class="status-badge {{ $barangay->disaster_status }}">
                            {{ ucfirst($barangay->disaster_status) }}
                        </div>
                    </div>

                    @if($barangay->disaster_status === 'safe')
                        <div class="barangay-info">All clear - no active disasters</div>
                    @else
                        {{-- Disaster Type --}}
                        @if($barangay->disaster_type)
                            <div class="barangay-info" style="margin-bottom: 12px;">
                                <strong>Type:</strong>
                                @php
                                    $disasterIcons = [
                                        'flood' => '🌊',
                                        'fire' => '🔥',
                                        'earthquake' => '🏚️',
                                        'typhoon' => '🌀',
                                        'landslide' => '⛰️',
                                        'other' => '❓'
                                    ];
                                @endphp
                                {{ $disasterIcons[$barangay->disaster_type] ?? '' }} {{ ucfirst($barangay->disaster_type) }}
                            </div>
                        @endif

                        {{-- Stats --}}
                        <div class="barangay-stats">
                            <div class="stat-item">
                                <div class="stat-item-label">Affected Families</div>
                                <div class="stat-item-value">{{ $barangay->affected_families }}</div>
                            </div>
                            <div class="stat-item">
                                <div class="stat-item-label">Donations Received</div>
                                <div class="stat-item-value">₱{{ number_format($barangay->total_raised, 0) }}</div>
                            </div>
                        </div>

                        {{-- Resource Needs Section --}}
                        @if($barangay->resourceNeeds->where('status', '!=', 'fulfilled')->count() > 0)
                            <div class="urgent-needs">
                                <div class="urgent-needs-label">Resource Needs:</div>
                                <div class="needs-tags">
                                    @foreach($barangay->resourceNeeds->where('status', '!=', 'fulfilled')->unique('category') as $need)
                                        <span class="need-tag">{{ ucfirst($need->category) }}</span>
                                    @endforeach
                                </div>
                            </div>
                        @endif

                        <button class="donate-btn" onclick="window.location.href='/donate/{{ $barangay->barangay_id }}'">
                            Donate to {{ $barangay->name }}
                        </button>
                    @endif
                </div>
            @endforeach
        </div>
    </section>

    
{{-- Track Donation Section --}}
<section class="track-section" id="track">
    <div class="track-container">
        <h2>Track Your Donation</h2>
        <p>Enter your tracking code to see blockchain verification and how your donation is being used</p>
        <form class="track-form" action="{{ route('donation.track') }}" method="POST">
            @csrf
            <input 
                type="text" 
                name="tracking_code" 
                class="track-input" 
                placeholder="Enter Tracking Code (e.g., CC002-2025-00001)"
                required
            >
            <button type="submit" class="track-btn">
                🔍 Track
            </button>
        </form>
    </div>
</section>

    {{-- Trust Section --}}
    <section class="trust-section">
        <div class="trust-container">
            <div class="trust-header">
                <h2>Why Trust BayanihanCebu?</h2>
            </div>
            <div class="trust-grid">
                <div class="trust-card">
                    <div class="trust-icon">
                        🛡️
                    </div>
                    <h3>Blockchain Verified</h3>
                    <p>Every transaction is recorded on the Lisk blockchain for complete transparency</p>
                </div>
                <div class="trust-card">
                    <div class="trust-icon icon-users">
                        👥
                    </div>
                    <h3>Direct to Barangays</h3>
                    <p>Donations go directly to affected communities, managed by local BDRRMC officers</p>
                </div>
                <div class="trust-card">
                    <div class="trust-icon icon-chart">
                        📊
                    </div>
                    <h3>Real-Time Tracking</h3>
                    <p>See exactly how your donation is being used with live updates and receipts</p>
                </div>
            </div>
        </div>
    </section>

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

        // Store markers for later reference
        const markers = {};
        let currentZoomLevel = 12;

        // Track current map zoom
        map.on('zoomend', function() {
            currentZoomLevel = map.getZoom();
            updateMarkerSizes();
        });

        // Custom marker icon function - Standardized pins by urgency only
        function createCustomIcon(status, urgencyLevel = 'low', resourceCount = 0, zoomLevel = 12) {
            // Urgency-based color coding
            const urgencyColors = {
                critical: '#ef4444',  // Red - Emergency/Critical
                high: '#f97316',      // Orange - High
                medium: '#f59e0b',    // Amber - Medium/Warning
                low: '#3b82f6'        // Blue - Low
            };

            const pinColor = urgencyColors[urgencyLevel] || urgencyColors['low'];

            // Standardized base pin sizes - ONLY based on urgency level
            const baseSizes = {
                critical: 40,  // Largest
                high: 32,      // Large
                medium: 26,    // Medium
                low: 22        // Small
            };

            const baseSize = baseSizes[urgencyLevel] || 22;

            // Zoom-responsive scaling
            let zoomMultiplier = 1.0;
            if (zoomLevel >= 18) {
                zoomMultiplier = 1.8;
            } else if (zoomLevel >= 15) {
                zoomMultiplier = 1.4;
            } else if (zoomLevel >= 12) {
                zoomMultiplier = 1.15;
            }

            const finalSize = Math.round(baseSize * zoomMultiplier);
            const iconAnchor = [finalSize / 2, finalSize];

            return L.divIcon({
                className: 'custom-marker-icon',
                html: `<div style="
                    background-color: ${pinColor};
                    width: ${finalSize}px;
                    height: ${finalSize}px;
                    border-radius: 50% 50% 50% 0;
                    transform: rotate(-45deg);
                    border: 3px solid white;
                    box-shadow: 0 4px 15px rgba(0,0,0,0.5);
                    transition: all 0.3s ease;
                    cursor: pointer;
                "></div>`,
                iconSize: [finalSize, finalSize],
                iconAnchor: iconAnchor,
                popupAnchor: [0, -iconAnchor[1]]
            });
        }

        // Update marker sizes when zoom changes
        function updateMarkerSizes() {
            Object.keys(markers).forEach(barangayId => {
                const markerData = markers[barangayId];
                if (markerData && markerData.marker) {
                    const newIcon = createCustomIcon(
                        markerData.status,
                        markerData.urgency,
                        markerData.resourceCount,
                        currentZoomLevel
                    );
                    markerData.marker.setIcon(newIcon);
                }
            });
        }

        // Format currency
        function formatCurrency(amount) {
            return '₱' + parseFloat(amount).toLocaleString('en-PH', {
                minimumFractionDigits: 0,
                maximumFractionDigits: 0
            });
        }

        // Format number
        function formatNumber(num) {
            return parseInt(num).toLocaleString('en-PH');
        }

        // Disaster type icons
        const disasterIcons = {
            'flood': '🌊',
            'fire': '🔥',
            'earthquake': '🏚️',
            'typhoon': '🌀',
            'landslide': '⛰️',
            'other': '❓'
        };

        // Get urgency badge color
        function getUrgencyColor(urgency) {
            const colors = {
                'critical': '#ef4444',
                'high': '#f97316',
                'medium': '#f59e0b',
                'low': '#3b82f6'
            };
            return colors[urgency] || colors['low'];
        }

        // Get highest urgency level from resource needs
        function getHighestUrgency(resourceNeeds) {
            if (!resourceNeeds || resourceNeeds.length === 0) return 'low';
            
            const urgencyOrder = { critical: 4, high: 3, medium: 2, low: 1 };
            let highest = 'low';
            let highestValue = 0;
            
            resourceNeeds.forEach(need => {
                const value = urgencyOrder[need.urgency] || 0;
                if (value > highestValue) {
                    highestValue = value;
                    highest = need.urgency;
                }
            });
            
            return highest;
        }

        // Load and display map data
        function loadMapData() {
            fetch('/api/barangays')
                .then(response => response.json())
                .then(data => {
                    // Clear existing markers
                    Object.values(markers).forEach(markerData => {
                        if (markerData && markerData.marker) {
                            map.removeLayer(markerData.marker);
                        }
                    });

                    // Reset markers object
                    for (let key in markers) {
                        delete markers[key];
                    }

                    let totalDonations = 0;
                    let totalFamilies = 0;
                    let affectedCount = 0;

                    // Add markers for each barangay - SKIP SAFE STATUS
                    data.forEach(barangay => {
                        // Skip safe barangays - only show barangays with active needs
                        if (barangay.status === 'safe') {
                            return;
                        }

                        // Get resource needs data
                        const resourceNeeds = barangay.resource_needs || [];
                        const resourceCount = barangay.resource_needs_count || 0;
                        const highestUrgency = barangay.highest_urgency || 'low';

                        const marker = L.marker([barangay.latitude, barangay.longitude], {
                            icon: createCustomIcon(
                                barangay.status,
                                highestUrgency,
                                resourceCount,
                                currentZoomLevel
                            )
                        }).addTo(map);

                        // Store marker reference with metadata
                        markers[barangay.id] = {
                            marker: marker,
                            status: barangay.status,
                            urgency: highestUrgency,
                            resourceCount: resourceCount
                        };

                        // Build Resource Needs HTML with enhanced details
                        let resourceNeedsHtml = '';
                        if (resourceNeeds.length > 0) {
                            resourceNeedsHtml = `
                                <div style="margin-top: 15px; padding-top: 15px; border-top: 2px solid #e5e7eb;">
                                    <div style="font-weight: 700; font-size: 15px; margin-bottom: 10px; color: #1f2937; display: flex; align-items: center; gap: 8px;">
                                        📋 Resource Needs
                                        <span style="background: #3b82f6; color: white; padding: 2px 8px; border-radius: 12px; font-size: 11px;">
                                            ${resourceCount}
                                        </span>
                                    </div>
                            `;

                            resourceNeeds.forEach(need => {
                                const urgencyColor = getUrgencyColor(need.urgency);
                                resourceNeedsHtml += `
                                    <div style="background: #f9fafb; border-left: 4px solid ${urgencyColor}; padding: 10px; margin-bottom: 8px; border-radius: 4px;">
                                        <div style="display: flex; justify-content: space-between; align-items: start; margin-bottom: 6px;">
                                            <div style="font-weight: 600; color: #1f2937; font-size: 14px;">
                                                ${need.category ? need.category.toUpperCase() : 'General'}
                                            </div>
                                            <div style="background: ${urgencyColor}; color: white; padding: 2px 8px; border-radius: 10px; font-size: 10px; font-weight: 700; text-transform: uppercase;">
                                                ${need.urgency}
                                            </div>
                                        </div>
                                        <div style="font-size: 12px; color: #6b7280; margin-bottom: 4px;">
                                            ${need.description || 'No description'}
                                        </div>
                                        <div style="font-size: 12px; color: #1f2937; margin-bottom: 4px;">
                                            <strong>Quantity:</strong> ${need.quantity}
                                        </div>
                                        <div style="display: flex; justify-content: space-between; font-size: 11px; color: #9ca3af; margin-top: 4px;">
                                            <span>Status: <span style="text-transform: uppercase; font-weight: 600;">${need.status}</span></span>
                                            ${need.created_at ? `<span>Added: ${need.created_at}</span>` : ''}
                                        </div>
                                    </div>
                                `;
                            });

                            resourceNeedsHtml += '</div>';
                        }

                        // Google Maps link
                        const googleMapsLink = `https://www.google.com/maps?q=${barangay.latitude},${barangay.longitude}`;

                        // Create enhanced popup content
                        const popupContent = `
                            <div style="padding: 15px; min-width: 320px; max-width: 400px;">
                                <div style="background: linear-gradient(135deg, #1e40af 0%, #3b82f6 100%); color: white; margin: -15px -15px 15px -15px; padding: 15px; border-radius: 8px 8px 0 0;">
                                    <h3 style="margin: 0 0 5px 0; font-size: 18px; font-weight: 700;">${barangay.name}</h3>
                                    <p style="margin: 0; font-size: 13px; opacity: 0.9;">${barangay.city || 'Cebu City'}</p>
                                </div>

                                <div style="padding: 0 5px;">
                                    <div style="padding: 10px 0; border-bottom: 1px solid #e5e7eb;">
                                        <div style="font-size: 12px; color: #6b7280; margin-bottom: 4px;">Status</div>
                                        <div style="display: inline-block; background: ${barangay.status === 'emergency' ? '#fee2e2' : barangay.status === 'critical' ? '#fed7aa' : barangay.status === 'warning' ? '#fef3c7' : '#d1fae5'}; color: ${barangay.status === 'emergency' ? '#991b1b' : barangay.status === 'critical' ? '#9a3412' : barangay.status === 'warning' ? '#92400e' : '#065f46'}; padding: 4px 12px; border-radius: 12px; font-size: 11px; font-weight: 700; text-transform: uppercase;">
                                            ${barangay.status}
                                        </div>
                                    </div>

                                    ${barangay.disaster_type ? `
                                        <div style="padding: 10px 0; border-bottom: 1px solid #e5e7eb;">
                                            <div style="font-size: 12px; color: #6b7280; margin-bottom: 4px;">Disaster Type</div>
                                            <div style="font-size: 14px; font-weight: 600; color: #1f2937;">
                                                ${disasterIcons[barangay.disaster_type] || ''} ${barangay.disaster_type.charAt(0).toUpperCase() + barangay.disaster_type.slice(1)}
                                            </div>
                                        </div>
                                    ` : ''}

                                    ${barangay.affected_families ? `
                                        <div style="padding: 10px 0; border-bottom: 1px solid #e5e7eb;">
                                            <div style="font-size: 12px; color: #6b7280; margin-bottom: 4px;">Affected Families</div>
                                            <div style="font-size: 14px; font-weight: 600; color: #1f2937;">
                                                ${formatNumber(barangay.affected_families)}
                                            </div>
                                        </div>
                                    ` : ''}

                                    ${barangay.total_raised ? `
                                        <div style="padding: 10px 0; border-bottom: 1px solid #e5e7eb;">
                                            <div style="font-size: 12px; color: #6b7280; margin-bottom: 4px;">Donations Received</div>
                                            <div style="font-size: 14px; font-weight: 600; color: #10b981;">
                                                ${formatCurrency(barangay.total_raised)}
                                            </div>
                                        </div>
                                    ` : ''}

                                    ${resourceNeedsHtml}

                                    <div style="margin-top: 15px; padding-top: 15px; border-top: 2px solid #e5e7eb;">
                                        <a href="${googleMapsLink}" target="_blank" style="display: block; text-align: center; background: #3b82f6; color: white; padding: 10px; border-radius: 8px; text-decoration: none; font-weight: 600; font-size: 13px; margin-bottom: 8px;">
                                            📍 View on Google Maps
                                        </a>
                                        ${barangay.status !== 'safe' && resourceCount > 0 ? `
                                            <button onclick="window.location.href='/donate/${barangay.id}'" style="width: 100%; background: #ef4444; color: white; padding: 10px; border: none; border-radius: 8px; font-weight: 600; font-size: 13px; cursor: pointer;">
                                                ❤️ Donate to ${barangay.name}
                                            </button>
                                        ` : ''}
                                    </div>
                                </div>
                            </div>
                        `;

                        marker.bindPopup(popupContent, {
                            maxWidth: 420,
                            maxHeight: 500,
                            className: 'custom-leaflet-popup'
                        });

                        // Click only - no hover interaction

                        // Update statistics
                        if (barangay.status !== 'safe') {
                            totalDonations += parseFloat(barangay.total_raised) || 0;
                            totalFamilies += parseInt(barangay.affected_families) || 0;
                            affectedCount++;
                        }
                    });

                    // Update summary statistics in map section
                    document.getElementById('totalDonations').textContent = formatCurrency(totalDonations);
                    document.getElementById('affectedFamilies').textContent = formatNumber(totalFamilies);
                    document.getElementById('affectedBarangays').textContent = affectedCount;

                    // Populate barangay list (only affected ones with resource needs)
                    const barangayList = document.getElementById('barangayList');
                    const affectedBarangays = data.filter(b => b.status !== 'safe');
                
                    if (affectedBarangays.length === 0) {
                        barangayList.innerHTML = `
                            <div style="text-align: center; padding: 20px; color: #10b981;">
                                <div style="font-size: 48px; margin-bottom: 10px;">✅</div>
                                <div style="font-weight: 600;">All Clear</div>
                                <div style="font-size: 13px; color: #6b7280;">No barangays currently need assistance</div>
                            </div>
                        `;
                    } else {
                        barangayList.innerHTML = affectedBarangays
                            .sort((a, b) => {
                                const urgencyOrder = { critical: 4, high: 3, medium: 2, low: 1 };
                                const statusOrder = { emergency: 0, critical: 1, warning: 2 };

                                // Sort by highest urgency first, then by status
                                const aUrgency = a.highest_urgency || 'low';
                                const bUrgency = b.highest_urgency || 'low';

                                if (urgencyOrder[aUrgency] !== urgencyOrder[bUrgency]) {
                                    return urgencyOrder[bUrgency] - urgencyOrder[aUrgency];
                                }

                                return statusOrder[a.status] - statusOrder[b.status];
                            })
                            .map(barangay => {
                                const highestUrgency = barangay.highest_urgency || 'low';
                                const urgencyColor = getUrgencyColor(highestUrgency);
                                const needsCount = barangay.resource_needs_count || 0;

                                return `
                                    <div class="barangay-item" onclick="focusBarangay('${barangay.id}')" style="position: relative; cursor: pointer;">
                                        ${needsCount > 0 ? `
                                            <div style="position: absolute; top: -5px; right: -5px; background: ${urgencyColor}; color: white; width: 24px; height: 24px; border-radius: 50%; display: flex; align-items: center; justify-content: center; font-size: 11px; font-weight: 700; box-shadow: 0 2px 4px rgba(0,0,0,0.2);">
                                                ${needsCount}
                                            </div>
                                        ` : ''}
                                        <div class="barangay-info-summary">
                                            <div class="barangay-name-summary">📍 ${barangay.name}</div>
                                            <div class="barangay-meta">
                                                ${barangay.affected_families || 0} families • ${formatCurrency(barangay.total_raised || 0)} raised
                                            </div>
                                            ${needsCount > 0 ? `
                                                <div style="font-size: 11px; color: ${urgencyColor}; font-weight: 600; margin-top: 4px;">
                                                    ${needsCount} resource need${needsCount > 1 ? 's' : ''} • ${highestUrgency.toUpperCase()} urgency
                                                </div>
                                            ` : ''}
                                        </div>
                                        <span class="status-badge ${barangay.status}">
                                            ${barangay.status.toUpperCase()}
                                        </span>
                                    </div>
                                `;
                            }).join('');
                    }
                })
                .catch(error => {
                    console.error('Error loading map data:', error);
                    const barangayList = document.getElementById('barangayList');
                    if (barangayList) {
                        barangayList.innerHTML = `
                            <div style="text-align: center; padding: 20px; color: #ef4444;">
                                <div style="font-size: 48px; margin-bottom: 10px;">⚠️</div>
                                <div style="font-weight: 600;">Error Loading Data</div>
                                <div style="font-size: 13px; color: #6b7280;">Please refresh the page</div>
                            </div>
                        `;
                    }
                });
        }

        // Function to focus on a specific barangay
        function focusBarangay(barangayId) {
            if (markers[barangayId] && markers[barangayId].marker) {
                map.setView(markers[barangayId].marker.getLatLng(), 15);
                markers[barangayId].marker.openPopup();
            }
        }

        // Load hero statistics from API
        function loadHeroStatistics() {
            fetch('/api/statistics')
                .then(response => response.json())
                .then(stats => {
                    document.getElementById('heroTotalDonations').textContent = formatCurrency(stats.total_donations || 0);
                    document.getElementById('heroAffectedFamilies').textContent = formatNumber(stats.total_affected_families || 0);
                    document.getElementById('heroVerifiedTransactions').textContent = formatNumber(stats.total_donors || 0);
                    document.getElementById('heroActiveFundraisers').textContent = formatNumber(stats.barangays_affected || 0);
                })
                .catch(error => {
                    console.error('Error loading hero statistics:', error);
                });
        }

        // Initialize map and data on page load
        loadMapData();
        loadHeroStatistics();

        // Auto-refresh map data every 30 seconds for real-time updates
        setInterval(() => {
            loadMapData();
            loadHeroStatistics();
        }, 30000);

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
@include('partials.footer')
</body>
</html>
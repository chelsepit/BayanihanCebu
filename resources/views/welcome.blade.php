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
        }

        .section-header {
            text-align: center;
            margin-bottom: 50px;
        }

        .section-header h2 {
            font-size: 36px;
            font-weight: 700;
            color: #1f2937;
            margin-bottom: 12px;
        }

        .section-header p {
            font-size: 18px;
            color: #6b7280;
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

        /* Barangay Cards Grid */
        .barangay-cards {
            display: grid;
            grid-template-columns: repeat(3, 1fr);
            gap: 20px;
            max-width: 1200px;
            margin: 0 auto 40px;
        }

        .barangay-card {
            background: white;
            border-radius: 12px;
            padding: 20px;
            box-shadow: 0 1px 3px rgba(0,0,0,0.1);
            transition: all 0.3s;
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

        /* Map Container */
        .map-container {
            max-width: 1200px;
            margin: 0 auto;
            background: white;
            border-radius: 12px;
            box-shadow: 0 1px 3px rgba(0,0,0,0.1);
            overflow: hidden;
            height: 600px;
            margin-bottom: 40px;
        }

        #barangayMap {
            width: 100%;
            height: 100%;
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
            }

            .trust-grid {
                grid-template-columns: 1fr;
            }

            .hero-text h2 {
                font-size: 32px;
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
            }

            .track-form {
                flex-direction: column;
            }

            .hero-buttons {
                flex-direction: column;
            }
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
                    <div class="stat-number">₱480,632</div>
                    <div class="stat-label">Total Donations</div>
                    <div class="verified-badge">✓ Verified</div>
                </div>
                <div class="stat-card">
                    <div class="stat-number">475</div>
                    <div class="stat-label">Families Affected</div>
                </div>
                <div class="stat-card">
                    <div class="stat-number">13</div>
                    <div class="stat-label">Verified Transactions</div>
                </div>
                <div class="stat-card">
                    <div class="stat-number">3</div>
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

        <div class="status-legend">
            <div class="legend-item">
                <span class="legend-dot safe"></span>
                Safe
            </div>
            <div class="legend-item">
                <span class="legend-dot warning"></span>
                Warning
            </div>
            <div class="legend-item">
                <span class="legend-dot critical"></span>
                Critical
            </div>
            <div class="legend-item">
                <span class="legend-dot emergency"></span>
                Emergency
            </div>
        </div>

        {{-- Barangay Cards --}}
        <div class="barangay-cards">
            {{-- Apas - Safe --}}
            <div class="barangay-card">
                <div class="barangay-header">
                    <div class="barangay-name">📍 Apas</div>
                    <div class="status-badge safe">Safe</div>
                </div>
                <div class="barangay-info">All clear - no active disasters</div>
            </div>

            {{-- Basak Pardo - Safe --}}
            <div class="barangay-card">
                <div class="barangay-header">
                    <div class="barangay-name">📍 Basak Pardo</div>
                    <div class="status-badge safe">Safe</div>
                </div>
                <div class="barangay-info">All clear - no active disasters</div>
            </div>

            {{-- Basak San Nicolas - Warning --}}
            <div class="barangay-card">
                <div class="barangay-header">
                    <div class="barangay-name">📍 Basak San Nicolas</div>
                    <div class="status-badge warning">Warning</div>
                </div>
                <div class="barangay-stats">
                    <div class="stat-item">
                        <div class="stat-item-label">Affected Families</div>
                        <div class="stat-item-value">30</div>
                    </div>
                    <div class="stat-item">
                        <div class="stat-item-label">Donations Received</div>
                        <div class="stat-item-value">₱32,000</div>
                    </div>
                </div>
                <div class="urgent-needs">
                    <div class="urgent-needs-label">Urgent Needs:</div>
                    <div class="needs-tags">
                        <span class="need-tag">Food</span>
                    </div>
                </div>
                <button class="donate-btn">Donate to Basak San Nicolas</button>
            </div>

            {{-- Busay - Safe --}}
            <div class="barangay-card">
                <div class="barangay-header">
                    <div class="barangay-name">📍 Busay</div>
                    <div class="status-badge safe">Safe</div>
                </div>
                <div class="barangay-info">All clear - no active disasters</div>
            </div>

            {{-- Capitol Site - Safe --}}
            <div class="barangay-card">
                <div class="barangay-header">
                    <div class="barangay-name">📍 Capitol Site</div>
                    <div class="status-badge safe">Safe</div>
                </div>
                <div class="barangay-info">All clear - no active disasters</div>
            </div>

            {{-- Mabolo - Safe --}}
            <div class="barangay-card">
                <div class="barangay-header">
                    <div class="barangay-name">📍 Mabolo</div>
                    <div class="status-badge safe">Safe</div>
                </div>
                <div class="barangay-info">All clear - no active disasters</div>
            </div>

            {{-- Tisa - Safe --}}
            <div class="barangay-card">
                <div class="barangay-header">
                    <div class="barangay-name">📍 Tisa</div>
                    <div class="status-badge safe">Safe</div>
                </div>
                <div class="barangay-info">All clear - no active disasters</div>
            </div>

            {{-- Banilad - Warning --}}
            <div class="barangay-card">
                <div class="barangay-header">
                    <div class="barangay-name">📍 Banilad</div>
                    <div class="status-badge warning">Warning</div>
                </div>
                <div class="barangay-stats">
                    <div class="stat-item">
                        <div class="stat-item-label">Affected Families</div>
                        <div class="stat-item-value">45</div>
                    </div>
                    <div class="stat-item">
                        <div class="stat-item-label">Donations Received</div>
                        <div class="stat-item-value">₱126,135</div>
                    </div>
                </div>
                <div class="urgent-needs">
                    <div class="urgent-needs-label">Urgent Needs:</div>
                    <div class="needs-tags">
                        <span class="need-tag">Food</span>
                        <span class="need-tag">Water</span>
                    </div>
                </div>
                <button class="donate-btn">Donate to Banilad</button>
            </div>

            {{-- Talamban - Warning --}}
            <div class="barangay-card">
                <div class="barangay-header">
                    <div class="barangay-name">📍 Talamban</div>
                    <div class="status-badge warning">Warning</div>
                </div>
                <div class="barangay-stats">
                    <div class="stat-item">
                        <div class="stat-item-label">Affected Families</div>
                        <div class="stat-item-value">30</div>
                    </div>
                    <div class="stat-item">
                        <div class="stat-item-label">Donations Received</div>
                        <div class="stat-item-value">₱40,000</div>
                    </div>
                </div>
                <div class="urgent-needs">
                    <div class="urgent-needs-label">Urgent Needs:</div>
                    <div class="needs-tags">
                        <span class="need-tag">Food</span>
                    </div>
                </div>
                <button class="donate-btn">Donate to Talamban</button>
            </div>

            {{-- Lahug - Critical --}}
            <div class="barangay-card">
                <div class="barangay-header">
                    <div class="barangay-name">📍 Lahug</div>
                    <div class="status-badge critical">Critical</div>
                </div>
                <div class="barangay-stats">
                    <div class="stat-item">
                        <div class="stat-item-label">Affected Families</div>
                        <div class="stat-item-value">120</div>
                    </div>
                    <div class="stat-item">
                        <div class="stat-item-label">Donations Received</div>
                        <div class="stat-item-value">₱90,500</div>
                    </div>
                </div>
                <div class="urgent-needs">
                    <div class="urgent-needs-label">Urgent Needs:</div>
                    <div class="needs-tags">
                        <span class="need-tag">Medical</span>
                        <span class="need-tag">Shelter</span>
                    </div>
                </div>
                <button class="donate-btn">Donate to Lahug</button>
            </div>

            {{-- Guadalupe - Emergency --}}
            <div class="barangay-card">
                <div class="barangay-header">
                    <div class="barangay-name">📍 Guadalupe</div>
                    <div class="status-badge emergency">Emergency</div>
                </div>
                <div class="barangay-stats">
                    <div class="stat-item">
                        <div class="stat-item-label">Affected Families</div>
                        <div class="stat-item-value">250</div>
                    </div>
                    <div class="stat-item">
                        <div class="stat-item-label">Donations Received</div>
                        <div class="stat-item-value">₱335,000</div>
                    </div>
                </div>
                <div class="urgent-needs">
                    <div class="urgent-needs-label">Urgent Needs:</div>
                    <div class="needs-tags">
                        <span class="need-tag">Food</span>
                        <span class="need-tag">Water</span>
                        <span class="need-tag">Medical</span>
                    </div>
                </div>
                <button class="donate-btn">Donate to Guadalupe</button>
            </div>
        </div>

        {{-- Map Container (PRESERVED FROM YOUR CODE) --}}
        <div class="map-container">
            <div id="barangayMap"></div>
        </div>
    </section>

    {{-- Track Donation Section --}}
    <section class="track-section" id="track">
        <div class="track-container">
            <h2>Track Your Donation</h2>
            <p>Enter your transaction ID to see blockchain verification and how your donation is being used</p>
            <form class="track-form" action="{{ route('donation.track') }}" method="GET">
                <input 
                    type="text" 
                    name="transaction_id" 
                    class="track-input" 
                    placeholder="Enter Transaction ID (e.g., DQXt-1234689100)"
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
        // Initialize map centered on Cebu City (PRESERVED FROM YOUR CODE)
        const map = L.map('barangayMap').setView([10.3157, 123.8854], 12);

        // Add OpenStreetMap tile layer
        L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
            attribution: '© <a href="https://www.openstreetmap.org/copyright">OpenStreetMap</a> contributors',
            maxZoom: 19,
        }).addTo(map);

        // Custom marker icon function
        function createCustomIcon(status) {
            const colors = {
                safe: '#10b981',
                warning: '#f59e0b',
                critical: '#f97316',
                emergency: '#ef4444'
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
                        <div style="padding: 15px;">
                            <h3 style="margin: 0 0 10px 0; font-size: 18px; font-weight: 600;">${barangay.name}</h3>
                            <p style="margin: 0 0 10px 0; color: #6b7280;">${barangay.city}</p>
                            <div style="padding: 8px 0; border-top: 1px solid #e5e7eb;">
                                <strong>Status:</strong> ${barangay.status}
                            </div>
                            ${barangay.affected_families ? `
                                <div style="padding: 8px 0; border-top: 1px solid #e5e7eb;">
                                    <strong>Affected Families:</strong> ${barangay.affected_families}
                                </div>
                            ` : ''}
                            ${barangay.total_received ? `
                                <div style="padding: 8px 0; border-top: 1px solid #e5e7eb;">
                                    <strong>Donations:</strong> ${formatCurrency(barangay.total_received)}
                                </div>
                            ` : ''}
                        </div>
                    `;

                    marker.bindPopup(popupContent);
                });
            })
            .catch(error => {
                console.error('Error loading map data:', error);
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
@include('partials.footer')
</body>
</html>
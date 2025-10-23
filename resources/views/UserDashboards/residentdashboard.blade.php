<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>BayanihanCebu - Resident Dashboard</title>

    <!-- Tailwind CSS - Use CDN for reliability -->
    <script src="https://cdn.tailwindcss.com"></script>

    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">

    <!-- Material Symbols (for footer icons) -->
    <link rel="stylesheet"
        href="https://fonts.googleapis.com/css2?family=Material+Symbols+Outlined:opsz,wght,FILL,GRAD@20..48,100..700,0..1,-50..200" />

    <!-- Custom CSS inline (for footer styles) -->
    <style>
        /* Footer Styles */
        .footer {
            background: linear-gradient(to bottom, #f9fafb 0%, #e0f2fe 100%);
            padding: 3rem 0 1rem;
            margin-top: 5rem;
            position: relative;
        }

        .footer .container {
            max-width: 1200px;
            margin: 0 auto;
            padding: 0 20px;
            position: relative;
            z-index: 1;
        }

        .footer-grid {
            display: grid;
            grid-template-columns: 2fr 1fr 1fr 1.5fr;
            gap: 3rem;
            margin-bottom: 3rem;
        }

        .footer-col h4 {
            font-size: 1.125rem;
            font-weight: 700;
            margin-bottom: 1.5rem;
            color: #111827;
            padding-bottom: 0.5rem;
            border-bottom: 2px solid #d1d5db;
            transition: border-color 0.3s;
        }

        .footer-col h4:hover {
            border-color: #2563eb;
        }

        .footer-col p {
            color: #374151;
            line-height: 1.7;
            font-size: 0.9375rem;
        }

        .footer-col ul {
            display: flex;
            flex-direction: column;
            gap: 0.875rem;
            list-style: none;
            padding: 0;
            margin: 0;
        }

        .footer-col ul li a {
            color: #374151;
            display: inline-flex;
            align-items: center;
            gap: 0.5rem;
            font-size: 0.9375rem;
            transition: all 0.3s ease;
            text-decoration: none;
        }

        .footer-col ul li a .material-symbols-outlined {
            font-size: 1.125rem;
        }

        .footer-col ul li a:hover {
            color: #2563eb;
            transform: translateX(8px);
        }

        .contact-info-footer {
            display: flex;
            flex-direction: column;
            gap: 0.75rem;
            margin-bottom: 1.5rem;
        }

        .contact-info-footer p {
            display: flex;
            align-items: center;
            gap: 0.5rem;
            color: #374151;
            font-size: 0.9375rem;
            transition: all 0.3s;
        }

        .contact-info-footer p:hover {
            color: #2563eb;
        }

        .contact-info-footer .material-symbols-outlined {
            font-size: 1.25rem;
            color: #2563eb;
        }

        .social-links {
            display: flex;
            gap: 0.75rem;
        }

        .social-link {
            width: 40px;
            height: 40px;
            background: #ffffff;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            color: #374151;
            transition: all 0.3s cubic-bezier(0.34, 1.56, 0.64, 1);
            box-shadow: 0 2px 8px rgba(0, 0, 0, 0.08);
            border: 2px solid transparent;
            text-decoration: none;
        }

        .social-link:hover {
            transform: translateY(-8px) scale(1.15);
            background: #2563eb;
            color: #ffffff;
            box-shadow: 0 8px 16px rgba(37, 99, 235, 0.3);
        }

        .social-link i {
            font-size: 1.125rem;
        }

        .footer-bottom-content {
            display: flex;
            flex-direction: column;
            align-items: center;
            text-align: center;
            gap: 0.5rem;
            margin-bottom: 2rem;
        }

        .footer-bottom-content p {
            color: #4b5563;
            font-size: 0.9375rem;
            margin: 0;
        }

        .footer-bottom-links {
            display: flex;
            gap: 1rem;
            align-items: center;
        }

        .footer-bottom-links a {
            color: #4b5563;
            font-size: 0.875rem;
            transition: color 0.3s;
            text-decoration: none;
        }

        .footer-bottom-links a:hover {
            color: #2563eb;
        }

        .footer-bottom-links span {
            color: #9ca3af;
        }

        .footer-badge {
            background: linear-gradient(135deg, #2563eb, #14b8a6);
            color: #ffffff;
            padding: 1rem;
            border-radius: 0.75rem;
            text-align: center;
            box-shadow: 0 4px 12px rgba(37, 99, 235, 0.2);
        }

        .footer-badge p {
            margin: 0;
            font-size: 0.875rem;
            font-weight: 600;
            color: #ffffff;
        }

        .payment-option.selected {
            border-color: #2563eb !important;
            background-color: #eff6ff !important;
            box-shadow: 0 0 0 3px rgba(37, 99, 235, 0.1);
        }

        .payment-option:active {
            transform: scale(0.98);
        }

        /* Responsive Footer */
        @media (max-width: 1024px) {
            .footer-grid {
                grid-template-columns: 1fr 1fr;
                gap: 2.5rem;
            }
        }

        @media (max-width: 768px) {
            .footer {
                padding: 2rem 0 1rem;
            }

            .footer-grid {
                grid-template-columns: 1fr;
                gap: 2rem;
            }

            .social-links {
                justify-content: flex-start;
            }
        }
    </style>

    <style>
        .modal {
            display: none;
            position: fixed;
            z-index: 1000;
            left: 0;
            top: 0;
            width: 100%;
            height: 100%;
            background-color: rgba(0, 0, 0, 0.5);
            align-items: center;
            justify-content: center;
        }

        .modal.active {
            display: flex;
        }

        @keyframes slideIn {
            from {
                opacity: 0;
                transform: translateY(20px);
            }

            to {
                opacity: 1;
                transform: translateY(0);
            }
        }

        .animate-slide-in {
            animation: slideIn 0.3s ease-out;
        }

        /* Barangay Cards Styles */
        .barangay-cards {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(350px, 1fr));
            gap: 1.5rem;
            margin-bottom: 2rem;
        }

        .barangay-card {
            background: white;
            border-radius: 12px;
            border: 1px solid #e5e7eb;
            padding: 1.5rem;
            transition: all 0.3s ease;
        }

        .barangay-card:hover {
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.1);
            transform: translateY(-2px);
        }

        .barangay-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 1rem;
        }

        .barangay-name {
            font-size: 1.125rem;
            font-weight: 700;
            color: #1f2937;
        }

        .status-badge {
            padding: 0.25rem 0.75rem;
            border-radius: 9999px;
            font-size: 0.75rem;
            font-weight: 600;
            text-transform: uppercase;
        }

        .status-badge.safe {
            background-color: #d1fae5;
            color: #065f46;
        }

        .status-badge.warning {
            background-color: #fef3c7;
            color: #92400e;
        }

        .status-badge.critical {
            background-color: #fee2e2;
            color: #991b1b;
        }

        .status-badge.emergency {
            background-color: #fecaca;
            color: #7f1d1d;
        }

        .barangay-info {
            color: #6b7280;
            font-size: 0.875rem;
            line-height: 1.5;
        }

        .barangay-stats {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 1rem;
            margin: 1rem 0;
            padding: 1rem;
            background-color: #f9fafb;
            border-radius: 8px;
        }

        .stat-item {
            text-align: center;
        }

        .stat-item-label {
            font-size: 0.75rem;
            color: #6b7280;
            margin-bottom: 0.25rem;
        }

        .stat-item-value {
            font-size: 1.25rem;
            font-weight: 700;
            color: #1f2937;
        }

        .urgent-needs {
            margin: 1rem 0;
            padding: 0.75rem;
            background-color: #fef2f2;
            border-left: 3px solid #ef4444;
            border-radius: 4px;
        }

        .urgent-needs-label {
            font-size: 0.75rem;
            font-weight: 600;
            color: #991b1b;
            margin-bottom: 0.5rem;
        }

        .needs-tags {
            display: flex;
            flex-wrap: wrap;
            gap: 0.5rem;
        }

        .need-tag {
            padding: 0.25rem 0.75rem;
            background-color: #fee2e2;
            color: #991b1b;
            border-radius: 9999px;
            font-size: 0.75rem;
            font-weight: 500;
        }

        .donate-btn {
            width: 100%;
            padding: 0.75rem 1.5rem;
            background: linear-gradient(to right, #dc2626, #b91c1c);
            color: white;
            font-weight: 600;
            border-radius: 8px;
            border: none;
            cursor: pointer;
            transition: all 0.3s ease;
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 0.5rem;
        }

        .donate-btn:hover {
            background: linear-gradient(to right, #b91c1c, #991b1b);
            box-shadow: 0 4px 12px rgba(220, 38, 38, 0.3);
        }

        /* Tab styles */
        .tab-button {
            border-bottom-color: transparent;
            color: #6b7280;
            transition: all 0.3s;
        }

        .tab-button:hover {
            color: #1f2937;
            border-bottom-color: #e5e7eb;
        }

        .tab-button.active {
            color: #CA6702;
            border-bottom-color: #CA6702;
        }

        .tab-content {
            animation: fadeIn 0.3s ease-in;
        }

        @keyframes fadeIn {
            from {
                opacity: 0;
                transform: translateY(10px);
            }

            to {
                opacity: 1;
                transform: translateY(0);
            }
        }

        /* Donation card styles */
        .donation-card {
            background: white;
            border-radius: 12px;
            padding: 20px;
            border: 1px solid #e5e7eb;
            transition: all 0.3s;
        }

        .donation-card:hover {
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.1);
            transform: translateY(-2px);
        }

        .tracking-code-badge {
            display: inline-block;
            background: #eff6ff;
            color: #2563eb;
            padding: 6px 12px;
            border-radius: 6px;
            font-size: 14px;
            font-weight: 700;
            font-family: 'Courier New', monospace;
            margin-bottom: 12px;
        }

        /* Loading spinner */
        .loading-spinner {
            display: inline-block;
            width: 40px;
            height: 40px;
            border: 4px solid #f3f4f6;
            border-top: 4px solid #CA6702;
            border-radius: 50%;
            animation: spin 1s linear infinite;
        }

        @keyframes spin {
            0% {
                transform: rotate(0deg);
            }

            100% {
                transform: rotate(360deg);
            }
        }

        /* Responsive */
        @media (max-width: 640px) {
            .barangay-cards {
                grid-template-columns: 1fr;
            }

            .barangay-stats {
                grid-template-columns: 1fr;
            }
        }
    </style>
</head>

<body class="bg-gray-50">

    <!-- Top Navigation Bar -->
    <nav class="bg-[#CA6702] text-white shadow-md">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="flex justify-between items-center h-16">
                <div class="flex items-center gap-4">
                    <button class="md:hidden text-white hover:opacity-80">
                        <i class="fas fa-bars text-xl"></i>
                    </button>
                    <div>
                        <h1 class="text-xl font-bold">BayanihanCebu</h1>
                        <p class="text-xs opacity-90">Resident Dashboard</p>
                    </div>
                </div>

                <div class="flex items-center gap-4">
                    <div class="flex items-center gap-3">
                        <div class="text-right hidden md:block">
                            <p class="text-sm">Welcome,</p>
                            <p class="text-sm font-semibold">{{ session('user_name') ?? 'resident@test.com' }}</p>
                        </div>
                        <form action="{{ route('logout') }}" method="POST">
                            @csrf
                            <button type="submit"
                                class="px-4 py-2 bg-white text-[#CA6702] rounded-lg hover:bg-orange-50 transition text-sm font-medium">
                                <i class="fas fa-sign-out-alt mr-1"></i> Sign Out
                            </button>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </nav>

    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">

        <!-- Tabs Navigation -->
        <div class="bg-white rounded-xl shadow-sm mb-6">
            <div class="border-b border-gray-200">
                <nav class="-mb-px flex" aria-label="Tabs">
                    <button onclick="switchTab('barangays')" id="tab-barangays"
                        class="tab-button active whitespace-nowrap py-4 px-6 border-b-2 font-medium text-sm">
                        <i class="fas fa-map-marked-alt mr-2"></i>
                        Affected Barangays
                    </button>
                    <button onclick="switchTab('donations')" id="tab-donations"
                        class="tab-button whitespace-nowrap py-4 px-6 border-b-2 font-medium text-sm">
                        <i class="fas fa-heart mr-2"></i>
                        My Donations
                    </button>
                </nav>
            </div>
        </div>

        <!-- Barangays Tab Content -->
        <div id="content-barangays" class="tab-content">
            <!-- Alert Banner -->
            <div class="bg-orange-50 border-l-4 border-[#CA6702] p-4 mb-6 rounded-r-lg">
                <div class="flex items-start">
                    <i class="fas fa-exclamation-triangle text-[#CA6702] text-xl mt-1 mr-3"></i>
                    <div>
                        <h3 class="text-[#001219] font-semibold text-lg mb-1">Help Disaster-Affected Barangays</h3>
                        <p class="text-gray-700 text-sm">View what barangays need and donate to help affected families.
                            All
                            donations are verified on the blockchain for transparency.</p>
                    </div>
                </div>
            </div>

            <!-- Statistics Cards -->
            <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-8">
                <div class="bg-white rounded-xl shadow-sm p-6 border border-gray-200">
                    <div class="flex items-center gap-4">
                        <div class="w-14 h-14 bg-red-100 rounded-lg flex items-center justify-center">
                            <i class="fas fa-exclamation-triangle text-red-600 text-2xl"></i>
                        </div>
                        <div>
                            <p class="text-gray-600 text-sm">Active Needs</p>
                            <h3 class="text-3xl font-bold text-gray-900" id="activeNeedsCount">0</h3>
                        </div>
                    </div>
                </div>

                <div class="bg-white rounded-xl shadow-sm p-6 border border-gray-200">
                    <div class="flex items-center gap-4">
                        <div class="w-14 h-14 bg-orange-100 rounded-lg flex items-center justify-center">
                            <i class="fas fa-map-marker-alt text-orange-600 text-2xl"></i>
                        </div>
                        <div>
                            <p class="text-gray-600 text-sm">Affected Barangays</p>
                            <h3 class="text-3xl font-bold text-gray-900" id="affectedBarangaysCount">0</h3>
                        </div>
                    </div>
                </div>

                <div class="bg-white rounded-xl shadow-sm p-6 border border-gray-200">
                    <div class="flex items-center gap-4">
                        <div class="w-14 h-14 bg-green-100 rounded-lg flex items-center justify-center">
                            <i class="fas fa-heart text-green-600 text-2xl"></i>
                        </div>
                        <div>
                            <p class="text-gray-600 text-sm">Your Impact</p>
                            <h3 class="text-xl font-bold text-green-600">Help Now</h3>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Filter Section (Hidden - not functional with server-side rendering) -->
            <div class="bg-white rounded-xl shadow-sm p-6 mb-6 hidden">
                <div class="flex items-center gap-2 mb-4">
                    <i class="fas fa-filter text-gray-600"></i>
                    <h3 class="font-semibold text-gray-800">Filter Barangays</h3>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Disaster Status</label>
                        <select id="statusFilter"
                            class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                            <option value="">All Statuses</option>
                            <option value="emergency">Emergency</option>
                            <option value="critical">Critical</option>
                            <option value="warning">Warning</option>
                            <option value="safe">Safe</option>
                        </select>
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Disaster Type</label>
                        <select id="disasterTypeFilter"
                            class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                            <option value="">All Types</option>
                            <option value="flood">Flood</option>
                            <option value="fire">Fire</option>
                            <option value="typhoon">Typhoon</option>
                            <option value="earthquake">Earthquake</option>
                            <option value="landslide">Landslide</option>
                        </select>
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Search</label>
                        <input type="text" id="searchFilter" placeholder="Search barangay name..."
                            class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                    </div>
                </div>
            </div>

            <!-- Barangay Cards Section -->
            <div class="mb-6">
                <div class="flex items-center justify-between mb-6">
                    <h2 class="text-2xl font-bold text-gray-900">Disaster-Affected Barangays</h2>
                    <span class="text-sm text-gray-600">Showing {{ $barangays->count() }} barangay(s)</span>
                </div>

                <!-- Barangay Cards Grid -->
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
                                        <div class="stat-item-value">PHP {{ number_format($barangay->total_raised, 0) }}</div>
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

                                <button class="donate-btn" onclick="openDonationModal('{{ $barangay->barangay_id }}')">
                                    <i class="fas fa-heart"></i> Donate to {{ $barangay->name }}
                                </button>
                            @endif
                        </div>
                    @endforeach
                </div>
            </div>
        </div>

        <!-- My Donations Tab Content -->
        <div id="content-donations" class="tab-content hidden">
            <div class="bg-white rounded-xl shadow-sm p-6 mb-6">
                <h2 class="text-2xl font-bold text-gray-900 mb-4">
                    <i class="fas fa-heart text-red-500 mr-2"></i>
                    My Donations
                </h2>
                <p class="text-gray-600 mb-6">View all your donations with tracking codes and blockchain verification
                </p>

                <!-- Loading State -->
                <div id="donations-loading" class="text-center py-12">
                    <div class="loading-spinner mx-auto mb-4"></div>
                    <p class="text-gray-600">Loading your donations...</p>
                </div>

                <!-- Empty State -->
                <div id="donations-empty" class="hidden text-center py-12">
                    <div class="text-6xl mb-4">ðŸ“¦</div>
                    <h3 class="text-xl font-semibold text-gray-800 mb-2">No Donations Yet</h3>
                    <p class="text-gray-600 mb-6">You haven't made any donations yet. Start making a difference today!
                    </p>
                    <button onclick="switchTab('barangays')"
                        class="px-6 py-3 bg-red-600 text-white rounded-lg hover:bg-red-700 transition font-semibold">
                        <i class="fas fa-heart mr-2"></i>Make Your First Donation
                    </button>
                </div>

                <!-- Donations Grid -->
                <div id="donations-grid" class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4" style="display: none;"></div>
            </div>
        </div>
    </div>


    <script>
        const csrfToken = document.querySelector('meta[name="csrf-token"]').content;
        let allBarangays = [];

        const disasterIcons = {
            'flood': 'ðŸŒŠ',
            'fire': 'ðŸ”¥',
            'earthquake': 'ðŸšï¸',
            'typhoon': 'ðŸŒ€',
            'landslide': 'â›°ï¸',
            'other': 'â“'
        };

        document.addEventListener('DOMContentLoaded', function () {
            // Barangays are now server-rendered, no need to load dynamically
            // loadBarangays(); // REMOVED - using server-side rendering

            // Filter functionality removed - barangays are now server-rendered
            // document.getElementById('statusFilter').addEventListener('change', filterBarangays);
            // document.getElementById('disasterTypeFilter').addEventListener('change', filterBarangays);
            // document.getElementById('searchFilter').addEventListener('input', filterBarangays);

            // Check if we should show donations tab (from URL hash)
            if (window.location.hash === '#donations') {
                switchTab('donations');
            }
        });

        // Tab switching function
        function switchTab(tabName) {
            // Update tab buttons
            document.querySelectorAll('.tab-button').forEach(btn => {
                btn.classList.remove('active');
            });
            document.getElementById('tab-' + tabName).classList.add('active');

            // Update tab content
            document.querySelectorAll('.tab-content').forEach(content => {
                content.classList.add('hidden');
            });
            document.getElementById('content-' + tabName).classList.remove('hidden');

            // Load donations when switching to donations tab
            if (tabName === 'donations') {
                loadMyDonations();
            }

            // Update URL hash
            window.location.hash = tabName;
        }

        async function loadBarangays() {
            try {
                const response = await fetch('/api/barangays', {
                    method: 'GET',
                    headers: {
                        'Accept': 'application/json',
                        'X-CSRF-TOKEN': csrfToken
                    }
                });

                if (!response.ok) throw new Error('Network response was not ok');

                const barangays = await response.json();
                allBarangays = barangays;

                // Update statistics
                const affectedCount = barangays.filter(b => b.disaster_status !== 'safe').length;
                const totalNeeds = barangays.reduce((sum, b) => sum + (b.urgent_needs || 0), 0);

                document.getElementById('affectedBarangaysCount').textContent = affectedCount;
                document.getElementById('activeNeedsCount').textContent = totalNeeds;

                displayBarangays(barangays);

            } catch (error) {
                console.error('Error loading barangays:', error);
                document.getElementById('barangayCardsGrid').innerHTML = `
                    <div class="col-span-full text-center py-12">
                        <div class="text-red-600 mb-4">
                            <i class="fas fa-exclamation-circle text-4xl"></i>
                        </div>
                        <p class="text-lg font-semibold text-gray-800">Error Loading Barangays</p>
                        <p class="text-sm text-gray-600 mt-2">${error.message}</p>
                        <button onclick="loadBarangays()" class="mt-4 px-6 py-2 bg-[#CA6702] text-white rounded-lg hover:bg-[#BB3E03] transition">
                            <i class="fas fa-redo mr-2"></i>Try Again
                        </button>
                    </div>
                `;
            }
        }

        function displayBarangays(barangays) {
            const grid = document.getElementById('barangayCardsGrid');
            document.getElementById('barangayCount').textContent = `Showing ${barangays.length} barangay(s)`;

            if (barangays.length === 0) {
                grid.innerHTML = `
                    <div class="col-span-full text-center py-12 text-gray-500">
                        <i class="fas fa-check-circle text-5xl mb-4 text-green-500"></i>
                        <p class="text-lg font-semibold">No barangays match your filters</p>
                        <p class="text-sm mt-2">Try adjusting your search criteria</p>
                    </div>
                `;
                return;
            }

            grid.innerHTML = barangays.map(barangay => {
                const isSafe = barangay.disaster_status === 'safe';

                return `
                    <div class="barangay-card">
                        <div class="barangay-header">
                            <div class="barangay-name">ðŸ“ ${barangay.name}</div>
                            <div class="status-badge ${barangay.disaster_status || 'safe'}">
                                ${barangay.disaster_status ? barangay.disaster_status.charAt(0).toUpperCase() + barangay.disaster_status.slice(1) : 'Unknown'}
                            </div>
                        </div>

                        ${isSafe ? `
                            <div class="barangay-info">All clear - no active disasters</div>
                        ` : `
                            ${barangay.disaster_type ? `
                                <div class="barangay-info" style="margin-bottom: 12px;">
                                    <strong>Type:</strong> ${disasterIcons[barangay.disaster_type] || ''} ${barangay.disaster_type ? barangay.disaster_type.charAt(0).toUpperCase() + barangay.disaster_type.slice(1) : ''}
                                </div>
                            ` : ''}

                            <div class="barangay-stats">
                                <div class="stat-item">
                                    <div class="stat-item-label">Affected Families</div>
                                    <div class="stat-item-value">${barangay.affected_families || 0}</div>
                                </div>
                                <div class="stat-item">
                                    <div class="stat-item-label">Donations Received</div>
                                    <div class="stat-item-value">â‚±${Number(barangay.needs_summary || 0).toLocaleString()}</div>
                                </div>
                            </div>

                            ${barangay.urgent_needs > 0 ? `
                                <div class="urgent-needs">
                                    <div class="urgent-needs-label">Urgent Needs: ${barangay.urgent_needs} active request(s)</div>
                                    <div class="needs-tags">
                                        <span class="need-tag">Food</span>
                                        <span class="need-tag">Water</span>
                                        <span class="need-tag">Medical</span>
                                    </div>
                                </div>
                            ` : ''}

                            <button class="donate-btn" onclick='openDonationModal("${barangay.id}", "${barangay.name}", "General")'>
                                Donate to ${barangay.name}
                            </button>
                        `}
                    </div>
                `;
            }).join('');
        }

        function filterBarangays() {
            const status = document.getElementById('statusFilter').value.toLowerCase();
            const disasterType = document.getElementById('disasterTypeFilter').value.toLowerCase();
            const search = document.getElementById('searchFilter').value.toLowerCase();

            const filtered = allBarangays.filter(barangay => {
                const matchStatus = !status || barangay.disaster_status === status;
                const matchDisasterType = !disasterType || barangay.disaster_type === disasterType;
                const matchSearch = !search || barangay.name.toLowerCase().includes(search);

                return matchStatus && matchDisasterType && matchSearch;
            });

            displayBarangays(filtered);
        }

        // OLD FUNCTION REMOVED - Now using modal-based donation
        // function openDonationModal(barangayId, barangayName, category) {
        //     const url = `/donate?barangay_id=${barangayId}&barangay_name=${encodeURIComponent(barangayName)}`;
        //     window.location.href = url;
        // }

        // Load My Donations (AUTHENTICATED - Only shows logged-in user's donations)
        let donationsLoaded = false;

        async function loadMyDonations() {
            // Only load once
            if (donationsLoaded) return;
            donationsLoaded = true;

            const loadingEl = document.getElementById('donations-loading');
            const emptyEl = document.getElementById('donations-empty');
            const gridEl = document.getElementById('donations-grid');

            try {
                // Fetch donations from authenticated API endpoint
                // This ONLY returns donations made by the logged-in user
                const response = await fetch('/api/donations/my-donations', {
                    method: 'GET',
                    headers: {
                        'Accept': 'application/json',
                        'X-CSRF-TOKEN': csrfToken
                    },
                    credentials: 'same-origin' // Include session cookies
                });

                if (!response.ok) {
                    if (response.status === 401) {
                        console.error('User not authenticated');
                        showEmptyDonations();
                        return;
                    }
                    throw new Error('Failed to fetch donations');
                }

                const result = await response.json();

                if (!result.success) {
                    showEmptyDonations();
                    return;
                }

                const donations = result.donations;

                if (donations.length === 0) {
                    showEmptyDonations();
                } else {
                    displayDonations(donations);
                }

            } catch (error) {
                console.error('Error loading donations:', error);
                showEmptyDonations();
            }
        }

        function showEmptyDonations() {
            document.getElementById('donations-loading').classList.add('hidden');
            document.getElementById('donations-empty').classList.remove('hidden');
        }

        function displayDonations(donations) {
            document.getElementById('donations-loading').classList.add('hidden');

            const grid = document.getElementById('donations-grid');
            grid.innerHTML = '';

            // Sort by date (newest first)
            donations.sort((a, b) => new Date(b.created_at) - new Date(a.created_at));

            donations.forEach(donation => {
                const card = document.createElement('div');
                card.className = 'donation-card';
                card.innerHTML = `
                    <div class="tracking-code-badge">${donation.tracking_code}</div>

                    <div style="font-size: 24px; font-weight: 700; color: #059669; margin-bottom: 12px;">
                        PHP ${Number(donation.amount).toLocaleString('en-PH', { minimumFractionDigits: 2 })}
                    </div>

                    <div style="display: flex; flex-direction: column; gap: 8px; margin: 12px 0;">
                        <div style="display: flex; justify-content: space-between; font-size: 14px;">
                            <span style="color: #6b7280;">Beneficiary:</span>
                            <span style="font-weight: 600; color: #1f2937;">${donation.barangay_name}</span>
                        </div>

                        <div style="display: flex; justify-content: space-between; font-size: 14px;">
                            <span style="color: #6b7280;">Type:</span>
                            <span style="font-weight: 600; color: #1f2937;">Monetary</span>
                        </div>

                        <div style="display: flex; justify-content: space-between; font-size: 14px;">
                            <span style="color: #6b7280;">Status:</span>
                            <span class="px-2 py-1 rounded-full text-xs font-semibold" style="background: ${donation.status === 'confirmed' ? '#d1fae5' : '#fef3c7'}; color: ${donation.status === 'confirmed' ? '#065f46' : '#92400e'};">
                                ${donation.status.charAt(0).toUpperCase() + donation.status.slice(1)}
                            </span>
                        </div>

                        <div style="display: flex; justify-content: space-between; font-size: 14px;">
                            <span style="color: #6b7280;">Date:</span>
                            <span style="font-weight: 600; color: #1f2937;">${formatDonationDate(donation.created_at)}</span>
                        </div>
                    </div>

                    ${donation.blockchain_tx_hash && donation.blockchain_status === 'confirmed' ? `
                        <div style="display: inline-flex; align-items: center; gap: 6px; padding: 6px 12px; background: #d1fae5; color: #065f46; border-radius: 6px; font-size: 12px; font-weight: 600; margin-top: 12px;">
                            <i class="fas fa-shield-alt"></i>
                            Blockchain Verified
                        </div>
                    ` : donation.payment_status === 'paid' ? `
                        <div style="display: inline-flex; align-items: center; gap: 6px; padding: 6px 12px; background: #eff6ff; color: #1e40af; border-radius: 6px; font-size: 12px; font-weight: 600; margin-top: 12px;">
                            <i class="fas fa-hourglass-half"></i>
                            Recording on Blockchain...
                        </div>
                    ` : donation.payment_status === 'pending' ? `
                        <div style="display: inline-flex; align-items: center; gap: 6px; padding: 6px 12px; background: #fee2e2; color: #991b1b; border-radius: 6px; font-size: 12px; font-weight: 600; margin-top: 12px;">
                            <i class="fas fa-times-circle"></i>
                            Payment Not Completed
                        </div>
                        <p style="font-size: 11px; color: #6b7280; margin-top: 8px; font-style: italic;">
                            This donation was created but payment was not completed. Create a new donation to try again.
                        </p>
                    ` : `
                        <div style="display: inline-flex; align-items: center; gap: 6px; padding: 6px 12px; background: #fee2e2; color: #991b1b; border-radius: 6px; font-size: 12px; font-weight: 600; margin-top: 12px;">
                            <i class="fas fa-exclamation-circle"></i>
                            ${donation.payment_status || 'Unknown Status'}
                        </div>
                    `}

                    ${donation.blockchain_tx_hash && donation.blockchain_status === 'confirmed' ? `
                        <div style="margin-top: 12px; padding: 10px; background: #f0f9ff; border-left: 3px solid #0ea5e9; border-radius: 4px;">
                            <div style="display: flex; align-items: start; gap: 8px;">
                                <i class="fas fa-info-circle" style="color: #0ea5e9; margin-top: 2px; font-size: 14px;"></i>
                                <div style="flex: 1;">
                                    <p style="font-size: 11px; color: #0369a1; margin: 0 0 6px 0; font-weight: 600;">
                                        Blockchain Transparency
                                    </p>
                                    <p style="font-size: 11px; color: #075985; margin: 0 0 8px 0; line-height: 1.4;">
                                        Your donation is permanently recorded on Lisk Sepolia blockchain for full transparency. Anyone can verify this transaction.
                                    </p>
                                    <a href="${donation.explorer_url}"
                                       target="_blank"
                                       style="display: inline-flex; align-items: center; gap: 4px; font-size: 12px; color: #0284c7; text-decoration: none; font-weight: 600; padding: 4px 8px; background: white; border-radius: 4px; border: 1px solid #bae6fd;">
                                        <i class="fas fa-external-link-alt" style="font-size: 10px;"></i>
                                        View Blockchain Record
                                    </a>
                                </div>
                            </div>
                        </div>
                    ` : ''}

                    <div style="display: flex; gap: 8px; margin-top: 16px;">
                        <button onclick="copyDonationCode('${donation.tracking_code}')"
                                class="flex-1 px-4 py-2 bg-gray-200 text-gray-800 rounded-lg hover:bg-gray-300 transition font-semibold text-sm">
                            <i class="fas fa-copy mr-1"></i>Copy Code
                        </button>
                    </div>
                `;

                grid.appendChild(card);
            });

            grid.style.display = 'grid';
        }

        function formatDonationDate(dateString) {
            const date = new Date(dateString);
            const options = { year: 'numeric', month: 'short', day: 'numeric' };
            return date.toLocaleDateString('en-US', options);
        }

        function copyDonationCode(code) {
            navigator.clipboard.writeText(code).then(() => {
                const btn = event.target.closest('button');
                const originalHTML = btn.innerHTML;
                btn.innerHTML = '<i class="fas fa-check mr-1"></i>Copied!';
                btn.classList.add('bg-green-200', 'text-green-800');
                btn.classList.remove('bg-gray-200', 'text-gray-800');

                setTimeout(() => {
                    btn.innerHTML = originalHTML;
                    btn.classList.remove('bg-green-200', 'text-green-800');
                    btn.classList.add('bg-gray-200', 'text-gray-800');
                }, 2000);
            });
        }

    </script>

    <!-- Donation Modal -->
    <div id="donationModal" class="fixed inset-0 z-50 flex items-center justify-center bg-black/50" style="display: none;">
        <div class="bg-white rounded-2xl shadow-2xl w-full max-w-lg p-8 relative animate-fadeIn">

            <!-- Header -->
            <div class="flex items-center justify-between mb-6">
                <h2 class="text-2xl font-bold text-gray-800 flex items-center gap-2">
                    💖 Make a Donation
                </h2>
                <button onclick="closeDonationModal()" class="text-gray-400 hover:text-gray-600 text-xl font-bold">
                    ×
                </button>
            </div>

            <p class="text-sm text-gray-500 mb-6">
                Support a barangay in need. Your donation will be processed securely via PayMongo.
            </p>

            <!-- Donation Form -->
            <form id="donationForm" class="space-y-4">
                @csrf

                <!-- Barangay Selection -->
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Select Barangay</label>
                    <select name="barangay_id" id="barangaySelect" required
                        class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:ring-2 focus:ring-blue-500">
                        <option value="">Choose a barangay...</option>
                        @foreach($barangays as $b)
                            <option value="{{ $b->barangay_id }}">{{ $b->name }}</option>
                        @endforeach
                    </select>
                </div>

                <!-- Amount -->
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Amount (PHP)</label>
                    <input type="number" name="amount" min="1" required placeholder="1000"
                        class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:ring-2 focus:ring-blue-500">
                </div>

                <!-- Donation Type (Hidden - Only Monetary Accepted) -->
                <input type="hidden" name="donation_type" value="monetary">

                <!-- Personal Info -->
                <div class="pt-3 border-t border-gray-100">
                    <h3 class="text-sm font-semibold text-gray-700 mt-2 mb-2">Donor Details</h3>

                    <input type="text" name="donor_name" placeholder="Full Name *" required
                        value="{{ session('user_name') ?? '' }}"
                        class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm mb-2 focus:ring-2 focus:ring-blue-500">

                    <input type="email" name="donor_email" placeholder="Email (optional)"
                        value="{{ session('email') ?? '' }}"
                        class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm mb-2 focus:ring-2 focus:ring-blue-500">

                    <input type="tel" name="donor_phone" placeholder="Phone (optional)"
                        class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:ring-2 focus:ring-blue-500">
                </div>

                <!-- Payment Method (Icons) -->
                <div class="pt-4 border-t border-gray-100">
                    <h3 class="text-sm font-semibold text-gray-700 mb-3">Payment Method</h3>

                    <div class="grid grid-cols-3 gap-3">
                        <!-- GCash -->
                        <div class="payment-option cursor-pointer border-2 border-gray-300 rounded-xl p-3 flex flex-col items-center hover:border-blue-500 hover:bg-blue-50 transition"
                            data-method="gcash" onclick="selectPaymentMethod(this)">
                            <div class="h-10 w-10 mb-2 flex items-center justify-center">
                                <svg viewBox="0 0 48 48" class="h-full w-full">
                                    <rect fill="#2E7CF6" width="48" height="48" rx="8"/>
                                    <text x="24" y="32" font-family="Arial, sans-serif" font-size="20" font-weight="bold" fill="white" text-anchor="middle">G</text>
                                </svg>
                            </div>
                            <span class="text-xs font-medium text-gray-700">GCash</span>
                            <span class="text-[10px] text-gray-500 mt-1">E-Wallet</span>
                        </div>

                        <!-- GrabPay -->
                        <div class="payment-option cursor-pointer border-2 border-gray-300 rounded-xl p-3 flex flex-col items-center hover:border-green-500 hover:bg-green-50 transition"
                            data-method="grabpay" onclick="selectPaymentMethod(this)">
                            <div class="h-10 w-10 mb-2 flex items-center justify-center bg-green-600 rounded-lg">
                                <span class="text-white font-bold text-lg">Grab</span>
                            </div>
                            <span class="text-xs font-medium text-gray-700">GrabPay</span>
                            <span class="text-[10px] text-gray-500 mt-1">E-Wallet</span>
                        </div>

                        <!-- PayMaya -->
                        <div class="payment-option cursor-pointer border-2 border-gray-300 rounded-xl p-3 flex flex-col items-center hover:border-purple-500 hover:bg-purple-50 transition"
                            data-method="paymaya" onclick="selectPaymentMethod(this)">
                            <div class="h-10 w-10 mb-2 flex items-center justify-center">
                                <svg viewBox="0 0 48 48" class="h-full w-full">
                                    <defs>
                                        <linearGradient id="mayaGradient" x1="0%" y1="0%" x2="100%" y2="100%">
                                            <stop offset="0%" style="stop-color:#00D632;stop-opacity:1" />
                                            <stop offset="100%" style="stop-color:#00B528;stop-opacity:1" />
                                        </linearGradient>
                                    </defs>
                                    <rect fill="url(#mayaGradient)" width="48" height="48" rx="8"/>
                                    <text x="24" y="32" font-family="Arial, sans-serif" font-size="18" font-weight="bold" fill="white" text-anchor="middle">Maya</text>
                                </svg>
                            </div>
                            <span class="text-xs font-medium text-gray-700">Maya</span>
                            <span class="text-[10px] text-gray-500 mt-1">E-Wallet</span>
                        </div>
                    </div>

                    <input type="hidden" name="payment_method" id="paymentMethod" required>

                    <!-- Payment method error -->
                    <p id="paymentMethodError" class="text-red-500 text-xs mt-2 hidden">
                        <i class="fas fa-exclamation-circle mr-1"></i>Please select a payment method
                    </p>
                </div>

                <!-- Payment Info -->
                <div class="mt-4 bg-blue-50 border border-blue-200 rounded-lg p-3">
                    <p class="text-xs text-blue-800">
                        <i class="fas fa-info-circle mr-1"></i>
                        <strong>Secure Payment:</strong> You'll be redirected to PayMongo's secure checkout page.
                        Your donation will be recorded on the Lisk blockchain for transparency.
                    </p>
                </div>

                <!-- Buttons -->
                <div class="flex gap-3 pt-5">
                    <button type="submit"
                        class="flex-1 bg-blue-600 hover:bg-blue-700 text-white py-2.5 rounded-lg font-semibold text-sm transition">
                        Proceed to Payment
                    </button>
                    <button type="button" onclick="closeDonationModal()"
                        class="flex-1 bg-gray-100 hover:bg-gray-200 text-gray-700 py-2.5 rounded-lg font-semibold text-sm transition">
                        Cancel
                    </button>
                </div>
            </form>

            <!-- Success Message -->
            <div id="successMessage" class="hidden mt-6 bg-green-50 border border-green-200 rounded-lg p-4">
                <h4 class="font-bold text-green-800 mb-1">✅ Donation Successful!</h4>
                <p class="text-sm text-green-700 mb-1">Your tracking code:
                    <span id="trackingCodeDisplay" class="font-mono font-bold"></span>
                </p>
                <p class="text-xs text-green-600">Blockchain verification in progress...</p>
                <button onclick="window.location.href='#donations'"
                    class="w-full mt-3 bg-green-600 hover:bg-green-700 text-white py-2 rounded-lg font-semibold text-sm transition">
                    View My Donations
                </button>
            </div>

        </div>
    </div>

    <!-- Animations and JS -->
    <style>
        @keyframes fadeIn {
            from {
                opacity: 0;
                transform: translateY(10px);
            }

            to {
                opacity: 1;
                transform: translateY(0);
            }
        }

        .animate-fadeIn {
            animation: fadeIn 0.2s ease-out;
        }

        .payment-option.selected {
            border: 2px solid #2563eb;
            background-color: #eff6ff;
        }
    </style>

    <script>
        function selectPaymentMethod(el) {
            // Remove selected class from all options
            document.querySelectorAll('.payment-option').forEach(opt => {
                opt.classList.remove('selected');
            });

            // Add selected class to clicked option
            el.classList.add('selected');

            // Map UI method names to PayMongo API values
            const methodMap = {
                'gcash': 'gcash',
                'grabpay': 'grab_pay',  // Note: underscore for PayMongo API
                'paymaya': 'paymaya'
            };

            const uiMethod = el.getAttribute('data-method');
            const paymongoMethod = methodMap[uiMethod] || uiMethod;

            // Set hidden input value
            document.getElementById('paymentMethod').value = paymongoMethod;

            // Hide error message if visible
            const errorEl = document.getElementById('paymentMethodError');
            if (errorEl) {
                errorEl.classList.add('hidden');
            }
        }

        // Handle donation form submission with PayMongo integration
        document.addEventListener('DOMContentLoaded', function() {
            const donationForm = document.getElementById('donationForm');

            if (!donationForm) {
                console.error('Donation form not found');
                return;
            }

            donationForm.addEventListener('submit', async function (e) {
                e.preventDefault();

                const submitBtn = e.target.querySelector('button[type="submit"]');
                const originalText = submitBtn.innerHTML;
                submitBtn.disabled = true;
                submitBtn.innerHTML = '<i class="fas fa-spinner fa-spin mr-2"></i>Processing...';

                const formData = new FormData(e.target);
                const data = {
                    barangay_id: formData.get('barangay_id'),
                    amount: parseFloat(formData.get('amount')),
                    donation_type: formData.get('donation_type'),
                    payment_method: formData.get('payment_method'),
                    is_anonymous: false,
                    donor_name: formData.get('donor_name'),
                };

                // Only include email and phone if they have values
                const email = formData.get('donor_email');
                const phone = formData.get('donor_phone');

                if (email && email.trim() !== '') {
                    data.donor_email = email;
                }

                if (phone && phone.trim() !== '') {
                    data.donor_phone = phone;
                }

                // Validation
                if (!data.payment_method) {
                    alert('Please select a payment method');
                    submitBtn.disabled = false;
                    submitBtn.innerHTML = originalText;
                    return;
                }

                if (data.amount < 100) {
                    alert('Minimum donation amount is PHP 100');
                    submitBtn.disabled = false;
                    submitBtn.innerHTML = originalText;
                    return;
                }

                try {
                    // Call backend API to create PayMongo checkout session
                    const response = await fetch('/donations/create-payment', {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/json',
                            'Accept': 'application/json',
                            'X-CSRF-TOKEN': csrfToken
                        },
                        credentials: 'same-origin', // CRITICAL: Include session cookies for authentication
                        body: JSON.stringify(data)
                    });

                    const result = await response.json();

                    if (result.success) {
                        // Store donation info in sessionStorage for when user returns
                        sessionStorage.setItem('pending_donation_id', result.data.donation_id);
                        sessionStorage.setItem('pending_tracking_code', result.data.tracking_code);

                        // Redirect to PayMongo checkout
                        window.location.href = result.data.checkout_url;
                    } else {
                        alert('Error: ' + (result.message || 'Something went wrong'));
                        submitBtn.disabled = false;
                        submitBtn.innerHTML = originalText;
                    }
                } catch (error) {
                    console.error('Payment error:', error);
                    alert('Failed to process payment. Please try again.');
                    submitBtn.disabled = false;
                    submitBtn.innerHTML = originalText;
                }
            });
        });

        function openDonationModal(barangayId = null) {
            const modal = document.getElementById('donationModal');
            modal.style.display = 'flex';

            // Pre-select barangay if provided
            if (barangayId) {
                document.getElementById('barangaySelect').value = barangayId;
            }
        }

        function closeDonationModal() {
            const modal = document.getElementById('donationModal');
            modal.style.display = 'none';

            // Reset form
            document.getElementById('donationForm').reset();
            document.querySelectorAll('.payment-option').forEach(opt => opt.classList.remove('selected'));
        }

        function showSuccessNotification(trackingCode) {
            const notification = document.createElement('div');
            notification.className = 'fixed top-4 right-4 bg-green-500 text-white px-6 py-4 rounded-lg shadow-lg z-50 animate-slide-in';
            notification.innerHTML = `
          <div class="flex items-center gap-3">
              <i class="fas fa-check-circle text-2xl"></i>
              <div>
                  <h4 class="font-bold">Payment Successful!</h4>
                  <p class="text-sm">Tracking: <span class="font-mono">${trackingCode}</span></p>
                  <p class="text-xs mt-1">Recording on blockchain...</p>
              </div>
          </div>
      `;
            document.body.appendChild(notification);

            setTimeout(() => {
                notification.remove();
                switchTab('donations');
                donationsLoaded = false;
                loadMyDonations();
            }, 5000);
        }
    </script>

    @include('partials.footer')
</body>

</html>

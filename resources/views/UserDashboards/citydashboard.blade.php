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
            border-bottom: 3px solid #CA6702;
            color: #CA6702;
        }
        #cityMap { height: 600px; }
        .error-message {
            background-color: #FEE2E2;
            color: #991B1B;
            padding: 1rem;
            border-radius: 0.5rem;
            margin: 1rem 0;
        }

        /* Modal Overlay Styles */
        .modal {
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background-color: rgba(0, 0, 0, 0.5);
            display: flex;
            align-items: center;
            justify-content: center;
            z-index: 9999;
            overflow-y: auto;
            padding: 20px;
        }

        .modal.hidden {
            display: none;
        }

        /* Map Section Styles */
        .city-map-container {
            display: grid;
            grid-template-columns: 1fr 380px;
            gap: 20px;
            height: 700px;
            overflow: visible;
            position: relative;
            z-index: 1;
        }

        .city-map-wrapper {
            background: white;
            border-radius: 12px;
            box-shadow: 0 2px 8px rgba(0,0,0,0.1);
            overflow: hidden;
            position: relative;
            height: 100%;
            z-index: 1;
        }

        #cityBarangayMap {
            width: 100%;
            height: 100%;
        }

        /* Map Legend */
        .city-map-legend-overlay {
            position: absolute;
            bottom: 20px;
            left: 20px;
            background: white;
            padding: 15px 20px;
            border-radius: 10px;
            box-shadow: 0 4px 12px rgba(0,0,0,0.15);
            z-index: 1000;
        }

        .city-legend-title {
            font-weight: 700;
            margin-bottom: 12px;
            font-size: 14px;
            color: #1f2937;
        }

        .city-legend-items {
            display: flex;
            gap: 15px;
            flex-wrap: wrap;
        }

        .city-legend-item {
            display: flex;
            align-items: center;
            gap: 8px;
            font-size: 13px;
            color: #4b5563;
        }

        .city-legend-dot {
            width: 14px;
            height: 14px;
            border-radius: 50%;
            border: 2px solid white;
            box-shadow: 0 1px 3px rgba(0,0,0,0.2);
        }

        /* Summary Panel */
        .city-summary-panel {
            display: flex;
            flex-direction: column;
            gap: 20px;
            overflow: visible;
            height: 100%;
        }

        .city-summary-card {
            background: white;
            border-radius: 12px;
            padding: 20px;
            box-shadow: 0 1px 3px rgba(0,0,0,0.1);
        }

        .city-card-title {
            font-size: 16px;
            font-weight: 700;
            color: #1f2937;
            margin-bottom: 15px;
            display: flex;
            align-items: center;
            gap: 8px;
        }

        .city-card-icon {
            width: 28px;
            height: 28px;
            background: #CA6702;
            border-radius: 8px;
            display: flex;
            align-items: center;
            justify-content: center;
            color: white;
            font-size: 14px;
        }

        /* Stats Grid */
        .city-stats-grid-summary {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 10px;
        }

        .city-stat-box {
            text-align: center;
            padding: 12px 8px;
            background: #f9fafb;
            border-radius: 8px;
            border: 1px solid #e5e7eb;
        }

        .city-stat-box .stat-number {
            font-size: 20px;
            font-weight: 700;
            color: #1f2937;
            margin-bottom: 4px;
        }

        .city-stat-box .stat-label {
            font-size: 10px;
            color: #6b7280;
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }

        /* Barangay List */
        .city-barangay-list {
            display: flex;
            flex-direction: column;
            gap: 10px;
            max-height: 400px;
            overflow-y: auto;
            overflow-x: hidden;
        }

        .city-barangay-list::-webkit-scrollbar {
            width: 6px;
        }

        .city-barangay-list::-webkit-scrollbar-track {
            background: #f1f1f1;
            border-radius: 10px;
        }

        .city-barangay-list::-webkit-scrollbar-thumb {
            background: #cbd5e1;
            border-radius: 10px;
        }

        .city-barangay-list::-webkit-scrollbar-thumb:hover {
            background: #94a3b8;
        }

        .city-barangay-item {
            padding: 12px;
            border: 1px solid #e5e7eb;
            border-radius: 8px;
            display: flex;
            justify-content: space-between;
            align-items: center;
            transition: all 0.3s;
            cursor: pointer;
        }

        .city-barangay-item:hover {
            border-color: #CA6702;
            box-shadow: 0 2px 8px rgba(202, 103, 2, 0.1);
            transform: translateX(2px);
        }

        .city-barangay-info-summary {
            flex: 1;
        }

        .city-barangay-name-summary {
            font-weight: 600;
            color: #1f2937;
            margin-bottom: 4px;
            font-size: 13px;
        }

        .city-barangay-meta {
            font-size: 11px;
            color: #6b7280;
        }

        .city-status-badge {
            padding: 4px 10px;
            border-radius: 12px;
            font-size: 10px;
            font-weight: 600;
            text-transform: uppercase;
        }

        .city-status-badge.safe {
            background: #d1fae5;
            color: #065f46;
        }

        .city-status-badge.warning {
            background: #fef3c7;
            color: #92400e;
        }

        .city-status-badge.critical {
            background: #fed7aa;
            color: #9a3412;
        }

        .city-status-badge.emergency {
            background: #fee2e2;
            color: #991b1b;
        }

        /* Ensure no horizontal scroll */
        #map-tab {
            overflow-x: hidden;
            overflow-y: auto;
        }
    </style>


</head>
<body class="bg-gray-50">

    <!-- Top Header -->
    <div class="bg-[#CA6702] text-white px-6 py-4 flex justify-between items-center">
        <div>
            <h1 class="text-2xl font-bold">City Dashboard (LDRRMO)</h1>
            <p class="text-sm opacity-90">Cebu City Disaster Management / Public Works</p>
        </div>
        <div class="flex items-center gap-4">
            <div class="text-right">
                <p class="text-sm opacity-90">Logged in as LDRRMO</p>
                <p class="font-medium">{{ session('user_name', 'Admin') }}</p>
            </div>

            <!-- Messages Toggle Button -->
            <div class="relative">
                <button id="conversations-toggle"
                        onclick="toggleConversationsSidebar()"
                        class="relative bg-white/20 hover:bg-white/30 p-3 rounded-lg transition"
                        title="Messages">
                    <i class="fas fa-comments text-xl"></i>
                    <!-- Active Conversations Badge -->
                    <span id="conversations-badge-header"
                          class="hidden absolute -top-1 -right-1 bg-red-500 text-white text-xs font-bold rounded-full h-5 w-5 flex items-center justify-center">
                        0
                    </span>
                </button>
            </div>

            <!-- Notification Bell -->
            <div class="relative">
                <button id="notification-bell"
                        onclick="toggleNotifications()"
                        class="relative bg-white/20 hover:bg-white/30 p-3 rounded-lg transition">
                    <i class="fas fa-bell text-xl"></i>
                    <span id="notification-badge"
                          class="hidden absolute -top-1 -right-1 bg-red-500 text-white text-xs font-bold rounded-full w-5 h-5 flex items-center justify-center">
                        0
                    </span>
                </button>

                <!-- Notifications Dropdown -->
                <div id="notifications-dropdown"
                     class="hidden absolute right-0 mt-2 w-96 bg-white rounded-lg shadow-xl border max-h-96 overflow-hidden" style="z-index: 9999;">

                    <!-- Header -->
                    <div class="px-4 py-3 border-b bg-gray-50 flex items-center justify-between">
                        <h3 class="font-bold text-gray-900">Notifications</h3>
                        <div class="flex items-center gap-2">
                            <span id="notification-count" class="text-xs text-gray-500">0 unread</span>
                            <button onclick="markAllAsRead()"
                                    class="text-xs text-[#CA6702] hover:text-[#BB3E03] font-semibold">
                                Mark all read
                            </button>
                        </div>
                    </div>

            <!-- Filter Tabs -->
            <div class="flex border-b bg-gray-50">
                <button onclick="filterNotifications('all')"
                        id="notif-filter-all"
                        class="flex-1 px-4 py-2 text-sm font-semibold border-b-2 border-[#CA6702] text-[#CA6702]">
                    All
                </button>
                <button onclick="filterNotifications('match_request')"
                        id="notif-filter-match_request"
                        class="flex-1 px-4 py-2 text-sm font-semibold border-b-2 border-transparent text-gray-600 hover:text-[#CA6702]">
                    Matches
                </button>
                <button onclick="filterNotifications('match_accepted')"
                        id="notif-filter-match_accepted"
                        class="flex-1 px-4 py-2 text-sm font-semibold border-b-2 border-transparent text-gray-600 hover:text-[#CA6702]">
                    Accepted
                </button>
                <button onclick="filterNotifications('message')"
                        id="notif-filter-message"
                        class="flex-1 px-4 py-2 text-sm font-semibold border-b-2 border-transparent text-gray-600 hover:text-[#CA6702]">
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
                        class="text-sm text-[#CA6702] hover:text-[#BB3E03] font-semibold">
                    View All Notifications
                </button>
            </div>
        </div>
            </div>

            <form action="{{ route('logout') }}" method="POST">
                @csrf
                <button type="submit" class="bg-white/20 hover:bg-white/30 px-4 py-2 rounded text-sm transition">
                    <i class="fas fa-sign-out-alt mr-1"></i> Logout
                </button>
            </form>
        </div>
    </div>

    <!-- Main Layout with Sidebar -->
    <div class="flex h-screen overflow-hidden">
        <!-- Right Sidebar - Conversations (Facebook-style) -->
        <div id="conversations-sidebar"
             class="w-80 bg-white border-l border-gray-200 flex-shrink-0 flex flex-col transition-all duration-300 translate-x-full fixed right-0 z-[9999] h-full">

            <!-- Sidebar Header -->
            <div class="px-4 py-4 border-b border-gray-200 flex items-center justify-between bg-white">
                <h2 class="text-xl font-bold text-gray-900">Messages</h2>
                <span id="conversations-badge-sidebar"
                      class="hidden px-2 py-1 bg-red-500 text-white text-xs font-bold rounded-full">
                    0
                </span>
            </div>

            <!-- Search Bar (Optional) -->
            <div class="px-3 py-2 border-b border-gray-100">
                <div class="relative">
                    <input type="text"
                           id="conversation-search"
                           placeholder="Search conversations..."
                           onkeyup="filterConversations()"
                           class="w-full pl-10 pr-4 py-2 bg-gray-100 rounded-full text-sm focus:outline-none focus:ring-2 focus:ring-indigo-500">
                    <i class="fas fa-search absolute left-3 top-1/2 transform -translate-y-1/2 text-gray-400 text-sm"></i>
                </div>
            </div>

            <!-- Conversations List -->
            <div id="conversations-sidebar-list" class="flex-1 overflow-y-auto">
                <div class="text-center py-12 text-gray-500">
                    <div class="inline-block animate-spin rounded-full h-10 w-10 border-b-2 border-[#CA6702] mb-3"></div>
                    <p class="text-sm">Loading conversations...</p>
                </div>
            </div>

            <!-- Sidebar Footer -->
            <div class="px-4 py-3 border-t border-gray-200 bg-gray-50">
                <button onclick="switchTab('my-matches', event)"
                        class="w-full text-center text-[#CA6702] hover:text-[#BB3E03] font-semibold text-sm py-2 hover:bg-orange-50 rounded transition">
                    <i class="fas fa-th-large mr-2"></i>View All Matches
                </button>
            </div>
        </div>

        <!-- Sidebar Overlay -->
        <div id="sidebar-overlay"
             onclick="toggleConversationsSidebar()"
             class="hidden fixed inset-0 bg-black bg-opacity-50 z-[9998]"></div>

        <!-- Main Content Area -->
        <div class="flex-1 overflow-y-auto bg-gray-50">
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
                <button onclick="switchTab('my-matches', event)" class="tab-btn px-6 py-4 font-medium text-gray-700 transition" data-tab="my-matches">
                    <i class="fas fa-handshake mr-2"></i> My Matches
                </button>
                <button onclick="switchTab('analytics', event)" class="tab-btn px-6 py-4 font-medium text-gray-700 transition">
                    <i class="fas fa-chart-bar mr-2"></i> Analytics
                </button>
                <button onclick="switchTab('barangays', event)" class="tab-btn px-6 py-4 font-medium text-gray-700 transition">
                    <i class="fas fa-list mr-2"></i> Barangays
                </button>
            </div>
        </div>

        <!-- TAB 1: Interactive Map -->
        <div id="map-tab" class="tab-content active bg-white rounded-b-xl shadow-sm p-6">
            <h2 class="text-2xl font-bold mb-4">Live Disaster Map of Cebu</h2>
            <p class="text-gray-600 text-sm mb-6">Real-time status of all barangays across Cebu City</p>

            {{-- Map Container with Summary Panel --}}
            <div class="city-map-container">
                {{-- Interactive Map --}}
                <div class="city-map-wrapper">
                    <div id="cityBarangayMap"></div>

                    {{-- Map Legend Overlay --}}
                    <div class="city-map-legend-overlay">
                        <div class="city-legend-title">📍 Disaster Status</div>
                        <div class="city-legend-items">
                            <div class="city-legend-item">
                                <span class="city-legend-dot" style="background: #10b981;"></span>
                                <span>Safe</span>
                            </div>
                            <div class="city-legend-item">
                                <span class="city-legend-dot" style="background: #f59e0b;"></span>
                                <span>Warning</span>
                            </div>
                            <div class="city-legend-item">
                                <span class="city-legend-dot" style="background: #f97316;"></span>
                                <span>Critical</span>
                            </div>
                            <div class="city-legend-item">
                                <span class="city-legend-dot" style="background: #ef4444;"></span>
                                <span>Emergency</span>
                            </div>
                        </div>
                    </div>
                </div>

                {{-- Summary Panel --}}
                <div class="city-summary-panel">
                    {{-- City Statistics --}}
                    <div class="city-summary-card">
                        <div class="city-card-title">
                            <div class="city-card-icon">📊</div>
                            City Overview
                        </div>
                        <div class="city-stats-grid-summary">
                            <div class="city-stat-box">
                                <div class="stat-number" id="cityTotalDonations">₱0</div>
                                <div class="stat-label">Total Donations</div>
                            </div>
                            <div class="city-stat-box">
                                <div class="stat-number" id="cityAffectedFamilies">0</div>
                                <div class="stat-label">Affected Families</div>
                            </div>
                            <div class="city-stat-box">
                                <div class="stat-number" id="cityAffectedBarangays">0</div>
                                <div class="stat-label">Barangays Affected</div>
                            </div>
                            <div class="city-stat-box">
                                <div class="stat-number" id="cityTotalDonors">0</div>
                                <div class="stat-label">Donors</div>
                            </div>
                        </div>
                    </div>

                    {{-- Barangays List (Needing Help + Safe) --}}
                    <div class="city-summary-card" style="flex: 1; min-height: 0;">
                        <div class="city-card-title">
                            <div class="city-card-icon">⚠️</div>
                            All Barangays Status
                        </div>
                        <div class="city-barangay-list" id="cityBarangayList">
                            <div style="text-align: center; padding: 20px; color: #6b7280;">
                                <i class="fas fa-spinner fa-spin"></i> Loading...
                            </div>
                        </div>
                    </div>
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
                    <button onclick="filterResourceNeeds('all')" id="resource-tab-all" class="resource-filter-tab pb-4 px-1 border-b-2 font-medium text-sm border-[#CA6702] text-[#CA6702]">
                        All Requests
                        <span id="resource-count-all" class="ml-2 px-2 py-0.5 bg-gray-200 rounded-full text-xs">0</span>
                    </button>
                    <button onclick="filterResourceNeeds('pending')" id="resource-tab-pending" class="resource-filter-tab pb-4 px-1 border-b-2 font-medium text-sm text-gray-500 border-transparent hover:text-gray-700">
                        Pending Verification
                        <span id="resource-count-pending" class="ml-2 px-2 py-0.5 bg-yellow-100 text-yellow-800 rounded-full text-xs">0</span>
                    </button>
                    <button onclick="filterResourceNeeds('verified')" id="resource-tab-verified" class="resource-filter-tab pb-4 px-1 border-b-2 font-medium text-sm text-gray-500 border-transparent hover:text-gray-700">
                        Verified Requests
                        <span id="resource-count-verified" class="ml-2 px-2 py-0.5 bg-green-100 text-green-800 rounded-full text-xs">0</span>
                    </button>
                    <button onclick="filterResourceNeeds('rejected')" id="resource-tab-rejected" class="resource-filter-tab pb-4 px-1 border-b-2 font-medium text-sm text-gray-500 border-transparent hover:text-gray-700">
                        Rejected
                        <span id="resource-count-rejected" class="ml-2 px-2 py-0.5 bg-red-100 text-red-800 rounded-full text-xs">0</span>
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

        <!-- Verification Modal -->
        <div id="verificationModal" class="hidden fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50">
            <div class="bg-white rounded-lg p-6 max-w-md w-full mx-4">
                <h3 class="text-lg font-semibold mb-4" id="verificationModalTitle">Verify Resource Request</h3>
                <div id="verificationModalContent"></div>
                <div class="mt-6 flex justify-end space-x-3">
                    <button onclick="closeVerificationModal()" class="px-4 py-2 bg-gray-200 text-gray-700 rounded hover:bg-gray-300">Cancel</button>
                    <button id="rejectBtn" onclick="handleReject()" class="hidden px-4 py-2 bg-red-600 text-white rounded hover:bg-red-700">Reject</button>
                    <button id="verifyBtn" onclick="handleVerify()" class="hidden px-4 py-2 bg-green-600 text-white rounded hover:bg-green-700">Verify</button>
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
                    class="px-4 py-2 rounded-lg font-semibold text-sm transition bg-[#CA6702] text-white">
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
        <div class="bg-gradient-to-br from-[#CA6702] to-[#BB3E03] rounded-lg p-4 text-white">
            <div class="flex items-center justify-between">
                <div>
                    <p class="opacity-90 text-sm">Total Matches</p>
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

<!-- Match Details Modal -->
<div id="matchDetailsModal" class="modal hidden">
    <div class="bg-white rounded-lg shadow-2xl max-w-6xl w-full mx-4 max-h-[90vh] overflow-y-auto">
        <!-- Modal Header -->
        <div class="sticky top-0 bg-white border-b px-6 py-4 flex justify-between items-center z-10">
            <h3 class="text-2xl font-bold text-gray-800">
                <i class="fas fa-info-circle text-blue-600 mr-2"></i>
                Match Details & Logistics
            </h3>
            <button onclick="closeMatchDetailsModal()" class="text-gray-400 hover:text-gray-600 transition">
                <i class="fas fa-times text-2xl"></i>
            </button>
        </div>

        <!-- Modal Body -->
        <div id="matchDetailsContent" class="p-6">
            <!-- Loading State -->
            <div class="text-center py-12">
                <div class="inline-block animate-spin rounded-full h-12 w-12 border-b-2 border-blue-600 mb-4"></div>
                <p class="text-gray-600">Loading match details...</p>
            </div>
        </div>

        <!-- Modal Footer -->
        <div class="sticky bottom-0 bg-gray-50 border-t px-6 py-4 flex justify-end gap-3">
            <button onclick="closeMatchDetailsModal()" class="px-6 py-2 border border-gray-300 rounded-lg hover:bg-gray-100 transition">
                Close
            </button>
            <button id="initiateMatchFromDetails" class="px-6 py-2 bg-green-600 text-white rounded-lg hover:bg-green-700 transition font-semibold">
                <i class="fas fa-handshake mr-2"></i>Initiate Match
            </button>
        </div>
    </div>
</div>

<!-- Messenger-Style Chat Boxes Container (Bottom Right) -->
<div id="chat-boxes-container" class="fixed bottom-0 right-0 flex flex-row-reverse gap-2 p-4 z-[9999] pointer-events-none">
    <!-- Chat boxes will be dynamically inserted here -->
</div>

<!-- Chat Box Template (Hidden) -->
<template id="chat-box-template">
    <div class="chat-box w-80 bg-white rounded-t-lg shadow-2xl flex flex-col pointer-events-auto" data-match-id="">
        <!-- Chat Header -->
        <div class="chat-header bg-gradient-to-r from-indigo-600 to-purple-600 px-4 py-3 flex items-center justify-between rounded-t-lg cursor-pointer">
            <div class="flex items-center gap-2 flex-1 min-w-0">
                <div class="w-8 h-8 rounded-full bg-white/20 flex items-center justify-center text-white font-bold text-xs">
                    <i class="fas fa-handshake"></i>
                </div>
                <div class="flex-1 min-w-0">
                    <p class="chat-title text-sm font-semibold text-white truncate">Loading...</p>
                    <p class="chat-subtitle text-xs text-indigo-100 truncate">Group chat</p>
                </div>
            </div>
            <div class="flex items-center gap-2">
                <button onclick="minimizeChatBox(this)" class="minimize-btn text-white hover:bg-white/20 p-1 rounded transition">
                    <i class="fas fa-minus text-sm"></i>
                </button>
                <button onclick="closeChatBox(this)" class="close-btn text-white hover:bg-white/20 p-1 rounded transition">
                    <i class="fas fa-times text-sm"></i>
                </button>
            </div>
        </div>

        <!-- Chat Body (Messages) -->
        <div class="chat-body overflow-y-auto p-3 bg-gray-50 space-y-2" style="height: 400px; max-height: 400px; min-height: 400px;">
            <div class="text-center py-12">
                <div class="inline-block animate-spin rounded-full h-8 w-8 border-b-2 border-indigo-600 mb-2"></div>
                <p class="text-xs text-gray-600">Loading conversation...</p>
            </div>
        </div>

        <!-- Chat Footer (Input) -->
        <div class="chat-footer border-t bg-white p-3 rounded-b-lg">
            <form class="send-message-form flex gap-2" onsubmit="sendChatMessage(event, this)">
                <input
                    type="text"
                    class="message-input flex-1 px-3 py-2 text-sm border border-gray-300 rounded-full focus:ring-2 focus:ring-indigo-500 focus:border-transparent"
                    placeholder="Type a message..."
                    required
                    maxlength="1000"
                />
                <button
                    type="submit"
                    class="px-4 py-2 bg-indigo-600 text-white rounded-full hover:bg-indigo-700 transition text-sm font-semibold">
                    <i class="fas fa-paper-plane"></i>
                </button>
            </form>
        </div>
    </div>
</template>


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

                const data = await response.json();

                if (!response.ok) {
                    // Try to get error message from response body
                    const errorMessage = data.message || data.error || response.statusText;
                    throw new Error(errorMessage);
                }

                return data;
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
        let loadedTabs = { map: false, resources: false, analytics: false, barangays: false };

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
            // Note: Map is now handled by city-dashboard-map.js
            // This function is kept for compatibility but does nothing
            console.log('initMap called - map initialization handled by city-dashboard-map.js');
            return true;
        }

     async function loadMapData() {
    // Note: Map data loading is now handled by city-dashboard-map.js
    // This function is kept for compatibility but does nothing
    console.log('loadMapData called - map data loading handled by city-dashboard-map.js');
    return;
}

        // ============================================
        // RESOURCE NEEDS & MATCHING
        // ============================================
        let currentResourceNeeds = [];
        let allResourceNeeds = [];
        let currentResourceNeedsFilter = 'all';
        let currentVerificationNeedId = null;

        async function loadResourceNeeds() {
            const container = document.getElementById('resourceNeedsList');
            container.innerHTML = '<div class="text-center py-8"><i class="fas fa-spinner fa-spin text-3xl text-gray-400"></i><p class="mt-2 text-gray-600">Loading resource needs...</p></div>';

            try {
                const filter = currentResourceNeedsFilter || 'all';
                const response = await fetchAPI(`/api/ldrrmo/resource-needs?filter=${filter}`);

                allResourceNeeds = response;
                currentResourceNeeds = response;

                // Update counts
                updateResourceNeedsCounts();

                if (!response || response.length === 0) {
                    container.innerHTML = `
                        <div class="text-center py-12">
                            <i class="fas fa-check-circle text-6xl text-green-500 mb-4"></i>
                            <h3 class="text-xl font-semibold text-gray-800 mb-2">No Resource Needs Found</h3>
                            <p class="text-gray-600">No requests match the current filter</p>
                        </div>
                    `;
                    return;
                }

                container.innerHTML = response.map(need => {
                    const urgencyColors = {
                        'critical': 'bg-red-100 text-red-800 border-red-300',
                        'high': 'bg-orange-100 text-orange-800 border-orange-300',
                        'medium': 'bg-yellow-100 text-yellow-800 border-yellow-300',
                        'low': 'bg-green-100 text-green-800 border-green-300'
                    };

                    const verificationBadges = {
                        'pending': 'bg-yellow-100 text-yellow-800',
                        'verified': 'bg-green-100 text-green-800',
                        'rejected': 'bg-red-100 text-red-800'
                    };

                    const verificationIcons = {
                        'pending': '⏳',
                        'verified': '✓',
                        'rejected': '✗'
                    };

                    const urgencyClass = urgencyColors[need.urgency] || urgencyColors['low'];
                    const verificationClass = verificationBadges[need.verification_status] || verificationBadges['pending'];
                    const verificationIcon = verificationIcons[need.verification_status] || '⏳';

                    // Rejection reason display
                    const rejectionHtml = (need.verification_status === 'rejected' && need.rejection_reason) ? `
                        <div class="mt-3 p-3 bg-red-50 border border-red-200 rounded">
                            <p class="text-sm text-red-800"><strong>Rejection Reason:</strong> ${escapeHtml(need.rejection_reason)}</p>
                        </div>
                    ` : '';

                    // Action buttons based on verification status
                    let actionButtons = '';
                    if (need.verification_status === 'pending') {
                        actionButtons = `
                            <button onclick="openVerificationModal(${need.id}, 'verify')"
                                class="px-4 py-2 bg-green-600 text-white text-sm rounded hover:bg-green-700 whitespace-nowrap">
                                ✓ Verify
                            </button>
                            <button onclick="openVerificationModal(${need.id}, 'reject')"
                                class="px-4 py-2 bg-red-600 text-white text-sm rounded hover:bg-red-700 whitespace-nowrap">
                                ✗ Reject
                            </button>
                        `;
                    } else if (need.verification_status === 'verified') {
                        actionButtons = `
                            <button onclick="revertVerification(${need.id})"
                                class="px-4 py-2 bg-yellow-600 text-white text-sm rounded hover:bg-yellow-700 whitespace-nowrap">
                                ↺ Revert
                            </button>
                            <button onclick="findMatchesForNeed(${need.id})"
                                class="px-4 py-2 bg-blue-600 text-white text-sm rounded hover:bg-blue-700 whitespace-nowrap">
                                🔍 Find Match
                            </button>
                        `;
                    } else {
                        actionButtons = `
                            <button onclick="revertVerification(${need.id})"
                                class="px-4 py-2 bg-yellow-600 text-white text-sm rounded hover:bg-yellow-700 whitespace-nowrap">
                                ↺ Revert
                            </button>
                        `;
                    }

                    return `
                        <div class="border-2 ${urgencyClass} rounded-xl p-6 hover:shadow-lg transition-all">
                            <div class="flex items-start justify-between gap-4">
                                <div class="flex-1">
                                    <div class="flex items-center gap-3 mb-3 flex-wrap">
                                        <h3 class="text-xl font-bold text-gray-900">${escapeHtml(need.barangay_name)}</h3>
                                        <span class="px-3 py-1 ${urgencyClass} text-xs font-bold rounded-full uppercase border">
                                            ${escapeHtml(need.urgency)}
                                        </span>
                                        <span class="px-3 py-1 ${verificationClass} text-xs font-bold rounded-full">
                                            ${verificationIcon} ${escapeHtml(need.verification_status).toUpperCase()}
                                        </span>
                                        <span class="px-3 py-1 bg-blue-100 text-blue-800 text-xs font-semibold rounded-full">
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
                                    ${rejectionHtml}
                                </div>

                                <div class="flex flex-col gap-2 ml-4">
                                    ${actionButtons}
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

        // Filter resource needs by verification status
        function filterResourceNeeds(filter) {
            currentResourceNeedsFilter = filter;

            // Update active tab styling
            document.querySelectorAll('.resource-filter-tab').forEach(btn => {
                btn.classList.remove('border-[#CA6702]', 'text-[#CA6702]');
                btn.classList.add('border-transparent', 'text-gray-500');
            });

            const activeTab = document.getElementById(`resource-tab-${filter}`);
            if (activeTab) {
                activeTab.classList.add('border-[#CA6702]', 'text-[#CA6702]');
                activeTab.classList.remove('border-transparent', 'text-gray-500');
            }

            loadResourceNeeds();
        }

        // Update resource needs counts
        async function updateResourceNeedsCounts() {
            try {
                const allResponse = await fetchAPI('/api/ldrrmo/resource-needs?filter=all');
                const pendingResponse = await fetchAPI('/api/ldrrmo/resource-needs?filter=pending');
                const verifiedResponse = await fetchAPI('/api/ldrrmo/resource-needs?filter=verified');
                const rejectedResponse = await fetchAPI('/api/ldrrmo/resource-needs?filter=rejected');

                document.getElementById('resource-count-all').textContent = allResponse.length;
                document.getElementById('resource-count-pending').textContent = pendingResponse.length;
                document.getElementById('resource-count-verified').textContent = verifiedResponse.length;
                document.getElementById('resource-count-rejected').textContent = rejectedResponse.length;
            } catch (error) {
                console.error('Error updating counts:', error);
            }
        }

        // Open verification modal
        function openVerificationModal(needId, action) {
            currentVerificationNeedId = needId;
            const need = currentResourceNeeds.find(n => n.id === needId);
            const modal = document.getElementById('verificationModal');
            const title = document.getElementById('verificationModalTitle');
            const content = document.getElementById('verificationModalContent');
            const verifyBtn = document.getElementById('verifyBtn');
            const rejectBtn = document.getElementById('rejectBtn');

            if (action === 'reject') {
                title.textContent = 'Reject Resource Request';
                content.innerHTML = `
                    <p class="text-gray-700 mb-4">Please provide a reason for rejecting this request:</p>
                    <textarea id="rejectionReason" class="w-full border rounded p-2 h-24 focus:ring-2 focus:ring-red-500 focus:border-red-500" placeholder="Enter rejection reason..."></textarea>
                    <div class="mt-3 p-3 bg-gray-50 rounded text-sm">
                        <p><strong>Barangay:</strong> ${escapeHtml(need.barangay_name)}</p>
                        <p><strong>Category:</strong> ${escapeHtml(need.category)}</p>
                        <p><strong>Quantity:</strong> ${escapeHtml(need.quantity)}</p>
                    </div>
                `;
                verifyBtn.classList.add('hidden');
                rejectBtn.classList.remove('hidden');
            } else {
                title.textContent = 'Verify Resource Request';
                content.innerHTML = `
                    <p class="text-gray-700 mb-4">Are you sure you want to verify this resource request?</p>
                    <div class="bg-gray-50 p-3 rounded">
                        <p class="mb-2"><strong>Barangay:</strong> ${escapeHtml(need.barangay_name)}</p>
                        <p class="mb-2"><strong>Category:</strong> ${escapeHtml(need.category)}</p>
                        <p class="mb-2"><strong>Quantity:</strong> ${escapeHtml(need.quantity)}</p>
                        <p><strong>Description:</strong> ${escapeHtml(need.description)}</p>
                    </div>
                `;
                verifyBtn.classList.remove('hidden');
                rejectBtn.classList.add('hidden');
            }

            modal.classList.remove('hidden');
        }

        // Close verification modal
        function closeVerificationModal() {
            document.getElementById('verificationModal').classList.add('hidden');
            currentVerificationNeedId = null;
        }

        // Handle verify action
        async function handleVerify() {
            if (!currentVerificationNeedId) return;

            try {
                const response = await fetchAPI(`/api/ldrrmo/resource-needs/${currentVerificationNeedId}/verify`, {
                    method: 'POST',
                    body: JSON.stringify({ action: 'verify' })
                });

                if (response.success) {
                    alert('✅ Resource need verified successfully!');
                    closeVerificationModal();
                    loadResourceNeeds();
                } else {
                    alert('❌ Error: ' + response.message);
                }
            } catch (error) {
                console.error('Error verifying need:', error);
                alert('Failed to verify resource need');
            }
        }

        // Handle reject action
        async function handleReject() {
            if (!currentVerificationNeedId) return;

            const reason = document.getElementById('rejectionReason').value.trim();
            if (!reason) {
                alert('⚠️ Please provide a rejection reason');
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
                    alert('✅ Resource need rejected');
                    closeVerificationModal();
                    loadResourceNeeds();
                } else {
                    alert('❌ Error: ' + response.message);
                }
            } catch (error) {
                console.error('Error rejecting need:', error);
                alert('Failed to reject resource need');
            }
        }

        // Revert verification status
        async function revertVerification(needId) {
            const confirmed = await confirm('Are you sure you want to revert this to pending status?');
            if (!confirmed) return;

            try {
                const response = await fetchAPI(`/api/ldrrmo/resource-needs/${needId}/revert`, {
                    method: 'POST'
                });

                if (response.success) {
                    alert('✅ Verification status reverted to pending');
                    loadResourceNeeds();
                } else {
                    alert('❌ Error: ' + response.message);
                }
            } catch (error) {
                console.error('Error reverting verification:', error);
                alert('Failed to revert verification');
            }
        }

        // Find matches for a specific need (renamed to avoid conflict)
        function findMatchesForNeed(needId) {
            findMatches(needId);
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
                            ${escapeHtml(match.donation.items_description || match.donation.item_name || 'N/A')}
                        </p>
                        <p class="text-sm text-gray-600 mb-2">
                            <i class="fas fa-cubes mr-1"></i>
                            Available: <span class="font-semibold">${escapeHtml(match.donation.quantity || '0')}</span>
                        </p>

                        <div class="flex items-center gap-2 text-xs text-gray-500 mb-2">
                            <span><i class="fas fa-user mr-1"></i>${escapeHtml(match.donation.donor_name || 'Anonymous')}</span>
                            <span><i class="fas fa-calendar mr-1"></i>${escapeHtml(match.donation.recorded_at || 'N/A')}</span>
                        </div>

                        <div class="mt-2">
                            ${(match.can_fully_fulfill || match.can_fulfill) ?
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
                        <button onclick="contactBarangay(${need.id}, ${match.donation.id}, '${match.barangay.id || ''}', '${escapeHtml(match.barangay.name || 'Unknown')}', ${match.match_score || 0}, ${match.can_fully_fulfill || match.can_fulfill || false})"
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

    // Update conversation icon badge (show active matches with conversations)
    const activeMatches = matches.filter(m =>
        (m.status === 'pending' || m.status === 'accepted') && m.has_conversation
    ).length;

    const conversationBadge = document.getElementById('conversations-badge-header');
    if (conversationBadge) {
        if (activeMatches > 0) {
            conversationBadge.textContent = activeMatches;
            conversationBadge.classList.remove('hidden');
        } else {
            conversationBadge.classList.add('hidden');
        }
    }
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

            <div class="flex gap-2 justify-end items-center">
                ${match.status === 'pending' ? `
                    <span class="text-xs text-gray-500 mr-2">
                        <i class="fas fa-info-circle"></i> Waiting for barangay response
                    </span>
                    <button onclick="cancelMatch(${match.id})"
                            class="px-4 py-2 bg-red-100 text-red-700 rounded-lg hover:bg-red-200 transition text-sm font-semibold">
                        <i class="fas fa-times mr-1"></i> Cancel Request
                    </button>
                ` : ''}

                ${match.status === 'rejected' ? `
                    <span class="text-xs text-red-600 mr-2">
                        <i class="fas fa-ban"></i> Rejected by ${match.donating_barangay.name}
                    </span>
                ` : ''}

                ${match.status === 'accepted' ? `
                    <span class="text-xs text-green-600 mr-2">
                        <i class="fas fa-check-circle"></i> Accepted - Awaiting completion
                    </span>
                ` : ''}

                ${match.status === 'cancelled' ? `
                    <span class="text-xs text-gray-600 mr-2">
                        <i class="fas fa-ban"></i> Cancelled by LDRRMO
                    </span>
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
        btn.classList.remove('bg-[#CA6702]', 'text-white');
        btn.classList.add('bg-gray-200', 'text-gray-700');
    });
    document.getElementById(`my-matches-filter-${status}`).classList.remove('bg-gray-200', 'text-gray-700');
    document.getElementById(`my-matches-filter-${status}`).classList.add('bg-[#CA6702]', 'text-white');

    // Reload matches
    loadMyMatches();
}

async function cancelMatch(matchId) {
    const confirmed = await confirm('⚠️ Are you sure you want to cancel this match request?\n\nThis action cannot be undone and both barangays will be notified.');
    if (!confirmed) {
        return;
    }

    // Show loading state
    const cancelBtn = event.target.closest('button');
    const originalText = cancelBtn.innerHTML;
    cancelBtn.disabled = true;
    cancelBtn.innerHTML = '<i class="fas fa-spinner fa-spin mr-1"></i> Cancelling...';

    try {
        const response = await fetchAPI(`/api/ldrrmo/matches/${matchId}/cancel`, {
            method: 'POST'
        });

        if (response.success) {
            // Show success message
            alert('✅ Match request cancelled successfully!\n\nBoth barangays have been notified.');
            // Reload matches to update the list
            loadMyMatches();
        } else {
            // Show error message
            alert('❌ Error: ' + (response.message || 'Failed to cancel match'));
            // Restore button
            cancelBtn.disabled = false;
            cancelBtn.innerHTML = originalText;
        }
    } catch (error) {
        console.error('Error cancelling match:', error);
        alert('❌ Failed to cancel match. Please try again.\n\nError: ' + (error.message || 'Network error'));
        // Restore button
        cancelBtn.disabled = false;
        cancelBtn.innerHTML = originalText;
    }
}

// Messenger-Style Chat Boxes
let openChatBoxes = new Map(); // matchId -> { element, interval }

function viewConversation(matchId) {
    // Check if chat box already exists
    if (openChatBoxes.has(matchId)) {
        // If minimized, maximize it
        const existingBox = openChatBoxes.get(matchId).element;
        const chatBody = existingBox.querySelector('.chat-body');
        const chatFooter = existingBox.querySelector('.chat-footer');
        if (chatBody.classList.contains('hidden')) {
            chatBody.classList.remove('hidden');
            chatFooter.classList.remove('hidden');
        }
        // Bring to front by re-appending
        existingBox.parentElement.appendChild(existingBox);
        return;
    }

    // Create new chat box from template
    const template = document.getElementById('chat-box-template');
    const chatBox = template.content.cloneNode(true).querySelector('.chat-box');
    chatBox.setAttribute('data-match-id', matchId);

    // Add to container
    const container = document.getElementById('chat-boxes-container');
    container.appendChild(chatBox);

    // Load conversation
    loadChatConversation(matchId, chatBox);

    // Start auto-refresh
    const refreshInterval = setInterval(() => {
        loadChatConversation(matchId, chatBox, true);
    }, 5000);

    // Store reference
    openChatBoxes.set(matchId, {
        element: chatBox,
        interval: refreshInterval
    });
}

function closeChatBox(button) {
    const chatBox = button.closest('.chat-box');
    const matchId = parseInt(chatBox.getAttribute('data-match-id'));

    // Clear interval
    if (openChatBoxes.has(matchId)) {
        clearInterval(openChatBoxes.get(matchId).interval);
        openChatBoxes.delete(matchId);
    }

    // Remove element
    chatBox.remove();
}

function minimizeChatBox(button) {
    const chatBox = button.closest('.chat-box');
    const chatBody = chatBox.querySelector('.chat-body');
    const chatFooter = chatBox.querySelector('.chat-footer');
    const icon = button.querySelector('i');

    if (chatBody.classList.contains('hidden')) {
        // Maximize
        chatBody.classList.remove('hidden');
        chatFooter.classList.remove('hidden');
        icon.classList.remove('fa-window-maximize');
        icon.classList.add('fa-minus');
    } else {
        // Minimize
        chatBody.classList.add('hidden');
        chatFooter.classList.add('hidden');
        icon.classList.remove('fa-minus');
        icon.classList.add('fa-window-maximize');
    }
}

async function loadChatConversation(matchId, chatBox, silent = false) {
    try {
        const messagesContainer = chatBox.querySelector('.chat-body');

        if (!silent) {
            messagesContainer.innerHTML = `
                <div class="text-center py-12">
                    <div class="inline-block animate-spin rounded-full h-12 w-12 border-b-2 border-indigo-600 mb-4"></div>
                    <p class="text-gray-600">Loading conversation...</p>
                </div>
            `;
        }

        const response = await fetchAPI(`/api/ldrrmo/matches/${matchId}/conversation`);

        if (!response.success) {
            // No conversation yet
            messagesContainer.innerHTML = `
                <div class="text-center py-12">
                    <i class="fas fa-comments-slash text-6xl text-gray-300 mb-4"></i>
                    <h3 class="text-xl font-semibold text-gray-700 mb-2">No Conversation Yet</h3>
                    <p class="text-gray-500 mb-4">
                        The conversation will start once the donating barangay accepts the match request.
                    </p>
                    <p class="text-sm text-gray-400">
                        As LDRRMO, you can monitor and participate in the conversation once it begins.
                    </p>
                </div>
            `;
            return;
        }

        // Update chat header
        const matchInfo = response.match;
        const chatTitle = chatBox.querySelector('.chat-title');
        const chatSubtitle = chatBox.querySelector('.chat-subtitle');

        chatTitle.textContent = `${matchInfo.requesting_barangay} ↔ ${matchInfo.donating_barangay}`;
        chatSubtitle.textContent = matchInfo.resource_need;

        // Display messages
        displayChatMessages(chatBox, response.conversation.messages);

    } catch (error) {
        console.error('Error loading conversation:', error);
        const messagesContainer = chatBox.querySelector('.chat-body');
        messagesContainer.innerHTML = `
            <div class="text-center py-8">
                <i class="fas fa-exclamation-circle text-4xl text-red-400 mb-2"></i>
                <p class="text-xs text-gray-500">${error.message || 'Failed to load'}</p>
            </div>
        `;
    }
}

function displayChatMessages(chatBox, messages) {
    const container = chatBox.querySelector('.chat-body');

    if (!messages || messages.length === 0) {
        container.innerHTML = `
            <div class="text-center py-8">
                <i class="fas fa-inbox text-3xl text-gray-300 mb-2"></i>
                <p class="text-xs text-gray-500">No messages yet</p>
            </div>
        `;
        return;
    }

    // Render messages (Messenger-style bubbles)
    const html = messages.map(msg => {
        const isLDRRMO = msg.sender_role === 'ldrrmo';
        const isRequester = msg.sender_role === 'requester';
        const isDonor = msg.sender_role === 'donor';

        let bgColor, textColor;
        if (isLDRRMO) {
            bgColor = 'bg-indigo-600';
            textColor = 'text-white';
        } else if (isRequester) {
            bgColor = 'bg-blue-500';
            textColor = 'text-white';
        } else {
            bgColor = 'bg-green-500';
            textColor = 'text-white';
        }

        // LDRRMO messages on the right, others on the left (Messenger style)
        if (isLDRRMO) {
            return `
                <div class="flex items-start gap-2 justify-end">
                    <div class="flex-1 flex flex-col items-end">
                        <p class="text-xs text-gray-600 mb-0.5">You</p>
                        <div class="${bgColor} ${textColor} rounded-2xl px-3 py-2 inline-block max-w-[85%]">
                            <p class="text-sm whitespace-pre-wrap break-words">${escapeHtml(msg.message)}</p>
                        </div>
                        <p class="text-xs text-gray-400 mt-0.5">${formatTimeSimple(msg.created_at)}</p>
                    </div>
                </div>
            `;
        } else {
            return `
                <div class="flex items-start gap-2">
                    <div class="flex-shrink-0 w-6 h-6 rounded-full ${bgColor} flex items-center justify-center text-white text-xs font-bold">
                        ${msg.sender_name.substring(0, 1)}
                    </div>
                    <div class="flex-1">
                        <p class="text-xs text-gray-600 mb-0.5">${msg.sender_name}</p>
                        <div class="${bgColor} ${textColor} rounded-2xl px-3 py-2 inline-block max-w-[85%]">
                            <p class="text-sm whitespace-pre-wrap break-words">${escapeHtml(msg.message)}</p>
                        </div>
                        <p class="text-xs text-gray-400 mt-0.5">${formatTimeSimple(msg.created_at)}</p>
                    </div>
                </div>
            `;
        }
    }).join('');

    container.innerHTML = html;

    // Scroll to bottom
    setTimeout(() => {
        container.scrollTop = container.scrollHeight;
    }, 100);
}

function formatTimeSimple(dateString) {
    if (!dateString) return '';
    const date = new Date(dateString);
    return date.toLocaleTimeString('en-US', { hour: 'numeric', minute: '2-digit' });
}

async function sendChatMessage(event, form) {
    event.preventDefault();

    const chatBox = form.closest('.chat-box');
    const matchId = parseInt(chatBox.getAttribute('data-match-id'));
    const input = form.querySelector('.message-input');
    const message = input.value.trim();

    if (!message) return;

    // Disable input while sending
    const submitBtn = form.querySelector('button[type="submit"]');
    const originalBtnText = submitBtn.innerHTML;
    input.disabled = true;
    submitBtn.disabled = true;
    submitBtn.innerHTML = '<i class="fas fa-spinner fa-spin"></i>';

    try {
        const response = await fetchAPI(`/api/ldrrmo/matches/${matchId}/messages`, {
            method: 'POST',
            body: JSON.stringify({ message })
        });

        if (response.success) {
            // Clear input
            input.value = '';

            // Reload conversation
            await loadChatConversation(matchId, chatBox, true);
        } else {
            alert('❌ Error: ' + (response.message || 'Failed to send message'));
        }
    } catch (error) {
        console.error('Error sending message:', error);
        alert('❌ Failed to send message. Please try again.');
    } finally {
        // Re-enable input
        input.disabled = false;
        submitBtn.disabled = false;
        submitBtn.innerHTML = originalBtnText;
        input.focus();
    }
}

// Chat boxes are ready to use - no additional setup needed

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

// Match Success Modal System
window.showMatchSuccessModal = function(matchData) {
    const modal = document.getElementById('matchSuccessModal');

    // Update match ID
    document.getElementById('matchSuccessId').textContent = `#${matchData.match_id}`;

    // Update barangay names
    document.getElementById('matchSuccessRequesting').textContent = matchData.requesting_barangay;
    document.getElementById('matchSuccessDonating').textContent = matchData.donating_barangay;

    // Show modal
    modal.classList.remove('hidden');
}

window.closeMatchSuccessModal = function() {
    document.getElementById('matchSuccessModal').classList.add('hidden');
}

async function contactBarangay(needId, donationId, barangayId, barangayName, matchScore, canFullyFulfill) {
    // Show confirmation modal
    const confirmed = await confirm(
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

        console.log('Initiating match with data:', {
            resource_need_id: needId,
            physical_donation_id: donationId,
            match_score: matchScore,
            quantity_requested: needData?.quantity || '',
            can_fully_fulfill: canFullyFulfill
        });

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
            // Show custom match success modal
            showMatchSuccessModal({
                match_id: response.data.match_id,
                status: response.data.status,
                requesting_barangay: response.data.requesting_barangay,
                donating_barangay: response.data.donating_barangay
            });

            // Close the suggested matches modal
            closeMatchModal();

            // Refresh the resource needs list
            loadResourceNeeds();

            // TODO: Update notification bell count
            // loadNotifications();
        } else {
            showAlert('Error: ' + response.message, '❌ Error');
        }
    } catch (error) {
        console.error('Error initiating match:', error);
        // Show more detailed error message if available
        const errorMsg = error.message || 'Failed to initiate match request. Please try again.';
        alert('❌ ' + errorMsg);
    }
}

function closeSuccessModal() {
    const modal = document.getElementById('successMatchModal');
    if (modal) modal.remove();
}
        document.addEventListener('click', function(e) {
            const modal = document.getElementById('suggestedMatchesModal');
            if (e.target === modal) {
                closeMatchModal();
            }
        });

        /**
 * View Match Details Modal Functions
 * Handles the "View Details" button functionality for resource matches
 */

// Global variables to store current match details
let currentMatchDetails = null;

/**
 * View detailed match information
 * Called when "View Details" button is clicked
 * @param {number} needId - Resource need ID
 * @param {number} donationId - Physical donation ID
 */
async function viewMatchDetails(needId, donationId) {
    try {
        // Show modal with loading state
        openMatchDetailsModal();

        // Fetch match details from API
        const response = await fetchAPI(`/api/ldrrmo/match-details/${needId}/${donationId}`);

        if (!response.success) {
            throw new Error(response.message || 'Failed to load match details');
        }

        // Store match details globally (response already contains need, donation, match_analysis)
        currentMatchDetails = response;

        // Render the match details (pass response directly, not response.data)
        renderMatchDetails(response);

    } catch (error) {
        console.error('Error loading match details:', error);

        // Show error message
        document.getElementById('matchDetailsContent').innerHTML = `
            <div class="text-center py-12">
                <i class="fas fa-exclamation-circle text-6xl text-red-400 mb-4"></i>
                <h3 class="text-xl font-semibold text-gray-700 mb-2">Failed to Load Details</h3>
                <p class="text-gray-500 mb-4">${error.message}</p>
                <button onclick="viewMatchDetails(${needId}, ${donationId})"
                        class="px-6 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition">
                    Try Again
                </button>
            </div>
        `;
    }
}

/**
 * Open the match details modal
 */
function openMatchDetailsModal() {
    const modal = document.getElementById('matchDetailsModal');
    if (modal) {
        modal.classList.remove('hidden');

        // Show loading state
        document.getElementById('matchDetailsContent').innerHTML = `
            <div class="text-center py-12">
                <div class="inline-block animate-spin rounded-full h-12 w-12 border-b-2 border-blue-600 mb-4"></div>
                <p class="text-gray-600">Loading match details...</p>
            </div>
        `;
    }
}

/**
 * Close the match details modal
 */
function closeMatchDetailsModal() {
    const modal = document.getElementById('matchDetailsModal');
    if (modal) {
        modal.classList.add('hidden');
        currentMatchDetails = null;
    }
}

/**
 * Render match details in the modal - SIMPLIFIED VERSION
 *
 * @param {Object} data - Match details data from API
 */
function renderMatchDetails(data) {
    const { need, donation, match_analysis } = data;

    // Calculate match score percentage and compatibility label
    const matchScore = match_analysis.match_score || 0;
    let compatibility = 'Poor Match';
    let scoreClass = 'bg-red-100 text-red-700';

    if (matchScore >= 80) {
        compatibility = 'Excellent Match';
        scoreClass = 'bg-green-100 text-green-700';
    } else if (matchScore >= 60) {
        compatibility = 'Good Match';
        scoreClass = 'bg-blue-100 text-blue-700';
    } else if (matchScore >= 40) {
        compatibility = 'Fair Match';
        scoreClass = 'bg-yellow-100 text-yellow-700';
    }

    // Get urgency badge class
    const urgencyBadges = {
        'critical': 'bg-red-100 text-red-700',
        'high': 'bg-orange-100 text-orange-700',
        'medium': 'bg-yellow-100 text-yellow-700',
        'low': 'bg-green-100 text-green-700'
    };
    const urgencyClass = urgencyBadges[need.urgency] || urgencyBadges['low'];

    // Build simplified HTML
    const html = `
        <!-- Match Score Overview -->
        <div class="bg-gradient-to-r from-blue-50 to-indigo-50 border-2 border-blue-200 rounded-xl p-6 mb-6">
            <div class="flex items-center justify-between flex-wrap gap-4">
                <div>
                    <h4 class="text-lg font-semibold text-gray-800 mb-2">Match Compatibility</h4>
                    <span class="px-4 py-2 rounded-full text-sm font-bold ${scoreClass}">
                        <i class="fas fa-star"></i>
                        ${matchScore.toFixed(1)}% - ${compatibility}
                    </span>
                </div>
                <div class="text-right">
                    <div class="text-sm text-gray-600 mb-1">Fulfillment Status</div>
                    <div class="flex items-center gap-2">
                        ${match_analysis.can_fully_fulfill
                            ? '<i class="fas fa-check-circle text-green-600 text-xl"></i><span class="font-semibold text-green-700">Full Fulfillment</span>'
                            : '<i class="fas fa-exclamation-triangle text-yellow-600 text-xl"></i><span class="font-semibold text-yellow-700">Partial Fulfillment</span>'
                        }
                    </div>
                </div>
            </div>
        </div>

        <!-- Two Column Layout -->
        <div class="grid grid-cols-1 lg:grid-cols-2 gap-6 mb-6">
            <!-- Resource Need Card -->
            <div class="bg-blue-50 border-2 border-blue-200 rounded-xl p-5">
                <div class="flex items-center gap-3 mb-4">
                    <div class="w-12 h-12 bg-blue-600 rounded-lg flex items-center justify-center">
                        <i class="fas fa-hand-holding-heart text-white text-xl"></i>
                    </div>
                    <div>
                        <h4 class="font-bold text-lg text-gray-800">Resource Need</h4>
                        <p class="text-sm text-gray-600">${need.barangay_name || 'Unknown'}</p>
                    </div>
                </div>

                <div class="space-y-3">
                    <div class="flex justify-between">
                        <span class="text-sm text-gray-600">Category:</span>
                        <span class="font-semibold">${need.category || 'General'}</span>
                    </div>
                    <div class="flex justify-between">
                        <span class="text-sm text-gray-600">Quantity:</span>
                        <span class="font-semibold">${need.quantity || 'N/A'}</span>
                    </div>
                    <div class="flex justify-between">
                        <span class="text-sm text-gray-600">Urgency:</span>
                        <span class="px-3 py-1 rounded-full text-xs font-bold ${urgencyClass}">
                            ${(need.urgency || 'low').toUpperCase()}
                        </span>
                    </div>
                    <div class="flex justify-between">
                        <span class="text-sm text-gray-600">Status:</span>
                        <span class="font-semibold">${need.status || 'pending'}</span>
                    </div>
                </div>

                ${need.description ? `
                <div class="mt-4 pt-4 border-t border-blue-300">
                    <p class="text-sm text-gray-700">
                        <i class="fas fa-info-circle text-blue-600 mr-1"></i>
                        ${need.description}
                    </p>
                </div>
                ` : ''}
            </div>

            <!-- Physical Donation Card -->
            <div class="bg-green-50 border-2 border-green-200 rounded-xl p-5">
                <div class="flex items-center gap-3 mb-4">
                    <div class="w-12 h-12 bg-green-600 rounded-lg flex items-center justify-center">
                        <i class="fas fa-box-open text-white text-xl"></i>
                    </div>
                    <div>
                        <h4 class="font-bold text-lg text-gray-800">Available Donation</h4>
                        <p class="text-sm text-gray-600">${donation.barangay_name || 'Unknown'}</p>
                    </div>
                </div>

                <div class="space-y-3">
                    <div class="flex justify-between">
                        <span class="text-sm text-gray-600">Category:</span>
                        <span class="font-semibold">${donation.category || 'General'}</span>
                    </div>
                    <div class="flex justify-between">
                        <span class="text-sm text-gray-600">Quantity:</span>
                        <span class="font-semibold">${donation.quantity || 'N/A'}</span>
                    </div>
                    <div class="flex justify-between">
                        <span class="text-sm text-gray-600">Status:</span>
                        <span class="px-3 py-1 rounded-full text-xs font-bold bg-green-100 text-green-700">
                            ${donation.distribution_status || 'available'}
                        </span>
                    </div>
                    <div class="flex justify-between">
                        <span class="text-sm text-gray-600">Donor:</span>
                        <span class="font-semibold">${donation.donor_name || 'Anonymous'}</span>
                    </div>
                </div>

                ${donation.items_description ? `
                <div class="mt-4 pt-4 border-t border-green-300">
                    <p class="text-sm text-gray-700">
                        <i class="fas fa-box text-green-600 mr-1"></i>
                        ${donation.items_description}
                    </p>
                </div>
                ` : ''}
            </div>
        </div>

        <!-- Action Info -->
        <div class="bg-gray-50 border border-gray-200 rounded-lg p-4 text-center">
            <p class="text-sm text-gray-600">
                <i class="fas fa-info-circle mr-1"></i>
                Click "Initiate Match" below to send a request to both barangays
            </p>
        </div>
    `;

    // Update modal content
    document.getElementById('matchDetailsContent').innerHTML = html;

    // Update "Initiate Match" button
    const initiateBtn = document.getElementById('initiateMatchFromDetails');
    if (initiateBtn) {
        initiateBtn.innerHTML = '<i class="fas fa-handshake mr-2"></i>Initiate Match';
        initiateBtn.onclick = () => {
            closeMatchDetailsModal();
            contactBarangay(
                need.id,
                donation.id,
                donation.barangay_id,
                donation.barangay_name,
                match_analysis.match_score,
                match_analysis.can_fully_fulfill
            );
        };
    }
}

/**
 * Get CSS class for match score badge
 *
 * @param {number} score - Match score percentage
 * @returns {string} CSS class name
 */
function getMatchScoreBadgeClass(score) {
    if (score >= 90) return 'match-score-excellent';
    if (score >= 75) return 'match-score-good';
    if (score >= 60) return 'match-score-fair';
    return 'match-score-poor';
}

/**
 * Get urgency badge CSS class
 *
 * @param {string} urgency - Urgency level
 * @returns {string} CSS class name
 */
function getUrgencyBadgeClass(urgency) {
    const classes = {
        'critical': 'bg-red-100 text-red-700',
        'high': 'bg-orange-100 text-orange-700',
        'medium': 'bg-yellow-100 text-yellow-700',
        'low': 'bg-green-100 text-green-700',
    };
    return classes[urgency] || 'bg-gray-100 text-gray-700';
}

/**
 * Get icon for match factor
 *
 * @param {string} status - Factor status
 * @returns {string} Font Awesome icon name
 */
function getFactorIcon(status) {
    const icons = {
        'match': 'check-circle',
        'mismatch': 'times-circle',
        'good': 'smile',
        'moderate': 'meh',
        'far': 'frown',
        'urgent': 'exclamation-triangle',
        'normal': 'info-circle',
        'full': 'check-double',
        'partial': 'minus-circle',
    };
    return icons[status] || 'circle';
}

/**
 * Escape HTML to prevent XSS
 *
 * @param {string} text - Text to escape
 * @returns {string} Escaped text
 */
function escapeHtml(text) {
    const div = document.createElement('div');
    div.textContent = text;
    return div.innerHTML;
}

// Close modal when clicking outside
document.addEventListener('DOMContentLoaded', function() {
    const modal = document.getElementById('matchDetailsModal');
    if (modal) {
        modal.addEventListener('click', function(e) {
            if (e.target === modal) {
                closeMatchDetailsModal();
            }
        });
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
    // Load notifications immediately
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
        // Don't show error to user, just log it (fail silently for better UX)
    }
}

async function updateUnreadCount() {
    try {
        const data = await fetchAPI('/api/notifications/unread-count');
        const count = data.count || 0;

        const badge = document.getElementById('notification-badge');
        const countText = document.getElementById('notification-count');

        if (!badge || !countText) return;

        if (count > 0) {
            badge.classList.remove('hidden');
            badge.textContent = count > 99 ? '99+' : count;
            countText.textContent = `${count} unread`;

            // Add subtle animation when count increases
            badge.classList.add('animate-pulse');
            setTimeout(() => badge.classList.remove('animate-pulse'), 1000);
        } else {
            badge.classList.add('hidden');
            countText.textContent = 'No unread';
        }

        // Update count text
        document.getElementById('notification-count').textContent =
            count === 0 ? 'No unread' : `${count} unread`;

    } catch (error) {
        console.error('Error updating unread count:', error);
        // Fail silently
    }
}

async function updateConversationBadge() {
    try {
        // Fetch active matches with conversations (lightweight query)
        const data = await fetchAPI('/api/ldrrmo/matches?status=all');

        if (!data || !Array.isArray(data)) return;

        // Count active matches with conversations
        const activeConversations = data.filter(m =>
            (m.status === 'pending' || m.status === 'accepted') && m.has_conversation
        ).length;

        const badge = document.getElementById('conversations-badge-header');
        if (!badge) return;

        if (activeConversations > 0) {
            badge.textContent = activeConversations > 99 ? '99+' : activeConversations;
            badge.classList.remove('hidden');
        } else {
            badge.classList.add('hidden');
        }

    } catch (error) {
        console.error('Error updating conversation badge:', error);
        // Fail silently
    }
}

// Conversations Sidebar (Facebook-style)
let conversationsSidebarOpen = false;
let activeConversationsData = [];

function toggleConversationsSidebar() {
    const sidebar = document.getElementById('conversations-sidebar');
    const overlay = document.getElementById('sidebar-overlay');

    conversationsSidebarOpen = !conversationsSidebarOpen;

    if (conversationsSidebarOpen) {
        sidebar.classList.remove('translate-x-full');
        sidebar.classList.add('translate-x-0');
        overlay.classList.remove('hidden');
        // Load conversations when opening
        loadSidebarConversations();
    } else {
        sidebar.classList.add('translate-x-full');
        sidebar.classList.remove('translate-x-0');
        overlay.classList.add('hidden');
    }
}

// Refresh conversations every 20 seconds (only if sidebar is open)
setInterval(() => {
    if (conversationsSidebarOpen) {
        loadSidebarConversations();
    }
}, 20000);

async function loadSidebarConversations() {
    try {
        const container = document.getElementById('conversations-sidebar-list');

        if (!container) return;

        // Fetch active matches with conversations
        const data = await fetchAPI('/api/ldrrmo/matches?status=all');

        if (!data || !Array.isArray(data)) {
            container.innerHTML = `
                <div class="text-center py-12 text-gray-400">
                    <i class="fas fa-exclamation-circle text-4xl mb-3"></i>
                    <p class="text-sm text-xs">Failed to load</p>
                </div>
            `;
            return;
        }

        // Filter active conversations
        activeConversationsData = data.filter(m =>
            (m.status === 'pending' || m.status === 'accepted') && m.has_conversation
        );

        displaySidebarConversations(activeConversationsData);
        updateConversationBadges(activeConversationsData.length);

    } catch (error) {
        console.error('Error loading conversations:', error);
        const container = document.getElementById('conversations-sidebar-list');
        if (container) {
            container.innerHTML = `
                <div class="text-center py-12 text-gray-400">
                    <i class="fas fa-exclamation-circle text-4xl mb-3"></i>
                    <p class="text-sm text-xs">Error loading</p>
                </div>
            `;
        }
    }
}

function displaySidebarConversations(conversations) {
    const container = document.getElementById('conversations-sidebar-list');

    if (!container) return;

    if (!conversations || conversations.length === 0) {
        container.innerHTML = `
            <div class="text-center py-12 px-4 text-gray-400">
                <i class="fas fa-comments-slash text-4xl mb-3"></i>
                <p class="text-sm font-semibold text-gray-700 mb-1">No Conversations</p>
                <p class="text-xs text-gray-500">Start a match to begin</p>
            </div>
        `;
        return;
    }

    const html = conversations.map(convo => {
        const statusDots = {
            'pending': 'bg-yellow-400',
            'accepted': 'bg-green-400',
            'completed': 'bg-blue-400',
            'rejected': 'bg-red-400'
        };

        // Safely get barangay names from object or string
        const requestingName = convo.requesting_barangay?.name || convo.requesting_barangay || 'Unknown';
        const donatingName = convo.donating_barangay?.name || convo.donating_barangay || 'Unknown';
        const categoryName = convo.resource_need?.category || convo.category || 'Resource Match';
        const initials = String(requestingName).substring(0, 2).toUpperCase();

        return `
            <div onclick="openConversationFromSidebar(${convo.id})"
                 class="px-3 py-3 border-b border-gray-100 hover:bg-gray-50 cursor-pointer transition">

                <div class="flex items-center gap-3">
                    <!-- Avatar with Status Dot -->
                    <div class="relative flex-shrink-0">
                        <div class="w-12 h-12 rounded-full bg-gradient-to-br from-indigo-500 to-purple-600 flex items-center justify-center text-white font-bold text-sm">
                            ${initials}
                        </div>
                        <div class="absolute bottom-0 right-0 w-3 h-3 ${statusDots[convo.status] || 'bg-gray-400'} rounded-full border-2 border-white"></div>
                    </div>

                    <!-- Content -->
                    <div class="flex-1 min-w-0">
                        <p class="font-semibold text-gray-900 text-sm truncate mb-0.5">
                            ${requestingName}
                        </p>
                        <p class="text-xs text-gray-600 truncate mb-0.5">
                            ↔ ${donatingName}
                        </p>
                        <p class="text-xs text-gray-500 truncate">
                            ${categoryName}
                        </p>
                    </div>

                    <!-- Notification Badge -->
                    ${convo.unread_messages ? `
                        <div class="flex-shrink-0">
                            <span class="inline-flex items-center justify-center w-5 h-5 bg-red-500 text-white text-xs rounded-full font-bold">
                                ${convo.unread_messages > 9 ? '9+' : convo.unread_messages}
                            </span>
                        </div>
                    ` : ''}
                </div>
            </div>
        `;
    }).join('');

    container.innerHTML = html;
}

function updateConversationBadges(count) {
    const badges = [
        document.getElementById('conversations-badge-header'),
        document.getElementById('conversations-badge-sidebar')
    ];

    badges.forEach(badge => {
        if (!badge) return;
        if (count > 0) {
            badge.textContent = count > 99 ? '99+' : count;
            badge.classList.remove('hidden');
        } else {
            badge.classList.add('hidden');
        }
    });
}

function filterConversations() {
    const searchTerm = document.getElementById('conversation-search').value.toLowerCase();

    if (!searchTerm) {
        displaySidebarConversations(activeConversationsData);
        return;
    }

    const filtered = activeConversationsData.filter(convo => {
        const requestingName = String(convo.requesting_barangay?.name || convo.requesting_barangay || '').toLowerCase();
        const donatingName = String(convo.donating_barangay?.name || convo.donating_barangay || '').toLowerCase();
        const category = String(convo.resource_need?.category || convo.category || '').toLowerCase();

        return requestingName.includes(searchTerm) ||
               donatingName.includes(searchTerm) ||
               category.includes(searchTerm);
    });

    displaySidebarConversations(filtered);
}

function openConversationFromSidebar(matchId) {
    // Close sidebar after opening conversation
    toggleConversationsSidebar();
    viewConversation(matchId);
}

function formatTimeAgo(dateString) {
    if (!dateString) return 'Recently';

    const date = new Date(dateString);
    const now = new Date();
    const seconds = Math.floor((now - date) / 1000);

    if (seconds < 60) return 'Just now';
    if (seconds < 3600) return `${Math.floor(seconds / 60)}m ago`;
    if (seconds < 86400) return `${Math.floor(seconds / 3600)}h ago`;
    if (seconds < 604800) return `${Math.floor(seconds / 86400)}d ago`;

    return date.toLocaleDateString('en-US', { month: 'short', day: 'numeric' });
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
                // Go to My Matches tab
                showTab('my-matches');
                // Reload matches to ensure fresh data
                setTimeout(() => {
                    loadMyMatches();
                }, 300);
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
        btn.classList.remove('border-[#CA6702]', 'text-[#CA6702]');
        btn.classList.add('border-transparent', 'text-gray-600');
    });

    const activeBtn = document.getElementById(`notif-filter-${type}`);
    activeBtn.classList.remove('border-transparent', 'text-gray-600');
    activeBtn.classList.add('border-[#CA6702]', 'text-[#CA6702]');

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

    <!-- City Dashboard Map Script -->
    <script src="{{ asset('js/city/city-dashboard-map.js') }}"></script>

    </div> <!-- Close Main Content Area -->
    </div> <!-- Close Main Layout with Sidebar -->

    <!-- Simple Alert Modal -->
    <div id="alertModal" class="hidden fixed inset-0 bg-black bg-opacity-50 z-[10000] flex items-center justify-center p-4">
        <div class="bg-white rounded-lg shadow-xl max-w-md w-full">
            <div id="alertModalHeader" class="px-6 py-4 border-b">
                <h3 id="alertModalTitle" class="text-lg font-bold text-gray-900"></h3>
            </div>
            <div class="px-6 py-4">
                <p id="alertModalMessage" class="text-gray-700 whitespace-pre-wrap"></p>
            </div>
            <div class="px-6 py-4 border-t flex justify-end gap-3">
                <button id="alertModalCancelBtn" onclick="closeAlert(false)"
                        class="hidden px-4 py-2 bg-gray-200 text-gray-700 rounded-lg hover:bg-gray-300 transition">
                    Cancel
                </button>
                <button id="alertModalOkBtn" onclick="closeAlert(true)"
                        class="px-4 py-2 bg-[#CA6702] text-white rounded-lg hover:bg-[#BB3E03] transition">
                    OK
                </button>
            </div>
        </div>
    </div>

    <!-- Messenger-Style Chat Boxes Container -->
    <div id="chat-boxes-container" class="fixed bottom-0 right-20 flex items-end gap-4 z-[9999] pointer-events-none">
        <!-- Chat boxes will be appended here -->
    </div>

    <!-- Chat Box Template -->
    <template id="chat-box-template">
        <div class="chat-box bg-white rounded-t-lg shadow-2xl w-80 flex flex-col pointer-events-auto" style="height: 450px;">
            <!-- Chat Header -->
            <div class="chat-header bg-indigo-600 text-white px-4 py-3 rounded-t-lg flex items-center justify-between">
                <div class="flex-1 min-w-0">
                    <h3 class="chat-title font-bold text-sm truncate">Loading...</h3>
                    <p class="chat-subtitle text-xs opacity-90 truncate">Conversation</p>
                </div>
                <div class="flex items-center gap-2">
                    <button onclick="minimizeChatBox(this)" class="hover:bg-indigo-700 p-1 rounded transition">
                        <i class="fas fa-minus text-xs"></i>
                    </button>
                    <button onclick="closeChatBox(this)" class="hover:bg-indigo-700 p-1 rounded transition">
                        <i class="fas fa-times text-xs"></i>
                    </button>
                </div>
            </div>

            <!-- Chat Body (Messages) -->
            <div class="chat-body overflow-y-auto p-4 space-y-3 bg-gray-50" style="height: 400px; max-height: 400px; min-height: 400px;">
                <!-- Messages will be loaded here -->
            </div>

            <!-- Chat Footer (Input) -->
            <div class="chat-footer border-t bg-white px-3 py-3">
                <form onsubmit="sendChatMessage(event, this)" class="flex items-center gap-2">
                    <input type="text"
                           class="message-input flex-1 border border-gray-300 rounded-full px-4 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-indigo-500"
                           placeholder="Type a message..."
                           required>
                    <button type="submit"
                            class="bg-indigo-600 text-white p-2 rounded-full hover:bg-indigo-700 transition w-9 h-9 flex items-center justify-center">
                        <i class="fas fa-paper-plane text-sm"></i>
                    </button>
                </form>
            </div>
        </div>
    </template>

    <!-- Match Success Modal -->
    <div id="matchSuccessModal" class="hidden fixed inset-0 bg-black bg-opacity-50 z-[10000] flex items-center justify-center p-4">
        <div class="bg-white rounded-2xl shadow-2xl max-w-md w-full">
            <!-- Success Icon -->
            <div class="flex justify-center pt-8 pb-4">
                <div class="w-20 h-20 bg-green-100 rounded-full flex items-center justify-center">
                    <svg class="w-10 h-10 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 13l4 4L19 7"></path>
                    </svg>
                </div>
            </div>

            <!-- Title -->
            <div class="text-center px-6 pb-4">
                <h2 class="text-2xl font-bold text-gray-900 mb-2">Match Request Sent!</h2>
                <p class="text-gray-600 text-sm">Both barangays have been notified</p>
            </div>

            <!-- Details Box -->
            <div class="px-6 pb-4">
                <div class="bg-gray-50 rounded-xl p-4 space-y-3">
                    <div class="flex justify-between items-center">
                        <span class="text-sm text-gray-600">Match ID:</span>
                        <span id="matchSuccessId" class="font-bold text-gray-900">#7</span>
                    </div>
                    <div class="flex justify-between items-center">
                        <span class="text-sm text-gray-600">Status:</span>
                        <span class="px-3 py-1 bg-yellow-100 text-yellow-800 text-xs font-bold rounded-full">pending</span>
                    </div>
                </div>
            </div>

            <!-- Barangays List -->
            <div class="px-6 pb-4">
                <div class="space-y-2">
                    <div class="flex items-start gap-2">
                        <svg class="w-5 h-5 text-green-600 flex-shrink-0 mt-0.5" fill="currentColor" viewBox="0 0 20 20">
                            <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/>
                        </svg>
                        <div>
                            <p id="matchSuccessRequesting" class="text-sm font-semibold text-gray-900">Basak San Nicolas</p>
                            <p class="text-xs text-gray-500">(Notified)</p>
                        </div>
                    </div>
                    <div class="flex items-start gap-2">
                        <svg class="w-5 h-5 text-green-600 flex-shrink-0 mt-0.5" fill="currentColor" viewBox="0 0 20 20">
                            <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/>
                        </svg>
                        <div>
                            <p id="matchSuccessDonating" class="text-sm font-semibold text-gray-900">Apas</p>
                            <p class="text-xs text-gray-500">(Action Required)</p>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Info Banner -->
            <div class="px-6 pb-4">
                <div class="bg-blue-50 border border-blue-200 rounded-lg p-3 flex items-start gap-2">
                    <svg class="w-5 h-5 text-blue-600 flex-shrink-0 mt-0.5" fill="currentColor" viewBox="0 0 20 20">
                        <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7-4a1 1 0 11-2 0 1 1 0 012 0zM9 9a1 1 0 000 2v3a1 1 0 001 1h1a1 1 0 100-2v-3a1 1 0 00-1-1H9z" clip-rule="evenodd"/>
                    </svg>
                    <p class="text-xs text-blue-800">Track this match in the "My Matches" tab</p>
                </div>
            </div>

            <!-- Action Button -->
            <div class="px-6 pb-6">
                <button onclick="closeMatchSuccessModal()"
                        class="w-full py-3 bg-indigo-600 text-white font-bold rounded-lg hover:bg-indigo-700 transition text-base">
                    Got it!
                </button>
            </div>
        </div>
    </div>

    <script>
        // Simple Alert Modal System
        let alertCallback = null;

        function showAlert(message, title = 'Notice', showCancel = false) {
            return new Promise((resolve) => {
                const modal = document.getElementById('alertModal');
                const titleEl = document.getElementById('alertModalTitle');
                const messageEl = document.getElementById('alertModalMessage');
                const cancelBtn = document.getElementById('alertModalCancelBtn');

                titleEl.textContent = title;
                messageEl.textContent = message;

                if (showCancel) {
                    cancelBtn.classList.remove('hidden');
                } else {
                    cancelBtn.classList.add('hidden');
                }

                modal.classList.remove('hidden');
                alertCallback = resolve;
            });
        }

        function closeAlert(result) {
            document.getElementById('alertModal').classList.add('hidden');
            if (alertCallback) {
                alertCallback(result);
                alertCallback = null;
            }
        }

        // Override native alert with modal
        window.alert = function(message) {
            return showAlert(message);
        };

        // Override native confirm with modal
        window.confirm = function(message) {
            return showAlert(message, 'Confirm', true);
        };

        // ============================================
        // MESSENGER-STYLE CHAT BOXES
        // ============================================

        let openChatBoxes = new Map(); // matchId -> { element, interval }

        function viewConversation(matchId) {
            // Check if chat box already exists
            if (openChatBoxes.has(matchId)) {
                // If minimized, maximize it
                const existingBox = openChatBoxes.get(matchId).element;
                const chatBody = existingBox.querySelector('.chat-body');
                const chatFooter = existingBox.querySelector('.chat-footer');
                if (chatBody.classList.contains('hidden')) {
                    chatBody.classList.remove('hidden');
                    chatFooter.classList.remove('hidden');
                }
                // Bring to front by re-appending
                existingBox.parentElement.appendChild(existingBox);
                return;
            }

            // Create new chat box from template
            const template = document.getElementById('chat-box-template');
            const chatBox = template.content.cloneNode(true).querySelector('.chat-box');
            chatBox.setAttribute('data-match-id', matchId);

            // Add to container
            const container = document.getElementById('chat-boxes-container');
            container.appendChild(chatBox);

            // Load conversation
            loadChatConversation(matchId, chatBox);

            // Start auto-refresh
            const refreshInterval = setInterval(() => {
                loadChatConversation(matchId, chatBox, true);
            }, 5000);

            // Store reference
            openChatBoxes.set(matchId, {
                element: chatBox,
                interval: refreshInterval
            });
        }

        function closeChatBox(button) {
            const chatBox = button.closest('.chat-box');
            const matchId = parseInt(chatBox.getAttribute('data-match-id'));

            // Clear interval
            if (openChatBoxes.has(matchId)) {
                clearInterval(openChatBoxes.get(matchId).interval);
                openChatBoxes.delete(matchId);
            }

            // Remove element
            chatBox.remove();
        }

        function minimizeChatBox(button) {
            const chatBox = button.closest('.chat-box');
            const chatBody = chatBox.querySelector('.chat-body');
            const chatFooter = chatBox.querySelector('.chat-footer');
            const icon = button.querySelector('i');

            if (chatBody.classList.contains('hidden')) {
                // Maximize
                chatBody.classList.remove('hidden');
                chatFooter.classList.remove('hidden');
                icon.classList.remove('fa-window-maximize');
                icon.classList.add('fa-minus');
            } else {
                // Minimize
                chatBody.classList.add('hidden');
                chatFooter.classList.add('hidden');
                icon.classList.remove('fa-minus');
                icon.classList.add('fa-window-maximize');
            }
        }

        async function loadChatConversation(matchId, chatBox, silent = false) {
            try {
                const messagesContainer = chatBox.querySelector('.chat-body');

                if (!silent) {
                    messagesContainer.innerHTML = `
                        <div class="text-center py-12">
                            <div class="inline-block animate-spin rounded-full h-12 w-12 border-b-2 border-indigo-600 mb-4"></div>
                            <p class="text-gray-600">Loading conversation...</p>
                        </div>
                    `;
                }

                const response = await fetchAPI(`/api/ldrrmo/matches/${matchId}/conversation`);

                if (!response.success) {
                    // No conversation yet
                    messagesContainer.innerHTML = `
                        <div class="text-center py-12">
                            <i class="fas fa-comments-slash text-6xl text-gray-300 mb-4"></i>
                            <h3 class="text-xl font-semibold text-gray-700 mb-2">No Conversation Yet</h3>
                            <p class="text-gray-500 mb-4">
                                The conversation will start once the donating barangay accepts the match request.
                            </p>
                            <p class="text-sm text-gray-400">
                                As LDRRMO, you can monitor and participate in the conversation once it begins.
                            </p>
                        </div>
                    `;
                    return;
                }

                // Update chat header
                const matchInfo = response.match;
                const chatTitle = chatBox.querySelector('.chat-title');
                const chatSubtitle = chatBox.querySelector('.chat-subtitle');

                chatTitle.textContent = `${matchInfo.requesting_barangay} ↔ ${matchInfo.donating_barangay}`;
                chatSubtitle.textContent = matchInfo.resource_need;

                // Display messages
                displayChatMessages(chatBox, response.conversation.messages);

            } catch (error) {
                console.error('Error loading conversation:', error);
                const messagesContainer = chatBox.querySelector('.chat-body');
                messagesContainer.innerHTML = `
                    <div class="text-center py-8">
                        <i class="fas fa-exclamation-circle text-4xl text-red-400 mb-2"></i>
                        <p class="text-xs text-gray-500">${error.message || 'Failed to load'}</p>
                    </div>
                `;
            }
        }

        function displayChatMessages(chatBox, messages) {
            const container = chatBox.querySelector('.chat-body');

            if (!messages || messages.length === 0) {
                container.innerHTML = `
                    <div class="text-center py-8">
                        <i class="fas fa-inbox text-3xl text-gray-300 mb-2"></i>
                        <p class="text-xs text-gray-500">No messages yet</p>
                    </div>
                `;
                return;
            }

            // Render messages (Messenger-style bubbles)
            const html = messages.map(msg => {
                // Handle system messages differently
                if (msg.message_type === 'system') {
                    return `
                        <div class="flex justify-center mb-3">
                            <div class="bg-gray-100 rounded-lg px-4 py-2 max-w-[80%]">
                                <p class="text-xs text-gray-600 text-center">${escapeHtml(msg.message)}</p>
                            </div>
                        </div>
                    `;
                }

                // Use is_mine flag from API to determine if message is from current user
                const isMe = msg.is_mine === true;

                let bgColor, textColor, initial;
                if (msg.sender_role === 'ldrrmo') {
                    bgColor = 'bg-indigo-600';
                    textColor = 'text-white';
                    initial = 'L';
                } else if (msg.sender_role === 'requester') {
                    bgColor = 'bg-blue-500';
                    textColor = 'text-white';
                    initial = 'R';
                } else {
                    bgColor = 'bg-green-500';
                    textColor = 'text-white';
                    initial = 'D';
                }

                // My messages on the right, others on the left (Messenger style)
                if (isMe) {
                    return `
                        <div class="flex items-start gap-2 justify-end mb-3">
                            <div class="flex flex-col items-end max-w-[85%]">
                                <p class="text-xs text-gray-600 mb-1">You</p>
                                <div class="${bgColor} ${textColor} rounded-2xl px-4 py-2">
                                    <p class="text-sm whitespace-pre-wrap break-words">${escapeHtml(msg.message)}</p>
                                </div>
                                <p class="text-xs text-gray-400 mt-1">${formatTimeSimple(msg.created_at)}</p>
                            </div>
                        </div>
                    `;
                } else {
                    return `
                        <div class="flex items-start gap-2 mb-3">
                            <div class="flex-shrink-0 w-8 h-8 rounded-full ${bgColor} flex items-center justify-center text-white text-xs font-bold">
                                ${initial}
                            </div>
                            <div class="flex flex-col max-w-[85%]">
                                <p class="text-xs text-gray-600 mb-1">${escapeHtml(msg.sender_name)}</p>
                                <div class="${bgColor} ${textColor} rounded-2xl px-4 py-2">
                                    <p class="text-sm whitespace-pre-wrap break-words">${escapeHtml(msg.message)}</p>
                                </div>
                                <p class="text-xs text-gray-400 mt-1">${formatTimeSimple(msg.created_at)}</p>
                            </div>
                        </div>
                    `;
                }
            }).join('');

            container.innerHTML = html;

            // Scroll to bottom
            setTimeout(() => {
                container.scrollTop = container.scrollHeight;
            }, 100);
        }

        function formatTimeSimple(dateString) {
            if (!dateString) return '';
            const date = new Date(dateString);
            return date.toLocaleTimeString('en-US', { hour: 'numeric', minute: '2-digit' });
        }

        async function sendChatMessage(event, form) {
            event.preventDefault();

            const chatBox = form.closest('.chat-box');
            const matchId = parseInt(chatBox.getAttribute('data-match-id'));
            const input = form.querySelector('.message-input');
            const message = input.value.trim();

            if (!message) return;

            // Disable input while sending
            const submitBtn = form.querySelector('button[type="submit"]');
            const originalBtnText = submitBtn.innerHTML;
            input.disabled = true;
            submitBtn.disabled = true;
            submitBtn.innerHTML = '<i class="fas fa-spinner fa-spin"></i>';

            try {
                const response = await fetchAPI(`/api/ldrrmo/matches/${matchId}/messages`, {
                    method: 'POST',
                    body: JSON.stringify({ message })
                });

                if (response.success) {
                    // Clear input
                    input.value = '';

                    // Reload conversation
                    await loadChatConversation(matchId, chatBox, true);
                } else {
                    alert('❌ Error: ' + (response.message || 'Failed to send message'));
                }
            } catch (error) {
                console.error('Error sending message:', error);
                alert('❌ Failed to send message. Please try again.');
            } finally {
                // Re-enable input
                input.disabled = false;
                submitBtn.disabled = false;
                submitBtn.innerHTML = originalBtnText;
                input.focus();
            }
        }

        console.log('✅ Messenger-style chat boxes loaded');
    </script>

</body>
</html>~

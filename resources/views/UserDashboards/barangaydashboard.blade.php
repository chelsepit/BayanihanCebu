<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>BayanihanCebu - BDRRMC</title>

    <!-- External CSS -->
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link rel="stylesheet" href="{{ asset('css/barangay/dashboard.css') }}">
</head>
<body data-barangay-id="{{ session('barangay_id') }}">

    <!-- Top Header -->
    <div class="bg-[#CA6702] text-white px-6 py-4 flex justify-between items-center">
        <div>
            <h1 class="text-xl font-semibold">BayanihanCebu - BDRRMC</h1>
            <p class="text-sm opacity-90">Barangay {{ $barangay->name ?? 'Lahug' }}</p>
        </div>
        <div class="flex items-center gap-4">
            <div class="text-right">
                <p class="text-sm opacity-90">Logged in as</p>
                <p class="font-medium">{{ session('user_name') }}</p>
            </div>
            <form action="{{ route('logout') }}" method="POST">
                @csrf
                <button type="submit" class="bg-white/20 hover:bg-white/30 px-4 py-2 rounded text-sm transition">
                    Logout
                </button>
            </form>
        </div>
    </div>

    <div class="max-w-7xl mx-auto px-6 py-6">
        
        <!-- Barangay Status Card -->
        <div class="bg-white rounded-lg shadow-sm p-6 mb-6">
            <div class="flex justify-between items-center mb-4">
                <div>
                    <h2 class="text-xl font-semibold text-gray-800">Barangay Status</h2>
                    @php
                        $status = $barangay->disaster_status ?? 'safe';
                        $statusConfig = [
                            'safe' => ['bg' => 'bg-green-100', 'text' => 'text-green-700', 'icon' => '✅', 'label' => 'Safe'],
                            'warning' => ['bg' => 'bg-yellow-100', 'text' => 'text-yellow-700', 'icon' => '⚠️', 'label' => 'Warning'],
                            'critical' => ['bg' => 'bg-orange-100', 'text' => 'text-orange-700', 'icon' => '🔶', 'label' => 'Critical'],
                            'emergency' => ['bg' => 'bg-red-100', 'text' => 'text-red-700', 'icon' => '🚨', 'label' => 'Emergency']
                        ];
                        $config = $statusConfig[$status] ?? $statusConfig['safe'];
                    @endphp
                    <span class="inline-block mt-2 px-3 py-1 {{ $config['bg'] }} {{ $config['text'] }} text-sm font-medium rounded">
                        {{ $config['icon'] }} {{ strtoupper($config['label']) }}
                    </span>
                </div>
                <button onclick="openEditStatusModal()" class="flex items-center gap-2 px-4 py-2 border border-gray-300 rounded hover:bg-gray-50 transition">
                    <i class="fas fa-edit"></i> Edit Status
                </button>
            </div>

            <!-- Statistics Grid -->
            <div class="grid grid-cols-1 md:grid-cols-4 gap-4 mt-6">
                <div class="flex items-center gap-4">
                    <div class="w-12 h-12 bg-orange-100 rounded-lg flex items-center justify-center">
                        <i class="fas fa-users text-orange-600 text-xl"></i>
                    </div>
                    <div>
                        <p class="text-sm text-gray-600">Affected Families</p>
                        <p class="text-2xl font-bold text-gray-800">{{ $stats['affected_families'] ?? 120 }}</p>
                    </div>
                </div>

                <div class="flex items-center gap-4">
                    <div class="w-12 h-12 bg-green-100 rounded-lg flex items-center justify-center">
                        <i class="fas fa-dollar-sign text-green-600 text-xl"></i>
                    </div>
                    <div>
                        <p class="text-sm text-gray-600">Total Donations</p>
                        <p class="text-2xl font-bold text-gray-800" id="totalDonationsCount">₱{{ number_format($stats['total_value'] ?? 0, 2) }}</p>
                    </div>
                </div>

                <div class="flex items-center gap-4">
                    <div class="w-12 h-12 bg-[#E0F2F1] rounded-lg flex items-center justify-center">
                        <i class="fas fa-box text-[#005F73] text-xl"></i>
                    </div>
                    <div>
                        <p class="text-sm text-gray-600">Active Requests</p>
                        <p class="text-2xl font-bold text-gray-800" id="activeRequestsCount">{{ $stats['active_requests'] ?? 0 }}</p>
                    </div>
                </div>

                <div class="flex items-center gap-4">
                    <div class="w-12 h-12 bg-purple-100 rounded-lg flex items-center justify-center">
                        <i class="fas fa-check-circle text-purple-600 text-xl"></i>
                    </div>
                    <div>
                        <p class="text-sm text-gray-600">Verified Donations</p>
                        <p class="text-2xl font-bold text-gray-800">{{ $stats['verified_donations'] ?? 13 }}</p>
                    </div>
                </div>
            </div>
        </div>

        @include('barangay.partials.modals.edit-status-modal')

        <!-- Tabs Navigation -->
        <div class="bg-white rounded-t-lg shadow-sm border-b">
            <div class="flex gap-2 px-6">
                <button onclick="switchTab('needs')" class="tab-btn active">Resource Requests</button>
                <button onclick="switchTab('online')" class="tab-btn">Online Donations</button>
                <button onclick="switchTab('physical')" class="tab-btn">Donations Received</button>
                <button onclick="showTab('match-requests')"
                    class="tab-button px-6 py-3 text-gray-600 hover:text-[#CA6702] hover:border-[#CA6702] border-b-2 border-transparent transition font-semibold"
                    data-tab="match-requests">
                <i class="fas fa-inbox mr-2"></i>Match Requests
                <span id="incoming-requests-badge" class="hidden ml-2 bg-red-500 text-white text-xs font-bold px-2 py-1 rounded-full">0</span>
            </button>
            <button onclick="showTab('my-requests')"
                    class="tab-button px-6 py-3 text-gray-600 hover:text-[#CA6702] hover:border-[#CA6702] border-b-2 border-transparent transition font-semibold"
                    data-tab="my-requests">
                <i class="fas fa-paper-plane mr-2"></i>Pending Requests
            </button>
            <button onclick="showTab('active-matches')"
                    class="tab-button px-6 py-3 text-gray-600 hover:text-[#CA6702] hover:border-[#CA6702] border-b-2 border-transparent transition font-semibold"
                    data-tab="active-matches">
                <i class="fas fa-comments mr-2"></i>Active Matches
                <span id="active-matches-badge" class="hidden ml-2 bg-green-500 text-white text-xs font-bold px-2 py-1 rounded-full">0</span>
            </button>
            </div>
        </div>

        @include('barangay.partials.tabs.resource-requests')
        @include('barangay.partials.tabs.online-donations')
        @include('barangay.partials.tabs.physical-donations')
        @include('barangay.partials.tabs.match-requests')
        @include('barangay.partials.tabs.pending-requests')
        @include('barangay.partials.tabs.active-matches')
        @include('barangay.partials.modals.respond-match-modal')
    </div>

    @include('barangay.partials.modals.record-donation-modal')
    @include('barangay.partials.modals.success-modal')
    @include('barangay.partials.modals.distribute-modal')
    @include('barangay.partials.modals.view-distribution-modal')
    @include('barangay.partials.modals.need-modal')

    <!-- Hidden Print Receipt -->
    <div id="printReceipt" style="display: none;"></div>

    <!-- Messenger-Style Chat Boxes Container -->
    <div id="chat-boxes-container" class="fixed bottom-0 right-20 flex items-end gap-4 z-[9999] pointer-events-none">
        <!-- Chat boxes will be appended here -->
    </div>

    <!-- Chat Box Template -->
    <template id="chat-box-template">
        <div class="chat-box bg-white rounded-t-lg shadow-2xl w-80 flex flex-col pointer-events-auto" style="height: 500px;">
            <!-- Chat Header -->
            <div class="chat-header bg-[#CA6702] text-white px-4 py-3 rounded-t-lg flex items-center justify-between flex-shrink-0">
                <div class="flex-1 min-w-0">
                    <h3 class="chat-title font-bold text-sm truncate">Loading...</h3>
                    <p class="chat-subtitle text-xs opacity-90 truncate">Conversation</p>
                </div>
                <div class="flex items-center gap-2">
                    <button onclick="minimizeChatBox(this)" class="hover:bg-[#BB3E03] p-1 rounded transition">
                        <i class="fas fa-minus text-xs"></i>
                    </button>
                    <button onclick="closeChatBox(this)" class="hover:bg-[#BB3E03] p-1 rounded transition">
                        <i class="fas fa-times text-xs"></i>
                    </button>
                </div>
            </div>

            <!-- Chat Body (Messages) -->
            <div class="chat-body flex-1 overflow-y-auto p-4 space-y-3 bg-gray-50">
                <!-- Messages will be loaded here -->
            </div>

            <!-- Chat Footer (Input) -->
            <div class="chat-footer border-t bg-white px-3 py-3 flex-shrink-0">
                <form onsubmit="sendChatMessage(event, this)" class="flex items-center gap-2">
                    <input type="text"
                           class="message-input flex-1 border border-gray-300 rounded-full px-4 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-[#CA6702]"
                           placeholder="Type a message..."
                           required>
                    <button type="submit"
                            class="bg-[#CA6702] text-white p-2 rounded-full hover:bg-[#BB3E03] transition w-9 h-9 flex items-center justify-center">
                        <i class="fas fa-paper-plane text-sm"></i>
                    </button>
                </form>
            </div>
        </div>
    </template>

    <!-- External JavaScript Files -->
    <script src="{{ asset('js/barangay/utils.js') }}"></script>
    <script src="{{ asset('js/barangay/tabs.js') }}"></script>
    <script src="{{ asset('js/barangay/photo-upload.js') }}"></script>
    <script src="{{ asset('js/barangay/resource-needs.js') }}"></script>
    <script src="{{ asset('js/barangay/donations.js') }}"></script>
    <script src="{{ asset('js/barangay/modals.js') }}"></script>
    <script src="{{ asset('js/barangay/matching.js') }}"></script>
    <script src="{{ asset('js/barangay/matching-chat.js') }}"></script>
    <script src="{{ asset('js/barangay/init.js') }}"></script>

    @include('barangay.partials.modals.conversation-modal')
    @include('barangay.partials.modals.complete-match-modal')

</body>
</html>
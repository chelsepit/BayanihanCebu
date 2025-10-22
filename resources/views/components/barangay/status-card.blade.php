{{-- Barangay Status Card Component --}}
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

<div class="bg-white rounded-lg shadow-sm p-6 mb-6">
    <div class="flex justify-between items-center mb-4">
        <div>
            <h2 class="text-xl font-semibold text-gray-800">Barangay Status</h2>
            <span class="inline-block mt-2 px-3 py-1 {{ $config['bg'] }} {{ $config['text'] }} text-sm font-medium rounded">
                {{ $config['icon'] }} {{ strtoupper($config['label']) }}
            </span>
        </div>
        <button onclick="openEditStatusModal()" class="flex items-center gap-2 px-4 py-2 border border-gray-300 rounded hover:bg-gray-50 transition">
            <i class="fas fa-edit"></i> Edit Status
        </button>
    </div>

    {{-- Statistics Grid --}}
    <div class="grid grid-cols-1 md:grid-cols-4 gap-4 mt-6">
        <div class="flex items-center gap-4">
            <div class="w-12 h-12 bg-orange-100 rounded-lg flex items-center justify-center">
                <i class="fas fa-users text-orange-600 text-xl"></i>
            </div>
            <div>
                <p class="text-sm text-gray-600">Affected Families</p>
                <p class="text-2xl font-bold text-gray-800">{{ $stats['affected_families'] ?? 0 }}</p>
            </div>
        </div>

        <div class="flex items-center gap-4">
            <div class="w-12 h-12 bg-green-100 rounded-lg flex items-center justify-center">
                <i class="fas fa-dollar-sign text-green-600 text-xl"></i>
            </div>
            <div>
                <p class="text-sm text-gray-600">Total Donations</p>
                <p class="text-2xl font-bold text-gray-800" id="totalDonationsCount">₱90,500</p>
            </div>
        </div>

        <div class="flex items-center gap-4">
            <div class="w-12 h-12 bg-blue-100 rounded-lg flex items-center justify-center">
                <i class="fas fa-box text-blue-600 text-xl"></i>
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
                <p class="text-2xl font-bold text-gray-800">{{ $stats['verified_donations'] ?? 0 }}</p>
            </div>
        </div>
    </div>
</div>

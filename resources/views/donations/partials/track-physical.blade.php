{{-- Partial View for Physical Donations --}}
{{-- Location: resources/views/donations/partials/track-physical.blade.php --}}

<div class="bg-white rounded-xl shadow-xl overflow-hidden mb-6">
    
    <!-- Header with Gradient -->
    <div class="bg-gradient-to-r from-purple-600 to-purple-800 px-8 py-6">
        <div class="flex items-center justify-between">
            <div>
                <p class="text-purple-100 text-sm font-medium mb-1">Tracking Code</p>
                <p class="text-3xl font-bold text-white tracking-wide">{{ $donation->tracking_code }}</p>
            </div>
            <div class="text-right">
                <span class="inline-block px-4 py-2 
                    @if($donation->distribution_status === 'pending_distribution') bg-yellow-400 text-yellow-900
                    @elseif($donation->distribution_status === 'partially_distributed') bg-blue-400 text-blue-900
                    @else bg-green-400 text-green-900
                    @endif
                    font-bold rounded-full text-sm shadow-lg">
                    {{ strtoupper(str_replace('_', ' ', $donation->distribution_status)) }}
                </span>
            </div>
        </div>
        <div class="mt-3">
            <span class="inline-flex items-center px-3 py-1 bg-white/20 backdrop-blur-sm text-white text-sm font-semibold rounded-full">
                <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"/>
                </svg>
                Physical Donation
            </span>
        </div>
    </div>

    <!-- Main Content -->
    <div class="p-8">
        
        <!-- Donation & Beneficiary Info Cards -->
        <div class="grid md:grid-cols-2 gap-6 mb-8">
            <!-- Donation Details Card -->
            <div class="bg-gradient-to-br from-purple-50 to-white rounded-xl p-6 border border-purple-100 shadow-sm">
                <div class="flex items-center mb-4">
                    <div class="w-10 h-10 bg-purple-100 rounded-lg flex items-center justify-center mr-3">
                        <svg class="w-6 h-6 text-purple-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                        </svg>
                    </div>
                    <h3 class="text-lg font-bold text-gray-800">Donation Details</h3>
                </div>
                <div class="space-y-3">
                    <div class="flex items-center justify-between py-2 border-b border-purple-100">
                        <span class="text-gray-600 font-medium">Donor</span>
                        <span class="font-bold text-gray-900">{{ $donation->donor_name }}</span>
                    </div>
                    <div class="flex items-center justify-between py-2 border-b border-purple-100">
                        <span class="text-gray-600 font-medium">Category</span>
                        <span class="inline-flex items-center px-3 py-1 bg-purple-100 text-purple-800 font-semibold rounded-full text-sm capitalize">
                            <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 7h.01M7 3h5c.512 0 1.024.195 1.414.586l7 7a2 2 0 010 2.828l-7 7a2 2 0 01-2.828 0l-7-7A1.994 1.994 0 013 12V7a4 4 0 014-4z"/>
                            </svg>
                            {{ $donation->category }}
                        </span>
                    </div>
                    <div class="flex items-center justify-between py-2 border-b border-purple-100">
                        <span class="text-gray-600 font-medium">Quantity</span>
                        <span class="font-bold text-gray-900">{{ $donation->quantity }}</span>
                    </div>
                    <div class="flex items-center justify-between py-2 border-b border-purple-100">
                        <span class="text-gray-600 font-medium">Estimated Value</span>
                        <span class="font-bold text-purple-600 text-lg">₱{{ number_format($donation->estimated_value, 2) }}</span>
                    </div>
                    <div class="flex items-center justify-between py-2">
                        <span class="text-gray-600 font-medium">Date Donated</span>
                        <span class="font-semibold text-gray-800">{{ $donation->recorded_at->format('M d, Y') }}</span>
                    </div>
                </div>
            </div>
            
            <!-- Beneficiary Info Card -->
            <div class="bg-gradient-to-br from-blue-50 to-white rounded-xl p-6 border border-blue-100 shadow-sm">
                <div class="flex items-center mb-4">
                    <div class="w-10 h-10 bg-blue-100 rounded-lg flex items-center justify-center mr-3">
                        <svg class="w-6 h-6 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"/>
                        </svg>
                    </div>
                    <h3 class="text-lg font-bold text-gray-800">Beneficiary Info</h3>
                </div>
                <div class="space-y-3">
                    <div class="flex items-center justify-between py-2 border-b border-blue-100">
                        <span class="text-gray-600 font-medium">Barangay</span>
                        <span class="font-bold text-gray-900">{{ $donation->barangay->name }}</span>
                    </div>
                    <div class="flex items-center justify-between py-2 border-b border-blue-100">
                        <span class="text-gray-600 font-medium">Recipients</span>
                        <span class="font-semibold text-gray-800">{{ $donation->intended_recipients }}</span>
                    </div>
                    <div class="flex items-center justify-between py-2">
                        <span class="text-gray-600 font-medium">Recorded By</span>
                        <span class="font-semibold text-gray-800">{{ $donation->recorder->full_name ?? 'BDRRMC Officer' }}</span>
                    </div>
                </div>
            </div>
        </div>

        <!-- Items Description Card -->
        <div class="bg-gradient-to-r from-gray-50 to-gray-100 rounded-xl p-6 mb-8 border border-gray-200">
            <div class="flex items-start">
                <div class="w-10 h-10 bg-white rounded-lg flex items-center justify-center mr-4 flex-shrink-0 shadow-sm">
                    <svg class="w-6 h-6 text-gray-700" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                    </svg>
                </div>
                <div class="flex-1">
                    <h4 class="text-md font-bold text-gray-800 mb-2">Items Donated</h4>
                    <p class="text-gray-700 leading-relaxed">{{ $donation->items_description }}</p>
                    @if($donation->notes)
                        <div class="mt-3 pt-3 border-t border-gray-300">
                            <p class="text-sm text-gray-600"><strong class="text-gray-800">Notes:</strong> {{ $donation->notes }}</p>
                        </div>
                    @endif
                </div>
            </div>
        </div>

        <!-- Blockchain Status Card -->
        <div class="mb-8 p-6 rounded-xl border-2
            @if($donation->blockchain_status === 'confirmed') border-green-300 bg-gradient-to-r from-green-50 to-emerald-50
            @elseif($donation->blockchain_status === 'failed') border-red-300 bg-gradient-to-r from-red-50 to-rose-50
            @else border-yellow-300 bg-gradient-to-r from-yellow-50 to-amber-50
            @endif
            shadow-lg">
            <div class="flex items-start">
                <div class="flex-shrink-0 mr-4">
                    @if($donation->blockchain_status === 'confirmed')
                        <div class="w-12 h-12 bg-green-500 rounded-full flex items-center justify-center shadow-lg">
                            <svg class="w-7 h-7 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M5 13l4 4L19 7"/>
                            </svg>
                        </div>
                    @elseif($donation->blockchain_status === 'failed')
                        <div class="w-12 h-12 bg-red-500 rounded-full flex items-center justify-center shadow-lg">
                            <svg class="w-7 h-7 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M6 18L18 6M6 6l12 12"/>
                            </svg>
                        </div>
                    @else
                        <div class="w-12 h-12 bg-yellow-500 rounded-full flex items-center justify-center shadow-lg animate-pulse">
                            <svg class="w-7 h-7 text-white animate-spin" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"/>
                            </svg>
                        </div>
                    @endif
                </div>
                <div class="flex-1">
                    <h4 class="text-lg font-bold mb-2
                        @if($donation->blockchain_status === 'confirmed') text-green-900
                        @elseif($donation->blockchain_status === 'failed') text-red-900
                        @else text-yellow-900
                        @endif">
                        🔗 Blockchain Status: {{ strtoupper($donation->blockchain_status ?? 'pending') }}
                    </h4>
                    <p class="text-sm mb-3
                        @if($donation->blockchain_status === 'confirmed') text-green-800
                        @elseif($donation->blockchain_status === 'failed') text-red-800
                        @else text-yellow-800
                        @endif">
                        @if($donation->blockchain_status === 'confirmed')
                            ✓ This donation has been permanently recorded on the Lisk blockchain for complete transparency and immutability.
                        @elseif($donation->blockchain_status === 'failed')
                            ✗ Blockchain recording failed. Our system will automatically retry. Your donation is still valid.
                        @else
                            ⏳ Your donation is being recorded on the blockchain. This process typically takes 1-3 minutes.
                        @endif
                    </p>
                    @if($donation->blockchain_tx_hash)
                        <a href="https://sepolia-blockscout.lisk.com/tx/{{ $donation->blockchain_tx_hash }}" 
                           target="_blank" 
                           class="inline-flex items-center px-4 py-2 bg-white border-2 border-blue-500 text-blue-600 font-semibold rounded-lg hover:bg-blue-50 transition-all duration-200 shadow-md hover:shadow-lg">
                            <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 6H6a2 2 0 00-2 2v10a2 2 0 002 2h10a2 2 0 002-2v-4M14 4h6m0 0v6m0-6L10 14"/>
                            </svg>
                            View on Blockchain Explorer
                        </a>
                    @endif
                    @if($donation->ipfs_hash)
                        <a href="https://gateway.pinata.cloud/ipfs/{{ $donation->ipfs_hash }}" 
                           target="_blank" 
                           class="inline-flex items-center px-4 py-2 ml-2 bg-white border-2 border-purple-500 text-purple-600 font-semibold rounded-lg hover:bg-purple-50 transition-all duration-200 shadow-md hover:shadow-lg">
                            <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                            </svg>
                            View Photos on IPFS
                        </a>
                    @endif
                </div>
            </div>
        </div>

        <!-- Blockchain Verification Status Card -->
        @if($donation->offchain_hash)
        <div class="mb-8 p-6 rounded-xl border-2
            @if($donation->verification_status === 'verified') border-green-300 bg-gradient-to-r from-green-50 to-emerald-50
            @elseif($donation->verification_status === 'mismatch') border-red-300 bg-gradient-to-r from-red-50 to-rose-50
            @else border-gray-300 bg-gradient-to-r from-gray-50 to-slate-50
            @endif
            shadow-lg">
            <div class="flex items-start">
                <div class="flex-shrink-0 mr-4">
                    @if($donation->verification_status === 'verified')
                        <div class="w-12 h-12 bg-green-500 rounded-full flex items-center justify-center shadow-lg">
                            <svg class="w-7 h-7 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                            </svg>
                        </div>
                    @elseif($donation->verification_status === 'mismatch')
                        <div class="w-12 h-12 bg-red-500 rounded-full flex items-center justify-center shadow-lg">
                            <svg class="w-7 h-7 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/>
                            </svg>
                        </div>
                    @else
                        <div class="w-12 h-12 bg-gray-400 rounded-full flex items-center justify-center shadow-lg">
                            <svg class="w-7 h-7 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                            </svg>
                        </div>
                    @endif
                </div>
                <div class="flex-1">
                    <h4 class="text-lg font-bold mb-2
                        @if($donation->verification_status === 'verified') text-green-900
                        @elseif($donation->verification_status === 'mismatch') text-red-900
                        @else text-gray-900
                        @endif">
                        🔐 Data Integrity Verification: {{ strtoupper($donation->getVerificationStatusLabel()) }}
                    </h4>
                    <p class="text-sm mb-3
                        @if($donation->verification_status === 'verified') text-green-800
                        @elseif($donation->verification_status === 'mismatch') text-red-800
                        @else text-gray-700
                        @endif">
                        @if($donation->verification_status === 'verified')
                            ✓ The donation data has been verified and matches the blockchain record. This donation data is authentic and has not been tampered with.
                        @elseif($donation->verification_status === 'mismatch')
                            ⚠️ Warning: Data integrity check failed! The local data does not match the blockchain record. This may indicate tampering.
                        @else
                            ℹ️ This donation data has not yet been verified against the blockchain.
                        @endif
                    </p>

                    @if($donation->verification_status !== 'verified' && $donation->blockchain_status === 'confirmed' && $donation->blockchain_tx_hash)
                        <button onclick="verifyNow()" id="verifyButton" class="mt-3 px-6 py-3 bg-blue-600 hover:bg-blue-700 text-white font-bold rounded-lg shadow-lg transition-all duration-200 flex items-center gap-2">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"/>
                            </svg>
                            VERIFY BLOCKCHAIN NOW
                        </button>
                    @endif

                    @if($donation->verified_at)
                        <p class="text-xs text-gray-600 mb-2">
                            <strong>Last Verified:</strong> {{ $donation->verified_at->format('M d, Y h:i A') }}
                        </p>
                    @endif

                    <!-- Hash Display (collapsed by default) -->
                    <details class="mt-3">
                        <summary class="cursor-pointer text-sm font-semibold text-gray-700 hover:text-gray-900">
                            View Technical Details
                        </summary>
                        <div class="mt-3 p-4 bg-white rounded-lg border border-gray-200 space-y-2">
                            <div>
                                <p class="text-xs font-semibold text-gray-700">Offchain Hash (Local):</p>
                                <p class="text-xs font-mono text-gray-600 break-all">{{ $donation->offchain_hash }}</p>
                            </div>
                            @if($donation->onchain_hash)
                            <div>
                                <p class="text-xs font-semibold text-gray-700">Onchain Hash (Blockchain):</p>
                                <p class="text-xs font-mono text-gray-600 break-all">{{ $donation->onchain_hash }}</p>
                            </div>
                            @endif
                            <div class="flex items-center gap-2 mt-2">
                                @if($donation->offchain_hash === $donation->onchain_hash)
                                    <span class="text-xs px-2 py-1 bg-green-100 text-green-800 rounded">✓ Hashes Match</span>
                                @elseif($donation->onchain_hash)
                                    <span class="text-xs px-2 py-1 bg-red-100 text-red-800 rounded">✗ Hashes Do Not Match</span>
                                @else
                                    <span class="text-xs px-2 py-1 bg-gray-100 text-gray-800 rounded">Awaiting Blockchain Verification</span>
                                @endif
                            </div>
                        </div>
                    </details>
                </div>
            </div>
        </div>
        @endif

        <!-- Distribution History -->
        @if($donation->distributions->count() > 0)
        <div class="mb-8">
            <div class="flex items-center mb-4">
                <div class="w-10 h-10 bg-blue-100 rounded-lg flex items-center justify-center mr-3">
                    <svg class="w-6 h-6 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-6 9l2 2 4-4"/>
                    </svg>
                </div>
                <h3 class="text-xl font-bold text-gray-800">Distribution History</h3>
            </div>
            <div class="space-y-4">
                @foreach($donation->distributions as $index => $distribution)
                <div class="bg-gradient-to-r from-blue-50 to-indigo-50 rounded-xl p-5 border border-blue-200 shadow-md hover:shadow-lg transition-shadow duration-200">
                    <div class="flex items-start justify-between mb-3">
                        <div class="flex-1">
                            <div class="flex items-center mb-2">
                                <span class="inline-block w-8 h-8 bg-blue-500 text-white rounded-full flex items-center justify-center font-bold text-sm mr-3">
                                    {{ $index + 1 }}
                                </span>
                                <h4 class="font-bold text-gray-900 text-lg">{{ $distribution->distributed_to }}</h4>
                            </div>
                            <p class="text-sm text-gray-700 ml-11"><strong>Quantity:</strong> {{ $distribution->quantity_distributed }}</p>
                            @if($distribution->notes)
                                <p class="text-sm text-gray-600 ml-11 mt-1 italic">{{ $distribution->notes }}</p>
                            @endif
                        </div>
                        <div class="text-right ml-4">
                            <p class="text-sm font-semibold text-gray-700">{{ $distribution->distributed_at->format('M d, Y') }}</p>
                            <p class="text-xs text-gray-500">{{ $distribution->distributed_at->format('h:i A') }}</p>
                        </div>
                    </div>
                    <div class="flex items-center text-xs text-gray-600 bg-white rounded-lg px-3 py-2 ml-11">
                        <svg class="w-4 h-4 mr-2 text-blue-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/>
                        </svg>
                        <span>Distributed by: <strong>{{ $distribution->distributor->full_name ?? 'BDRRMC Officer' }}</strong></span>
                    </div>
                    @if($distribution->photo_urls && count($distribution->photo_urls) > 0)
                        <div class="mt-3 ml-11">
                            <span class="inline-flex items-center text-xs text-green-700 bg-green-100 px-3 py-1 rounded-full font-semibold">
                                <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                                </svg>
                                {{ count($distribution->photo_urls) }} Photo{{ count($distribution->photo_urls) > 1 ? 's' : '' }} Attached
                            </span>
                        </div>
                    @endif
                </div>
                @endforeach
            </div>
        </div>
        @else
        <div class="mb-8 p-6 bg-yellow-50 rounded-xl border-2 border-yellow-200">
            <div class="flex items-center">
                <svg class="w-6 h-6 text-yellow-600 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                </svg>
                <p class="text-sm font-semibold text-yellow-800">
                    <strong>Pending Distribution:</strong> This donation is awaiting distribution to beneficiaries. Check back soon for updates!
                </p>
            </div>
        </div>
        @endif

        <!-- Timeline -->
        <div class="mb-8">
            <h3 class="text-xl font-bold text-gray-800 mb-6 flex items-center">
                <svg class="w-6 h-6 mr-2 text-purple-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/>
                </svg>
                Donation Journey
            </h3>
            <ol class="relative border-l-4 border-purple-200 ml-6 space-y-8">
                <!-- Step 1 -->
                <li class="ml-8">
                    <span class="absolute flex items-center justify-center w-10 h-10 bg-green-500 rounded-full -left-5 ring-4 ring-white shadow-lg">
                        <svg class="w-5 h-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
                        </svg>
                    </span>
                    <div class="bg-white p-4 rounded-lg shadow-md border border-gray-200">
                        <p class="text-base font-bold text-gray-900">✓ Donation Received</p>
                        <p class="text-sm text-gray-600">{{ $donation->recorded_at->format('M d, Y h:i A') }}</p>
                    </div>
                </li>
                
                <!-- Step 2 -->
                <li class="ml-8">
                    <span class="absolute flex items-center justify-center w-10 h-10 
                        @if($donation->blockchain_status === 'confirmed') bg-green-500
                        @elseif($donation->blockchain_status === 'failed') bg-red-500
                        @else bg-yellow-500 animate-pulse
                        @endif
                        rounded-full -left-5 ring-4 ring-white shadow-lg">
                        @if($donation->blockchain_status === 'confirmed')
                            <svg class="w-5 h-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
                            </svg>
                        @else
                            <svg class="w-5 h-5 text-white animate-spin" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"/>
                            </svg>
                        @endif
                    </span>
                    <div class="bg-white p-4 rounded-lg shadow-md border border-gray-200">
                        <p class="text-base font-bold text-gray-900">
                            @if($donation->blockchain_status === 'confirmed') ✓ 
                            @elseif($donation->blockchain_status === 'failed') ✗ 
                            @else ⏳ 
                            @endif
                            Blockchain Verification
                        </p>
                        <p class="text-sm text-gray-600">
                            @if($donation->blockchain_recorded_at)
                                {{ $donation->blockchain_recorded_at->format('M d, Y h:i A') }}
                            @else
                                In progress...
                            @endif
                        </p>
                    </div>
                </li>
                
                <!-- Step 3 -->
                <li class="ml-8">
                    <span class="absolute flex items-center justify-center w-10 h-10 
                        @if($donation->distributions->count() > 0) bg-green-500
                        @else bg-gray-300
                        @endif
                        rounded-full -left-5 ring-4 ring-white shadow-lg">
                        @if($donation->distributions->count() > 0)
                            <svg class="w-5 h-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
                            </svg>
                        @else
                            <svg class="w-5 h-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/>
                            </svg>
                        @endif
                    </span>
                    <div class="bg-white p-4 rounded-lg shadow-md border border-gray-200">
                        <p class="text-base font-bold text-gray-900">
                            @if($donation->distributions->count() > 0) ✓ @else ⏳ @endif
                            Distribution to Beneficiaries
                        </p>
                        <p class="text-sm text-gray-600">
                            @if($donation->distributions->count() > 0)
                                {{ $donation->distributions->first()->distributed_at->format('M d, Y h:i A') }}
                            @else
                                Pending
                            @endif
                        </p>
                    </div>
                </li>
                
                <!-- Step 4 -->
                <li class="ml-8">
                    <span class="absolute flex items-center justify-center w-10 h-10 
                        @if($donation->distribution_status === 'fully_distributed') bg-green-500
                        @else bg-gray-300
                        @endif
                        rounded-full -left-5 ring-4 ring-white shadow-lg">
                        @if($donation->distribution_status === 'fully_distributed')
                            <svg class="w-5 h-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
                            </svg>
                        @else
                            <svg class="w-5 h-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/>
                            </svg>
                        @endif
                    </span>
                    <div class="bg-white p-4 rounded-lg shadow-md border border-gray-200">
                        <p class="text-base font-bold text-gray-900">
                            @if($donation->distribution_status === 'fully_distributed') ✓ @else ⏳ @endif
                            Fully Distributed
                        </p>
                        <p class="text-sm text-gray-600">
                            @if($donation->distribution_status === 'fully_distributed')
                                Completed
                            @else
                                Awaiting completion
                            @endif
                        </p>
                    </div>
                </li>
            </ol>
        </div>

    </div>
</div>

<!-- Action Buttons -->
<div class="flex flex-col sm:flex-row gap-4 mb-6">
    <a href="{{ route('home') }}" class="flex-1 px-8 py-4 bg-gradient-to-r from-blue-600 to-blue-700 hover:from-blue-700 hover:to-blue-800 text-white font-bold rounded-xl transition-all duration-200 text-center shadow-lg hover:shadow-xl transform hover:-translate-y-0.5 flex items-center justify-center">
        <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/>
        </svg>
        Return to Map
    </a>
    <button onclick="window.print()" class="flex-1 px-8 py-4 bg-white border-2 border-gray-300 hover:border-gray-400 text-gray-800 font-bold rounded-xl transition-all duration-200 shadow-md hover:shadow-lg transform hover:-translate-y-0.5 flex items-center justify-center">
        <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z"/>
        </svg>
        Print Receipt
    </button>
</div>

<!-- Share Section -->
<div class="bg-gradient-to-r from-purple-50 to-blue-50 rounded-xl p-6 text-center border border-purple-200">
    <p class="text-gray-700 mb-4 font-semibold">Thank you for your generosity! 💜</p>
    <div class="flex flex-col sm:flex-row justify-center gap-3">
        <button onclick="shareOnFacebook()" class="px-6 py-3 bg-blue-600 hover:bg-blue-700 text-white font-semibold rounded-lg transition-all duration-200 shadow-md hover:shadow-lg flex items-center justify-center">
            <svg class="w-5 h-5 mr-2" fill="currentColor" viewBox="0 0 24 24">
                <path d="M24 12.073c0-6.627-5.373-12-12-12s-12 5.373-12 12c0 5.99 4.388 10.954 10.125 11.854v-8.385H7.078v-3.47h3.047V9.43c0-3.007 1.792-4.669 4.533-4.669 1.312 0 2.686.235 2.686.235v2.953H15.83c-1.491 0-1.956.925-1.956 1.874v2.25h3.328l-.532 3.47h-2.796v8.385C19.612 23.027 24 18.062 24 12.073z"/>
            </svg>
            Share on Facebook
        </button>
        <button onclick="copyTrackingCode()" class="px-6 py-3 bg-purple-600 hover:bg-purple-700 text-white font-semibold rounded-lg transition-all duration-200 shadow-md hover:shadow-lg flex items-center justify-center">
            <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 16H6a2 2 0 01-2-2V6a2 2 0 012-2h8a2 2 0 012 2v2m-6 12h8a2 2 0 002-2v-8a2 2 0 00-2-2h-8a2 2 0 00-2 2v8a2 2 0 002 2z"/>
            </svg>
            Copy Tracking Code
        </button>
    </div>
</div>

<script>
function shareOnFacebook() {
    const url = window.location.href;
    const shareUrl = `https://www.facebook.com/sharer/sharer.php?u=${encodeURIComponent(url)}`;
    window.open(shareUrl, '_blank', 'width=600,height=400');
}

function copyTrackingCode() {
    const trackingCode = '{{ $donation->tracking_code }}';
    navigator.clipboard.writeText(trackingCode).then(() => {
        // Create a toast notification
        const toast = document.createElement('div');
        toast.className = 'fixed top-4 right-4 bg-green-500 text-white px-6 py-3 rounded-lg shadow-lg z-50 flex items-center';
        toast.innerHTML = `
            <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
            </svg>
            Tracking code copied to clipboard!
        `;
        document.body.appendChild(toast);
        setTimeout(() => {
            toast.remove();
        }, 3000);
    }).catch(err => {
        alert('Failed to copy tracking code. Please copy manually: {{ $donation->tracking_code }}');
    });
}

async function verifyNow() {
    const button = document.getElementById('verifyButton');
    const originalHTML = button.innerHTML;

    try {
        button.disabled = true;
        button.innerHTML = '<svg class="w-5 h-5 animate-spin" fill="none" stroke="currentColor" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path></svg> VERIFYING...';

        const response = await fetch('/api/verify-physical-donation/{{ $donation->tracking_code }}', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': '{{ csrf_token() }}'
            }
        });

        const result = await response.json();

        if (result.success) {
            if (result.status === 'verified') {
                alert('✅ SUCCESS!\n\nThis donation is VERIFIED on blockchain.\n\nThe hashes match perfectly!');
                location.reload();
            } else if (result.status === 'mismatch') {
                alert('⚠️ WARNING!\n\nHash mismatch detected!\n\nThis donation may have been tampered with!');
                location.reload();
            } else {
                alert('ℹ️ Not ready yet!\n\nBlockchain recording is still in progress.\n\nPlease wait 30 seconds and try again.');
                button.disabled = false;
                button.innerHTML = originalHTML;
            }
        } else {
            alert('❌ Error: ' + (result.message || result.error) + '\n\nPlease try again in a moment.');
            button.disabled = false;
            button.innerHTML = originalHTML;
        }
    } catch (error) {
        console.error('Verification error:', error);
        alert('❌ Error verifying blockchain. Please try again.');
        button.disabled = false;
        button.innerHTML = originalHTML;
    }
}
</script>
{{-- Partial View for Online Donations --}}
{{-- Location: resources/views/donations/partials/track-online.blade.php --}}

<div class="bg-white rounded-lg shadow-lg p-8 mb-6">
    
    <!-- Tracking Code -->
    <div class="flex items-center justify-between mb-6 pb-6 border-b">
        <div>
            <p class="text-sm text-gray-600 mb-1">Tracking Code</p>
            <p class="text-2xl font-bold text-gray-800">{{ $donation->tracking_code }}</p>
            <span class="inline-block mt-2 px-3 py-1 bg-blue-100 text-blue-800 text-xs font-semibold rounded-full">
                Online Donation
            </span>
        </div>
        <div class="text-right">
            <span class="px-4 py-2 
                @if($donation->verification_status === 'verified') bg-green-100 text-green-800
                @elseif($donation->verification_status === 'rejected') bg-red-100 text-red-800
                @else bg-yellow-100 text-yellow-800
                @endif
                font-semibold rounded-full">
                {{ strtoupper($donation->verification_status) }}
            </span>
        </div>
    </div>

    <!-- Donation Details -->
    <div class="grid md:grid-cols-2 gap-6 mb-6">
        <div>
            <h3 class="text-lg font-semibold text-gray-800 mb-4">Donation Details</h3>
            <div class="space-y-3">
                <div class="flex justify-between">
                    <span class="text-gray-600">Donor:</span>
                    <span class="font-semibold text-gray-800">{{ $donation->getDonorDisplayName() }}</span>
                </div>
                <div class="flex justify-between">
                    <span class="text-gray-600">Amount:</span>
                    <span class="font-semibold text-gray-800">₱{{ number_format($donation->amount, 2) }}</span>
                </div>
                <div class="flex justify-between">
                    <span class="text-gray-600">Payment Method:</span>
                    <span class="font-semibold text-gray-800 uppercase">{{ $donation->payment_method }}</span>
                </div>
                <div class="flex justify-between">
                    <span class="text-gray-600">Date:</span>
                    <span class="font-semibold text-gray-800">{{ $donation->created_at->format('M d, Y') }}</span>
                </div>
            </div>
        </div>

        <div>
            <h3 class="text-lg font-semibold text-gray-800 mb-4">Beneficiary</h3>
            <div class="space-y-3">
                <div class="flex justify-between">
                    <span class="text-gray-600">Barangay:</span>
                    <span class="font-semibold text-gray-800">{{ $donation->barangay->name }}</span>
                </div>
                @if($donation->disaster)
                <div class="flex justify-between">
                    <span class="text-gray-600">Disaster:</span>
                    <span class="font-semibold text-gray-800">{{ $donation->disaster->title }}</span>
                </div>
                @endif
                @if($donation->verified_at)
                <div class="flex justify-between">
                    <span class="text-gray-600">Verified:</span>
                    <span class="font-semibold text-gray-800">{{ $donation->verified_at->format('M d, Y') }}</span>
                </div>
                @endif
            </div>
        </div>
    </div>

    <!-- Blockchain Status -->
    <div class="p-4 
        @if($donation->blockchain_status === 'confirmed') bg-green-50 border-green-200
        @elseif($donation->blockchain_status === 'failed') bg-red-50 border-red-200
        @else bg-yellow-50 border-yellow-200
        @endif
        rounded-lg border-2 mb-4">
        <div class="flex items-center">
            <span class="text-2xl mr-3">
                @if($donation->blockchain_status === 'confirmed') ✓
                @elseif($donation->blockchain_status === 'failed') ✗
                @else ⟳
                @endif
            </span>
            <div>
                <h4 class="font-semibold 
                    @if($donation->blockchain_status === 'confirmed') text-green-800
                    @elseif($donation->blockchain_status === 'failed') text-red-800
                    @else text-yellow-800
                    @endif">
                    Blockchain Status: {{ strtoupper($donation->blockchain_status ?? 'pending') }}
                </h4>
                <p class="text-sm 
                    @if($donation->blockchain_status === 'confirmed') text-green-600
                    @elseif($donation->blockchain_status === 'failed') text-red-600
                    @else text-yellow-600
                    @endif">
                    @if($donation->blockchain_status === 'confirmed')
                        This donation has been permanently recorded on the Lisk blockchain.
                    @elseif($donation->blockchain_status === 'failed')
                        Blockchain recording failed. The system will retry automatically.
                    @else
                        Your donation is being recorded on the blockchain. This may take a few minutes.
                    @endif
                </p>
                @if($donation->blockchain_tx_hash)
                    <a href="https://sepolia-blockscout.lisk.com/tx/{{ $donation->blockchain_tx_hash }}" 
                       target="_blank" 
                       class="text-sm text-blue-600 hover:text-blue-800 inline-flex items-center mt-2">
                        View on Blockchain Explorer
                        <svg class="w-3 h-3 ml-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 6H6a2 2 0 00-2 2v10a2 2 0 002 2h10a2 2 0 002-2v-4M14 4h6m0 0v6m0-6L10 14"/>
                        </svg>
                    </a>
                @endif
            </div>
        </div>
    </div>

</div>

<!-- Action Buttons -->
<div class="flex gap-4 justify-center">
    <a href="{{ route('home') }}" class="px-6 py-3 bg-blue-600 hover:bg-blue-700 text-white font-semibold rounded-lg transition-colors">
        Return to Map
    </a>
    <button onclick="window.print()" class="px-6 py-3 bg-gray-100 hover:bg-gray-200 text-gray-800 font-semibold rounded-lg transition-colors">
        Print Receipt
    </button>
</div>
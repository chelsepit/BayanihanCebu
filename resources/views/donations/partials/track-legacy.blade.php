{{-- Partial View for Legacy Donations (old 'donations' table) --}}
{{-- Location: resources/views/donations/partials/track-legacy.blade.php --}}

<div class="bg-white rounded-lg shadow-lg p-8 mb-6">
    
    <!-- Tracking Code -->
    <div class="flex items-center justify-between mb-6 pb-6 border-b">
        <div>
            <p class="text-sm text-gray-600 mb-1">Tracking Code</p>
            <p class="text-2xl font-bold text-gray-800">{{ $donation->tracking_code }}</p>
        </div>
        <div class="text-right">
            <span class="px-4 py-2 bg-{{ $donation->status_color }}-100 text-{{ $donation->status_color }}-800 font-semibold rounded-full">
                {{ ucfirst($donation->status) }}
            </span>
        </div>
    </div>

    <!-- Donation Details -->
    <div class="grid md:grid-cols-2 gap-6 mb-6">
        <div>
            <h3 class="text-lg font-semibold text-gray-800 mb-4">Donation Details</h3>
            <div class="space-y-3">
                <div class="flex justify-between">
                    <span class="text-gray-600">Amount:</span>
                    <span class="font-semibold text-gray-800">₱{{ number_format($donation->amount, 2) }}</span>
                </div>
                <div class="flex justify-between">
                    <span class="text-gray-600">Type:</span>
                    <span class="font-semibold text-gray-800">{{ ucfirst($donation->donation_type) }}</span>
                </div>
                <div class="flex justify-between">
                    <span class="text-gray-600">Date:</span>
                    <span class="font-semibold text-gray-800">{{ $donation->created_at->format('M d, Y') }}</span>
                </div>
                <div class="flex justify-between">
                    <span class="text-gray-600">Donor:</span>
                    <span class="font-semibold text-gray-800">{{ $donation->donor_display_name }}</span>
                </div>
            </div>
        </div>

        <div>
            <h3 class="text-lg font-semibold text-gray-800 mb-4">Beneficiary</h3>
            <div class="space-y-3">
                <div class="flex justify-between">
                    <span class="text-gray-600">Barangay:</span>
                    <span class="font-semibold text-gray-800">{{ $donation->disaster->barangay->name }}</span>
                </div>
                <div class="flex justify-between">
                    <span class="text-gray-600">Disaster:</span>
                    <span class="font-semibold text-gray-800">{{ $donation->disaster->type_display }}</span>
                </div>
                <div class="flex justify-between">
                    <span class="text-gray-600">Severity:</span>
                    <span class="px-2 py-1 bg-{{ $donation->disaster->severity_color }}-100 text-{{ $donation->disaster->severity_color }}-800 text-xs font-semibold rounded">
                        {{ ucfirst($donation->disaster->severity) }}
                    </span>
                </div>
            </div>
        </div>
    </div>

    <!-- Blockchain Info -->
    @if($donation->transaction_hash)
        <div class="bg-blue-50 border border-blue-200 rounded-lg p-4 mb-6">
            <div class="flex items-start gap-3">
                <svg class="w-6 h-6 text-blue-600 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"/>
                </svg>
                <div class="flex-1">
                    <p class="font-semibold text-blue-900 mb-1">Blockchain Verified</p>
                    <p class="text-sm text-blue-800 mb-2">This donation has been recorded on the Lisk blockchain</p>
                    <p class="text-xs text-blue-700 font-mono break-all">
                        TX: {{ $donation->transaction_hash }}
                    </p>
                </div>
            </div>
        </div>
    @endif

    <!-- Distribution Info -->
    @if($donation->distributed_at)
        <div class="bg-green-50 border border-green-200 rounded-lg p-4 mb-6">
            <div class="flex items-start gap-3">
                <svg class="w-6 h-6 text-green-600 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
                </svg>
                <div class="flex-1">
                    <p class="font-semibold text-green-900 mb-1">Distribution Complete</p>
                    <p class="text-sm text-green-800 mb-2">
                        Distributed on {{ $donation->distributed_at->format('M d, Y \a\t h:i A') }}
                    </p>
                    @if($donation->distribution_notes)
                        <p class="text-sm text-green-700">{{ $donation->distribution_notes }}</p>
                    @endif
                </div>
            </div>
        </div>
    @endif

    <!-- Status Timeline -->
    <div class="mt-6">
        <h3 class="text-lg font-semibold text-gray-800 mb-4">Status Timeline</h3>
        <div class="space-y-4">
            
            <!-- Received -->
            <div class="flex items-start gap-4">
                <div class="flex-shrink-0">
                    <div class="w-10 h-10 bg-green-500 rounded-full flex items-center justify-center">
                        <svg class="w-5 h-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
                        </svg>
                    </div>
                </div>
                <div class="flex-1">
                    <p class="font-semibold text-gray-800">Donation Received</p>
                    <p class="text-sm text-gray-600">{{ $donation->created_at->format('M d, Y \a\t h:i A') }}</p>
                </div>
            </div>

            <!-- Confirmed -->
            @if(in_array($donation->status, ['confirmed', 'distributed', 'completed']))
                <div class="flex items-start gap-4">
                    <div class="flex-shrink-0">
                        <div class="w-10 h-10 bg-green-500 rounded-full flex items-center justify-center">
                            <svg class="w-5 h-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
                            </svg>
                        </div>
                    </div>
                    <div class="flex-1">
                        <p class="font-semibold text-gray-800">Payment Confirmed</p>
                        <p class="text-sm text-gray-600">Verified and recorded on blockchain</p>
                    </div>
                </div>
            @else
                <div class="flex items-start gap-4">
                    <div class="flex-shrink-0">
                        <div class="w-10 h-10 bg-gray-300 rounded-full flex items-center justify-center">
                            <svg class="w-5 h-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/>
                            </svg>
                        </div>
                    </div>
                    <div class="flex-1">
                        <p class="font-semibold text-gray-600">Payment Pending</p>
                        <p class="text-sm text-gray-500">Awaiting confirmation</p>
                    </div>
                </div>
            @endif

            <!-- Distributed -->
            @if(in_array($donation->status, ['distributed', 'completed']))
                <div class="flex items-start gap-4">
                    <div class="flex-shrink-0">
                        <div class="w-10 h-10 bg-green-500 rounded-full flex items-center justify-center">
                            <svg class="w-5 h-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
                            </svg>
                        </div>
                    </div>
                    <div class="flex-1">
                        <p class="font-semibold text-gray-800">Distributed to Beneficiaries</p>
                        <p class="text-sm text-gray-600">{{ $donation->distributed_at?->format('M d, Y \a\t h:i A') }}</p>
                    </div>
                </div>
            @else
                <div class="flex items-start gap-4">
                    <div class="flex-shrink-0">
                        <div class="w-10 h-10 bg-gray-300 rounded-full flex items-center justify-center">
                            <svg class="w-5 h-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/>
                            </svg>
                        </div>
                    </div>
                    <div class="flex-1">
                        <p class="font-semibold text-gray-600">Awaiting Distribution</p>
                        <p class="text-sm text-gray-500">Will be distributed soon</p>
                    </div>
                </div>
            @endif

        </div>
    </div>

</div>

<!-- Action Buttons -->
<div class="flex gap-4 justify-center">
    <a href="{{ route('home') }}" class="px-6 py-3 bg-blue-600 hover:bg-blue-700 text-white font-semibold rounded-lg transition-colors">
        Make Another Donation
    </a>
</div>
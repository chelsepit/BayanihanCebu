<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Track Your Donation - BayanihanCebu</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <style>
        @media print {
            header, .no-print {
                display: none !important;
            }
            body {
                background: white !important;
                margin: 0;
                padding: 0;
            }
        }
    </style>
</head>
<body>

    @if(isset($donation))
        {{-- Header for tracking results --}}
        <header style="background: linear-gradient(to right, #1d4ed8, #1e3a8a); color: white; box-shadow: 0 4px 6px rgba(0,0,0,0.1);">
            <div style="max-width: 1280px; margin: 0 auto; padding: 16px 24px;">
                <div style="display: flex; justify-content: space-between; align-items: center;">
                    <div>
                        <h1 style="font-size: 20px; font-weight: 700; margin: 0;">BayanihanCebu - Donation Tracking</h1>
                        <p style="color: #bfdbfe; font-size: 14px; margin: 4px 0 0 0;">Barangay {{ $donation->barangay->name }}</p>
                    </div>
                    <a href="{{ route('home') }}" style="background: #2563eb; color: white; text-decoration: none; padding: 8px 16px; border-radius: 8px; font-size: 14px; font-weight: 500; display: flex; align-items: center; transition: background 0.3s;" class="no-print">
                        <svg width="16" height="16" fill="currentColor" viewBox="0 0 24 24" style="margin-right: 8px;">
                            <path d="M10 20v-6h4v6h5v-8h3L12 3 2 12h3v8z"/>
                        </svg>
                        Back to Map
                    </a>
                </div>
            </div>
        </header>

        {{-- Show tracking results --}}
        @if($donation_type === 'physical')
            {{-- Physical Donation Tracking --}}
            @include('donations.partials.track-physical')
        @else
            {{-- Online Donation Tracking --}}
            @include('donations.partials.track-online')
        @endif
    @else
        {{-- Show tracking form --}}
        <div class="min-h-screen flex items-center justify-center px-4" style="background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);">
            <div class="max-w-2xl w-full">
                <div class="bg-white rounded-xl shadow-xl p-8">
                    <div class="text-center mb-8">
                        <div class="inline-flex items-center justify-center w-16 h-16 bg-purple-100 rounded-full mb-4">
                            <svg class="w-8 h-8 text-purple-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/>
                            </svg>
                        </div>
                        <h1 class="text-3xl font-bold text-gray-900 mb-2">Track Your Donation</h1>
                        <p class="text-gray-600">Enter your tracking code to see your donation's journey</p>
                    </div>

                    @if(session('error'))
                        <div class="mb-6 bg-red-50 border-l-4 border-red-500 p-4 rounded">
                            <div class="flex">
                                <svg class="w-5 h-5 text-red-500 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                                </svg>
                                <p class="text-red-700">{{ session('error') }}</p>
                            </div>
                        </div>
                    @endif

                    <form action="{{ route('donation.track') }}" method="POST" class="space-y-6">
                        @csrf
                        <div>
                            <label for="tracking_code" class="block text-sm font-medium text-gray-700 mb-2">
                                Tracking Code
                            </label>
                            <input
                                type="text"
                                name="tracking_code"
                                id="tracking_code"
                                required
                                class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-purple-500 focus:border-transparent transition"
                                placeholder="e.g., CC001-2025-00001"
                                value="{{ old('tracking_code') }}"
                            >
                            @error('tracking_code')
                                <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>

                        <button
                            type="submit"
                            class="w-full bg-gradient-to-r from-purple-600 to-blue-600 hover:from-purple-700 hover:to-blue-700 text-white font-bold py-3 px-6 rounded-lg transition-all duration-200 shadow-lg hover:shadow-xl transform hover:-translate-y-0.5 flex items-center justify-center"
                        >
                            <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/>
                            </svg>
                            Track Donation
                        </button>
                    </form>

                    <div class="mt-8 pt-6 border-t border-gray-200">
                        <div class="text-center">
                            <p class="text-sm text-gray-600 mb-4">Need help?</p>
                            <a href="{{ route('home') }}" class="inline-flex items-center text-blue-600 hover:text-blue-800 text-sm font-medium transition">
                                <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/>
                                </svg>
                                Back to Map
                            </a>
                        </div>
                    </div>
                </div>

                {{-- Recent Verified Donations List --}}
                <div class="mt-12 bg-white rounded-xl shadow-xl p-8" id="recent-donations">
                    <div class="text-center mb-8">
                        <h2 class="text-3xl font-bold text-gray-900 mb-2">Blockchain-Verified Donations</h2>
                        <p class="text-gray-600">All donations are recorded on the blockchain for complete transparency</p>
                    </div>

                    {{-- Loading State --}}
                    <div id="donations-loading" class="text-center py-12">
                        <div class="inline-block animate-spin rounded-full h-10 w-10 border-b-2 border-purple-600"></div>
                        <p class="mt-4 text-gray-600">Loading verified donations...</p>
                    </div>

                    {{-- Donations Grid --}}
                    <div id="donations-content" class="hidden">
                        <div class="grid md:grid-cols-2 gap-8 mb-8">
                            {{-- Non-Monetary Donations --}}
                            <div>
                                <h3 class="text-xl font-bold text-gray-900 mb-2">Non-Monetary Donations</h3>
                                <p class="text-sm text-gray-600 mb-4">In-kind contributions tracked on-chain</p>
                                <div id="physical-donations-list" class="space-y-3">
                                    {{-- Physical donations will be inserted here --}}
                                </div>
                            </div>

                            {{-- Monetary Donations --}}
                            <div>
                                <h3 class="text-xl font-bold text-gray-900 mb-2">Monetary Donations</h3>
                                <p class="text-sm text-gray-600 mb-4">Financial contributions verified on blockchain</p>
                                <div id="online-donations-list" class="space-y-3">
                                    {{-- Online donations will be inserted here --}}
                                </div>
                            </div>
                        </div>

                        {{-- View All Button --}}
                        <div class="text-center pt-6 border-t border-gray-200">
                            <a href="{{ route('donations.all') }}" class="inline-flex items-center px-6 py-3 bg-gradient-to-r from-purple-600 to-blue-600 hover:from-purple-700 hover:to-blue-700 text-white font-semibold rounded-lg transition-all duration-200 shadow-lg hover:shadow-xl transform hover:-translate-y-0.5">
                                <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 10h16M4 14h16M4 18h16"/>
                                </svg>
                                View All Transactions
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        {{-- JavaScript to load donations --}}
        <script>
            async function loadRecentDonations() {
                try {
                    const response = await fetch('/api/donations/recent-verified');
                    const data = await response.json();

                    if (!data.success) {
                        throw new Error('Failed to load donations');
                    }

                    displayDonations(data.donations);
                } catch (error) {
                    console.error('Error loading donations:', error);
                    document.getElementById('donations-loading').innerHTML = `
                        <p class="text-red-600">Failed to load donations. Please try again later.</p>
                    `;
                }
            }

            function displayDonations(donations) {
                // Hide loading, show content
                document.getElementById('donations-loading').classList.add('hidden');
                document.getElementById('donations-content').classList.remove('hidden');

                // Separate donations by type
                const physicalDonations = donations.filter(d => d.type === 'physical').slice(0, 5);
                const onlineDonations = donations.filter(d => d.type === 'online').slice(0, 5);

                // Render physical donations
                const physicalList = document.getElementById('physical-donations-list');

                if (donations.length === 0) {
                    grid.innerHTML = `
                        <div class="col-span-2 text-center py-8 text-gray-600">
                            <p>No verified donations yet.</p>
                        </div>
                    `;
                    return;
                }

                grid.innerHTML = donations.map(donation => {
                    const isPhysical = donation.type === 'physical';
                    const statusBadge = donation.blockchain_status === 'confirmed' ?
                        '<span class="inline-flex items-center px-2 py-1 rounded-full text-xs font-semibold bg-green-100 text-green-800"><svg class="w-3 h-3 mr-1" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/></svg>Verified</span>' :
                        '<span class="inline-flex items-center px-2 py-1 rounded-full text-xs font-semibold bg-yellow-100 text-yellow-800"><svg class="w-3 h-3 mr-1 animate-spin" fill="none" stroke="currentColor" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path></svg>Pending</span>';

                    return `
                        <div class="bg-gradient-to-br from-white to-gray-50 border border-gray-200 rounded-lg p-6 hover:shadow-lg transition-shadow">
                            <div class="flex justify-between items-start mb-4">
                                <div>
                                    <a href="/donation/track?tracking_code=${donation.tracking_code}" class="text-blue-600 hover:text-blue-800 font-mono text-sm font-semibold">
                                        ${donation.tracking_code}
                                    </a>
                                    ${statusBadge}
                                </div>
                                <div class="flex flex-col items-end">
                                    <span class="text-xs text-gray-500">${donation.time_ago}</span>
                                </div>
                            </div>

                            <div class="mb-4">
                                ${isPhysical ? `
                                    <h3 class="text-lg font-bold text-gray-900 mb-1">${donation.category}</h3>
                                    <p class="text-gray-600 text-sm">${donation.items_description}</p>
                                    <p class="text-sm text-gray-500 mt-1">Est. Value: ₱${parseFloat(donation.estimated_value).toLocaleString('en-PH', {minimumFractionDigits: 2})}</p>
                                ` : `
                                    <h3 class="text-2xl font-bold text-green-600 mb-1">₱${parseFloat(donation.amount).toLocaleString('en-PH', {minimumFractionDigits: 2})}</h3>
                                    <p class="text-gray-600 text-sm">Monetary • ${donation.payment_method || 'GCash'}</p>
                                `}
                            </div>

                            <div class="space-y-2 text-sm">
                                <div class="flex justify-between">
                                    <span class="text-gray-600">Donor:</span>
                                    <span class="font-medium text-gray-900">${donation.donor_name}</span>
                                </div>
                                ${donation.blockchain_tx_hash ? `
                                    <div class="mt-3 pt-3 border-t border-gray-200">
                                        <a href="${donation.explorer_url}" target="_blank" class="inline-flex items-center text-xs text-blue-600 hover:text-blue-800 font-medium">
                                            <svg class="w-3 h-3 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 6H6a2 2 0 00-2 2v10a2 2 0 002 2h10a2 2 0 002-2v-4M14 4h6m0 0v6m0-6L10 14"/>
                                            </svg>
                                            View on Blockchain
                                        </a>
                                        <p class="text-xs text-gray-500 mt-1 font-mono truncate" title="${donation.blockchain_tx_hash}">${donation.blockchain_tx_hash.substring(0, 20)}...</p>
                                    </div>
                                ` : ''}
                            </div>

                            ${donation.blockchain_status === 'confirmed' ? `
                                <div class="mt-4 pt-4 border-t border-gray-200">
                                    <div class="flex items-center text-xs text-green-600">
                                        <svg class="w-4 h-4 mr-1" fill="currentColor" viewBox="0 0 20 20">
                                            <path fill-rule="evenodd" d="M2.166 4.999A11.954 11.954 0 0010 1.944 11.954 11.954 0 0017.834 5c.11.65.166 1.32.166 2.001 0 5.225-3.34 9.67-8 11.317C5.34 16.67 2 12.225 2 7c0-.682.057-1.35.166-2.001zm11.541 3.708a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/>
                                        </svg>
                                        Permanently recorded on blockchain
                                    </div>
                                </div>
                            ` : ''}
                        </div>
                    `;
                }).join('');
            }

            // Load donations when page loads
            document.addEventListener('DOMContentLoaded', loadRecentDonations);
        </script>
    @endif

</body>
</html>

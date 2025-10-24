<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>All Verified Transactions - BayanihanCebu</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="bg-gradient-to-br from-gray-50 to-gray-100">

    {{-- Header --}}
    <header style="background: linear-gradient(to right, #1d4ed8, #1e3a8a); color: white; box-shadow: 0 4px 6px rgba(0,0,0,0.1);">
        <div style="max-width: 1280px; margin: 0 auto; padding: 16px 24px;">
            <div style="display: flex; justify-content: space-between; align-items: center;">
                <div>
                    <h1 style="font-size: 24px; font-weight: 700; margin: 0;">BayanihanCebu - Verified Transactions</h1>
                    <p style="color: #bfdbfe; font-size: 14px; margin: 4px 0 0 0;">Complete blockchain-verified donation history</p>
                </div>
                <a href="{{ route('home') }}" style="background: #2563eb; color: white; text-decoration: none; padding: 8px 16px; border-radius: 8px; font-size: 14px; font-weight: 500; display: flex; align-items: center; transition: background 0.3s;">
                    <svg width="16" height="16" fill="currentColor" viewBox="0 0 24 24" style="margin-right: 8px;">
                        <path d="M10 20v-6h4v6h5v-8h3L12 3 2 12h3v8z"/>
                    </svg>
                    Back to Home
                </a>
            </div>
        </div>
    </header>

    {{-- Main Content --}}
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-12">
        <div class="text-center mb-12">
            <div class="inline-flex items-center justify-center w-20 h-20 bg-gradient-to-br from-purple-100 to-blue-100 rounded-full mb-6">
                <svg class="w-10 h-10 text-purple-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"/>
                </svg>
            </div>
            <h1 class="text-4xl font-bold text-gray-900 mb-3">All Verified Transactions</h1>
            <p class="text-lg text-gray-600 max-w-2xl mx-auto">Every donation recorded on the Lisk Sepolia blockchain for complete transparency and accountability</p>
        </div>

        {{-- Filters --}}
        <div class="bg-white rounded-xl shadow-lg p-6 mb-8">
            <div class="flex flex-wrap items-center gap-4">
                <div class="flex items-center gap-2">
                    <label class="text-sm font-medium text-gray-700">Type:</label>
                    <select id="typeFilter" class="px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-purple-500 focus:border-transparent">
                        <option value="all">All Donations</option>
                        <option value="physical">Non-Monetary Only</option>
                        <option value="online">Monetary Only</option>
                    </select>
                </div>
                <div class="flex items-center gap-2">
                    <label class="text-sm font-medium text-gray-700">Search:</label>
                    <input type="text" id="searchInput" placeholder="Tracking code, donor name..." class="px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-purple-500 focus:border-transparent">
                </div>
                <div class="ml-auto flex items-center gap-2">
                    <span id="resultCount" class="text-sm text-gray-600"></span>
                </div>
            </div>
        </div>

        {{-- Loading State --}}
        <div id="loading" class="text-center py-20">
            <div class="inline-block animate-spin rounded-full h-16 w-16 border-b-2 border-purple-600"></div>
            <p class="mt-6 text-gray-600 text-lg">Loading verified transactions...</p>
        </div>

        {{-- Error State --}}
        <div id="error" class="hidden bg-red-50 border-l-4 border-red-500 p-6 rounded-lg">
            <div class="flex items-center">
                <svg class="w-6 h-6 text-red-500 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                </svg>
                <p class="text-red-700">Failed to load transactions. Please try again later.</p>
            </div>
        </div>

        {{-- Donations Grid --}}
        <div id="donationsGrid" class="hidden grid md:grid-cols-2 gap-6">
            {{-- Donations will be inserted here by JavaScript --}}
        </div>

        {{-- Empty State --}}
        <div id="emptyState" class="hidden text-center py-20">
            <svg class="w-16 h-16 text-gray-400 mx-auto mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 13V6a2 2 0 00-2-2H6a2 2 0 00-2 2v7m16 0v5a2 2 0 01-2 2H6a2 2 0 01-2-2v-5m16 0h-2.586a1 1 0 00-.707.293l-2.414 2.414a1 1 0 01-.707.293h-3.172a1 1 0 01-.707-.293l-2.414-2.414A1 1 0 006.586 13H4"/>
            </svg>
            <p class="text-gray-600">No transactions found matching your filters.</p>
        </div>
    </div>

    {{-- JavaScript --}}
    <script>
        let allDonations = [];
        let filteredDonations = [];

        async function loadAllDonations() {
            try {
                const response = await fetch('/api/donations/recent-verified');
                const data = await response.json();

                if (!data.success) {
                    throw new Error('Failed to load donations');
                }

                allDonations = data.donations;
                filteredDonations = allDonations;
                displayDonations();
            } catch (error) {
                console.error('Error loading donations:', error);
                document.getElementById('loading').classList.add('hidden');
                document.getElementById('error').classList.remove('hidden');
            }
        }

        function applyFilters() {
            const typeFilter = document.getElementById('typeFilter').value;
            const searchTerm = document.getElementById('searchInput').value.toLowerCase();

            filteredDonations = allDonations.filter(donation => {
                // Type filter
                if (typeFilter !== 'all' && donation.type !== typeFilter) {
                    return false;
                }

                // Search filter
                if (searchTerm) {
                    const searchableText = `${donation.tracking_code} ${donation.donor_name} ${donation.category || ''} ${donation.items_description || ''}`.toLowerCase();
                    if (!searchableText.includes(searchTerm)) {
                        return false;
                    }
                }

                return true;
            });

            displayDonations();
        }

        function displayDonations() {
            document.getElementById('loading').classList.add('hidden');
            document.getElementById('error').classList.add('hidden');

            const grid = document.getElementById('donationsGrid');
            const emptyState = document.getElementById('emptyState');
            const resultCount = document.getElementById('resultCount');

            resultCount.textContent = `${filteredDonations.length} transaction${filteredDonations.length !== 1 ? 's' : ''}`;

            if (filteredDonations.length === 0) {
                grid.classList.add('hidden');
                emptyState.classList.remove('hidden');
                return;
            }

            emptyState.classList.add('hidden');
            grid.classList.remove('hidden');
            grid.innerHTML = filteredDonations.map(donation => createDonationCard(donation)).join('');
        }

        function createDonationCard(donation) {
            const isPhysical = donation.type === 'physical';
            const bgColor = isPhysical ? 'from-purple-50 to-purple-100' : 'from-green-50 to-green-100';
            const borderColor = isPhysical ? 'border-purple-300' : 'border-green-300';
            const accentColor = isPhysical ? 'text-purple-600' : 'text-green-600';
            const iconBg = isPhysical ? 'bg-purple-500' : 'bg-green-500';

            return `
                <div class="bg-gradient-to-br ${bgColor} border-2 ${borderColor} rounded-xl p-6 hover:shadow-xl transition-all duration-200 transform hover:-translate-y-1">
                    <div class="flex items-start justify-between mb-4">
                        <div class="flex items-center gap-3">
                            <div class="${iconBg} p-2 rounded-lg">
                                ${isPhysical ? `
                                    <svg class="w-5 h-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"/>
                                    </svg>
                                ` : `
                                    <svg class="w-5 h-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                                    </svg>
                                `}
                            </div>
                            <div>
                                <a href="/donation/track?tracking_code=${donation.tracking_code}" class="font-mono text-sm font-bold text-blue-600 hover:text-blue-800 hover:underline">
                                    ${donation.tracking_code}
                                </a>
                                <span class="ml-2 inline-flex items-center px-2 py-1 rounded-full text-xs font-semibold bg-green-100 text-green-800">
                                    <svg class="w-3 h-3 mr-1" fill="currentColor" viewBox="0 0 20 20">
                                        <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/>
                                    </svg>
                                    Verified
                                </span>
                            </div>
                        </div>
                        <span class="text-xs text-gray-500 whitespace-nowrap">${donation.time_ago}</span>
                    </div>

                    <div class="mb-4">
                        ${isPhysical ? `
                            <h3 class="text-xl font-bold text-gray-900 mb-2">${donation.category}</h3>
                            <p class="text-sm text-gray-700 mb-2">${donation.items_description}</p>
                            <p class="text-lg font-bold ${accentColor}">Est. Value: ₱${parseFloat(donation.estimated_value).toLocaleString('en-PH', {minimumFractionDigits: 2})}</p>
                        ` : `
                            <h3 class="text-3xl font-bold ${accentColor} mb-1">₱${parseFloat(donation.amount).toLocaleString('en-PH', {minimumFractionDigits: 2})}</h3>
                            <p class="text-sm text-gray-700">Monetary Donation • ${donation.payment_method || 'GCash'}</p>
                        `}
                    </div>

                    <div class="space-y-2 text-sm border-t-2 ${borderColor} pt-4">
                        <div class="flex justify-between items-center">
                            <span class="text-gray-600 font-medium">Donor:</span>
                            <span class="font-bold text-gray-900">${donation.donor_name}</span>
                        </div>
                        ${donation.barangay_name ? `
                            <div class="flex justify-between items-center">
                                <span class="text-gray-600 font-medium">Barangay:</span>
                                <span class="text-gray-900">${donation.barangay_name}</span>
                            </div>
                        ` : ''}
                    </div>

                    ${donation.blockchain_tx_hash ? `
                        <div class="mt-4 pt-4 border-t-2 ${borderColor}">
                            <a href="${donation.explorer_url}" target="_blank" class="inline-flex items-center text-sm ${accentColor} hover:underline font-semibold mb-2">
                                <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 6H6a2 2 0 00-2 2v10a2 2 0 002 2h10a2 2 0 002-2v-4M14 4h6m0 0v6m0-6L10 14"/>
                                </svg>
                                View on Blockchain Explorer
                            </a>
                            <p class="text-xs text-gray-600 font-mono break-all" title="${donation.blockchain_tx_hash}">
                                TX: ${donation.blockchain_tx_hash}
                            </p>
                        </div>
                    ` : ''}

                    <div class="mt-4 pt-4 border-t-2 ${borderColor}">
                        <div class="flex items-center text-xs ${accentColor} font-semibold">
                            <svg class="w-4 h-4 mr-1" fill="currentColor" viewBox="0 0 20 20">
                                <path fill-rule="evenodd" d="M2.166 4.999A11.954 11.954 0 0010 1.944 11.954 11.954 0 0017.834 5c.11.65.166 1.32.166 2.001 0 5.225-3.34 9.67-8 11.317C5.34 16.67 2 12.225 2 7c0-.682.057-1.35.166-2.001zm11.541 3.708a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/>
                            </svg>
                            Permanently recorded on blockchain
                        </div>
                    </div>
                </div>
            `;
        }

        // Event listeners
        document.getElementById('typeFilter').addEventListener('change', applyFilters);
        document.getElementById('searchInput').addEventListener('input', applyFilters);

        // Load donations on page load
        document.addEventListener('DOMContentLoaded', loadAllDonations);
    </script>

    @include('partials.footer')
</body>
</html>

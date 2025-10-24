{{-- Blockchain-Verified Donations Section --}}
<section class="py-12 bg-white">
    <div class="container mx-auto px-4" style="max-width: 1400px;">
        <div class="text-center mb-12">
            <h2 class="text-3xl font-bold text-gray-900 mb-3">Blockchain-Verified Donations</h2>
            <p class="text-base text-gray-600">All donations are recorded on the blockchain for complete transparency</p>
        </div>

        {{-- Loading State --}}
        <div id="verified-donations-loading" class="text-center py-12">
            <div class="inline-block animate-spin rounded-full h-12 w-12 border-b-2 border-purple-600"></div>
            <p class="mt-4 text-gray-600">Loading verified donations...</p>
        </div>

        {{-- Donations Grid --}}
        <div id="verified-donations-content" class="hidden">
            <div class="grid md:grid-cols-2 gap-6 mb-8">
                {{-- Non-Monetary Donations --}}
                <div class="bg-gray-50 border border-gray-200 rounded-lg p-5">
                    <div class="flex items-center justify-between mb-6 pb-4 border-b-2 border-purple-100">
                        <div>
                            <h3 class="text-2xl font-bold text-gray-900">Non-Monetary Donations</h3>
                            <p class="text-sm text-gray-600 mt-1">In-kind contributions tracked on-chain</p>
                        </div>
                        <div class="bg-purple-100 p-3 rounded-lg">
                            <svg class="w-6 h-6 text-purple-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"/>
                            </svg>
                        </div>
                    </div>
                    <div id="physical-donations-list" class="space-y-3">
                        {{-- Physical donations will be inserted here --}}
                    </div>
                </div>

                {{-- Monetary Donations --}}
                <div class="bg-gray-50 border border-gray-200 rounded-lg p-5">
                    <div class="flex items-center justify-between mb-6 pb-4 border-b-2 border-green-100">
                        <div>
                            <h3 class="text-2xl font-bold text-gray-900">Monetary Donations</h3>
                            <p class="text-sm text-gray-600 mt-1">Financial contributions verified on blockchain</p>
                        </div>
                        <div class="bg-green-100 p-3 rounded-lg">
                            <svg class="w-6 h-6 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                            </svg>
                        </div>
                    </div>
                    <div id="online-donations-list" class="space-y-3">
                        {{-- Online donations will be inserted here --}}
                    </div>
                </div>
            </div>

            {{-- View All Button --}}
            <div class="text-center pt-6">
                <a href="{{ route('donations.all') }}" class="inline-flex items-center px-8 py-4 bg-gradient-to-r from-purple-600 to-blue-600 hover:from-purple-700 hover:to-blue-700 text-white font-bold rounded-xl transition-all duration-200 shadow-lg hover:shadow-xl transform hover:-translate-y-0.5">
                    <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 10h16M4 14h16M4 18h16"/>
                    </svg>
                    View All Transactions
                </a>
            </div>

            {{-- Error State --}}
            <div id="verified-donations-error" class="hidden text-center py-8">
                <p class="text-red-600">Failed to load donations. Please try again later.</p>
            </div>
        </div>
    </div>
</section>

{{-- JavaScript to load and display donations --}}
<script>
    async function loadVerifiedDonations() {
        try {
            const response = await fetch('/api/donations/recent-verified');
            const data = await response.json();

            if (!data.success) {
                throw new Error('Failed to load donations');
            }

            displayVerifiedDonations(data.donations);
        } catch (error) {
            console.error('Error loading verified donations:', error);
            document.getElementById('verified-donations-loading').classList.add('hidden');
            document.getElementById('verified-donations-error').classList.remove('hidden');
        }
    }

    function displayVerifiedDonations(donations) {
        // Hide loading, show content
        document.getElementById('verified-donations-loading').classList.add('hidden');
        document.getElementById('verified-donations-content').classList.remove('hidden');

        // Separate donations by type
        const physicalDonations = donations.filter(d => d.type === 'physical').slice(0, 5);
        const onlineDonations = donations.filter(d => d.type === 'online').slice(0, 5);

        // Render physical donations
        const physicalList = document.getElementById('physical-donations-list');
        if (physicalDonations.length === 0) {
            physicalList.innerHTML = `
                <div class="text-center py-6 text-gray-500">
                    <p>No verified physical donations yet.</p>
                </div>
            `;
        } else {
            physicalList.innerHTML = physicalDonations.map(donation => createDonationItem(donation, 'physical')).join('');
        }

        // Render online donations
        const onlineList = document.getElementById('online-donations-list');
        if (onlineDonations.length === 0) {
            onlineList.innerHTML = `
                <div class="text-center py-6 text-gray-500">
                    <p>No verified monetary donations yet.</p>
                </div>
            `;
        } else {
            onlineList.innerHTML = onlineDonations.map(donation => createDonationItem(donation, 'online')).join('');
        }
    }

    function createDonationItem(donation, type) {
        const isPhysical = type === 'physical';
        const accentColor = isPhysical ? 'text-purple-600' : 'text-green-600';
        const iconColor = isPhysical ? 'text-purple-500' : 'text-green-500';

        return `
            <div class="bg-white border border-gray-200 rounded-lg p-4 hover:shadow-sm transition-shadow">
                <div class="flex items-start justify-between mb-3">
                    <div class="flex-1">
                        <div class="flex items-center gap-2 mb-1">
                            <a href="/donation/track?tracking_code=${donation.tracking_code}"
                               class="font-mono text-sm font-bold text-blue-600 hover:text-blue-800 transition">
                                ${donation.tracking_code}
                            </a>
                            <span class="inline-flex items-center px-2 py-1 rounded-full text-xs font-semibold bg-green-100 text-green-800">
                                <svg class="w-3 h-3 mr-1" fill="currentColor" viewBox="0 0 20 20">
                                    <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/>
                                </svg>
                                Verified
                            </span>
                        </div>
                        ${isPhysical ? `
                            <h4 class="text-base font-bold text-gray-900">${donation.category}</h4>
                            <p class="text-sm text-gray-600 mt-1">${donation.items_description}</p>
                            <p class="text-sm font-semibold ${accentColor} mt-1">Est. Value: ₱${parseFloat(donation.estimated_value).toLocaleString('en-PH', {minimumFractionDigits: 2})}</p>
                        ` : `
                            <div class="flex items-baseline gap-2">
                                <h4 class="text-2xl font-bold ${accentColor}">₱${parseFloat(donation.amount).toLocaleString('en-PH', {minimumFractionDigits: 2})}</h4>
                                <span class="text-sm text-gray-600">• ${donation.payment_method || 'GCash'}</span>
                            </div>
                        `}
                    </div>
                    <span class="text-xs text-gray-500 whitespace-nowrap ml-2">${donation.time_ago}</span>
                </div>

                <div class="space-y-2 text-sm border-t border-gray-200 pt-3">
                    <div class="flex justify-between items-center">
                        <span class="text-gray-600">Donor:</span>
                        <span class="font-medium text-gray-900">${donation.donor_name}</span>
                    </div>
                    ${donation.barangay_name ? `
                    <div class="flex justify-between items-center">
                        <span class="text-gray-600">Barangay:</span>
                        <span class="text-gray-900">${donation.barangay_name}</span>
                    </div>
                    ` : ''}
                    ${donation.blockchain_tx_hash ? `
                        <div class="flex items-center justify-between pt-2 border-t border-gray-200">
                            <a href="${donation.explorer_url}" target="_blank"
                               class="inline-flex items-center text-xs ${iconColor} hover:underline font-medium">
                                <svg class="w-3 h-3 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 6H6a2 2 0 00-2 2v10a2 2 0 002 2h10a2 2 0 002-2v-4M14 4h6m0 0v6m0-6L10 14"/>
                                </svg>
                                View on Blockchain
                            </a>
                        </div>
                        <p class="text-xs text-gray-500 font-mono truncate" title="${donation.blockchain_tx_hash}">
                            TX: ${donation.blockchain_tx_hash.substring(0, 20)}...
                        </p>
                    ` : ''}
                </div>

                <div class="mt-3 pt-3 border-t border-gray-200">
                    <div class="flex items-center text-xs ${iconColor}">
                        <svg class="w-4 h-4 mr-1" fill="currentColor" viewBox="0 0 20 20">
                            <path fill-rule="evenodd" d="M2.166 4.999A11.954 11.954 0 0010 1.944 11.954 11.954 0 0017.834 5c.11.65.166 1.32.166 2.001 0 5.225-3.34 9.67-8 11.317C5.34 16.67 2 12.225 2 7c0-.682.057-1.35.166-2.001zm11.541 3.708a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/>
                        </svg>
                        Permanently recorded on blockchain
                    </div>
                </div>
            </div>
        `;
    }

    // Load donations when page loads
    document.addEventListener('DOMContentLoaded', loadVerifiedDonations);
</script>

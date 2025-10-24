{{-- Blockchain-Verified Donations Section --}}
<section class="py-12 bg-white">
    <div class="container mx-auto px-4" style="max-width: 1400px;">
        <div class="text-center mb-12">
            <h2 class="text-3xl font-bold text-gray-900 mb-3">Blockchain-Verified Donations</h2>
            <p class="text-base text-gray-600">All donations are recorded on the blockchain for complete transparency</p>
        </div>

        {{-- Loading State --}}
        <div id="verified-donations-loading" class="text-center py-12">
            <div class="inline-block animate-spin rounded-full h-12 w-12 border-b-2 border-orange-600"></div>
            <p class="mt-4 text-gray-600">Loading verified donations...</p>
        </div>

        {{-- Donations Tables --}}
        <div id="verified-donations-content" class="hidden">
            <div class="grid md:grid-cols-2 gap-6 mb-8">
                {{-- Non-Monetary Donations Table --}}
                <div class="bg-white border border-gray-200 rounded-lg overflow-hidden">
                    <div class="bg-gray-50 px-6 py-4 border-b border-gray-200">
                        <div class="flex items-center justify-between">
                            <div>
                                <h3 class="text-lg font-bold text-gray-900">Non-Monetary Donations</h3>
                                <p class="text-xs text-gray-600 mt-1">In-kind contributions tracked on-chain</p>
                            </div>
                            <div class="bg-orange-100 p-2 rounded">
                                <svg class="w-5 h-5 text-orange-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"/>
                                </svg>
                            </div>
                        </div>
                    </div>
                    <div class="overflow-x-auto">
                        <table class="w-full">
                            <thead class="bg-gray-50 border-b border-gray-200">
                                <tr>
                                    <th class="px-4 py-3 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">Tracking Code</th>
                                    <th class="px-4 py-3 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">Details</th>
                                    <th class="px-4 py-3 text-right text-xs font-semibold text-gray-600 uppercase tracking-wider">Value</th>
                                </tr>
                            </thead>
                            <tbody id="physical-donations-list" class="bg-white divide-y divide-gray-200">
                                {{-- Physical donations will be inserted here --}}
                            </tbody>
                        </table>
                    </div>
                </div>

                {{-- Monetary Donations Table --}}
                <div class="bg-white border border-gray-200 rounded-lg overflow-hidden">
                    <div class="bg-gray-50 px-6 py-4 border-b border-gray-200">
                        <div class="flex items-center justify-between">
                            <div>
                                <h3 class="text-lg font-bold text-gray-900">Monetary Donations</h3>
                                <p class="text-xs text-gray-600 mt-1">Financial contributions verified on blockchain</p>
                            </div>
                            <div class="bg-green-100 p-2 rounded">
                                <svg class="w-5 h-5 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                                </svg>
                            </div>
                        </div>
                    </div>
                    <div class="overflow-x-auto">
                        <table class="w-full">
                            <thead class="bg-gray-50 border-b border-gray-200">
                                <tr>
                                    <th class="px-4 py-3 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">Tracking Code</th>
                                    <th class="px-4 py-3 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">Donor</th>
                                    <th class="px-4 py-3 text-right text-xs font-semibold text-gray-600 uppercase tracking-wider">Amount</th>
                                </tr>
                            </thead>
                            <tbody id="online-donations-list" class="bg-white divide-y divide-gray-200">
                                {{-- Online donations will be inserted here --}}
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>

            {{-- View All Button --}}
            <<div class="text-center m-10 mt-16 pt-6 pb-6">
                <a href="{{ route('donations.all') }}"
                   class="inline-flex items-center px-10 py-5 bg-[#C87522] hover:bg-[#a65d1b] !text-white font-bold rounded-xl transition-all duration-200 shadow-lg hover:shadow-xl transform hover:-translate-y-0.5">
                    <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                              d="M4 6h16M4 10h16M4 14h16M4 18h16"/>
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
                <tr>
                    <td colspan="3" class="px-4 py-8 text-center text-sm text-gray-500">
                        No verified physical donations yet.
                    </td>
                </tr>
            `;
        } else {
            physicalList.innerHTML = physicalDonations.map(donation => createPhysicalRow(donation)).join('');
        }

        // Render online donations
        const onlineList = document.getElementById('online-donations-list');
        if (onlineDonations.length === 0) {
            onlineList.innerHTML = `
                <tr>
                    <td colspan="3" class="px-4 py-8 text-center text-sm text-gray-500">
                        No verified monetary donations yet.
                    </td>
                </tr>
            `;
        } else {
            onlineList.innerHTML = onlineDonations.map(donation => createOnlineRow(donation)).join('');
        }
    }

    function createPhysicalRow(donation) {
        return `
            <tr class="hover:bg-gray-50 transition-colors">
                <td class="px-4 py-4 whitespace-nowrap">
                    <a href="/donation/track?tracking_code=${donation.tracking_code}"
                       class="font-mono text-xs font-medium text-blue-600 hover:text-blue-800 transition">
                        ${donation.tracking_code}
                    </a>
                    <div class="flex items-center mt-1">
                        <span class="inline-flex items-center px-2 py-0.5 rounded text-xs font-medium bg-green-50 text-green-700 border border-green-200">
                            <svg class="w-3 h-3 mr-1" fill="currentColor" viewBox="0 0 20 20">
                                <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/>
                            </svg>
                            Verified
                        </span>
                        <span class="text-xs text-gray-500 ml-2">${donation.time_ago}</span>
                    </div>
                </td>
                <td class="px-4 py-4">
                    <div class="text-sm font-semibold text-gray-900">${donation.category}</div>
                    <div class="text-xs text-gray-600 mt-0.5">${donation.items_description}</div>
                    <div class="text-xs text-gray-500 mt-1">
                        <span class="font-medium text-gray-700">${donation.donor_name}</span>
                        ${donation.barangay_name ? `<span class="text-gray-400"> • ${donation.barangay_name}</span>` : ''}
                    </div>
                    ${donation.blockchain_tx_hash ? `
                        <div class="mt-2">
                            <a href="${donation.explorer_url}" target="_blank"
                               class="inline-flex items-center text-xs text-orange-600 hover:text-orange-800 font-medium transition">
                                <svg class="w-3 h-3 mr-1" fill="currentColor" viewBox="0 0 20 20">
                                    <path fill-rule="evenodd" d="M2.166 4.999A11.954 11.954 0 0010 1.944 11.954 11.954 0 0017.834 5c.11.65.166 1.32.166 2.001 0 5.225-3.34 9.67-8 11.317C5.34 16.67 2 12.225 2 7c0-.682.057-1.35.166-2.001zm11.541 3.708a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/>
                                </svg>
                                View on Blockchain
                            </a>
                        </div>
                    ` : ''}
                </td>
                <td class="px-4 py-4 whitespace-nowrap text-right">
                    <div class="text-sm font-bold text-orange-600">₱${parseFloat(donation.estimated_value).toLocaleString('en-PH', {minimumFractionDigits: 2})}</div>
                    <div class="text-xs text-gray-500">Est. Value</div>
                </td>
            </tr>
        `;
    }

    function createOnlineRow(donation) {
        return `
            <tr class="hover:bg-gray-50 transition-colors">
                <td class="px-4 py-4 whitespace-nowrap">
                    <a href="/donation/track?tracking_code=${donation.tracking_code}"
                       class="font-mono text-xs font-medium text-blue-600 hover:text-blue-800 transition">
                        ${donation.tracking_code}
                    </a>
                    <div class="flex items-center mt-1">
                        <span class="inline-flex items-center px-2 py-0.5 rounded text-xs font-medium bg-green-50 text-green-700 border border-green-200">
                            <svg class="w-3 h-3 mr-1" fill="currentColor" viewBox="0 0 20 20">
                                <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/>
                            </svg>
                            Verified
                        </span>
                        <span class="text-xs text-gray-500 ml-2">${donation.time_ago}</span>
                    </div>
                </td>
                <td class="px-4 py-4">
                    <div class="text-sm font-semibold text-gray-900">${donation.donor_name}</div>
                    ${donation.barangay_name ? `<div class="text-xs text-gray-600 mt-0.5">${donation.barangay_name}</div>` : ''}
                    <div class="inline-flex items-center px-2 py-0.5 bg-gray-100 rounded text-xs font-medium text-gray-700 mt-1">
                        ${donation.payment_method || 'GCash'}
                    </div>
                    ${donation.blockchain_tx_hash ? `
                        <div class="mt-2">
                            <a href="${donation.explorer_url}" target="_blank"
                               class="inline-flex items-center text-xs text-green-600 hover:text-green-800 font-medium transition">
                                <svg class="w-3 h-3 mr-1" fill="currentColor" viewBox="0 0 20 20">
                                    <path fill-rule="evenodd" d="M2.166 4.999A11.954 11.954 0 0010 1.944 11.954 11.954 0 0017.834 5c.11.65.166 1.32.166 2.001 0 5.225-3.34 9.67-8 11.317C5.34 16.67 2 12.225 2 7c0-.682.057-1.35.166-2.001zm11.541 3.708a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/>
                                </svg>
                                View on Blockchain
                            </a>
                        </div>
                    ` : ''}
                </td>
                <td class="px-4 py-4 whitespace-nowrap text-right">
                    <div class="text-lg font-bold text-green-600">₱${parseFloat(donation.amount).toLocaleString('en-PH', {minimumFractionDigits: 2})}</div>
                </td>
            </tr>
        `;
    }

    // Load donations when page loads
    document.addEventListener('DOMContentLoaded', loadVerifiedDonations);
</script>
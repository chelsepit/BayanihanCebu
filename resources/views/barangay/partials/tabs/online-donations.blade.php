<!-- TAB: Online Donations -->
<div id="online-tab" class="tab-content bg-white rounded-b-lg shadow-sm p-6">
    <h2 class="text-xl font-semibold mb-6">Online Donations (Blockchain Verified)</h2>

    <div class="overflow-x-auto">
        <table class="w-full">
            <thead class="bg-gray-50 border-b">
                <tr>
                    <th class="px-4 py-3 text-left text-sm font-medium text-gray-600">Date</th>
                    <th class="px-4 py-3 text-left text-sm font-medium text-gray-600">Donor</th>
                    <th class="px-4 py-3 text-left text-sm font-medium text-gray-600">Amount</th>
                    <th class="px-4 py-3 text-left text-sm font-medium text-gray-600">Payment Method</th>
                    <th class="px-4 py-3 text-left text-sm font-medium text-gray-600">Blockchain</th>
                </tr>
            </thead>
            <tbody id="onlineDonationsList">
                <tr>
                    <td colspan="5" class="px-4 py-12 text-center text-gray-500">
                        <i class="fas fa-globe text-5xl mb-4 text-gray-300"></i>
                        <p class="text-lg">No online donations yet.</p>
                        <p class="text-sm mt-2">Online donations from residents will appear here.</p>
                    </td>
                </tr>
            </tbody>
        </table>
    </div>
</div>

<!-- Verification Modal -->
<div id="verificationModal" class="hidden fixed inset-0 bg-black bg-opacity-50 z-50 flex items-center justify-center p-4">
    <div class="bg-white rounded-lg shadow-xl max-w-md w-full">
        <div class="p-6">
            <h3 class="text-xl font-bold mb-4">Verify Donation</h3>
            <p class="text-gray-600 mb-4">Are you sure you want to verify this donation?</p>

            <!-- Warning for blockchain-verified donations -->
            <div class="bg-yellow-50 border-l-4 border-yellow-400 p-3 mb-4 hidden" id="blockchainWarning">
                <div class="flex">
                    <div class="flex-shrink-0">
                        <i class="fas fa-exclamation-triangle text-yellow-400"></i>
                    </div>
                    <div class="ml-3">
                        <p class="text-sm text-yellow-700">
                            This donation is already verified on the blockchain and cannot be rejected.
                        </p>
                    </div>
                </div>
            </div>

            <div class="flex gap-3">
                <button onclick="confirmVerification('verify')" class="flex-1 px-4 py-2 bg-green-600 text-white rounded-lg hover:bg-green-700">
                    Verify
                </button>
                <button onclick="showRejectionForm()" class="flex-1 px-4 py-2 bg-red-600 text-white rounded-lg hover:bg-red-700">
                    Reject
                </button>
                <button onclick="closeVerificationModal()" class="flex-1 px-4 py-2 bg-gray-300 text-gray-700 rounded-lg hover:bg-gray-400">
                    Cancel
                </button>
            </div>

            <!-- Rejection Form (hidden by default) -->
            <div id="rejectionForm" class="hidden mt-4">
                <label class="block text-sm font-medium text-gray-700 mb-2">Reason for Rejection</label>
                <textarea id="rejectionReason" class="w-full px-3 py-2 border border-gray-300 rounded-lg" rows="3" placeholder="Enter reason..."></textarea>
                <div class="flex gap-3 mt-3">
                    <button onclick="confirmVerification('reject')" class="flex-1 px-4 py-2 bg-red-600 text-white rounded-lg hover:bg-red-700">
                        Confirm Reject
                    </button>
                    <button onclick="hideRejectionForm()" class="flex-1 px-4 py-2 bg-gray-300 text-gray-700 rounded-lg hover:bg-gray-400">
                        Cancel
                    </button>
                </div>
            </div>
        </div>
    </div>
</div>
<!-- TAB: Physical Donations Received -->
<div id="physical-tab" class="tab-content bg-white rounded-b-lg shadow-sm p-6">
    <div class="flex justify-between items-center mb-6">
        <h2 class="text-xl font-semibold">Physical Donations Received at Barangay</h2>
        <button onclick="openRecordModal()" class="px-4 py-2 bg-teal-500 text-white rounded hover:bg-teal-600 transition flex items-center gap-2">
            <i class="fas fa-clipboard-check"></i> Record Donation
        </button>
    </div>

    <div class="overflow-x-auto">
        <table class="w-full">
            <thead class="bg-gray-50 border-b">
                <tr>
                    <th class="px-4 py-3 text-left text-sm font-medium text-gray-600">Tracking Code</th>
                    <th class="px-4 py-3 text-left text-sm font-medium text-gray-600">Donor Name</th>
                    <th class="px-4 py-3 text-left text-sm font-medium text-gray-600">Date Recorded</th>
                    <th class="px-4 py-3 text-left text-sm font-medium text-gray-600">Category</th>
                    <th class="px-4 py-3 text-left text-sm font-medium text-gray-600">Items</th>
                    <th class="px-4 py-3 text-left text-sm font-medium text-gray-600">Blockchain</th>
                    <th class="px-4 py-3 text-left text-sm font-medium text-gray-600">Status</th>
                    <th class="px-4 py-3 text-left text-sm font-medium text-gray-600">Actions</th>
                </tr>
            </thead>
            <tbody id="donationsList">
                <tr>
                    <td colspan="8" class="px-4 py-12 text-center text-gray-500">
                        <i class="fas fa-spinner fa-spin text-3xl mb-2"></i>
                        <p>Loading donations...</p>
                    </td>
                </tr>
            </tbody>
        </table>
    </div>
</div>

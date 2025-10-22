<!-- MODAL: View Distribution Details -->
<div id="viewDistributionModal" class="modal">
    <div class="bg-white rounded-lg shadow-2xl max-w-lg w-full mx-4 max-h-[90vh] overflow-y-auto">
        <div class="border-b px-6 py-4 flex justify-between items-center">
            <div>
                <h3 class="text-lg font-semibold text-gray-800">Distribution Details</h3>
                <p class="text-sm text-gray-500">Tracking Code: <span id="viewTrackingCode" class="font-medium">---</span></p>
            </div>
            <button type="button" onclick="closeViewDistributionModal()" class="text-gray-400 hover:text-gray-600">
                <i class="fas fa-times text-xl"></i>
            </button>
        </div>

        <div class="p-6">
            <!-- Donation Information -->
            <div class="mb-6">
                <h4 class="text-base font-semibold text-gray-800 mb-3">Donation Information</h4>
                <div class="grid grid-cols-2 gap-4 text-sm">
                    <div>
                        <p class="text-gray-500">Donor Name</p>
                        <p class="font-medium text-gray-800" id="viewDonorName">---</p>
                    </div>
                    <div>
                        <p class="text-gray-500">Category</p>
                        <p class="font-medium text-gray-800" id="viewCategory">---</p>
                    </div>
                    <div>
                        <p class="text-gray-500">Quantity</p>
                        <p class="font-medium text-gray-800" id="viewQuantity">---</p>
                    </div>
                    <div>
                        <p class="text-gray-500">Date Received</p>
                        <p class="font-medium text-gray-800" id="viewDateReceived">---</p>
                    </div>
                </div>
                <div class="mt-3">
                    <p class="text-gray-500 text-sm">Items</p>
                    <p class="text-gray-800" id="viewItems">---</p>
                </div>
            </div>

            <!-- Distribution Status -->
            <div class="mb-6">
                <div class="flex justify-between items-center mb-3">
                    <h4 class="text-base font-semibold text-gray-800">Distribution Status</h4>
                    <span id="viewDistStatus" class="px-3 py-1 text-xs font-semibold rounded bg-green-100 text-green-700">
                        FULLY DISTRIBUTED
                    </span>
                </div>

                <div class="bg-gray-50 rounded-lg p-4 space-y-3">
                    <div class="flex items-start gap-3">
                        <i class="fas fa-calendar text-gray-400 mt-1"></i>
                        <div>
                            <p class="text-xs text-gray-500">Distribution Date</p>
                            <p class="font-medium text-gray-800" id="viewDistDate">---</p>
                        </div>
                    </div>
                    <div class="flex items-start gap-3">
                        <i class="fas fa-users text-gray-400 mt-1"></i>
                        <div>
                            <p class="text-xs text-gray-500">Distributed to</p>
                            <p class="font-medium text-gray-800" id="viewDistTo">---</p>
                        </div>
                    </div>
                    <div class="flex items-start gap-3">
                        <i class="fas fa-box text-gray-400 mt-1"></i>
                        <div>
                            <p class="text-xs text-gray-500">Quantity Distributed</p>
                            <p class="font-medium text-gray-800" id="viewDistQuantity">---</p>
                        </div>
                    </div>
                </div>

                <div class="mt-3" id="viewNotesSection" style="display: none;">
                    <p class="text-xs text-gray-500 mb-1">Notes</p>
                    <p class="text-gray-800" id="viewNotes">---</p>
                </div>
            </div>

            <!-- Distribution Photos -->
            <div class="mb-4">
                <div class="flex items-center gap-2 mb-3">
                    <i class="fas fa-camera text-gray-700"></i>
                    <h4 class="text-base font-semibold text-gray-800">Distribution Photos</h4>
                </div>
                <div id="viewPhotosGrid" class="grid grid-cols-3 gap-2">
                    <p class="text-sm text-gray-500 col-span-3">Loading photos...</p>
                </div>
            </div>

            <button type="button" onclick="closeViewDistributionModal()" class="w-full px-4 py-2 bg-gray-200 text-gray-700 rounded hover:bg-gray-300 transition">
                Close
            </button>
        </div>
    </div>
</div>

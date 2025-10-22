<!-- MODAL: Accept/Reject Match Request -->
<div id="respondMatchModal" class="hidden fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50">
    <div class="bg-white rounded-lg p-6 max-w-2xl w-full mx-4 max-h-[90vh] overflow-y-auto">
        <h3 class="text-xl font-bold mb-4" id="respondModalTitle">Respond to Match Request</h3>

        <div id="respondModalContent" class="mb-6">
            <!-- Match details will be inserted here -->
        </div>

        <div class="mb-4">
            <label class="block text-sm font-semibold text-gray-700 mb-2">
                Your Message <span class="text-red-500">*</span>
            </label>
            <textarea id="responseMessage"
                      rows="4"
                      class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500"
                      placeholder="Enter your message to the requesting barangay..."></textarea>
        </div>

        <div class="flex justify-end gap-3">
            <button onclick="closeRespondModal()"
                    class="px-6 py-2 bg-gray-200 text-gray-700 rounded-lg hover:bg-gray-300 font-semibold">
                Cancel
            </button>
            <button onclick="submitReject()"
                    id="rejectMatchBtn"
                    class="px-6 py-2 bg-red-600 text-white rounded-lg hover:bg-red-700 font-semibold">
                <i class="fas fa-times mr-2"></i>Reject
            </button>
            <button onclick="submitAccept()"
                    id="acceptMatchBtn"
                    class="px-6 py-2 bg-green-600 text-white rounded-lg hover:bg-green-700 font-semibold">
                <i class="fas fa-check mr-2"></i>Accept
            </button>
        </div>
    </div>
</div>

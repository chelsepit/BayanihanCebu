<!-- MODAL: Success Modal -->
<div id="successModal" class="modal">
    <div class="bg-white rounded-lg shadow-2xl max-w-md w-full mx-4">
        <div class="p-8 text-center">
            <div class="w-16 h-16 bg-green-100 rounded-full flex items-center justify-center mx-auto mb-4">
                <i class="fas fa-check-circle text-green-500 text-3xl"></i>
            </div>
            <h3 class="text-xl font-semibold text-gray-800 mb-2">Donation Recorded Successfully!</h3>
            <p class="text-gray-600 mb-6">The tracking code has been generated.</p>

            <div class="bg-blue-50 border border-blue-200 rounded-lg p-4 mb-6">
                <p class="text-sm text-gray-600 mb-1">Tracking Code</p>
                <p id="generatedTrackingCode" class="text-2xl font-bold text-[#0D47A1]">---</p>
            </div>

            <div class="bg-yellow-50 border border-yellow-200 rounded-lg p-4 mb-6">
                <div class="flex items-center justify-center gap-2 mb-2">
                    <i class="fas fa-spinner fa-spin text-yellow-600"></i>
                    <p class="text-sm font-semibold text-yellow-800">Recording to Blockchain...</p>
                </div>
                <p class="text-xs text-gray-600">This donation is being recorded on the blockchain for transparency. Verification will complete in ~30 seconds.</p>
            </div>

            <div class="flex gap-3">
                <button onclick="printReceipt()" class="flex-1 px-4 py-2 border border-gray-300 rounded hover:bg-gray-50 transition">
                    <i class="fas fa-print mr-2"></i> Print
                </button>
                <button onclick="closeSuccessModal()" class="flex-1 px-4 py-2 bg-[#0D47A1] text-white rounded hover:bg-[#0D47A1]/90 transition">
                    Done
                </button>
            </div>
        </div>
    </div>
</div>

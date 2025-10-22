<!-- MODAL: Distribution Modal (WITH PHOTO UPLOAD) -->
<div id="distributeModal" class="modal">
    <div class="bg-white rounded-lg shadow-2xl max-w-2xl w-full mx-4 max-h-[90vh] overflow-y-auto">
        <div class="border-b px-6 py-4 flex justify-between items-center">
            <div>
                <h3 class="text-xl font-semibold text-gray-800">Mark Donation as Distributed</h3>
                <p class="text-sm text-gray-500 mt-1">Upload evidence of distribution for tracking code <span id="distributeTrackingCode" class="font-medium">---</span></p>
            </div>
            <button onclick="closeDistributeModal()" class="text-gray-400 hover:text-gray-600">
                <i class="fas fa-times text-xl"></i>
            </button>
        </div>

        <form id="distributeForm" class="p-6">
            <input type="hidden" id="distributeDonationId">

            <h4 class="text-base font-semibold text-gray-800 mb-4">Distribution Details</h4>

            <div class="mb-4">
                <label class="block text-sm font-medium text-gray-700 mb-2">Distributed to *</label>
                <input type="text" name="distributed_to" required class="w-full px-3 py-2 border border-gray-300 rounded focus:outline-none focus:ring-2 focus:ring-blue-500" placeholder="e.g., 20 families in Sitio 1">
            </div>

            <div class="grid grid-cols-2 gap-4 mb-4">
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Quantity Distributed *</label>
                    <input type="text" name="quantity_distributed" required class="w-full px-3 py-2 border border-gray-300 rounded focus:outline-none focus:ring-2 focus:ring-blue-500" placeholder="e.g., 50 of 123 items">
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Distribution Date *</label>
                    <input type="date" name="distribution_date" value="{{ date('Y-m-d') }}" required class="w-full px-3 py-2 border border-gray-300 rounded focus:outline-none focus:ring-2 focus:ring-blue-500">
                </div>
            </div>

            <div class="mb-4">
                <label class="block text-sm font-medium text-gray-700 mb-2">Notes (Optional)</label>
                <textarea name="notes" rows="2" class="w-full px-3 py-2 border border-gray-300 rounded focus:outline-none focus:ring-2 focus:ring-blue-500" placeholder="Any additional information..."></textarea>
            </div>

            <div class="mb-4">
                <label class="block text-sm font-medium text-gray-700 mb-2">Distribution Status *</label>
                <select name="distribution_status" required class="w-full px-3 py-2 border border-gray-300 rounded focus:outline-none focus:ring-2 focus:ring-blue-500">
                    <option value="partially_distributed">Partially Distributed</option>
                    <option value="fully_distributed">Fully Distributed</option>
                </select>
            </div>

            <!-- Photo Upload Section - REQUIRED 5 PHOTOS -->
            <div class="mb-6">
                <h4 class="text-base font-semibold text-gray-800 mb-3 flex items-center gap-2">
                    Photo Evidence
                    <span class="text-sm font-normal text-red-600">(Required: Upload exactly 5 photos)</span>
                </h4>

                <input type="file" id="photoInput" accept="image/png,image/jpeg,image/jpg" multiple class="hidden">

                <div onclick="document.getElementById('photoInput').click()" class="border-2 border-dashed border-gray-300 rounded-lg p-8 text-center cursor-pointer hover:border-blue-500 transition">
                    <i class="fas fa-cloud-upload-alt text-4xl text-gray-400 mb-2"></i>
                    <p class="text-sm text-gray-600 mb-1">Click to upload photos</p>
                    <p class="text-xs text-gray-400">PNG, JPG up to 10MB each (Max 5 photos required)</p>
                </div>

                <!-- Photo Preview Grid -->
                <div id="photoPreviewGrid" class="grid grid-cols-5 gap-2 mt-4 hidden"></div>

                <div id="photoError" class="text-red-600 text-sm mt-2 hidden"></div>
                <div id="photoCount" class="text-blue-600 text-sm mt-2 hidden"></div>
            </div>

            <div class="flex gap-3 justify-end border-t pt-4">
                <button type="button" onclick="closeDistributeModal()" class="px-6 py-2 border border-gray-300 rounded hover:bg-gray-50 transition">
                    Cancel
                </button>
                <button type="submit" id="submitDistribution" class="px-6 py-2 bg-[#0D47A1] text-white rounded hover:bg-[#0D47A1]/90 transition" disabled>
                    Mark as Distributed
                </button>
            </div>
        </form>
    </div>
</div>

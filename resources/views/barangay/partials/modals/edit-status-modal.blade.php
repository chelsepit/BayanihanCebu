<!-- MODAL: Edit Barangay Status -->
<div id="editStatusModal" class="modal">
    <div class="bg-white rounded-lg shadow-2xl max-w-2xl w-full mx-4">
        <div class="border-b px-6 py-4 flex justify-between items-center">
            <div>
                <h3 class="text-xl font-semibold text-gray-800">Edit Barangay Status</h3>
                <p class="text-sm text-gray-500 mt-1">Update your barangay's disaster status and needs</p>
            </div>
            <button type="button" onclick="closeEditStatusModal()" class="text-gray-400 hover:text-gray-600">
                <i class="fas fa-times text-xl"></i>
            </button>
        </div>

        <form id="editStatusForm" class="p-6">
            <div class="mb-4">
                <label class="block text-sm font-medium text-gray-700 mb-2">
                    Disaster Status *
                    <span class="text-xs text-gray-500 ml-2">(This affects what LDRRMO sees on the map)</span>
                </label>
                <select id="editDisasterStatus" name="disaster_status" required class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500">
                    <option value="safe">✅ Safe - No active disasters</option>
                    <option value="warning">⚠️ Warning - Potential risk or minor impact</option>
                    <option value="critical">🔶 Critical - Significant impact, needs support</option>
                    <option value="emergency">🚨 Emergency - Severe disaster, urgent help needed</option>
                </select>
            </div>

            <div class="mb-4">
                <label class="block text-sm font-medium text-gray-700 mb-2">
                    Affected Families
                    <span class="text-xs text-gray-500 ml-2">(Leave as 0 if status is Safe)</span>
                </label>
                <input type="number" id="editAffectedFamilies" name="affected_families" min="0" value="0" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500">
            </div>

            <div class="mb-6">
                <label class="block text-sm font-medium text-gray-700 mb-2">
                    Needs Summary
                    <span class="text-xs text-gray-500 ml-2">(Brief description of situation and needs)</span>
                </label>
                <textarea id="editNeedsSummary" name="needs_summary" rows="4" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500" placeholder="Describe the current situation..."></textarea>
            </div>

            <div class="bg-blue-50 border border-blue-200 rounded-lg p-4 mb-6">
                <p class="text-sm font-semibold text-blue-900 mb-2">
                    <i class="fas fa-info-circle mr-1"></i> Preview
                </p>
                <p class="text-sm text-blue-800">
                    This information will be visible to LDRRMO and will appear on the city-wide disaster map.
                </p>
            </div>

            <div class="flex gap-3 justify-end border-t pt-4">
                <button type="button" onclick="closeEditStatusModal()" class="px-6 py-2 border border-gray-300 rounded hover:bg-gray-50 transition">
                    Cancel
                </button>
                <button type="submit" class="px-6 py-2 bg-[#0D47A1] text-white rounded hover:bg-[#0D47A1]/90 transition">
                    <i class="fas fa-save mr-2"></i> Update Status
                </button>
            </div>
        </form>
    </div>
</div>

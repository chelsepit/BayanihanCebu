<!-- MODAL: Complete Match Modal -->
<div id="completeMatchModal" class="hidden fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50">
    <div class="bg-white rounded-lg p-6 max-w-md w-full mx-4">
        <h3 class="text-xl font-bold mb-4">Mark Match as Complete</h3>

        <div class="bg-green-50 border-2 border-green-200 rounded-lg p-4 mb-4">
            <h4 class="font-semibold text-green-900 mb-2 flex items-center">
                <i class="fas fa-check-circle mr-2"></i>
                Confirm Transfer Completion
            </h4>
            <p class="text-sm text-green-800 mb-2">
                By marking this match as complete, you confirm that:
            </p>
            <ul class="text-sm text-green-800 space-y-1 list-disc list-inside">
                <li>The resources have been successfully transferred</li>
                <li>Both barangays are satisfied with the exchange</li>
                <li>This conversation will be archived (read-only)</li>
                <li>The donation and need status will be updated</li>
            </ul>
        </div>

        <div class="mb-4">
            <label class="block text-sm font-semibold text-gray-700 mb-2">
                Completion Notes (Optional)
            </label>
            <textarea id="completionNotes"
                      rows="3"
                      class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500"
                      placeholder="Add any final notes about the transfer..."></textarea>
        </div>

        <div class="flex gap-3 justify-end">
            <button onclick="closeCompleteMatchModal()"
                    class="px-6 py-2 bg-gray-200 text-gray-700 rounded-lg hover:bg-gray-300 font-semibold">
                Cancel
            </button>
            <button onclick="confirmCompleteMatch()"
                    class="px-6 py-2 bg-green-600 text-white rounded-lg hover:bg-green-700 font-semibold">
                <i class="fas fa-flag-checkered mr-2"></i>Mark Complete
            </button>
        </div>
    </div>
</div>

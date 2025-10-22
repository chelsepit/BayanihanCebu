<!-- TAB: Resource Requests -->
<div id="needs-tab" class="tab-content active bg-white rounded-b-lg shadow-sm p-6">
    <div class="flex justify-between items-center mb-6">
        <h2 class="text-xl font-semibold">Resource Requests for Your Barangay</h2>
        <div class="flex gap-3">
            <button onclick="openNeedModal()" class="px-4 py-2 bg-red-600 text-white rounded hover:bg-red-700 transition flex items-center gap-2">
                <i class="fas fa-plus"></i> Create Request
            </button>
        </div>
    </div>

    <!-- Bulk Actions Bar -->
    <div id="bulkActionsBar" class="hidden mb-4 flex gap-3 p-4 bg-gray-50 rounded-lg border border-gray-200">
        <button onclick="markAllAsFulfilled()" class="px-4 py-2 bg-green-500 text-white rounded hover:bg-green-600 transition flex items-center gap-2 text-sm">
            <i class="fas fa-check-double"></i> Mark All as Fulfilled
        </button>
        <button onclick="removeAllFulfilled()" class="px-4 py-2 bg-red-500 text-white rounded hover:bg-red-600 transition flex items-center gap-2 text-sm">
            <i class="fas fa-trash-alt"></i> Remove All Fulfilled
        </button>
        <div class="ml-auto flex items-center gap-2 text-sm text-gray-600">
            <span id="needsCount">0</span> requests
            <span class="text-gray-400">|</span>
            <span id="fulfilledCount" class="text-green-600">0 fulfilled</span>
        </div>
    </div>

    <div id="needsList" class="space-y-4">
        <div class="text-center py-12 text-gray-500">
            <i class="fas fa-spinner fa-spin text-3xl mb-2"></i>
            <p>Loading resource requests...</p>
        </div>
    </div>
</div>

<!-- TAB: Match Requests -->
<div id="match-requests-tab" class="tab-content bg-white rounded-b-xl shadow-sm p-6">
    <div class="mb-6">
        <h2 class="text-2xl font-bold mb-2">Incoming Match Requests</h2>
        <p class="text-gray-600">Review and respond to match requests from LDRRMO</p>
    </div>

    <!-- Statistics -->
    <div class="grid grid-cols-1 md:grid-cols-3 gap-4 mb-6">
        <div class="bg-gradient-to-br from-blue-500 to-blue-600 rounded-lg p-4 text-white">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-blue-100 text-sm">Pending Requests</p>
                    <p class="text-3xl font-bold" id="stats-pending-requests">0</p>
                </div>
                <i class="fas fa-inbox text-4xl opacity-20"></i>
            </div>
        </div>

        <div class="bg-gradient-to-br from-green-500 to-green-600 rounded-lg p-4 text-white">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-green-100 text-sm">Accepted</p>
                    <p class="text-3xl font-bold" id="stats-accepted-requests">0</p>
                </div>
                <i class="fas fa-check-circle text-4xl opacity-20"></i>
            </div>
        </div>

        <div class="bg-gradient-to-br from-red-500 to-red-600 rounded-lg p-4 text-white">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-red-100 text-sm">Rejected</p>
                    <p class="text-3xl font-bold" id="stats-rejected-requests">0</p>
                </div>
                <i class="fas fa-times-circle text-4xl opacity-20"></i>
            </div>
        </div>
    </div>

    <!-- Requests List -->
    <div id="incoming-requests-list" class="space-y-4">
        <div class="text-center py-8 text-gray-500">
            <i class="fas fa-spinner fa-spin text-3xl mb-2"></i>
            <p>Loading requests...</p>
        </div>
    </div>
</div>

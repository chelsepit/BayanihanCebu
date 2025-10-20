<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="<?php echo e(csrf_token()); ?>">
    <title>BayanihanCebu - BDRRMC</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        body { 
            font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', 'Roboto', sans-serif;
            background: #f5f5f5;
        }
        
        .tab-content { display: none; }
        .tab-content.active { display: block; }
        
        .tab-btn {
            padding: 12px 24px;
            border: none;
            background: transparent;
            cursor: pointer;
            font-weight: 500;
            color: #666;
            border-bottom: 3px solid transparent;
            transition: all 0.3s;
        }
        
        .tab-btn.active {
            color: #0D47A1;
            border-bottom-color: #0D47A1;
        }
        
        .modal {
            display: none;
            position: fixed;
            z-index: 1000;
            left: 0;
            top: 0;
            width: 100%;
            height: 100%;
            background-color: rgba(0,0,0,0.5);
            align-items: center;
            justify-content: center;
        }
        .modal.active { display: flex; }
        
        @media print {
            body * { visibility: hidden; }
            #printReceipt, #printReceipt * { visibility: visible; }
            #printReceipt { position: absolute; left: 0; top: 0; }
        }
    </style>
</head>
<body>

    <!-- Top Header - Dark Blue -->
    <div class="bg-[#0D47A1] text-white px-6 py-4 flex justify-between items-center">
        <div>
            <h1 class="text-xl font-semibold">BayanihanCebu - BDRRMC</h1>
            <p class="text-sm text-blue-200">Barangay <?php echo e($barangay->name ?? 'Lahug'); ?></p>
        </div>
        <div class="flex items-center gap-4">
            <div class="text-right">
                <p class="text-sm text-blue-200">Logged in as</p>
                <p class="font-medium"><?php echo e(session('user_name')); ?></p>
            </div>
            <form action="<?php echo e(route('logout')); ?>" method="POST">
                <?php echo csrf_field(); ?>
                <button type="submit" class="bg-white/20 hover:bg-white/30 px-4 py-2 rounded text-sm transition">
                    Logout
                </button>
            </form>
        </div>
    </div>

    <div class="max-w-7xl mx-auto px-6 py-6">
        
        <!-- Barangay Status Card -->
        <div class="bg-white rounded-lg shadow-sm p-6 mb-6">
            <div class="flex justify-between items-center mb-4">
                <div>
                    <h2 class="text-xl font-semibold text-gray-800">Barangay Status</h2>
                    <span class="inline-block mt-2 px-3 py-1 bg-orange-100 text-orange-700 text-sm font-medium rounded">
                        CRITICAL
                    </span>
                </div>
                <button onclick="openEditStatusModal()" class="flex items-center gap-2 px-4 py-2 border border-gray-300 rounded hover:bg-gray-50 transition">
                    <i class="fas fa-edit"></i> Edit Status
                </button>
            </div>

            <!-- Statistics Grid -->
            <div class="grid grid-cols-1 md:grid-cols-4 gap-4 mt-6">
                <div class="flex items-center gap-4">
                    <div class="w-12 h-12 bg-orange-100 rounded-lg flex items-center justify-center">
                        <i class="fas fa-users text-orange-600 text-xl"></i>
                    </div>
                    <div>
                        <p class="text-sm text-gray-600">Affected Families</p>
                        <p class="text-2xl font-bold text-gray-800"><?php echo e($stats['affected_families'] ?? 120); ?></p>
                    </div>
                </div>

                <div class="flex items-center gap-4">
                    <div class="w-12 h-12 bg-green-100 rounded-lg flex items-center justify-center">
                        <i class="fas fa-dollar-sign text-green-600 text-xl"></i>
                    </div>
                    <div>
                        <p class="text-sm text-gray-600">Total Donations</p>
                        <p class="text-2xl font-bold text-gray-800" id="totalDonationsCount">₱90,500</p>
                    </div>
                </div>

                <div class="flex items-center gap-4">
                    <div class="w-12 h-12 bg-blue-100 rounded-lg flex items-center justify-center">
                        <i class="fas fa-box text-blue-600 text-xl"></i>
                    </div>
                    <div>
                        <p class="text-sm text-gray-600">Active Requests</p>
                        <p class="text-2xl font-bold text-gray-800" id="activeRequestsCount"><?php echo e($stats['active_requests'] ?? 0); ?></p>
                    </div>
                </div>

                <div class="flex items-center gap-4">
                    <div class="w-12 h-12 bg-purple-100 rounded-lg flex items-center justify-center">
                        <i class="fas fa-check-circle text-purple-600 text-xl"></i>
                    </div>
                    <div>
                        <p class="text-sm text-gray-600">Verified Donations</p>
                        <p class="text-2xl font-bold text-gray-800"><?php echo e($stats['verified_donations'] ?? 13); ?></p>
                    </div>
                </div>
            </div>
        </div>

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

        <!-- Tabs Navigation -->
        <div class="bg-white rounded-t-lg shadow-sm border-b">
            <div class="flex gap-2 px-6">
                <button onclick="switchTab('needs')" class="tab-btn active">Resource Requests</button>
                <button onclick="switchTab('online')" class="tab-btn">Online Donations</button>
                <button onclick="switchTab('physical')" class="tab-btn">Donations Received</button>
                <button onclick="switchTab('map')" class="tab-btn">Coordination Map</button>
                <button onclick="showTab('match-requests')" 
                    class="tab-button px-6 py-3 text-gray-600 hover:text-indigo-600 hover:border-indigo-600 border-b-2 border-transparent transition font-semibold"
                    data-tab="match-requests">
                <i class="fas fa-inbox mr-2"></i>Match Requests
                <span id="incoming-requests-badge" class="hidden ml-2 bg-red-500 text-white text-xs font-bold px-2 py-1 rounded-full">0</span>
            </button>
            <button onclick="showTab('my-requests')" 
                    class="tab-button px-6 py-3 text-gray-600 hover:text-indigo-600 hover:border-indigo-600 border-b-2 border-transparent transition font-semibold"
                    data-tab="my-requests">
                <i class="fas fa-paper-plane mr-2"></i>My Requests
            </button>
            <button onclick="showTab('active-matches')" 
                    class="tab-button px-6 py-3 text-gray-600 hover:text-indigo-600 hover:border-indigo-600 border-b-2 border-transparent transition font-semibold"
                    data-tab="active-matches">
                <i class="fas fa-comments mr-2"></i>Active Matches
                <span id="active-matches-badge" class="hidden ml-2 bg-green-500 text-white text-xs font-bold px-2 py-1 rounded-full">0</span>
            </button>
            </div>
        </div>

        <!-- TAB 1: Resource Requests -->
        <div id="needs-tab" class="tab-content active bg-white rounded-b-lg shadow-sm p-6">
            <div class="flex justify-between items-center mb-6">
                <h2 class="text-xl font-semibold">Resource Requests for Your Barangay</h2>
                <div class="flex gap-3">
                    <button onclick="openRecordModal()" class="px-4 py-2 bg-teal-500 text-white rounded hover:bg-teal-600 transition flex items-center gap-2">
                        <i class="fas fa-clipboard-check"></i> Record Donation
                    </button>
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

        <!-- TAB 2: Online Donations -->
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
                            <th class="px-4 py-3 text-left text-sm font-medium text-gray-600">Status</th>
                            <th class="px-4 py-3 text-left text-sm font-medium text-gray-600">Blockchain</th>
                        </tr>
                    </thead>
                    <tbody id="onlineDonationsList">
                        <tr>
                            <td colspan="6" class="px-4 py-12 text-center text-gray-500">
                                <i class="fas fa-globe text-5xl mb-4 text-gray-300"></i>
                                <p class="text-lg">No online donations yet.</p>
                                <p class="text-sm mt-2">Online donations from residents will appear here.</p>
                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>

        <!-- TAB 3: Physical Donations Received -->
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
                            <th class="px-4 py-3 text-left text-sm font-medium text-gray-600">Status</th>
                            <th class="px-4 py-3 text-left text-sm font-medium text-gray-600">Actions</th>
                        </tr>
                    </thead>
                    <tbody id="donationsList">
                        <tr>
                            <td colspan="7" class="px-4 py-12 text-center text-gray-500">
                                <i class="fas fa-spinner fa-spin text-3xl mb-2"></i>
                                <p>Loading donations...</p>
                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>

        <!-- TAB 4: Coordination Map -->
        <div id="map-tab" class="tab-content bg-white rounded-b-lg shadow-sm p-6">
            <h2 class="text-xl font-semibold mb-6">Nearby Barangays Status</h2>
            
            <div class="bg-blue-50 border border-blue-200 rounded-lg p-4 flex items-start gap-3">
                <i class="fas fa-map-marker-alt text-blue-600 text-xl mt-1"></i>
                <div>
                    <p class="text-sm text-gray-700">
                        View status of nearby barangays to coordinate resource sharing and support
                    </p>
                </div>
            </div>
        </div>

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

<!-- TAB CONTENT: My Requests (for REQUESTERS) -->
<div id="my-requests-tab" class="tab-content bg-white rounded-b-xl shadow-sm p-6">
    <div class="mb-6">
        <h2 class="text-2xl font-bold mb-2">My Match Requests</h2>
        <p class="text-gray-600">Track the status of your resource requests</p>
    </div>

    <div id="my-requests-list" class="space-y-4">
        <div class="text-center py-8 text-gray-500">
            <i class="fas fa-spinner fa-spin text-3xl mb-2"></i>
            <p>Loading requests...</p>
        </div>
    </div>
</div>

<!-- TAB CONTENT: Active Matches (for BOTH) -->
<div id="active-matches-tab" class="tab-content bg-white rounded-b-xl shadow-sm p-6">
    <div class="mb-6">
        <h2 class="text-2xl font-bold mb-2">Active Matches</h2>
        <p class="text-gray-600">Ongoing conversations and coordination</p>
    </div>

    <div id="active-matches-list" class="space-y-4">
        <div class="text-center py-8 text-gray-500">
            <i class="fas fa-spinner fa-spin text-3xl mb-2"></i>
            <p>Loading matches...</p>
        </div>
    </div>
</div>
<!-- Accept/Reject Modal -->
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
    </div>

    <!-- MODAL 1: Record Donation Modal -->
    <div id="recordModal" class="modal">
        <div class="bg-white rounded-lg shadow-2xl max-w-2xl w-full mx-4 max-h-[90vh] overflow-y-auto">
            <div class="border-b px-6 py-4 flex justify-between items-center">
                <div>
                    <h3 class="text-xl font-semibold text-gray-800">Record Donation from Donor</h3>
                    <p class="text-sm text-gray-500 mt-1">Record physical donations received at Barangay <?php echo e($barangay->name ?? 'Lahug'); ?></p>
                </div>
                <button onclick="closeRecordModal()" class="text-gray-400 hover:text-gray-600">
                    <i class="fas fa-times text-xl"></i>
                </button>
            </div>
            
            <form id="recordDonationForm" class="p-6">
                <!-- Donor Information -->
                <div class="mb-6">
                    <h4 class="text-base font-semibold text-gray-800 mb-4">Donor Information</h4>
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">Full Name *</label>
                            <input type="text" name="donor_name" required class="w-full px-3 py-2 border border-gray-300 rounded focus:outline-none focus:ring-2 focus:ring-blue-500" placeholder="Juan Dela Cruz">
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">Contact Number *</label>
                            <input type="text" name="donor_contact" required class="w-full px-3 py-2 border border-gray-300 rounded focus:outline-none focus:ring-2 focus:ring-blue-500" placeholder="+63 912 345 6789">
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">Email Address (Optional)</label>
                            <input type="email" name="donor_email" class="w-full px-3 py-2 border border-gray-300 rounded focus:outline-none focus:ring-2 focus:ring-blue-500" placeholder="juan@example.com">
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">Address within Barangay *</label>
                            <input type="text" name="donor_address" required class="w-full px-3 py-2 border border-gray-300 rounded focus:outline-none focus:ring-2 focus:ring-blue-500" placeholder="123 Main Street">
                        </div>
                    </div>
                </div>

                <!-- Donation Details -->
                <div class="mb-6">
                    <h4 class="text-base font-semibold text-gray-800 mb-4">Donation Details</h4>
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">Donation Category *</label>
                            <select name="category" required class="w-full px-3 py-2 border border-gray-300 rounded focus:outline-none focus:ring-2 focus:ring-blue-500">
                                <option value="">Select category</option>
                                <option value="food">Food</option>
                                <option value="water">Water</option>
                                <option value="medical">Medical</option>
                                <option value="shelter">Shelter</option>
                                <option value="clothing">Clothing</option>
                                <option value="other">Other</option>
                            </select>
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">Quantity/Amount *</label>
                            <input type="text" name="quantity" required class="w-full px-3 py-2 border border-gray-300 rounded focus:outline-none focus:ring-2 focus:ring-blue-500" placeholder="e.g., 50 kilos, 100 pieces">
                        </div>
                        <div class="md:col-span-2">
                            <label class="block text-sm font-medium text-gray-700 mb-2">Item Description *</label>
                            <textarea name="items_description" required rows="3" class="w-full px-3 py-2 border border-gray-300 rounded focus:outline-none focus:ring-2 focus:ring-blue-500" placeholder="Please describe the items..."></textarea>
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">Estimated Value (Optional)</label>
                            <div class="flex items-center border border-gray-300 rounded">
                                <span class="px-3 text-gray-600">₱</span>
                                <input type="number" name="estimated_value" step="0.01" class="w-full px-3 py-2 border-0 focus:outline-none focus:ring-2 focus:ring-blue-500" placeholder="0.00">
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Allocation -->
                <div class="mb-6">
                    <h4 class="text-base font-semibold text-gray-800 mb-4">Allocation</h4>
                    <div class="grid grid-cols-1 gap-4">
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">Intended Recipients *</label>
                            <input type="text" name="intended_recipients" required class="w-full px-3 py-2 border border-gray-300 rounded focus:outline-none focus:ring-2 focus:ring-blue-500" placeholder="General Relief Distribution">
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">Notes/Special Instructions (Optional)</label>
                            <textarea name="notes" rows="2" class="w-full px-3 py-2 border border-gray-300 rounded focus:outline-none focus:ring-2 focus:ring-blue-500" placeholder="Any special instructions..."></textarea>
                        </div>
                    </div>
                </div>

                <!-- Submit Buttons -->
                <div class="flex gap-3 justify-end border-t pt-4">
                    <button type="button" onclick="closeRecordModal()" class="px-6 py-2 border border-gray-300 rounded hover:bg-gray-50 transition">
                        Cancel
                    </button>
                    <button type="submit" class="px-6 py-2 bg-[#0D47A1] text-white rounded hover:bg-[#0D47A1]/90 transition">
                        Generate Tracking Code
                    </button>
                </div>
            </form>
        </div>
    </div>

    <!-- MODAL 2: Success Modal -->
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

    <!-- MODAL 3: Distribution Modal (WITH PHOTO UPLOAD) -->
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
                        <input type="date" name="distribution_date" value="<?php echo e(date('Y-m-d')); ?>" required class="w-full px-3 py-2 border border-gray-300 rounded focus:outline-none focus:ring-2 focus:ring-blue-500">
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

    <!-- MODAL 4: Add Resource Need Modal -->
    <div id="needModal" class="modal">
        <div class="bg-white rounded-lg shadow-2xl max-w-2xl w-full mx-4">
            <div class="border-b px-6 py-4 flex justify-between items-center">
                <h3 class="text-xl font-semibold text-gray-800">Create Resource Request</h3>
                <button onclick="closeNeedModal()" class="text-gray-400 hover:text-gray-600">
                    <i class="fas fa-times text-xl"></i>
                </button>
            </div>
            
            <form id="needForm" class="p-6">
                <div class="grid grid-cols-2 gap-4 mb-4">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Category *</label>
                        <select name="category" required class="w-full px-3 py-2 border border-gray-300 rounded focus:outline-none focus:ring-2 focus:ring-blue-500">
                            <option value="">Select category</option>
                            <option value="food">Food</option>
                            <option value="water">Water</option>
                            <option value="medical">Medical</option>
                            <option value="shelter">Shelter</option>
                            <option value="clothing">Clothing</option>
                            <option value="other">Other</option>
                        </select>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Urgency *</label>
                        <select name="urgency" required class="w-full px-3 py-2 border border-gray-300 rounded focus:outline-none focus:ring-2 focus:ring-blue-500">
                            <option value="">Select urgency</option>
                            <option value="low">Low</option>
                            <option value="medium">Medium</option>
                            <option value="high">High</option>
                            <option value="critical">Critical</option>
                        </select>
                    </div>
                </div>

                <div class="grid grid-cols-2 gap-4 mb-4">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Quantity Needed *</label>
                        <input type="text" name="quantity" required class="w-full px-3 py-2 border border-gray-300 rounded focus:outline-none focus:ring-2 focus:ring-blue-500" placeholder="e.g., 500 family packs">
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Affected Families *</label>
                        <input type="number" name="affected_families" required class="w-full px-3 py-2 border border-gray-300 rounded focus:outline-none focus:ring-2 focus:ring-blue-500" placeholder="Number of families">
                    </div>
                </div>

                <div class="mb-6">
                    <label class="block text-sm font-medium text-gray-700 mb-2">Description *</label>
                    <textarea name="description" required rows="4" class="w-full px-3 py-2 border border-gray-300 rounded focus:outline-none focus:ring-2 focus:ring-blue-500" placeholder="Describe what you need and why..."></textarea>
                </div>

                <div class="flex gap-3 justify-end border-t pt-4">
                    <button type="button" onclick="closeNeedModal()" class="px-6 py-2 border border-gray-300 rounded hover:bg-gray-50 transition">
                        Cancel
                    </button>
                    <button type="submit" class="px-6 py-2 bg-red-600 text-white rounded hover:bg-red-700 transition">
                        Create Resource Request
                    </button>
                </div>
            </form>
        </div>
    </div>

    <!-- Hidden Print Receipt -->
    <div id="printReceipt" style="display: none;"></div>

    <script>
        const csrfToken = document.querySelector('meta[name="csrf-token"]').content;

           async function fetchAPI(url, options = {}) {
        try {
            const response = await fetch(url, {
                ...options,
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': csrfToken,
                    ...options.headers
                }
            });

            if (!response.ok) {
                throw new Error(`HTTP ${response.status}: ${response.statusText}`);
            }

            return await response.json();
        } catch (error) {
            console.error(`API Error (${url}):`, error);
            throw error;
        }
    }

        // ==================== TAB SWITCHING ====================
        function switchTab(tabName) {
            showTab(tabName);
        }

        function showTab(tabName) {
            // Remove active class from all tab contents
            document.querySelectorAll('.tab-content').forEach(tab => {
                tab.classList.remove('active');
            });

            // Remove active class from all tab buttons (.tab-btn and .tab-button)
            document.querySelectorAll('.tab-btn, .tab-button').forEach(btn => {
                btn.classList.remove('active');
                btn.classList.remove('text-indigo-600', 'border-indigo-600');
                btn.classList.add('text-gray-600', 'border-transparent');
            });

            // Add active class to the clicked tab content
            const tabContent = document.getElementById(tabName + '-tab');
            if (tabContent) {
                tabContent.classList.add('active');
            }

            // Add active class to the clicked button
            const activeButton = document.querySelector(`[data-tab="${tabName}"]`) || event?.target;
            if (activeButton) {
                activeButton.classList.add('active');
                if (activeButton.classList.contains('tab-button')) {
                    activeButton.classList.remove('text-gray-600', 'border-transparent');
                    activeButton.classList.add('text-indigo-600', 'border-indigo-600');
                }
            }

            // Load data for specific tabs
            if (tabName === 'physical') {
                loadPhysicalDonations();
            } else if (tabName === 'needs') {
                loadResourceNeeds();
            } else if (tabName === 'online') {
                loadOnlineDonations();
            } else if (tabName === 'match-requests') {
                loadIncomingRequests();
            } else if (tabName === 'my-requests') {
                loadMyRequests();
            } else if (tabName === 'active-matches') {
                loadActiveMatches();
            }
        }

        // ==================== LOAD RESOURCE NEEDS (UPDATED WITH BULK ACTIONS) ====================
        async function loadResourceNeeds() {
            const container = document.getElementById('needsList');
            container.innerHTML = '<div class="text-center py-8"><i class="fas fa-spinner fa-spin text-3xl text-gray-400"></i></div>';
            
            try {
                const response = await fetch('/api/bdrrmc/needs');
                const needs = await response.json();
                
                if (needs.length === 0) {
                    container.innerHTML = `
                        <div class="text-center py-12 text-gray-500">
                            <i class="fas fa-clipboard-list text-5xl mb-4 text-gray-300"></i>
                            <p class="text-lg">No resource requests yet.</p>
                            <p class="text-sm mt-2">Click "Create Request" to add your first resource need.</p>
                        </div>
                    `;
                    document.getElementById('bulkActionsBar').classList.add('hidden');
                    return;
                }
                
                // Show bulk actions bar
                document.getElementById('bulkActionsBar').classList.remove('hidden');
                
                // Update counts
                const pendingCount = needs.filter(n => n.status !== 'fulfilled').length;
                const fulfilledCount = needs.filter(n => n.status === 'fulfilled').length;
                document.getElementById('activeRequestsCount').textContent = pendingCount;
                document.getElementById('needsCount').textContent = needs.length;
                document.getElementById('fulfilledCount').textContent = fulfilledCount;
                
                container.innerHTML = needs.map(need => `
                    <div class="border border-gray-200 rounded-lg p-6 hover:shadow-md transition ${need.status === 'fulfilled' ? 'bg-green-50 opacity-75' : ''}">
                        <div class="flex justify-between items-start mb-4">
                            <div class="flex-1">
                                <div class="flex items-center gap-3 mb-3">
                                    <h3 class="text-lg font-semibold text-gray-800">${formatCategory(need.category)}</h3>
                                    <span class="px-3 py-1 text-xs font-semibold rounded ${getUrgencyBadge(need.urgency)}">
                                        ${need.urgency.toUpperCase()}
                                    </span>
                                    <span class="px-3 py-1 text-xs font-semibold rounded ${getNeedStatusBadge(need.status)}">
                                        ${formatStatus(need.status)}
                                    </span>
                                </div>
                                <p class="text-gray-700 mb-4">${need.description}</p>
                                <div class="grid grid-cols-3 gap-4 text-sm">
                                    <div>
                                        <p class="text-gray-500">Quantity:</p>
                                        <p class="font-medium text-gray-800">${need.quantity}</p>
                                    </div>
                                    <div>
                                        <p class="text-gray-500">Affected Families:</p>
                                        <p class="font-medium text-gray-800">120</p>
                                    </div>
                                    <div>
                                        <p class="text-gray-500">Created:</p>
                                        <p class="font-medium text-gray-800">${formatDate(need.created_at)}</p>
                                    </div>
                                </div>
                            </div>
                            <div class="ml-4 flex flex-col gap-2">
                                ${need.status !== 'fulfilled' ? `
                                    <button onclick="markNeedAsFulfilled(${need.id})" class="px-4 py-2 bg-green-500 text-white rounded hover:bg-green-600 transition text-sm flex items-center gap-2">
                                        <i class="fas fa-check"></i> Mark as Fulfilled
                                    </button>
                                    <button onclick="updateNeedStatus(${need.id})" class="px-4 py-2 bg-blue-500 text-white rounded hover:bg-blue-600 transition text-sm flex items-center gap-2">
                                        <i class="fas fa-edit"></i> Update Status
                                    </button>
                                ` : `
                                    <button onclick="removeNeed(${need.id})" class="px-4 py-2 bg-red-500 text-white rounded hover:bg-red-600 transition text-sm flex items-center gap-2">
                                        <i class="fas fa-trash"></i> Remove
                                    </button>
                                    <button onclick="markNeedAsPending(${need.id})" class="px-4 py-2 bg-gray-500 text-white rounded hover:bg-gray-600 transition text-sm flex items-center gap-2">
                                        <i class="fas fa-undo"></i> Reopen
                                    </button>
                                `}
                            </div>
                        </div>
                    </div>
                `).join('');
                
            } catch (error) {
                console.error('Error loading needs:', error);
                container.innerHTML = `
                    <div class="text-center py-12 text-red-500">
                        <i class="fas fa-exclamation-circle text-5xl mb-4"></i>
                        <p class="text-lg">Error loading resource requests</p>
                    </div>
                `;
            }
        }

        // ==================== LOAD PHYSICAL DONATIONS ====================
        async function loadPhysicalDonations() {
            const tbody = document.getElementById('donationsList');
            tbody.innerHTML = '<tr><td colspan="7" class="px-4 py-8 text-center"><i class="fas fa-spinner fa-spin text-3xl text-gray-400"></i></td></tr>';
            
            try {
                const response = await fetch('/api/bdrrmc/physical-donations');
                const donations = await response.json();
                
                if (donations.length === 0) {
                    tbody.innerHTML = `
                        <tr>
                            <td colspan="7" class="px-4 py-12 text-center text-gray-500">
                                <i class="fas fa-box-open text-5xl mb-4 text-gray-300"></i>
                                <p class="text-lg">No physical donations recorded yet.</p>
                            </td>
                        </tr>
                    `;
                    return;
                }
                
                tbody.innerHTML = donations.map(donation => `
                    <tr class="border-b hover:bg-gray-50">
                        <td class="px-4 py-3">
                            <a href="#" onclick="viewDonationDetails(${donation.id}); return false;" class="text-blue-600 hover:underline font-medium">${donation.tracking_code}</a>
                        </td>
                        <td class="px-4 py-3 text-gray-800">${donation.donor_name}</td>
                        <td class="px-4 py-3 text-gray-600">${formatDateShort(donation.recorded_at)}</td>
                        <td class="px-4 py-3 text-gray-600">${formatCategory(donation.category)}</td>
                        <td class="px-4 py-3 text-gray-600">${donation.quantity}</td>
                        <td class="px-4 py-3">
                            <span class="px-3 py-1 text-xs font-semibold rounded ${getDistributionStatusBadge(donation.distribution_status)}">
                                ${formatStatus(donation.distribution_status).toUpperCase()}
                            </span>
                        </td>
                        <td class="px-4 py-3">
                            <div class="flex gap-2">
                                ${donation.distribution_status === 'fully_distributed' ? `
                                    <button onclick="viewDistributionDetails(${donation.id})" class="flex items-center gap-2 px-3 py-1.5 text-sm text-gray-700 hover:bg-gray-100 rounded transition">
                                        <i class="fas fa-camera"></i> View Distribution
                                    </button>
                                ` : donation.distribution_status === 'partially_distributed' ? `
                                    <button onclick="viewDistributionDetails(${donation.id})" class="flex items-center gap-2 px-3 py-1.5 text-sm text-gray-700 hover:bg-gray-100 rounded transition">
                                        <i class="fas fa-eye"></i> View
                                    </button>
                                    <button onclick="openDistributeModal(${donation.id}, '${donation.tracking_code}')" class="flex items-center gap-2 px-3 py-1.5 text-sm bg-teal-500 text-white rounded hover:bg-teal-600 transition">
                                        <i class="fas fa-check-circle"></i> Mark Complete
                                    </button>
                                ` : `
                                    <button onclick="openPartialDistributeModal(${donation.id}, '${donation.tracking_code}')" class="flex items-center gap-2 px-3 py-1.5 text-sm text-gray-700 hover:bg-gray-100 rounded transition">
                                        Partially Dist.
                                    </button>
                                    <button onclick="openDistributeModal(${donation.id}, '${donation.tracking_code}')" class="flex items-center gap-2 px-3 py-1.5 text-sm bg-teal-500 text-white rounded hover:bg-teal-600 transition">
                                        <i class="fas fa-check-circle"></i> Fully Dist.
                                    </button>
                                `}
                            </div>
                        </td>
                    </tr>
                `).join('');
                
            } catch (error) {
                console.error('Error loading donations:', error);
                tbody.innerHTML = `
                    <tr>
                        <td colspan="7" class="px-4 py-12 text-center text-red-500">
                            <i class="fas fa-exclamation-circle text-5xl mb-4"></i>
                            <p class="text-lg">Error loading donations</p>
                        </td>
                    </tr>
                `;
            }
        }

        // ==================== LOAD ONLINE DONATIONS ====================
        async function loadOnlineDonations() {
            const tbody = document.getElementById('onlineDonationsList');
            
            try {
                const response = await fetch('/api/bdrrmc/online-donations');
                const donations = await response.json();
                
                if (donations.length === 0) {
                    tbody.innerHTML = `
                        <tr>
                            <td colspan="6" class="px-4 py-12 text-center text-gray-500">
                                <i class="fas fa-globe text-5xl mb-4 text-gray-300"></i>
                                <p class="text-lg">No online donations yet.</p>
                                <p class="text-sm mt-2">Online donations from residents will appear here.</p>
                            </td>
                        </tr>
                    `;
                }
            } catch (error) {
                console.error('Error loading online donations:', error);
            }
        }

        // ==================== EDIT STATUS MODAL ====================
        async function openEditStatusModal() {
            try {
                const response = await fetch('/api/bdrrmc/my-barangay');
                const barangay = await response.json();
                
                document.getElementById('editDisasterStatus').value = barangay.disaster_status || 'safe';
                document.getElementById('editAffectedFamilies').value = barangay.affected_families || 0;
                document.getElementById('editNeedsSummary').value = barangay.needs_summary || '';
                
                document.getElementById('editStatusModal').classList.add('active');
            } catch (error) {
                console.error('Error loading barangay info:', error);
                alert('Error loading barangay information. Please try again.');
            }
        }

        function closeEditStatusModal() {
            document.getElementById('editStatusModal').classList.remove('active');
        }

        document.getElementById('editStatusForm').addEventListener('submit', async function(e) {
            e.preventDefault();
            
            const formData = new FormData(e.target);
            const data = {
                disaster_status: formData.get('disaster_status'),
                affected_families: parseInt(formData.get('affected_families')) || 0,
                needs_summary: formData.get('needs_summary')
            };
            
            if (data.disaster_status === 'safe') {
                data.affected_families = 0;
            }
            
            try {
                const response = await fetch('/api/bdrrmc/my-barangay', {
                    method: 'PATCH',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': csrfToken
                    },
                    body: JSON.stringify(data)
                });
                
                const result = await response.json();
                
                if (result.success) {
                    closeEditStatusModal();
                    alert('✅ Barangay status updated successfully!\n\nThe changes will be reflected on the LDRRMO city map immediately.');
                    location.reload();
                } else {
                    alert('❌ Error updating barangay status. Please try again.');
                }
            } catch (error) {
                console.error('Error:', error);
                alert('❌ Error updating barangay status. Please try again.');
            }
        });

        document.getElementById('editDisasterStatus').addEventListener('change', function(e) {
            if (e.target.value === 'safe') {
                document.getElementById('editAffectedFamilies').value = 0;
                document.getElementById('editAffectedFamilies').disabled = true;
            } else {
                document.getElementById('editAffectedFamilies').disabled = false;
            }
        });

            // ==================== RECORD DONATION MODAL ====================
        function openRecordModal() {
            document.getElementById('recordModal').classList.add('active');
            document.getElementById('recordDonationForm').reset();
        }

        function closeRecordModal() {
            document.getElementById('recordModal').classList.remove('active');
        }

        document.getElementById('recordDonationForm').addEventListener('submit', async function(e) {
            e.preventDefault();
            
            const formData = new FormData(e.target);
            const data = Object.fromEntries(formData.entries());
            
            try {
                const response = await fetch('/api/bdrrmc/physical-donations', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': csrfToken
                    },
                    body: JSON.stringify(data)
                });
                
                const result = await response.json();
                
                if (result.success) {
                    closeRecordModal();
                    document.getElementById('generatedTrackingCode').textContent = result.tracking_code;
                    document.getElementById('successModal').classList.add('active');
                    loadPhysicalDonations();
                } else {
                    alert('Error recording donation. Please try again.');
                }
            } catch (error) {
                console.error('Error:', error);
                alert('Error recording donation. Please try again.');
            }
        });

        // ==================== SUCCESS MODAL ====================
        function closeSuccessModal() {
            document.getElementById('successModal').classList.remove('active');
        }

        function printReceipt() {
            const trackingCode = document.getElementById('generatedTrackingCode').textContent;
            const printContent = `
                <div style="padding: 40px; font-family: Arial, sans-serif;">
                    <div style="text-align: center; margin-bottom: 30px;">
                        <h1 style="color: #0D47A1; margin-bottom: 10px;">BayanihanCebu</h1>
                        <h2 style="color: #666;">Donation Receipt</h2>
                    </div>
                    <div style="background: #f3f4f6; padding: 20px; border-radius: 8px; margin-bottom: 20px;">
                        <p style="margin: 0 0 10px 0;"><strong>Tracking Code:</strong></p>
                        <p style="font-size: 24px; color: #0D47A1; font-weight: bold; margin: 0;">${trackingCode}</p>
                    </div>
                    <p style="color: #666; margin-bottom: 10px;">Date: ${new Date().toLocaleDateString()}</p>
                    <p style="color: #666;">Thank you for your generous donation!</p>
                </div>
            `;
            
            document.getElementById('printReceipt').innerHTML = printContent;
            window.print();
        }

        // ==================== DISTRIBUTE MODAL ====================
        function openDistributeModal(donationId, trackingCode) {
            document.getElementById('distributeDonationId').value = donationId;
            document.getElementById('distributeTrackingCode').textContent = trackingCode;
            document.getElementById('distributeModal').classList.add('active');
            document.getElementById('distributeForm').reset();
            uploadedPhotos = [];
            document.getElementById('photoPreviewGrid').innerHTML = '';
            document.getElementById('photoPreviewGrid').classList.add('hidden');
            document.getElementById('photoError').classList.add('hidden');
            document.getElementById('photoCount').classList.add('hidden');
            document.getElementById('submitDistribution').disabled = true;
        }

        function openPartialDistributeModal(donationId, trackingCode) {
            openDistributeModal(donationId, trackingCode);
            document.querySelector('select[name="distribution_status"]').value = 'partially_distributed';
        }

        function closeDistributeModal() {
            document.getElementById('distributeModal').classList.remove('active');
            uploadedPhotos = [];
        }

        // ==================== PHOTO UPLOAD HANDLING ====================
        let uploadedPhotos = [];
        const MAX_PHOTOS = 5;

        document.getElementById('photoInput').addEventListener('change', function(e) {
            const files = Array.from(e.target.files);
            const photoError = document.getElementById('photoError');
            const photoCount = document.getElementById('photoCount');
            const submitBtn = document.getElementById('submitDistribution');
            
            // Validate file count
            if (files.length > MAX_PHOTOS) {
                photoError.textContent = `Please select exactly ${MAX_PHOTOS} photos. You selected ${files.length}.`;
                photoError.classList.remove('hidden');
                photoCount.classList.add('hidden');
                submitBtn.disabled = true;
                return;
            }
            
            if (files.length < MAX_PHOTOS) {
                photoError.textContent = `Please select exactly ${MAX_PHOTOS} photos. You selected ${files.length}.`;
                photoError.classList.remove('hidden');
                photoCount.classList.add('hidden');
                submitBtn.disabled = true;
                return;
            }
            
            // Validate file types and sizes
            const validFiles = files.filter(file => {
                const isValidType = ['image/png', 'image/jpeg', 'image/jpg'].includes(file.type);
                const isValidSize = file.size <= 10 * 1024 * 1024; // 10MB
                return isValidType && isValidSize;
            });
            
            if (validFiles.length !== files.length) {
                photoError.textContent = 'Some files are invalid. Only PNG/JPG under 10MB allowed.';
                photoError.classList.remove('hidden');
                photoCount.classList.add('hidden');
                submitBtn.disabled = true;
                return;
            }
            
            // All validations passed
            photoError.classList.add('hidden');
            photoCount.textContent = `✓ ${files.length} photos selected`;
            photoCount.classList.remove('hidden');
            submitBtn.disabled = false;
            
            // Convert to base64 and preview
            uploadedPhotos = [];
            const previewGrid = document.getElementById('photoPreviewGrid');
            previewGrid.innerHTML = '';
            previewGrid.classList.remove('hidden');
            
            files.forEach((file, index) => {
                const reader = new FileReader();
                reader.onload = function(event) {
                    uploadedPhotos.push(event.target.result);
                    
                    // Create preview
                    const previewDiv = document.createElement('div');
                    previewDiv.className = 'relative';
                    previewDiv.innerHTML = `
                        <img src="${event.target.result}" class="w-full h-20 object-cover rounded border-2 border-green-500">
                        <div class="absolute top-1 right-1 bg-green-500 text-white rounded-full w-5 h-5 flex items-center justify-center text-xs">
                            ${index + 1}
                        </div>
                    `;
                    previewGrid.appendChild(previewDiv);
                };
                reader.readAsDataURL(file);
            });
        });

        // Distribution form submission
        document.getElementById('distributeForm').addEventListener('submit', async function(e) {
            e.preventDefault();
            
            // Validate photos
            if (uploadedPhotos.length !== MAX_PHOTOS) {
                alert(`Please upload exactly ${MAX_PHOTOS} photos before submitting.`);
                return;
            }
            
            const donationId = document.getElementById('distributeDonationId').value;
            const formData = new FormData(e.target);
            const data = Object.fromEntries(formData.entries());
            data.photo_urls = uploadedPhotos; // Add photos to data
            
            try {
                const response = await fetch(`/api/bdrrmc/physical-donations/${donationId}/distribute`, {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': csrfToken
                    },
                    body: JSON.stringify(data)
                });
                
                const result = await response.json();
                
                if (result.success) {
                    closeDistributeModal();
                    uploadedPhotos = []; // Reset photos
                    alert('Distribution recorded successfully with photo evidence!');
                    loadPhysicalDonations();
                } else {
                    alert('Error recording distribution. Please try again.');
                }
            } catch (error) {
                console.error('Error:', error);
                alert('Error recording distribution. Please try again.');
            }
        });

        // ==================== VIEW DISTRIBUTION DETAILS ====================
        async function viewDistributionDetails(donationId) {
            try {
                const response = await fetch(`/api/bdrrmc/physical-donations/${donationId}`);
                const donation = await response.json();
                
                // Populate modal with donation info
                document.getElementById('viewTrackingCode').textContent = donation.tracking_code;
                document.getElementById('viewDonorName').textContent = donation.donor_name;
                document.getElementById('viewCategory').textContent = formatCategory(donation.category);
                document.getElementById('viewQuantity').textContent = donation.quantity;
                document.getElementById('viewDateReceived').textContent = formatDateShort(donation.recorded_at);
                document.getElementById('viewItems').textContent = donation.items_description;
                
                // Distribution status badge
                const statusBadge = document.getElementById('viewDistStatus');
                statusBadge.textContent = formatStatus(donation.distribution_status).toUpperCase();
                statusBadge.className = `px-3 py-1 text-xs font-semibold rounded ${getDistributionStatusBadge(donation.distribution_status)}`;
                
                // Get latest distribution
                if (donation.distributions && donation.distributions.length > 0) {
                    const latestDist = donation.distributions[donation.distributions.length - 1];
                    
                    document.getElementById('viewDistDate').textContent = formatDateShort(latestDist.distributed_at);
                    document.getElementById('viewDistTo').textContent = latestDist.distributed_to;
                    document.getElementById('viewDistQuantity').textContent = latestDist.quantity_distributed;
                    
                    // Show notes if available
                    if (latestDist.notes) {
                        document.getElementById('viewNotesSection').style.display = 'block';
                        document.getElementById('viewNotes').textContent = latestDist.notes;
                    } else {
                        document.getElementById('viewNotesSection').style.display = 'none';
                    }
                    
                    // Display photos
                    const photosGrid = document.getElementById('viewPhotosGrid');
                    if (latestDist.photo_urls && latestDist.photo_urls.length > 0) {
                        photosGrid.innerHTML = latestDist.photo_urls.map(photo => `
                            <div class="aspect-square rounded-lg overflow-hidden border-2 border-gray-200 cursor-pointer hover:border-blue-500 transition" onclick="openPhotoModal('${photo}')">
                                <img src="${photo}" class="w-full h-full object-cover" alt="Distribution Photo">
                            </div>
                        `).join('');
                    } else {
                        photosGrid.innerHTML = '<p class="text-sm text-gray-500 col-span-3">No photos available</p>';
                    }
                } else {
                    // No distribution yet
                    document.getElementById('viewDistDate').textContent = 'Not distributed yet';
                    document.getElementById('viewDistTo').textContent = '---';
                    document.getElementById('viewDistQuantity').textContent = '---';
                    document.getElementById('viewNotesSection').style.display = 'none';
                    document.getElementById('viewPhotosGrid').innerHTML = '<p class="text-sm text-gray-500 col-span-3">No distribution recorded yet</p>';
                }
                
                // Open modal
                document.getElementById('viewDistributionModal').classList.add('active');
                
            } catch (error) {
                console.error('Error loading distribution details:', error);
                alert('Error loading distribution details');
            }
        }

        function closeViewDistributionModal() {
            document.getElementById('viewDistributionModal').classList.remove('active');
        }

        function openPhotoModal(photoUrl) {
            // Create full-screen photo viewer
            const modal = document.createElement('div');
            modal.className = 'modal active';
            modal.innerHTML = `
                <div class="max-w-4xl w-full mx-4">
                    <div class="bg-white rounded-lg p-4">
                        <div class="flex justify-between items-center mb-4">
                            <h3 class="text-lg font-semibold">Distribution Photo</h3>
                            <button onclick="this.closest('.modal').remove()" class="text-gray-400 hover:text-gray-600">
                                <i class="fas fa-times text-2xl"></i>
                            </button>
                        </div>
                        <img src="${photoUrl}" class="w-full rounded" alt="Distribution Photo">
                    </div>
                </div>
            `;
            document.body.appendChild(modal);
        }

        async function viewDonationDetails(donationId) {
            viewDistributionDetails(donationId);
        }

        // ==================== RESOURCE NEED MODAL ====================
        function openNeedModal() {
            document.getElementById('needModal').classList.add('active');
            document.getElementById('needForm').reset();
        }

        function closeNeedModal() {
            document.getElementById('needModal').classList.remove('active');
        }

        document.getElementById('needForm').addEventListener('submit', async function(e) {
            e.preventDefault();
            
            const formData = new FormData(e.target);
            const data = Object.fromEntries(formData.entries());
            
            try {
                const response = await fetch('/api/bdrrmc/needs', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': csrfToken
                    },
                    body: JSON.stringify(data)
                });
                
                const result = await response.json();
                
                if (result.success) {
                    closeNeedModal();
                    alert('Resource request created successfully!');
                    loadResourceNeeds();
                } else {
                    alert('Error creating resource request. Please try again.');
                }
            } catch (error) {
                console.error('Error:', error);
                alert('Error creating resource request. Please try again.');
            }
        });

                // ==================== BULK ACTIONS FOR RESOURCE NEEDS ====================
        
        async function markAllAsFulfilled() {
            if (!confirm('⚠️ Mark ALL pending resource requests as fulfilled?\n\nThis will mark all pending and partially fulfilled requests as completed.')) {
                return;
            }
            
            try {
                const response = await fetch('/api/bdrrmc/needs');
                const needs = await response.json();
                
                const pendingNeeds = needs.filter(n => n.status !== 'fulfilled');
                
                if (pendingNeeds.length === 0) {
                    alert('ℹ️ No pending requests to mark as fulfilled.');
                    return;
                }
                
                // Show progress
                const progressMsg = `Marking ${pendingNeeds.length} requests as fulfilled...`;
                console.log(progressMsg);
                
                // Mark each as fulfilled
                let successCount = 0;
                for (const need of pendingNeeds) {
                    try {
                        const res = await fetch(`/api/bdrrmc/needs/${need.id}`, {
                            method: 'PATCH',
                            headers: {
                                'Content-Type': 'application/json',
                                'X-CSRF-TOKEN': csrfToken
                            },
                            body: JSON.stringify({ status: 'fulfilled' })
                        });
                        
                        if (res.ok) successCount++;
                    } catch (err) {
                        console.error(`Failed to mark need ${need.id}:`, err);
                    }
                }
                
                alert(`✅ Successfully marked ${successCount} of ${pendingNeeds.length} requests as fulfilled!`);
                loadResourceNeeds();
                
            } catch (error) {
                console.error('Error:', error);
                alert('❌ Error marking requests as fulfilled.');
            }
        }

        async function removeAllFulfilled() {
            try {
                const response = await fetch('/api/bdrrmc/needs');
                const needs = await response.json();
                
                const fulfilledNeeds = needs.filter(n => n.status === 'fulfilled');
                
                if (fulfilledNeeds.length === 0) {
                    alert('ℹ️ No fulfilled requests to remove.');
                    return;
                }
                
                if (!confirm(`⚠️ PERMANENTLY DELETE ${fulfilledNeeds.length} fulfilled resource requests?\n\n⚡ This action CANNOT be undone!\n\nThe requests will be removed from the database.`)) {
                    return;
                }
                
                // Double confirmation for safety
                if (!confirm(`⚠️ Are you ABSOLUTELY SURE?\n\nThis will delete ${fulfilledNeeds.length} requests permanently.`)) {
                    return;
                }
                
                // Show progress
                console.log(`Deleting ${fulfilledNeeds.length} fulfilled requests...`);
                
                // Delete each fulfilled need
                let successCount = 0;
                for (const need of fulfilledNeeds) {
                    try {
                        const res = await fetch(`/api/bdrrmc/needs/${need.id}`, {
                            method: 'DELETE',
                            headers: {
                                'X-CSRF-TOKEN': csrfToken
                            }
                        });
                        
                        if (res.ok) successCount++;
                    } catch (err) {
                        console.error(`Failed to delete need ${need.id}:`, err);
                    }
                }
                
                alert(`✅ Successfully removed ${successCount} of ${fulfilledNeeds.length} fulfilled requests!`);
                loadResourceNeeds();
                
            } catch (error) {
                console.error('Error:', error);
                alert('❌ Error removing fulfilled requests.');
            }
        }

        async function markNeedAsPending(needId) {
            if (!confirm('Reopen this resource request?\n\nThis will change the status back to "pending".')) {
                return;
            }
            
            try {
                const response = await fetch(`/api/bdrrmc/needs/${needId}`, {
                    method: 'PATCH',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': csrfToken
                    },
                    body: JSON.stringify({ status: 'pending' })
                });
                
                const result = await response.json();
                
                if (result.success) {
                    alert('✅ Resource request reopened successfully!');
                    loadResourceNeeds();
                }
            } catch (error) {
                console.error('Error:', error);
                alert('❌ Error reopening resource request.');
            }
        }

        async function updateNeedStatus(needId) {
            const newStatus = prompt(
                'Update status:\n\n' +
                '1 = pending (not yet fulfilled)\n' +
                '2 = partially_fulfilled (some items received)\n' +
                '3 = fulfilled (completely fulfilled)\n\n' +
                'Enter 1, 2, or 3:',
                '1'
            );
            
            const statusMap = {
                '1': 'pending',
                '2': 'partially_fulfilled',
                '3': 'fulfilled'
            };
            
            if (!statusMap[newStatus]) {
                alert('❌ Invalid selection. Please enter 1, 2, or 3.');
                return;
            }
            
            try {
                const response = await fetch(`/api/bdrrmc/needs/${needId}`, {
                    method: 'PATCH',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': csrfToken
                    },
                    body: JSON.stringify({ status: statusMap[newStatus] })
                });
                
                const result = await response.json();
                
                if (result.success) {
                    alert('✅ Status updated successfully!');
                    loadResourceNeeds();
                }
            } catch (error) {
                console.error('Error:', error);
                alert('❌ Error updating status.');
            }
        }

        // ==================== RESOURCE REQUEST ACTIONS ====================
        async function markNeedAsFulfilled(needId) {
            if (!confirm('Mark this resource request as fulfilled?')) return;
            
            try {
                const response = await fetch(`/api/bdrrmc/needs/${needId}`, {
                    method: 'PATCH',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': csrfToken
                    },
                    body: JSON.stringify({ status: 'fulfilled' })
                });
                
                const result = await response.json();
                
                if (result.success) {
                    alert('✅ Resource request marked as fulfilled!');
                    loadResourceNeeds();
                }
            } catch (error) {
                console.error('Error:', error);
                alert('❌ Error updating status.');
            }
        }

        async function removeNeed(needId) {
            if (!confirm('⚠️ Remove this fulfilled request from the list?\n\nThis action CANNOT be undone.')) {
                return;
            }
            
            try {
                const response = await fetch(`/api/bdrrmc/needs/${needId}`, {
                    method: 'DELETE',
                    headers: {
                        'X-CSRF-TOKEN': csrfToken
                    }
                });
                
                const result = await response.json();
                
                if (result.success) {
                    alert('✅ Resource request removed successfully!');
                    loadResourceNeeds();
                }
            } catch (error) {
                console.error('Error:', error);
                alert('❌ Error removing resource request.');
            }
        }

        // ==================== HELPER FUNCTIONS ====================
        function formatDate(dateString) {
            const date = new Date(dateString);
            return date.toLocaleDateString('en-US', { 
                month: 'short', 
                day: 'numeric',
                year: 'numeric'
            });
        }

        function formatDateShort(dateString) {
            const date = new Date(dateString);
            return date.toLocaleDateString('en-US', { 
                month: '2-digit', 
                day: '2-digit',
                year: 'numeric'
            });
        }

        function formatStatus(status) {
            return status.replace(/_/g, ' ').replace(/\b\w/g, l => l.toUpperCase());
        }

        function formatCategory(category) {
            return category.charAt(0).toUpperCase() + category.slice(1);
        }

        function getUrgencyBadge(urgency) {
            const badges = {
                'low': 'bg-gray-100 text-gray-700',
                'medium': 'bg-yellow-100 text-yellow-700',
                'high': 'bg-orange-100 text-orange-700',
                'critical': 'bg-red-100 text-red-700'
            };
            return badges[urgency] || 'bg-gray-100 text-gray-700';
        }

        function getNeedStatusBadge(status) {
            const badges = {
                'pending': 'bg-yellow-100 text-yellow-700',
                'partially_fulfilled': 'bg-blue-100 text-blue-700',
                'fulfilled': 'bg-green-100 text-green-700'
            };
            return badges[status] || 'bg-gray-100 text-gray-700';
        }

        function getDistributionStatusBadge(status) {
            const badges = {
                'pending_distribution': 'bg-yellow-100 text-yellow-700',
                'partially_distributed': 'bg-blue-100 text-blue-700',
                'fully_distributed': 'bg-green-100 text-green-700'
            };
            return badges[status] || 'bg-gray-100 text-gray-700';
        }

        // ==================== INITIALIZE ON PAGE LOAD ====================
        document.addEventListener('DOMContentLoaded', function() {
            loadResourceNeeds(); // Load first tab by default
        });

        let currentMatchId = null;

// Load data when tabs are shown
document.addEventListener('DOMContentLoaded', function() {
    const matchRequestsTab = document.querySelector('[data-tab="match-requests"]');
    const myRequestsTab = document.querySelector('[data-tab="my-requests"]');
    const activeMatchesTab = document.querySelector('[data-tab="active-matches"]');
    
    if (matchRequestsTab) {
        matchRequestsTab.addEventListener('click', loadIncomingRequests);
    }
    if (myRequestsTab) {
        myRequestsTab.addEventListener('click', loadMyRequests);
    }
    if (activeMatchesTab) {
        activeMatchesTab.addEventListener('click', loadActiveMatches);
    }
    
    // Initial load to get badge counts
    loadIncomingRequests(true); // true = silent load for badge only
});

// ============================================
// INCOMING REQUESTS (FOR DONORS)
// ============================================

async function loadIncomingRequests(silentLoad = false) {
    if (!silentLoad) {
        document.getElementById('incoming-requests-list').innerHTML = `
            <div class="text-center py-8 text-gray-500">
                <i class="fas fa-spinner fa-spin text-3xl mb-2"></i>
                <p>Loading requests...</p>
            </div>
        `;
    }

    try {
        const response = await fetchAPI('/api/bdrrmc/matches/incoming');
        const matches = response.data || [];
        const counts = response.counts || { pending: 0, accepted: 0, rejected: 0 };

        // Update badge (only show pending requests)
        const badge = document.getElementById('incoming-requests-badge');
        if (counts.pending > 0) {
            badge.classList.remove('hidden');
            badge.textContent = counts.pending;
        } else {
            badge.classList.add('hidden');
        }

        // Update stats
        document.getElementById('stats-pending-requests').textContent = counts.pending;
        document.getElementById('stats-accepted-requests').textContent = counts.accepted;
        document.getElementById('stats-rejected-requests').textContent = counts.rejected;

        if (!silentLoad) {
            displayIncomingRequests(matches);
        }
    } catch (error) {
        console.error('Error loading incoming requests:', error);
        if (!silentLoad) {
            document.getElementById('incoming-requests-list').innerHTML = `
                <div class="text-center py-8 text-red-500">
                    <i class="fas fa-exclamation-circle text-3xl mb-2"></i>
                    <p>Failed to load requests</p>
                </div>
            `;
        }
    }
}

function displayIncomingRequests(requests) {
    const container = document.getElementById('incoming-requests-list');

    if (!requests || requests.length === 0) {
        container.innerHTML = `
            <div class="text-center py-12">
                <i class="fas fa-inbox text-6xl text-gray-300 mb-4"></i>
                <h3 class="text-xl font-semibold text-gray-700 mb-2">No Match Requests</h3>
                <p class="text-gray-500">You don't have any match requests at the moment.</p>
            </div>
        `;
        return;
    }

    const html = requests.map(request => {
        const statusBadge = getStatusBadge(request.status);
        const isPending = request.status === 'pending';

        return `
        <div class="border-2 ${isPending ? 'border-blue-200 bg-blue-50' : request.status === 'accepted' ? 'border-green-200 bg-green-50' : 'border-red-200 bg-red-50'} rounded-lg p-5 hover:shadow-lg transition">
            <div class="flex items-start justify-between mb-4">
                <div class="flex-1">
                    <div class="flex items-center gap-2 mb-2">
                        ${statusBadge}
                        <span class="px-3 py-1 bg-gray-100 text-gray-700 rounded-full text-xs font-bold">
                            <i class="fas fa-map-marker-alt mr-1"></i>${request.requesting_barangay.name}
                        </span>
                    </div>

                    <h3 class="text-lg font-bold text-gray-900 mb-1">
                        Request from ${request.requesting_barangay.name}
                    </h3>
                    <p class="text-sm text-gray-600">
                        <i class="fas fa-calendar mr-1"></i>
                        Received: ${request.created_at}
                    </p>
                </div>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mb-4">
                <!-- What they need -->
                <div class="bg-white border-2 border-blue-200 rounded-lg p-4">
                    <h4 class="font-semibold text-blue-900 mb-2 flex items-center">
                        <i class="fas fa-exclamation-circle mr-2 text-blue-600"></i>
                        They Need
                    </h4>
                    <div class="space-y-1 text-sm">
                        <p><span class="font-semibold">Category:</span> ${request.resource_need.category}</p>
                        <p><span class="font-semibold">Quantity:</span> ${request.resource_need.quantity}</p>
                        <p>
                            <span class="font-semibold">Urgency:</span>
                            <span class="px-2 py-1 rounded-full text-xs font-bold ${getUrgencyColor(request.resource_need.urgency)}">
                                ${request.resource_need.urgency.toUpperCase()}
                            </span>
                        </p>
                        ${request.resource_need.description ? `<p class="text-xs text-gray-600 mt-2">${request.resource_need.description}</p>` : ''}
                    </div>
                </div>

                <!-- Your donation -->
                <div class="bg-white border-2 border-green-200 rounded-lg p-4">
                    <h4 class="font-semibold text-green-900 mb-2 flex items-center">
                        <i class="fas fa-hands-helping mr-2 text-green-600"></i>
                        Your Donation
                    </h4>
                    <div class="space-y-1 text-sm">
                        <p><span class="font-semibold">Category:</span> ${request.physical_donation.category}</p>
                        <p><span class="font-semibold">Available:</span> ${request.physical_donation.quantity}</p>
                        <p><span class="font-semibold">Tracking:</span> ${request.physical_donation.tracking_code}</p>
                    </div>
                </div>
            </div>

            ${request.ldrrmo_message ? `
            <div class="bg-white border border-gray-200 rounded-lg p-3 mb-4">
                <p class="text-sm font-semibold text-gray-700 mb-1"><i class="fas fa-comment-alt mr-1"></i>LDRRMO Message:</p>
                <p class="text-sm text-gray-600">${request.ldrrmo_message}</p>
            </div>
            ` : ''}

            ${request.barangay_response ? `
            <div class="bg-white border border-gray-200 rounded-lg p-3 mb-4">
                <p class="text-sm font-semibold text-gray-700 mb-1"><i class="fas fa-reply mr-1"></i>Your Response:</p>
                <p class="text-sm text-gray-600">${request.barangay_response}</p>
            </div>
            ` : ''}

            <div class="flex gap-3 justify-end">
                ${isPending ? `
                <button onclick="openRespondModal(${request.id}, 'reject')"
                        class="px-6 py-2 bg-red-600 text-white rounded-lg hover:bg-red-700 transition font-semibold">
                    <i class="fas fa-times mr-2"></i>Reject
                </button>
                <button onclick="openRespondModal(${request.id}, 'accept')"
                        class="px-6 py-2 bg-green-600 text-white rounded-lg hover:bg-green-700 transition font-semibold">
                    <i class="fas fa-check mr-2"></i>Accept & Start Conversation
                </button>
                ` : request.status === 'accepted' ? `
                <button onclick="viewConversation(${request.id})"
                        class="px-6 py-2 bg-indigo-600 text-white rounded-lg hover:bg-indigo-700 transition font-semibold">
                    <i class="fas fa-comments mr-2"></i>View Conversation
                </button>
                ` : ''}
            </div>
        </div>
        `;
    }).join('');

    container.innerHTML = html;
}

// Helper function for status badges
function getStatusBadge(status) {
    const badges = {
        'pending': '<span class="px-3 py-1 bg-yellow-100 text-yellow-700 rounded-full text-xs font-bold"><i class="fas fa-clock mr-1"></i>Pending</span>',
        'accepted': '<span class="px-3 py-1 bg-green-100 text-green-700 rounded-full text-xs font-bold"><i class="fas fa-check-circle mr-1"></i>Accepted</span>',
        'rejected': '<span class="px-3 py-1 bg-red-100 text-red-700 rounded-full text-xs font-bold"><i class="fas fa-times-circle mr-1"></i>Rejected</span>',
        'completed': '<span class="px-3 py-1 bg-blue-100 text-blue-700 rounded-full text-xs font-bold"><i class="fas fa-flag-checkered mr-1"></i>Completed</span>',
        'cancelled': '<span class="px-3 py-1 bg-gray-100 text-gray-700 rounded-full text-xs font-bold"><i class="fas fa-ban mr-1"></i>Cancelled</span>'
    };
    return badges[status] || '<span class="px-3 py-1 bg-gray-100 text-gray-700 rounded-full text-xs font-bold">' + status + '</span>';
}

// Helper function for urgency colors
function getUrgencyColor(urgency) {
    const colors = {
        'low': 'bg-gray-100 text-gray-700',
        'medium': 'bg-blue-100 text-blue-700',
        'high': 'bg-orange-100 text-orange-700',
        'critical': 'bg-red-100 text-red-700'
    };
    return colors[urgency] || 'bg-gray-100 text-gray-700';
}

// Helper function for status colors (for inline use)
function getStatusColor(status) {
    const colors = {
        'pending': 'bg-yellow-100 text-yellow-700',
        'accepted': 'bg-green-100 text-green-700',
        'rejected': 'bg-red-100 text-red-700',
        'completed': 'bg-blue-100 text-blue-700',
        'cancelled': 'bg-gray-100 text-gray-700'
    };
    return colors[status] || 'bg-gray-100 text-gray-700';
}

// Helper function for status icons
function getStatusIcon(status) {
    const icons = {
        'pending': 'fas fa-clock',
        'accepted': 'fas fa-check-circle',
        'rejected': 'fas fa-times-circle',
        'completed': 'fas fa-flag-checkered',
        'cancelled': 'fas fa-ban'
    };
    return icons[status] || 'fas fa-question-circle';
}

// ============================================
// MY REQUESTS (FOR REQUESTERS)
// ============================================

async function loadMyRequests() {
    document.getElementById('my-requests-list').innerHTML = `
        <div class="text-center py-8 text-gray-500">
            <i class="fas fa-spinner fa-spin text-3xl mb-2"></i>
            <p>Loading your requests...</p>
        </div>
    `;

    try {
        const response = await fetchAPI('/api/bdrrmc/matches/my-requests');
        const matches = response.data || [];
        displayMyRequests(matches);
    } catch (error) {
        console.error('Error loading my requests:', error);
        document.getElementById('my-requests-list').innerHTML = `
            <div class="text-center py-8 text-red-500">
                <i class="fas fa-exclamation-circle text-3xl mb-2"></i>
                <p>Failed to load requests</p>
            </div>
        `;
    }
}

function displayMyRequests(requests) {
    const container = document.getElementById('my-requests-list');
    
    if (!requests || requests.length === 0) {
        container.innerHTML = `
            <div class="text-center py-12">
                <i class="fas fa-paper-plane text-6xl text-gray-300 mb-4"></i>
                <h3 class="text-xl font-semibold text-gray-700 mb-2">No Requests Sent</h3>
                <p class="text-gray-500">LDRRMO hasn't initiated any matches for your resource needs yet.</p>
            </div>
        `;
        return;
    }
    
    const html = requests.map(request => `
        <div class="border rounded-lg p-5 hover:shadow-md transition">
            <div class="flex items-start justify-between mb-3">
                <div class="flex-1">
                    <div class="flex items-center gap-2 mb-2">
                        <span class="px-3 py-1 rounded-full text-xs font-bold ${getStatusColor(request.status)}">
                            <i class="${getStatusIcon(request.status)} mr-1"></i>
                            ${request.status_label}
                        </span>
                    </div>
                    
                    <h3 class="text-lg font-bold text-gray-900">
                        Match with ${request.donating_barangay}
                    </h3>
                    <p class="text-sm text-gray-600">
                        <i class="fas fa-calendar mr-1"></i>
                        Initiated: ${request.initiated_at}
                    </p>
                </div>
            </div>

            <div class="bg-gray-50 border rounded-lg p-3 mb-3">
                <p class="text-sm"><span class="font-semibold">Your Need:</span> ${request.resource_need.category} - ${request.resource_need.quantity}</p>
                <p class="text-sm"><span class="font-semibold">Donor Has:</span> ${request.donation_items}</p>
            </div>

            ${request.response_message ? `
                <div class="bg-blue-50 border border-blue-200 rounded-lg p-3 mb-3">
                    <p class="text-xs text-blue-600 font-semibold mb-1">Response from ${request.donating_barangay}:</p>
                    <p class="text-sm text-gray-800">"${request.response_message}"</p>
                </div>
            ` : ''}

            <div class="flex gap-2 justify-end">
                ${request.status === 'accepted' ? `
                    <button onclick="viewConversation(${request.id})"
                            class="px-4 py-2 bg-indigo-600 text-white rounded-lg hover:bg-indigo-700 transition font-semibold">
                        <i class="fas fa-comments mr-2"></i>Open Conversation
                    </button>
                ` : ''}
                ${request.status === 'pending' ? `
                    <button class="px-4 py-2 bg-gray-200 text-gray-600 rounded-lg cursor-not-allowed" disabled>
                        <i class="fas fa-clock mr-2"></i>Waiting for Response
                    </button>
                ` : ''}
            </div>
        </div>
    `).join('');
    
    container.innerHTML = html;
}

// ============================================
// ACTIVE MATCHES (FOR BOTH)
// ============================================

async function loadActiveMatches() {
    document.getElementById('active-matches-list').innerHTML = `
        <div class="text-center py-8 text-gray-500">
            <i class="fas fa-spinner fa-spin text-3xl mb-2"></i>
            <p>Loading active matches...</p>
        </div>
    `;

    try {
        const response = await fetchAPI('/api/bdrrmc/matches/active');
        const matches = response.data || [];

        // Update badge (show total number or unread count)
        const badge = document.getElementById('active-matches-badge');
        const unreadTotal = matches.reduce((sum, m) => sum + (m.conversation?.unread_count || 0), 0);
        if (unreadTotal > 0) {
            badge.classList.remove('hidden');
            badge.textContent = unreadTotal;
        } else {
            badge.classList.add('hidden');
        }

        displayActiveMatches(matches);
    } catch (error) {
        console.error('Error loading active matches:', error);
        document.getElementById('active-matches-list').innerHTML = `
            <div class="text-center py-8 text-red-500">
                <i class="fas fa-exclamation-circle text-3xl mb-2"></i>
                <p>Failed to load matches</p>
            </div>
        `;
    }
}

function displayActiveMatches(matches) {
    const container = document.getElementById('active-matches-list');
    
    if (!matches || matches.length === 0) {
        container.innerHTML = `
            <div class="text-center py-12">
                <i class="fas fa-comments text-6xl text-gray-300 mb-4"></i>
                <h3 class="text-xl font-semibold text-gray-700 mb-2">No Active Matches</h3>
                <p class="text-gray-500">You don't have any active conversations at the moment.</p>
            </div>
        `;
        return;
    }
    
    const html = matches.map(match => `
        <div class="border-2 border-green-200 rounded-lg p-5 bg-green-50 hover:shadow-lg transition cursor-pointer"
             onclick="viewConversation(${match.id})">
            
            <div class="flex items-start justify-between mb-4">
                <div class="flex-1">
                    <div class="flex items-center gap-2 mb-2">
                        <span class="px-3 py-1 bg-green-100 text-green-700 rounded-full text-xs font-bold">
                            <i class="fas fa-check-circle mr-1"></i>Active Match
                        </span>
                        ${match.unread_messages > 0 ? `
                            <span class="px-3 py-1 bg-red-500 text-white rounded-full text-xs font-bold animate-pulse">
                                <i class="fas fa-envelope mr-1"></i>${match.unread_messages} New
                            </span>
                        ` : ''}
                    </div>
                    
                    <h3 class="text-lg font-bold text-gray-900 mb-1">
                        ${match.role === 'requester' ? 'Receiving from' : 'Donating to'} ${match.other_barangay}
                    </h3>
                    <p class="text-sm text-gray-600">
                        <i class="fas fa-handshake mr-1"></i>
                        Match accepted ${match.accepted_at}
                    </p>
                </div>

                <div class="text-right">
                    <p class="text-xs text-gray-500 mb-1">Last message:</p>
                    <p class="text-sm font-semibold text-gray-700">${match.last_message_at || 'No messages yet'}</p>
                </div>
            </div>

            <div class="bg-white border rounded-lg p-3 mb-3">
                <div class="grid grid-cols-2 gap-3 text-sm">
                    <div>
                        <p class="text-gray-600">Resource:</p>
                        <p class="font-semibold">${match.resource_category}</p>
                    </div>
                    <div>
                        <p class="text-gray-600">Total Messages:</p>
                        <p class="font-semibold">${match.total_messages}</p>
                    </div>
                </div>
            </div>

            ${match.last_message ? `
                <div class="bg-gray-50 border rounded-lg p-3 mb-3">
                    <p class="text-xs text-gray-500 mb-1">Latest message:</p>
                    <p class="text-sm text-gray-800">"${match.last_message.substring(0, 100)}${match.last_message.length > 100 ? '...' : ''}"</p>
                </div>
            ` : ''}

            <div class="flex gap-2 justify-end">
                <button class="px-4 py-2 bg-indigo-600 text-white rounded-lg hover:bg-indigo-700 transition font-semibold">
                    <i class="fas fa-comments mr-2"></i>Open Conversation
                </button>
            </div>
        </div>
    `).join('');
    
    container.innerHTML = html;
}

// ============================================
// RESPOND TO MATCH (ACCEPT/REJECT)
// ============================================

async function openRespondModal(matchId, action) {
    currentMatchId = matchId;

    // Get match details
    try {
        const response = await fetchAPI('/api/bdrrmc/matches/incoming');
        const matches = response.data || [];
        const match = matches.find(r => r.id === matchId);

        if (!match) {
            alert('Match not found');
            return;
        }

        // Update modal title
        const title = action === 'accept' ?
            '✅ Accept Match Request' :
            '❌ Reject Match Request';
        document.getElementById('respondModalTitle').textContent = title;

        // Display match details
        document.getElementById('respondModalContent').innerHTML = `
            <div class="bg-gray-50 border rounded-lg p-4 mb-4">
                <h4 class="font-semibold text-gray-900 mb-3">Match Details</h4>

                <div class="grid grid-cols-2 gap-4 mb-3">
                    <div>
                        <p class="text-xs text-gray-600">Requesting Barangay</p>
                        <p class="font-semibold">${match.requesting_barangay.name}</p>
                    </div>
                    <div>
                        <p class="text-xs text-gray-600">Status</p>
                        <p class="font-semibold">${getStatusBadge(match.status)}</p>
                    </div>
                </div>

                <div class="grid grid-cols-2 gap-4">
                    <div class="bg-blue-50 border border-blue-200 rounded p-3">
                        <p class="text-xs text-blue-600 font-semibold mb-1">They Need:</p>
                        <p class="text-sm font-semibold">${match.resource_need.category}</p>
                        <p class="text-sm text-gray-700">${match.resource_need.quantity}</p>
                        <p class="text-xs mt-1">${getUrgencyColor(match.resource_need.urgency)}</p>
                    </div>
                    <div class="bg-green-50 border border-green-200 rounded p-3">
                        <p class="text-xs text-green-600 font-semibold mb-1">You Have:</p>
                        <p class="text-sm font-semibold">${match.physical_donation.category}</p>
                        <p class="text-sm text-gray-700">${match.physical_donation.quantity}</p>
                        <p class="text-xs text-gray-500 mt-1">Code: ${match.physical_donation.tracking_code}</p>
                    </div>
                </div>

                ${match.ldrrmo_message ? `
                    <div class="mt-3 bg-blue-50 border border-blue-200 rounded p-3">
                        <p class="text-xs text-blue-600 font-semibold mb-1">Message from LDRRMO:</p>
                        <p class="text-sm text-gray-800">${match.ldrrmo_message}</p>
                    </div>
                ` : ''}
            </div>

            ${action === 'accept' ? `
                <div class="bg-green-50 border-2 border-green-200 rounded-lg p-4 mb-4">
                    <h4 class="font-semibold text-green-900 mb-2 flex items-center">
                        <i class="fas fa-check-circle mr-2"></i>
                        What happens when you accept?
                    </h4>
                    <ul class="text-sm text-green-800 space-y-1 list-disc list-inside">
                        <li>A conversation will be created with ${match.requesting_barangay.name}</li>
                        <li>Both barangays will be able to coordinate the transfer</li>
                        <li>You can discuss pickup/delivery details and timing</li>
                        <li>Your donation will be marked as "in process"</li>
                    </ul>
                </div>
            ` : `
                <div class="bg-red-50 border-2 border-red-200 rounded-lg p-4 mb-4">
                    <h4 class="font-semibold text-red-900 mb-2 flex items-center">
                        <i class="fas fa-exclamation-triangle mr-2"></i>
                        Please provide a reason for rejection
                    </h4>
                    <p class="text-sm text-red-800">
                        Help ${match.requesting_barangay.name} understand why you cannot fulfill this request.
                    </p>
                </div>
            `}
        `;
        
        // Show/hide buttons
        if (action === 'accept') {
            document.getElementById('acceptMatchBtn').classList.remove('hidden');
            document.getElementById('rejectMatchBtn').classList.add('hidden');
            document.getElementById('responseMessage').placeholder = 
                'Example: "Happy to help! We can arrange pickup this week. What time works best for you?"';
        } else {
            document.getElementById('acceptMatchBtn').classList.add('hidden');
            document.getElementById('rejectMatchBtn').classList.remove('hidden');
            document.getElementById('responseMessage').placeholder = 
                'Example: "Sorry, this donation has already been committed to another barangay." or "We need to keep this for our own residents."';
        }
        
        // Clear previous message
        document.getElementById('responseMessage').value = '';
        
        // Show modal
        document.getElementById('respondMatchModal').classList.remove('hidden');
        
    } catch (error) {
        console.error('Error loading match details:', error);
        alert('Failed to load match details');
    }
}

function closeRespondModal() {
    document.getElementById('respondMatchModal').classList.add('hidden');
    currentMatchId = null;
}

async function submitAccept() {
    const message = document.getElementById('responseMessage').value.trim();

    if (!message) {
        alert('⚠️ Please enter a message to the requesting barangay');
        return;
    }

    if (!currentMatchId) {
        alert('Error: No match selected');
        return;
    }

    try {
        const response = await fetchAPI(`/api/bdrrmc/matches/${currentMatchId}/respond`, {
            method: 'POST',
            body: JSON.stringify({
                action: 'accept',
                message: message
            })
        });

        if (response.success) {
            alert(
                '✅ Match Accepted!\n\n' +
                'A conversation has been created. You can now coordinate with the requesting barangay.\n\n' +
                'View it in the "Active Matches" tab.'
            );

            closeRespondModal();
            loadIncomingRequests();
            loadActiveMatches();

            // Refresh notifications
            if (typeof loadNotifications === 'function') {
                loadNotifications();
            }
        } else {
            alert('❌ Error: ' + response.message);
        }
    } catch (error) {
        console.error('Error accepting match:', error);
        alert('Failed to accept match. Please try again.');
    }
}

async function submitReject() {
    const message = document.getElementById('responseMessage').value.trim();

    if (!message) {
        alert('⚠️ Please provide a reason for rejecting this request');
        return;
    }

    if (!currentMatchId) {
        alert('Error: No match selected');
        return;
    }

    if (!confirm('Are you sure you want to reject this match request?')) {
        return;
    }

    try {
        const response = await fetchAPI(`/api/bdrrmc/matches/${currentMatchId}/respond`, {
            method: 'POST',
            body: JSON.stringify({
                action: 'reject',
                message: message
            })
        });

        if (response.success) {
            alert('✅ Match request has been rejected.\n\nThe requesting barangay has been notified.');

            closeRespondModal();
            loadIncomingRequests();

            // Refresh notifications
            if (typeof loadNotifications === 'function') {
                loadNotifications();
            }
        } else {
            alert('❌ Error: ' + response.message);
        }
    } catch (error) {
        console.error('Error rejecting match:', error);
        alert('Failed to reject match. Please try again.');
    }
}

console.log('✅ BDRRMC Match Requests system loaded');
let currentConversationMatchId = null;
let currentConversationData = null;
let messagePollingInterval = null;
let isAtBottom = true;

async function viewConversation(matchId) {
    currentConversationMatchId = matchId;
    
    // Show modal
    document.getElementById('conversationModal').classList.remove('hidden');
    
    // Load conversation
    await loadConversation(matchId);
    
    // Start polling for new messages every 5 seconds
    messagePollingInterval = setInterval(() => {
        loadConversation(matchId, true); // true = silent update
    }, 5000);
    
    // Focus on message input
    document.getElementById('messageInput').focus();
}

async function loadConversation(matchId, silentUpdate = false) {
    if (!silentUpdate) {
        document.getElementById('messagesContainer').innerHTML = `
            <div class="text-center py-12 text-gray-500">
                <i class="fas fa-spinner fa-spin text-3xl mb-2"></i>
                <p>Loading conversation...</p>
            </div>
        `;
    }
    
    try {
        const data = await fetchAPI(`/api/bdrrmc/matches/${matchId}/conversation`);
        currentConversationData = data;

        // Determine other barangay based on role
        const otherBarangay = data.my_role === 'requester' ?
            data.participants.donor.name :
            data.participants.requester.name;

        // Update header
        document.getElementById('conversationTitle').textContent =
            `Conversation with ${otherBarangay}`;
        document.getElementById('conversationSubtitle').textContent =
            `Match #${matchId} • ${data.my_role === 'requester' ? 'Receiving' : 'Donating'} ${data.resource_details.category}`;

        // Update status badge
        const statusBadge = document.getElementById('conversationStatus');
        if (data.is_active) {
            statusBadge.innerHTML = '<i class="fas fa-circle text-xs mr-1"></i>Active';
            statusBadge.className = 'px-3 py-1 bg-green-500 text-white rounded-full text-xs font-bold';
            document.getElementById('completeMatchBtn').classList.remove('hidden');
        } else {
            statusBadge.innerHTML = '<i class="fas fa-check-circle text-xs mr-1"></i>Completed';
            statusBadge.className = 'px-3 py-1 bg-gray-500 text-white rounded-full text-xs font-bold';
            document.getElementById('completeMatchBtn').classList.add('hidden');
        }

        // Update match info banner
        document.getElementById('matchInfoBanner').innerHTML = `
            <div class="flex items-center justify-between text-sm">
                <div class="flex items-center gap-6">
                    <div>
                        <span class="text-gray-600">Category:</span>
                        <span class="font-semibold text-indigo-600">${data.resource_details.category}</span>
                    </div>
                    <div>
                        <span class="text-gray-600">Need:</span>
                        <span class="font-semibold">${data.resource_details.quantity_needed}</span>
                    </div>
                    <div>
                        <span class="text-gray-600">Available:</span>
                        <span class="font-semibold">${data.resource_details.quantity_available}</span>
                    </div>
                    <div>
                        <span class="text-gray-600">Messages:</span>
                        <span class="font-semibold">${data.messages.length}</span>
                    </div>
                </div>
                <span class="px-3 py-1 ${getUrgencyColor(data.resource_details.urgency)} rounded-full text-xs font-bold">
                    ${data.resource_details.urgency.toUpperCase()}
                </span>
            </div>
        `;

        // Get current barangay ID from session
        const currentBarangayId = '<?php echo e(session("barangay_id")); ?>';

        // Display messages
        displayMessages(data.messages, currentBarangayId, silentUpdate);

        // Note: markAsReadFor is already called in the backend, so no need to call it again here
        
        // Disable input if conversation is closed
        const messageInput = document.getElementById('messageInput');
        const sendBtn = document.getElementById('sendMessageBtn');
        if (!data.is_active) {
            messageInput.disabled = true;
            messageInput.placeholder = 'This conversation has been completed and is now read-only';
            sendBtn.disabled = true;
            sendBtn.classList.add('opacity-50', 'cursor-not-allowed');
        } else {
            messageInput.disabled = false;
            messageInput.placeholder = 'Type your message...';
            sendBtn.disabled = false;
            sendBtn.classList.remove('opacity-50', 'cursor-not-allowed');
        }
        
    } catch (error) {
        console.error('Error loading conversation:', error);
        document.getElementById('messagesContainer').innerHTML = `
            <div class="text-center py-12 text-red-500">
                <i class="fas fa-exclamation-circle text-3xl mb-2"></i>
                <p>Failed to load conversation</p>
            </div>
        `;
    }
}

function displayMessages(messages, currentBarangayId, silentUpdate = false) {
    const container = document.getElementById('messagesContainer');
    
    // Check if user is at bottom before update
    const wasAtBottom = isAtBottom;
    if (!silentUpdate) {
        isAtBottom = true;
    }
    
    if (!messages || messages.length === 0) {
        container.innerHTML = `
            <div class="text-center py-12 text-gray-400">
                <i class="fas fa-comments text-6xl mb-4"></i>
                <h3 class="text-lg font-semibold text-gray-600 mb-2">Start the Conversation</h3>
                <p class="text-sm">Send your first message to begin coordinating the resource transfer.</p>
            </div>
        `;
        return;
    }
    
    const html = messages.map(msg => {
        // Use is_mine from backend instead of calculating
        const isOwn = msg.is_mine;
        const isSystem = msg.is_system || msg.message_type === 'system';

        if (isSystem) {
            return `
                <div class="flex justify-center my-6">
                    <div class="bg-blue-100 text-blue-800 px-4 py-2 rounded-full text-sm font-medium">
                        <i class="fas fa-info-circle mr-1"></i>${escapeHtml(msg.message)}
                    </div>
                </div>
            `;
        }

        return `
            <div class="flex ${isOwn ? 'justify-end' : 'justify-start'} mb-4">
                <div class="max-w-[70%]">
                    ${!isOwn ? `
                        <p class="text-xs text-gray-500 mb-1 ml-2">${escapeHtml(msg.sender_name || 'Unknown')}</p>
                    ` : ''}

                    <div class="px-4 py-3 rounded-lg ${
                        isOwn ?
                        'bg-indigo-600 text-white rounded-br-none' :
                        'bg-white border border-gray-200 text-gray-800 rounded-bl-none'
                    }">
                        <p class="text-sm whitespace-pre-wrap break-words">${escapeHtml(msg.message)}</p>

                        ${msg.attachment ? `
                            <div class="mt-2 pt-2 border-t ${isOwn ? 'border-indigo-500' : 'border-gray-200'}">
                                <a href="${msg.attachment.url}"
                                   target="_blank"
                                   class="text-xs ${isOwn ? 'text-indigo-200 hover:text-white' : 'text-indigo-600 hover:text-indigo-800'} hover:underline flex items-center">
                                    <i class="${msg.attachment.icon || 'fas fa-paperclip'} mr-1"></i>
                                    ${escapeHtml(msg.attachment.name || 'Attachment')}
                                </a>
                            </div>
                        ` : ''}

                        <div class="flex items-center justify-end mt-1 gap-2">
                            <p class="text-xs ${isOwn ? 'text-indigo-200' : 'text-gray-500'}">
                                ${msg.time_ago || msg.created_at}
                            </p>
                        </div>
                    </div>
                </div>
            </div>
        `;
    }).join('');
    
    container.innerHTML = html;
    
    // Scroll to bottom if user was at bottom or if it's a new load
    if (wasAtBottom || !silentUpdate) {
        scrollToBottom();
    }
    
    // Setup scroll listener
    container.onscroll = () => {
        const { scrollTop, scrollHeight, clientHeight } = container;
        isAtBottom = scrollTop + clientHeight >= scrollHeight - 50;
    };
}

function scrollToBottom() {
    const container = document.getElementById('messagesContainer');
    container.scrollTop = container.scrollHeight;
}

async function sendMessage() {
    const input = document.getElementById('messageInput');
    const message = input.value.trim();
    
    if (!message) {
        alert('⚠️ Please enter a message');
        return;
    }
    
    if (!currentConversationMatchId) {
        alert('Error: No conversation selected');
        return;
    }
    
    // Disable button while sending
    const sendBtn = document.getElementById('sendMessageBtn');
    const originalText = sendBtn.innerHTML;
    sendBtn.disabled = true;
    sendBtn.innerHTML = '<i class="fas fa-spinner fa-spin mr-2"></i>Sending...';
    
    try {
        const response = await fetchAPI(`/api/bdrrmc/matches/${currentConversationMatchId}/messages`, {
            method: 'POST',
            body: JSON.stringify({ message })
        });
        
        if (response.success) {
            // Clear input
            input.value = '';
            
            // Reload messages
            await loadConversation(currentConversationMatchId, true);
            
            // Scroll to bottom
            scrollToBottom();
            
            // Focus back on input
            input.focus();
        } else {
            alert('❌ Error: ' + response.message);
        }
    } catch (error) {
        console.error('Error sending message:', error);
        alert('Failed to send message. Please try again.');
    } finally {
        sendBtn.disabled = false;
        sendBtn.innerHTML = originalText;
    }
}

function handleMessageKeydown(event) {
    // Send on Enter (without Shift)
    if (event.key === 'Enter' && !event.shiftKey) {
        event.preventDefault();
        sendMessage();
    }
}

async function markMessagesAsRead(matchId) {
    try {
        await fetchAPI(`/api/bdrrmc/matches/${matchId}/messages/mark-read`, {
            method: 'POST'
        });
        
        // Update notification count
        if (typeof loadNotifications === 'function') {
            loadNotifications();
        }
    } catch (error) {
        console.error('Error marking messages as read:', error);
    }
}

function closeConversation() {
    // Stop polling
    if (messagePollingInterval) {
        clearInterval(messagePollingInterval);
        messagePollingInterval = null;
    }
    
    // Hide modal
    document.getElementById('conversationModal').classList.add('hidden');
    
    // Reset state
    currentConversationMatchId = null;
    currentConversationData = null;
    isAtBottom = true;
    
    // Reload lists to update counts
    if (typeof loadActiveMatches === 'function') {
        loadActiveMatches();
    }
    if (typeof loadMyMatches === 'function') {
        loadMyMatches();
    }
}

// ============================================
// COMPLETE MATCH
// ============================================

function openCompleteMatchModal() {
    document.getElementById('completionNotes').value = '';
    document.getElementById('completeMatchModal').classList.remove('hidden');
}

function closeCompleteMatchModal() {
    document.getElementById('completeMatchModal').classList.add('hidden');
}

async function confirmCompleteMatch() {
    if (!currentConversationMatchId) {
        alert('Error: No match selected');
        return;
    }
    
    const notes = document.getElementById('completionNotes').value.trim();
    
    if (!confirm('Are you sure you want to mark this match as complete? This action cannot be undone.')) {
        return;
    }
    
    try {
        const response = await fetchAPI(`/api/bdrrmc/matches/${currentConversationMatchId}/complete`, {
            method: 'POST',
            body: JSON.stringify({ 
                completion_notes: notes || 'Transfer completed successfully'
            })
        });
        
        if (response.success) {
            alert(
                '✅ Match Marked as Complete!\n\n' +
                'The resource transfer has been recorded as successful.\n' +
                '• Donation status: Updated to distributed\n' +
                '• Resource need: Marked as fulfilled\n' +
                '• Conversation: Now read-only\n\n' +
                'Thank you for coordinating this resource transfer!'
            );
            
            closeCompleteMatchModal();
            
            // Reload conversation to show completed state
            await loadConversation(currentConversationMatchId);
            
            // Update notification count
            if (typeof loadNotifications === 'function') {
                loadNotifications();
            }
        } else {
            alert('❌ Error: ' + response.message);
        }
    } catch (error) {
        console.error('Error completing match:', error);
        alert('Failed to complete match. Please try again.');
    }
}

function escapeHtml(text) {
    const div = document.createElement('div');
    div.textContent = text;
    return div.innerHTML;
}

console.log('✅ Conversation UI loaded');
    </script>
    <div id="conversationModal" class="hidden fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50">
    <div class="bg-white rounded-lg w-full max-w-4xl h-[90vh] flex flex-col mx-4">
        
        <!-- Header -->
        <div class="bg-indigo-600 text-white px-6 py-4 rounded-t-lg flex items-center justify-between">
            <div class="flex items-center gap-3">
                <button onclick="closeConversation()" class="text-white hover:bg-indigo-700 rounded-lg p-2">
                    <i class="fas fa-arrow-left text-xl"></i>
                </button>
                <div>
                    <h3 class="text-lg font-bold" id="conversationTitle">Conversation</h3>
                    <p class="text-indigo-200 text-sm" id="conversationSubtitle"></p>
                </div>
            </div>
            <div class="flex items-center gap-2">
                <span id="conversationStatus" class="px-3 py-1 bg-green-500 text-white rounded-full text-xs font-bold">
                    <i class="fas fa-circle text-xs mr-1"></i>Active
                </span>
                <button onclick="openCompleteMatchModal()" 
                        id="completeMatchBtn"
                        class="px-4 py-2 bg-white text-indigo-600 rounded-lg hover:bg-indigo-50 transition text-sm font-semibold">
                    <i class="fas fa-flag-checkered mr-1"></i>Mark Complete
                </button>
            </div>
        </div>

        <!-- Match Info Banner -->
        <div id="matchInfoBanner" class="bg-blue-50 border-b border-blue-200 px-6 py-3">
            <!-- Will be populated with match details -->
        </div>

        <!-- Messages Container -->
        <div id="messagesContainer" class="flex-1 overflow-y-auto px-6 py-4 bg-gray-50">
            <div class="text-center py-12 text-gray-500">
                <i class="fas fa-spinner fa-spin text-3xl mb-2"></i>
                <p>Loading conversation...</p>
            </div>
        </div>

        <!-- Message Input -->
        <div class="border-t bg-white px-6 py-4">
            <div class="flex items-end gap-3">
                <div class="flex-1">
                    <textarea id="messageInput" 
                              rows="3"
                              placeholder="Type your message..."
                              class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 resize-none"
                              onkeydown="handleMessageKeydown(event)"></textarea>
                    <p class="text-xs text-gray-500 mt-1">
                        <i class="fas fa-info-circle mr-1"></i>Press Enter to send, Shift+Enter for new line
                    </p>
                </div>
                <button onclick="sendMessage()" 
                        id="sendMessageBtn"
                        class="px-6 py-3 bg-indigo-600 text-white rounded-lg hover:bg-indigo-700 transition font-semibold h-fit">
                    <i class="fas fa-paper-plane mr-2"></i>Send
                </button>
            </div>
        </div>
    </div>
</div>

<!-- Complete Match Modal -->
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

</body>
</html><?php /**PATH C:\Users\Judd\BayanihanCebuBackEnd\resources\views/UserDashboards/barangaydashboard.blade.php ENDPATH**/ ?>
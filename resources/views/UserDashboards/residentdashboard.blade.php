<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>BDRRMC Dashboard - {{ $barangay->name ?? 'Barangay' }}</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        .tab-content { display: none; }
        .tab-content.active { display: block; }
        .tab-btn.active { 
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
        }
        @keyframes slideIn {
            from { opacity: 0; transform: translateY(20px); }
            to { opacity: 1; transform: translateY(0); }
        }
        .animate-slide-in { animation: slideIn 0.3s ease-out; }
        
        /* Modal styles */
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
        
        /* Print styles */
        @media print {
            body * { visibility: hidden; }
            #printReceipt, #printReceipt * { visibility: visible; }
            #printReceipt { position: absolute; left: 0; top: 0; }
        }
    </style>
</head>
<body class="bg-gray-50">

    <!-- Top Navigation -->
    <nav class="bg-white shadow-md sticky top-0 z-50">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="flex justify-between items-center h-16">
                <div class="flex items-center gap-3">
                    <div class="w-10 h-10 bg-gradient-to-br from-blue-500 to-purple-600 rounded-lg flex items-center justify-center">
                        <i class="fas fa-hands-helping text-white text-lg"></i>
                    </div>
                    <div>
                        <h1 class="text-xl font-bold text-gray-800">BDRRMC Dashboard</h1>
                        <p class="text-sm text-gray-500">{{ $barangay->name ?? 'Barangay' }}, {{ $barangay->city ?? 'City' }}</p>
                    </div>
                </div>
                
                <div class="flex items-center gap-4">
                    <div class="hidden md:flex items-center gap-2 px-4 py-2 bg-green-50 rounded-lg">
                        <div class="w-2 h-2 bg-green-500 rounded-full animate-pulse"></div>
                        <span class="text-sm font-medium text-green-700">Status: {{ ucfirst($barangay->disaster_status ?? 'safe') }}</span>
                    </div>
                    
                    <div class="flex items-center gap-3">
                        <span class="text-sm text-gray-600">{{ session('user_name') }}</span>
                        <form action="{{ route('logout') }}" method="POST">
                            @csrf
                            <button type="submit" class="px-4 py-2 bg-red-500 text-white rounded-lg hover:bg-red-600 transition text-sm">
                                <i class="fas fa-sign-out-alt mr-1"></i> Logout
                            </button>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </nav>

    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
        
        <!-- Statistics Cards -->
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6 mb-8">
            <div class="bg-gradient-to-br from-blue-500 to-blue-600 rounded-xl p-6 text-white shadow-lg">
                <div class="flex justify-between items-start">
                    <div>
                        <p class="text-blue-100 text-sm mb-1">Affected Families</p>
                        <h3 class="text-3xl font-bold">{{ $stats['affected_families'] ?? 0 }}</h3>
                    </div>
                    <div class="w-12 h-12 bg-white/20 rounded-lg flex items-center justify-center">
                        <i class="fas fa-users text-2xl"></i>
                    </div>
                </div>
            </div>

            <div class="bg-gradient-to-br from-green-500 to-green-600 rounded-xl p-6 text-white shadow-lg">
                <div class="flex justify-between items-start">
                    <div>
                        <p class="text-green-100 text-sm mb-1">Total Donations</p>
                        <h3 class="text-3xl font-bold" id="totalDonationsCount">{{ $stats['total_donations'] ?? 0 }}</h3>
                    </div>
                    <div class="w-12 h-12 bg-white/20 rounded-lg flex items-center justify-center">
                        <i class="fas fa-box text-2xl"></i>
                    </div>
                </div>
            </div>

            <div class="bg-gradient-to-br from-orange-500 to-orange-600 rounded-xl p-6 text-white shadow-lg">
                <div class="flex justify-between items-start">
                    <div>
                        <p class="text-orange-100 text-sm mb-1">Active Requests</p>
                        <h3 class="text-3xl font-bold" id="activeRequestsCount">{{ $stats['active_requests'] ?? 0 }}</h3>
                    </div>
                    <div class="w-12 h-12 bg-white/20 rounded-lg flex items-center justify-center">
                        <i class="fas fa-exclamation-circle text-2xl"></i>
                    </div>
                </div>
            </div>

            <div class="bg-gradient-to-br from-purple-500 to-purple-600 rounded-xl p-6 text-white shadow-lg">
                <div class="flex justify-between items-start">
                    <div>
                        <p class="text-purple-100 text-sm mb-1">Verified Donations</p>
                        <h3 class="text-3xl font-bold">{{ $stats['verified_donations'] ?? 0 }}</h3>
                    </div>
                    <div class="w-12 h-12 bg-white/20 rounded-lg flex items-center justify-center">
                        <i class="fas fa-check-circle text-2xl"></i>
                    </div>
                </div>
            </div>
        </div>

        <!-- Tab Navigation -->
        <div class="bg-white rounded-xl shadow-md mb-6 overflow-hidden">
            <div class="flex border-b overflow-x-auto">
                <button onclick="switchTab('physical')" class="tab-btn active px-6 py-4 font-medium transition flex-1 min-w-max">
                    <i class="fas fa-box mr-2"></i> Physical Donations
                </button>
                <button onclick="switchTab('needs')" class="tab-btn px-6 py-4 font-medium transition flex-1 min-w-max">
                    <i class="fas fa-clipboard-list mr-2"></i> Resource Needs
                </button>
                <button onclick="switchTab('online')" class="tab-btn px-6 py-4 font-medium transition flex-1 min-w-max">
                    <i class="fas fa-globe mr-2"></i> Online Donations
                </button>
                <button onclick="switchTab('barangay')" class="tab-btn px-6 py-4 font-medium transition flex-1 min-w-max">
                    <i class="fas fa-info-circle mr-2"></i> Barangay Info
                </button>
            </div>
        </div>

        <!-- TAB 1: Physical Donations -->
        <div id="physical-tab" class="tab-content active">
            <div class="bg-white rounded-xl shadow-md p-6">
                <div class="flex justify-between items-center mb-6">
                    <h2 class="text-2xl font-bold text-gray-800">
                        <i class="fas fa-box text-blue-500 mr-2"></i> Physical Donations Received
                    </h2>
                    <button onclick="openRecordModal()" class="px-6 py-3 bg-gradient-to-r from-blue-500 to-purple-600 text-white rounded-lg hover:shadow-lg transition">
                        <i class="fas fa-plus mr-2"></i> Record New Donation
                    </button>
                </div>

                <!-- Donations List -->
                <div id="donationsList" class="space-y-4">
                    <!-- Will be populated by JavaScript -->
                    <div class="text-center py-8 text-gray-500">
                        <i class="fas fa-spinner fa-spin text-3xl mb-2"></i>
                        <p>Loading donations...</p>
                    </div>
                </div>
            </div>
        </div>

        <!-- TAB 2: Resource Needs -->
        <div id="needs-tab" class="tab-content">
            <div class="bg-white rounded-xl shadow-md p-6">
                <div class="flex justify-between items-center mb-6">
                    <h2 class="text-2xl font-bold text-gray-800">
                        <i class="fas fa-clipboard-list text-orange-500 mr-2"></i> Resource Needs
                    </h2>
                    <button onclick="openNeedModal()" class="px-6 py-3 bg-gradient-to-r from-orange-500 to-red-600 text-white rounded-lg hover:shadow-lg transition">
                        <i class="fas fa-plus mr-2"></i> Add New Need
                    </button>
                </div>

                <!-- Needs List -->
                <div id="needsList" class="space-y-4">
                    <div class="text-center py-8 text-gray-500">
                        <i class="fas fa-spinner fa-spin text-3xl mb-2"></i>
                        <p>Loading needs...</p>
                    </div>
                </div>
            </div>
        </div>

        <!-- TAB 3: Online Donations -->
        <div id="online-tab" class="tab-content">
            <div class="bg-white rounded-xl shadow-md p-6">
                <h2 class="text-2xl font-bold text-gray-800 mb-6">
                    <i class="fas fa-globe text-green-500 mr-2"></i> Online Donations (Verified)
                </h2>

                <div id="onlineDonationsList" class="space-y-4">
                    <div class="text-center py-12 text-gray-500">
                        <i class="fas fa-info-circle text-4xl mb-3"></i>
                        <p class="text-lg">Online donations will appear here once Carl's system is integrated.</p>
                        <p class="text-sm mt-2">This is a read-only view of blockchain-verified donations.</p>
                    </div>
                </div>
            </div>
        </div>

        <!-- TAB 4: Barangay Info -->
        <div id="barangay-tab" class="tab-content">
            <div class="bg-white rounded-xl shadow-md p-6">
                <h2 class="text-2xl font-bold text-gray-800 mb-6">
                    <i class="fas fa-info-circle text-purple-500 mr-2"></i> Barangay Information
                </h2>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Barangay Name</label>
                        <input type="text" value="{{ $barangay->name ?? '' }}" readonly class="w-full px-4 py-2 border border-gray-300 rounded-lg bg-gray-50">
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">City/Municipality</label>
                        <input type="text" value="{{ $barangay->city ?? '' }}" readonly class="w-full px-4 py-2 border border-gray-300 rounded-lg bg-gray-50">
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Disaster Status</label>
                        <select id="disasterStatus" class="w-full px-4 py-2 border border-gray-300 rounded-lg">
                            <option value="safe" {{ ($barangay->disaster_status ?? '') == 'safe' ? 'selected' : '' }}>Safe</option>
                            <option value="at-risk" {{ ($barangay->disaster_status ?? '') == 'at-risk' ? 'selected' : '' }}>At Risk</option>
                            <option value="affected" {{ ($barangay->disaster_status ?? '') == 'affected' ? 'selected' : '' }}>Affected</option>
                            <option value="recovering" {{ ($barangay->disaster_status ?? '') == 'recovering' ? 'selected' : '' }}>Recovering</option>
                        </select>
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Barangay ID</label>
                        <input type="text" value="{{ $barangay->barangay_id ?? '' }}" readonly class="w-full px-4 py-2 border border-gray-300 rounded-lg bg-gray-50">
                    </div>

                    <div class="md:col-span-2">
                        <label class="block text-sm font-medium text-gray-700 mb-2">Needs Summary</label>
                        <textarea id="needsSummary" rows="4" class="w-full px-4 py-2 border border-gray-300 rounded-lg" placeholder="Brief summary of current needs...">{{ $barangay->needs_summary ?? '' }}</textarea>
                    </div>

                    <div class="md:col-span-2">
                        <button onclick="updateBarangayInfo()" class="px-6 py-3 bg-gradient-to-r from-blue-500 to-purple-600 text-white rounded-lg hover:shadow-lg transition">
                            <i class="fas fa-save mr-2"></i> Update Information
                        </button>
                    </div>
                </div>
            </div>
        </div>

    </div>

   <!-- MODAL 1: Record Donation Modal -->
    <div id="recordModal" class="modal">
        <div class="bg-white rounded-xl shadow-2xl max-w-3xl w-full mx-4 max-h-[90vh] overflow-y-auto">
            <div class="bg-gradient-to-r from-blue-500 to-purple-600 text-white p-6 rounded-t-xl">
                <div class="flex justify-between items-center">
                    <h3 class="text-2xl font-bold">
                        <i class="fas fa-box mr-2"></i> Record Physical Donation
                    </h3>
                    <button onclick="closeRecordModal()" class="text-white hover:text-gray-200">
                        <i class="fas fa-times text-2xl"></i>
                    </button>
                </div>
            </div>
            
            <form id="recordDonationForm" class="p-6">
                <!-- Donor Information -->
                <div class="mb-6">
                    <h4 class="text-lg font-semibold text-gray-800 mb-4 flex items-center">
                        <i class="fas fa-user text-blue-500 mr-2"></i> Donor Information
                    </h4>
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">Full Name *</label>
                            <input type="text" name="donor_name" required class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500" placeholder="Juan Dela Cruz">
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">Contact Number *</label>
                            <input type="text" name="donor_contact" required class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500" placeholder="09171234567">
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">Email (Optional)</label>
                            <input type="email" name="donor_email" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500" placeholder="email@example.com">
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">Address *</label>
                            <input type="text" name="donor_address" required class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500" placeholder="Complete address">
                        </div>
                    </div>
                </div>

                <!-- Donation Details -->
                <div class="mb-6">
                    <h4 class="text-lg font-semibold text-gray-800 mb-4 flex items-center">
                        <i class="fas fa-boxes text-green-500 mr-2"></i> Donation Details
                    </h4>
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">Category *</label>
                            <select name="category" required class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500">
                                <option value="">Select category...</option>
                                <option value="food">Food</option>
                                <option value="water">Water</option>
                                <option value="medical">Medical</option>
                                <option value="shelter">Shelter</option>
                                <option value="clothing">Clothing</option>
                                <option value="other">Other</option>
                            </select>
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">Estimated Value (₱)</label>
                            <input type="number" name="estimated_value" step="0.01" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500" placeholder="5000.00">
                        </div>
                        <div class="md:col-span-2">
                            <label class="block text-sm font-medium text-gray-700 mb-2">Items Description *</label>
                            <textarea name="items_description" required rows="3" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500" placeholder="Detailed description of donated items..."></textarea>
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">Quantity *</label>
                            <input type="text" name="quantity" required class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500" placeholder="e.g., 10 sacks, 50 pieces">
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">Intended Recipients *</label>
                            <input type="text" name="intended_recipients" required class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500" placeholder="e.g., Flood victims">
                        </div>
                        <div class="md:col-span-2">
                            <label class="block text-sm font-medium text-gray-700 mb-2">Additional Notes</label>
                            <textarea name="notes" rows="2" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500" placeholder="Any additional information..."></textarea>
                        </div>
                    </div>
                </div>

                <!-- Submit Button -->
                <div class="flex gap-3">
                    <button type="submit" class="flex-1 px-6 py-3 bg-gradient-to-r from-blue-500 to-purple-600 text-white rounded-lg hover:shadow-lg transition font-medium">
                        <i class="fas fa-save mr-2"></i> Record Donation
                    </button>
                    <button type="button" onclick="closeRecordModal()" class="px-6 py-3 bg-gray-300 text-gray-700 rounded-lg hover:bg-gray-400 transition font-medium">
                        Cancel
                    </button>
                </div>
            </form>
        </div>
    </div>

    <!-- MODAL 2: Success Modal -->
    <div id="successModal" class="modal">
        <div class="bg-white rounded-xl shadow-2xl max-w-md w-full mx-4 animate-slide-in">
            <div class="p-8 text-center">
                <div class="w-20 h-20 bg-green-100 rounded-full flex items-center justify-center mx-auto mb-4">
                    <i class="fas fa-check-circle text-green-500 text-4xl"></i>
                </div>
                <h3 class="text-2xl font-bold text-gray-800 mb-2">Donation Recorded!</h3>
                <p class="text-gray-600 mb-6">The donation has been successfully recorded in the system.</p>
                
                <div class="bg-blue-50 border-2 border-blue-200 rounded-lg p-4 mb-6">
                    <p class="text-sm text-gray-600 mb-2">Tracking Code</p>
                    <p id="generatedTrackingCode" class="text-2xl font-bold text-blue-600">---</p>
                </div>

                <div class="flex gap-3">
                    <button onclick="printReceipt()" class="flex-1 px-4 py-3 bg-gray-100 text-gray-700 rounded-lg hover:bg-gray-200 transition">
                        <i class="fas fa-print mr-2"></i> Print Receipt
                    </button>
                    <button onclick="closeSuccessModal()" class="flex-1 px-4 py-3 bg-gradient-to-r from-blue-500 to-purple-600 text-white rounded-lg hover:shadow-lg transition">
                        Done
                    </button>
                </div>
            </div>
        </div>
    </div>

    <!-- MODAL 3: Distribution Modal -->
    <div id="distributeModal" class="modal">
        <div class="bg-white rounded-xl shadow-2xl max-w-2xl w-full mx-4">
            <div class="bg-gradient-to-r from-green-500 to-teal-600 text-white p-6 rounded-t-xl">
                <div class="flex justify-between items-center">
                    <h3 class="text-2xl font-bold">
                        <i class="fas fa-hands-helping mr-2"></i> Record Distribution
                    </h3>
                    <button onclick="closeDistributeModal()" class="text-white hover:text-gray-200">
                        <i class="fas fa-times text-2xl"></i>
                    </button>
                </div>
            </div>
            
            <form id="distributeForm" class="p-6">
                <input type="hidden" id="distributeDonationId">
                
                <div class="mb-4">
                    <label class="block text-sm font-medium text-gray-700 mb-2">Distributed To *</label>
                    <input type="text" name="distributed_to" required class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-green-500" placeholder="e.g., 20 families in Sitio 1">
                </div>

                <div class="mb-4">
                    <label class="block text-sm font-medium text-gray-700 mb-2">Quantity Distributed *</label>
                    <input type="text" name="quantity_distributed" required class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-green-500" placeholder="e.g., 5 sacks of rice">
                </div>

                <div class="mb-4">
                    <label class="block text-sm font-medium text-gray-700 mb-2">Distribution Status *</label>
                    <select name="distribution_status" required class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-green-500">
                        <option value="partially_distributed">Partially Distributed</option>
                        <option value="fully_distributed">Fully Distributed</option>
                    </select>
                </div>

                <div class="mb-6">
                    <label class="block text-sm font-medium text-gray-700 mb-2">Notes</label>
                    <textarea name="notes" rows="3" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-green-500" placeholder="Additional details about the distribution..."></textarea>
                </div>

                <div class="flex gap-3">
                    <button type="submit" class="flex-1 px-6 py-3 bg-gradient-to-r from-green-500 to-teal-600 text-white rounded-lg hover:shadow-lg transition font-medium">
                        <i class="fas fa-check mr-2"></i> Record Distribution
                    </button>
                    <button type="button" onclick="closeDistributeModal()" class="px-6 py-3 bg-gray-300 text-gray-700 rounded-lg hover:bg-gray-400 transition font-medium">
                        Cancel
                    </button>
                </div>
            </form>
        </div>
    </div>

    <!-- MODAL 4: Add Resource Need Modal -->
    <div id="needModal" class="modal">
        <div class="bg-white rounded-xl shadow-2xl max-w-2xl w-full mx-4">
            <div class="bg-gradient-to-r from-orange-500 to-red-600 text-white p-6 rounded-t-xl">
                <div class="flex justify-between items-center">
                    <h3 class="text-2xl font-bold">
                        <i class="fas fa-clipboard-list mr-2"></i> Add Resource Need
                    </h3>
                    <button onclick="closeNeedModal()" class="text-white hover:text-gray-200">
                        <i class="fas fa-times text-2xl"></i>
                    </button>
                </div>
            </div>
            
            <form id="needForm" class="p-6">
                <div class="mb-4">
                    <label class="block text-sm font-medium text-gray-700 mb-2">Category *</label>
                    <select name="category" required class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-orange-500">
                        <option value="">Select category...</option>
                        <option value="food">Food</option>
                        <option value="water">Water</option>
                        <option value="medical">Medical</option>
                        <option value="shelter">Shelter</option>
                        <option value="clothing">Clothing</option>
                        <option value="other">Other</option>
                    </select>
                </div>

                <div class="mb-4">
                    <label class="block text-sm font-medium text-gray-700 mb-2">Description *</label>
                    <textarea name="description" required rows="3" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-orange-500" placeholder="Detailed description of what is needed..."></textarea>
                </div>

                <div class="mb-4">
                    <label class="block text-sm font-medium text-gray-700 mb-2">Quantity Needed *</label>
                    <input type="text" name="quantity" required class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-orange-500" placeholder="e.g., 50 sacks, 100 pieces">
                </div>

                <div class="mb-6">
                    <label class="block text-sm font-medium text-gray-700 mb-2">Urgency Level *</label>
                    <select name="urgency" required class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-orange-500">
                        <option value="low">Low</option>
                        <option value="medium">Medium</option>
                        <option value="high">High</option>
                        <option value="critical">Critical</option>
                    </select>
                </div>

                <div class="flex gap-3">
                    <button type="submit" class="flex-1 px-6 py-3 bg-gradient-to-r from-orange-500 to-red-600 text-white rounded-lg hover:shadow-lg transition font-medium">
                        <i class="fas fa-plus mr-2"></i> Add Need
                    </button>
                    <button type="button" onclick="closeNeedModal()" class="px-6 py-3 bg-gray-300 text-gray-700 rounded-lg hover:bg-gray-400 transition font-medium">
                        Cancel
                    </button>
                </div>
            </form>
        </div>
    </div>

    <!-- Hidden Print Receipt Template -->
    <div id="printReceipt" style="display: none;">
        <!-- Will be populated when printing -->
    </div>

    <script>
        // JavaScript code coming next...
    </script>

</body>
</html>
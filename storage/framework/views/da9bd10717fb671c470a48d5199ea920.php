<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="<?php echo e(csrf_token()); ?>">
    <title>BayanihanCebu - Resident Dashboard</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <script src="<?php echo e(asset('js/web3Helper.js')); ?>"></script>
     <?php echo app('Illuminate\Foundation\Vite')(['resources/css/app.css', 'resources/js/app.js']); ?>

    <style>
        .modal {
            display: none;
            position: fixed;
            z-index: 1000;
            left: 0;
            top: 0;
            width: 100%;
            height: 100%;
            background-color: rgba(0, 0, 0, 0.5);
            align-items: center;
            justify-content: center;
        }

        .modal.active {
            display: flex;
        }

        @keyframes slideIn {
            from {
                opacity: 0;
                transform: translateY(20px);
            }

            to {
                opacity: 1;
                transform: translateY(0);
            }
        }

        .animate-slide-in {
            animation: slideIn 0.3s ease-out;
        }
    </style>
</head>

<body class="bg-gray-50">

    <!-- Top Navigation Bar -->
    <nav class="bg-blue-700 text-white shadow-md">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="flex justify-between items-center h-16">
                <!-- Logo and Title -->
                <div class="flex items-center gap-4">
                    <button class="md:hidden text-white hover:text-blue-200">
                        <i class="fas fa-bars text-xl"></i>
                    </button>
                    <div>
                        <h1 class="text-xl font-bold">BayanihanCebu</h1>
                        <p class="text-xs text-blue-200">Resident Dashboard</p>
                    </div>
                </div>

                <!-- Wallet Connection & User Info -->
                <div class="flex items-center gap-4">
                    <!-- Connect Wallet Button -->
                    <button id="connectWalletBtn"
                        class="hidden md:flex items-center gap-2 px-4 py-2 bg-blue-600 hover:bg-blue-500 rounded-lg transition">
                        <i class="fas fa-wallet"></i>
                        <span id="walletStatus">Connect Wallet</span>
                    </button>
                    <span id="walletAddress" class="text-sm text-blue-200 hidden md:block"></span>
                    <span id="walletBalance" class="text-sm font-semibold hidden md:block"></span>

                    <!-- User Menu -->
                    <div class="flex items-center gap-3 border-l border-blue-600 pl-4">
                        <div class="text-right hidden md:block">
                            <p class="text-sm">Welcome,</p>
                            <p class="text-sm font-semibold"><?php echo e(session('user_name') ?? 'resident@test.com'); ?></p>
                        </div>
                        <form action="<?php echo e(route('logout')); ?>" method="POST">
                            <?php echo csrf_field(); ?>
                            <button type="submit"
                                class="px-4 py-2 bg-white text-blue-700 rounded-lg hover:bg-blue-50 transition text-sm font-medium">
                                <i class="fas fa-sign-out-alt mr-1"></i> Sign Out
                            </button>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </nav>

    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">

        <!-- Alert Banner -->
        <div class="bg-blue-50 border-l-4 border-blue-500 p-4 mb-6 rounded-r-lg">
            <div class="flex items-start">
                <i class="fas fa-exclamation-triangle text-blue-500 text-xl mt-1 mr-3"></i>
                <div>
                    <h3 class="text-blue-900 font-semibold text-lg mb-1">Help Disaster-Affected Barangays</h3>
                    <p class="text-blue-800 text-sm">View what barangays need and donate to help affected families. All
                        donations are verified on the blockchain for transparency.</p>
                </div>
            </div>
        </div>

        <!-- Statistics Cards -->
        <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-8">
            <!-- Active Needs Card -->
            <div class="bg-white rounded-xl shadow-sm p-6 border border-gray-200">
                <div class="flex items-center gap-4">
                    <div class="w-14 h-14 bg-red-100 rounded-lg flex items-center justify-center">
                        <i class="fas fa-exclamation-triangle text-red-600 text-2xl"></i>
                    </div>
                    <div>
                        <p class="text-gray-600 text-sm">Active Needs</p>
                        <h3 class="text-3xl font-bold text-gray-900" id="activeNeedsCount">5</h3>
                    </div>
                </div>
            </div>

            <!-- Affected Barangays Card -->
            <div class="bg-white rounded-xl shadow-sm p-6 border border-gray-200">
                <div class="flex items-center gap-4">
                    <div class="w-14 h-14 bg-orange-100 rounded-lg flex items-center justify-center">
                        <i class="fas fa-map-marker-alt text-orange-600 text-2xl"></i>
                    </div>
                    <div>
                        <p class="text-gray-600 text-sm">Affected Barangays</p>
                        <h3 class="text-3xl font-bold text-gray-900" id="affectedBarangaysCount">5</h3>
                    </div>
                </div>
            </div>

            <!-- Your Impact Card -->
            <div class="bg-white rounded-xl shadow-sm p-6 border border-gray-200">
                <div class="flex items-center gap-4">
                    <div class="w-14 h-14 bg-green-100 rounded-lg flex items-center justify-center">
                        <i class="fas fa-heart text-green-600 text-2xl"></i>
                    </div>
                    <div>
                        <p class="text-gray-600 text-sm">Your Impact</p>
                        <h3 class="text-xl font-bold text-green-600">Help Now</h3>
                    </div>
                </div>
            </div>
        </div>

        <!-- Filter Section -->
        <div class="bg-white rounded-xl shadow-sm p-6 mb-6">
            <div class="flex items-center gap-2 mb-4">
                <i class="fas fa-filter text-gray-600"></i>
                <h3 class="font-semibold text-gray-800">Filter Needs</h3>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                <!-- Urgency Filter -->
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Urgency</label>
                    <select id="urgencyFilter"
                        class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                        <option value="">All Urgencies</option>
                        <option value="critical">Critical</option>
                        <option value="high">High</option>
                        <option value="medium">Medium</option>
                        <option value="low">Low</option>
                    </select>
                </div>

                <!-- Category Filter -->
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Category</label>
                    <select id="categoryFilter"
                        class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                        <option value="">All Categories</option>
                        <option value="food">Food</option>
                        <option value="water">Water</option>
                        <option value="medical">Medical</option>
                        <option value="shelter">Shelter</option>
                        <option value="clothing">Clothing</option>
                    </select>
                </div>

                <!-- Search -->
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Search</label>
                    <input type="text" id="searchFilter" placeholder="Search barangay or description..."
                        class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                </div>
            </div>
        </div>

        <!-- Barangay Needs Section -->
        <div class="mb-6">
            <h2 class="text-2xl font-bold text-gray-900 mb-6">Barangay Needs</h2>

            <!-- Needs Grid -->
            <div id="needsGrid" class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <!-- Loading State -->
                <div class="col-span-2 text-center py-12">
                    <i class="fas fa-spinner fa-spin text-4xl text-gray-400 mb-4"></i>
                    <p class="text-gray-600">Loading barangay needs...</p>
                </div>
            </div>
        </div>

    </div>

    <!-- Donation Modal -->
    <div id="donationModal" class="modal">
        <div class="bg-white rounded-2xl shadow-2xl max-w-lg w-full mx-4 max-h-[90vh] overflow-y-auto">
            <!-- Modal Header -->
            <div class="bg-gradient-to-r from-blue-600 to-purple-600 text-white p-6 rounded-t-2xl">
                <div class="flex justify-between items-center">
                    <div>
                        <h3 class="text-2xl font-bold">Make a Donation</h3>
                        <p class="text-blue-100 text-sm mt-1">Help disaster-affected families</p>
                    </div>
                    <button onclick="closeDonationModal()" class="text-white hover:text-gray-200 transition">
                        <i class="fas fa-times text-2xl"></i>
                    </button>
                </div>
            </div>

            <!-- Modal Body -->
            <form id="donationForm" class="p-6">
                <input type="hidden" id="selectedBarangayId">
                <input type="hidden" id="selectedBarangayName">

                <!-- Barangay Info Display -->
                <div class="bg-blue-50 border border-blue-200 rounded-lg p-4 mb-6">
                    <p class="text-sm text-blue-800 mb-1">Donating to:</p>
                    <p class="text-lg font-bold text-blue-900" id="modalBarangayName">-</p>
                    <p class="text-sm text-blue-700" id="modalNeedCategory">-</p>
                </div>

                <!-- Donation Amount -->
                <div class="mb-6">
                    <label class="block text-sm font-medium text-gray-700 mb-2">
                        Amount (PHP) <span class="text-red-500">*</span>
                    </label>
                    <input type="number" id="donationAmount" required min="100" step="0.01"
                        class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 text-lg"
                        placeholder="Enter amount">

                    <!-- Quick Amount Buttons -->
                    <div class="flex gap-2 mt-3">
                        <button type="button" onclick="setAmount(500)"
                            class="flex-1 px-4 py-2 bg-gray-100 hover:bg-gray-200 rounded-lg text-sm font-medium transition">₱500</button>
                        <button type="button" onclick="setAmount(1000)"
                            class="flex-1 px-4 py-2 bg-gray-100 hover:bg-gray-200 rounded-lg text-sm font-medium transition">₱1,000</button>
                        <button type="button" onclick="setAmount(2500)"
                            class="flex-1 px-4 py-2 bg-gray-100 hover:bg-gray-200 rounded-lg text-sm font-medium transition">₱2,500</button>
                        <button type="button" onclick="setAmount(5000)"
                            class="flex-1 px-4 py-2 bg-gray-100 hover:bg-gray-200 rounded-lg text-sm font-medium transition">₱5,000</button>
                    </div>
                </div>

                <!-- Your Name -->
                <div class="mb-6">
                    <label class="block text-sm font-medium text-gray-700 mb-2">
                        Your Name <span class="text-red-500">*</span>
                    </label>
                    <input type="text" id="donorName" required
                        class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500"
                        placeholder="Enter your name" value="<?php echo e(session('user_name')); ?>">
                </div>

                <!-- Your Email (Optional) -->
                <div class="mb-6">
                    <label class="block text-sm font-medium text-gray-700 mb-2">
                        Your Email (Optional)
                    </label>
                    <input type="email" id="donorEmail"
                        class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500"
                        placeholder="email@example.com">
                </div>

                <!-- Submit Button -->
                <div class="flex gap-3">
                    <button type="submit"
                        class="flex-1 px-6 py-3 bg-gradient-to-r from-blue-600 to-purple-600 text-white rounded-lg hover:shadow-lg transition font-semibold">
                        <i class="fas fa-heart mr-2"></i> Donate Now
                    </button>
                    <button type="button" onclick="closeDonationModal()"
                        class="px-6 py-3 bg-gray-200 text-gray-700 rounded-lg hover:bg-gray-300 transition font-semibold">
                        Cancel
                    </button>
                </div>
            </form>
        </div>
    </div>

    <!-- Processing Modal -->
    <div id="processingModal" class="modal">
        <div class="bg-white rounded-2xl shadow-2xl max-w-md w-full mx-4 p-8 text-center">
            <div class="animate-spin w-16 h-16 border-4 border-blue-500 border-t-transparent rounded-full mx-auto mb-4">
            </div>
            <h3 class="text-xl font-bold text-gray-800 mb-2">Processing Transaction...</h3>
            <p class="text-gray-600 text-sm">Please wait for blockchain confirmation</p>
            <p class="text-xs text-gray-500 mt-4">This may take 10-30 seconds</p>
        </div>
    </div>

    <!-- Success Modal -->
    <div id="successModal" class="modal">
        <div class="bg-white rounded-2xl shadow-2xl max-w-md w-full mx-4 p-8 text-center animate-slide-in">
            <div class="w-20 h-20 bg-green-100 rounded-full flex items-center justify-center mx-auto mb-4">
                <i class="fas fa-check-circle text-green-500 text-4xl"></i>
            </div>
            <h3 class="text-2xl font-bold text-gray-800 mb-2">Donation Successful!</h3>
            <p class="text-gray-600 mb-6">Thank you for helping disaster-affected families</p>

            <div class="bg-blue-50 border-2 border-blue-200 rounded-lg p-4 mb-6">
                <p class="text-sm text-gray-600 mb-1">Blockchain Transaction Hash</p>
                <p id="successTxHash" class="font-mono text-xs text-blue-600 break-all mb-3">---</p>
                <button onclick="copyTxHash()" class="text-sm text-blue-600 hover:text-blue-700 font-medium">
                    <i class="fas fa-copy mr-1"></i> Copy Hash
                </button>
            </div>

            <div class="flex gap-3">
                <a id="explorerLink" href="#" target="_blank"
                    class="flex-1 px-6 py-3 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition font-semibold">
                    <i class="fas fa-external-link-alt mr-2"></i> View on Explorer
                </a>
                <button onclick="closeSuccessModal()"
                    class="flex-1 px-6 py-3 bg-gray-200 text-gray-700 rounded-lg hover:bg-gray-300 transition font-semibold">
                    Done
                </button>
            </div>
        </div>
    </div>

    <script>
        const csrfToken = document.querySelector('meta[name="csrf-token"]').content;
        let connectedAddress = null;
        let allNeeds = [];

        // Connect Wallet on Page Load
        document.addEventListener('DOMContentLoaded', function () {
            loadAllNeeds();

            // Connect Wallet Button
            const connectBtn = document.getElementById('connectWalletBtn');
            if (connectBtn) {
                connectBtn.addEventListener('click', async () => {
                    const result = await connectWallet();

                    if (result.success) {
                        connectedAddress = result.address;
                        document.getElementById('walletStatus').textContent = 'Connected';
                        document.getElementById('walletAddress').textContent = formatAddress(result.address);
                        document.getElementById('walletAddress').classList.remove('hidden');

                        const balance = await getBalance(result.address);
                        if (balance.success) {
                            document.getElementById('walletBalance').textContent = balance.balance + ' ETH';
                            document.getElementById('walletBalance').classList.remove('hidden');
                        }

                        alert('✅ Wallet connected successfully!');
                    } else {
                        alert('❌ ' + result.error);
                    }
                });
            }

            // Filter listeners
            document.getElementById('urgencyFilter').addEventListener('change', filterNeeds);
            document.getElementById('categoryFilter').addEventListener('change', filterNeeds);
            document.getElementById('searchFilter').addEventListener('input', filterNeeds);
        });

        // Load All Needs from API
        async function loadAllNeeds() {
            try {
                // Show loading state
                document.getElementById('needsGrid').innerHTML = `
                    <div class="col-span-2 text-center py-12">
                        <i class="fas fa-spinner fa-spin text-4xl text-gray-400 mb-4"></i>
                        <p class="text-gray-600">Loading barangay needs...</p>
                    </div>
                `;

                // Use the correct resident API endpoint
                const response = await fetch('/api/resident/urgent-needs', {
                    method: 'GET',
                    headers: {
                        'Accept': 'application/json',
                        'X-CSRF-TOKEN': csrfToken
                    }
                });

                if (!response.ok) {
                    throw new Error('Network response was not ok');
                }

                const data = await response.json();

                if (!data.success) {
                    throw new Error(data.message || 'Failed to load needs');
                }

                allNeeds = data.needs || [];

                // Update statistics
                if (data.statistics) {
                    document.getElementById('activeNeedsCount').textContent = data.statistics.active_needs || 0;
                    document.getElementById('affectedBarangaysCount').textContent = data.statistics.affected_barangays || 0;
                }

                displayNeeds(allNeeds);

            } catch (error) {
                console.error('Error loading needs:', error);
                const errorMessage = error.message || 'An error occurred while loading needs';
                document.getElementById('needsGrid').innerHTML =
                    '<div class="col-span-2 text-center py-12">' +
                    '<div class="text-red-600 mb-4"><i class="fas fa-exclamation-circle text-4xl"></i></div>' +
                    '<p class="text-lg font-semibold text-gray-800">Error Loading Needs</p>' +
                    '<p class="text-sm text-gray-600 mt-2">' + errorMessage.replace(/[<>]/g, '') + '</p>' +
                    '<button onclick="loadAllNeeds()" class="mt-4 px-6 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition">' +
                    '<i class="fas fa-redo mr-2"></i>Try Again' +
                    '</button>' +
                    '</div>';
            }        // Display Needs in Grid
        function displayNeeds(needs) {
            const grid = document.getElementById('needsGrid');

            if (needs.length === 0) {
                grid.innerHTML = `
            <div class="col-span-2 text-center py-12 text-gray-500">
                <i class="fas fa-check-circle text-5xl mb-4 text-green-500"></i>
                <p class="text-lg font-semibold">No active needs at the moment</p>
                <p class="text-sm mt-2">All barangays are safe or needs are fulfilled</p>
            </div>
        `;
                return;
            }

            grid.innerHTML = needs.map(need => {
                const urgencyColor = {
                    'critical': 'bg-red-100 text-red-800',
                    'high': 'bg-orange-100 text-orange-800',
                    'medium': 'bg-yellow-100 text-yellow-800',
                    'low': 'bg-blue-100 text-blue-800'
                };

                const statusColor = {
                    'emergency': 'bg-red-500',
                    'critical': 'bg-orange-500',
                    'warning': 'bg-yellow-500',
                    'safe': 'bg-green-500'
                };

                return `
            <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6 hover:shadow-md transition">
                <!-- Header -->
                <div class="flex justify-between items-start mb-4">
                    <div class="flex items-center gap-3">
                        <div class="w-12 h-12 bg-blue-100 rounded-lg flex items-center justify-center">
                            <i class="fas fa-map-marker-alt text-blue-600 text-xl"></i>
                        </div>
                        <div>
                            <h3 class="text-lg font-bold text-gray-900">${need.barangay_name}</h3>
                            <span class="px-3 py-1 ${urgencyColor[need.urgency]} text-xs font-semibold rounded-full uppercase">
                                ${need.urgency}
                            </span>
                        </div>
                    </div>
                    <div class="w-3 h-3 ${statusColor[need.disaster_status]} rounded-full" title="${need.disaster_status}"></div>
                </div>

                <!-- Info -->
                <div class="space-y-2 mb-4">
                    <div class="flex items-center gap-2 text-gray-700">
                        <i class="fas fa-box text-gray-400"></i>
                        <span class="font-medium">Category:</span>
                        <span class="capitalize">${need.category}</span>
                    </div>
                    <div class="flex items-center gap-2 text-gray-700">
                        <i class="fas fa-exclamation-circle text-gray-400"></i>
                        <span class="font-medium">Quantity:</span>
                        <span>${need.quantity}</span>
                    </div>
                    <div class="flex items-center gap-2 text-gray-700">
                        <i class="fas fa-users text-gray-400"></i>
                        <span class="font-medium">Affected Families:</span>
                        <span>${need.affected_families || 0}</span>
                    </div>
                </div>

                <!-- Description -->
                <p class="text-gray-600 text-sm mb-4">
                    ${need.description || `Urgent need for ${need.category} supplies for ${need.affected_families || 0} affected families in ${need.barangay_name}.`}
                </p>

                <!-- Donate Button -->
                <button onclick='openDonationModal("${need.barangay_id}", "${need.barangay_name}", "${need.category}")'
                        class="w-full px-6 py-3 bg-red-600 hover:bg-red-700 text-white rounded-lg transition font-semibold flex items-center justify-center gap-2">
                    <i class="fas fa-heart"></i>
                    Donate to ${need.barangay_name}
                </button>
            </div>
        `;
            }).join('');
        }

        // Filter Needs
        function filterNeeds() {
            const urgency = document.getElementById('urgencyFilter').value.toLowerCase();
            const category = document.getElementById('categoryFilter').value.toLowerCase();
            const search = document.getElementById('searchFilter').value.toLowerCase();

            const filtered = allNeeds.filter(need => {
                const matchUrgency = !urgency || need.urgency === urgency;
                const matchCategory = !category || need.category === category;
                const matchSearch = !search ||
                    need.barangay_name.toLowerCase().includes(search) ||
                    need.category.toLowerCase().includes(search);

                return matchUrgency && matchCategory && matchSearch;
            });

            displayNeeds(filtered);
        }

        // Open Donation Modal
        function openDonationModal(barangayId, barangayName, category) {
            if (!connectedAddress) {
                alert('⚠️ Please connect your MetaMask wallet first!');
                document.getElementById('connectWalletBtn').click();
                return;
            }

            document.getElementById('selectedBarangayId').value = barangayId;
            document.getElementById('selectedBarangayName').value = barangayName;
            document.getElementById('modalBarangayName').textContent = barangayName;
            document.getElementById('modalNeedCategory').textContent = `Need: ${category.charAt(0).toUpperCase() + category.slice(1)}`;
            document.getElementById('donationModal').classList.add('active');
            document.getElementById('donationForm').reset();
            document.getElementById('donorName').value = '<?php echo e(session("user_name")); ?>';
        }

        function closeDonationModal() {
            document.getElementById('donationModal').classList.remove('active');
        }

        function closeSuccessModal() {
            document.getElementById('successModal').classList.remove('active');
            loadAllNeeds(); // Reload needs
        }

        function setAmount(value) {
            document.getElementById('donationAmount').value = value;
        }

        function copyTxHash() {
            const txHash = document.getElementById('successTxHash').textContent;
            navigator.clipboard.writeText(txHash);
            alert('✅ Transaction hash copied to clipboard!');
        }

        // Handle Donation Form Submission
        document.addEventListener('DOMContentLoaded', function () {
            const donationForm = document.getElementById('donationForm');
            if (donationForm) {
                donationForm.addEventListener('submit', async (e) => {
                    e.preventDefault();

                    const barangayId = document.getElementById('selectedBarangayId').value;
                    const barangayName = document.getElementById('selectedBarangayName').value;
                    const amount = parseFloat(document.getElementById('donationAmount').value);
                    const donorName = document.getElementById('donorName').value;
                    const donorEmail = document.getElementById('donorEmail').value;

                    if (amount < 100) {
                        alert('⚠️ Minimum donation amount is ₱100');
                        return;
                    }

                    closeDonationModal();
                    document.getElementById('processingModal').classList.add('active');

                    try {
                        const txResult = await sendDonation(amount, barangayName);

                        document.getElementById('processingModal').classList.remove('active');

                        if (txResult.success) {
                            const response = await fetch('/api/donations', {
                                method: 'POST',
                                headers: {
                                    'Content-Type': 'application/json',
                                    'X-CSRF-TOKEN': csrfToken
                                },
                                body: JSON.stringify({
                                    donor_name: donorName,
                                    donor_email: donorEmail || null,
                                    target_barangay_id: barangayId,
                                    amount: amount,
                                    payment_method: 'metamask',
                                    tx_hash: txResult.txHash,
                                    wallet_address: txResult.donorAddress,
                                    blockchain_status: 'confirmed',
                                    explorer_url: txResult.explorerUrl
                                })
                            });

                            const result = await response.json();

                            if (result.success) {
                                document.getElementById('successTxHash').textContent = txResult.txHash;
                                document.getElementById('explorerLink').href = txResult.explorerUrl;
                                document.getElementById('successModal').classList.add('active');
                            } else {
                                alert('❌ Failed to save donation: ' + (result.message || 'Unknown error'));
                            }
                        } else {
                            alert('❌ Transaction failed: ' + txResult.error);
                        }

                    } catch (error) {
                        document.getElementById('processingModal').classList.remove('active');
                        alert('❌ Error: ' + error.message);
                        console.error('Donation error:', error);
                    }
                });
            }
        });
    </script>
    <?php echo $__env->make('partials.footer', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>
</body>

</html>
<?php /**PATH C:\Users\Judd\BayanihanCebuBackEnd\resources\views\UserDashboards\residentdashboard.blade.php ENDPATH**/ ?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="<?php echo e(csrf_token()); ?>">
    <title>BayanihanCebu - BDRRMC</title>

    <!-- External CSS -->
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link rel="stylesheet" href="<?php echo e(asset('css/barangay/dashboard.css')); ?>">
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
                    <?php
                        $status = $barangay->disaster_status ?? 'safe';
                        $statusConfig = [
                            'safe' => ['bg' => 'bg-green-100', 'text' => 'text-green-700', 'icon' => '✅', 'label' => 'Safe'],
                            'warning' => ['bg' => 'bg-yellow-100', 'text' => 'text-yellow-700', 'icon' => '⚠️', 'label' => 'Warning'],
                            'critical' => ['bg' => 'bg-orange-100', 'text' => 'text-orange-700', 'icon' => '🔶', 'label' => 'Critical'],
                            'emergency' => ['bg' => 'bg-red-100', 'text' => 'text-red-700', 'icon' => '🚨', 'label' => 'Emergency']
                        ];
                        $config = $statusConfig[$status] ?? $statusConfig['safe'];
                    ?>
                    <span class="inline-block mt-2 px-3 py-1 <?php echo e($config['bg']); ?> <?php echo e($config['text']); ?> text-sm font-medium rounded">
                        <?php echo e($config['icon']); ?> <?php echo e(strtoupper($config['label'])); ?>

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

        <?php echo $__env->make('barangay.partials.modals.edit-status-modal', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>

        <!-- Tabs Navigation -->
        <div class="bg-white rounded-t-lg shadow-sm border-b">
            <div class="flex gap-2 px-6">
                <button onclick="switchTab('needs')" class="tab-btn active">Resource Requests</button>
                <button onclick="switchTab('online')" class="tab-btn">Online Donations</button>
                <button onclick="switchTab('physical')" class="tab-btn">Donations Received</button>
                <button onclick="showTab('match-requests')"
                    class="tab-button px-6 py-3 text-gray-600 hover:text-indigo-600 hover:border-indigo-600 border-b-2 border-transparent transition font-semibold"
                    data-tab="match-requests">
                <i class="fas fa-inbox mr-2"></i>Match Requests
                <span id="incoming-requests-badge" class="hidden ml-2 bg-red-500 text-white text-xs font-bold px-2 py-1 rounded-full">0</span>
            </button>
            <button onclick="showTab('my-requests')"
                    class="tab-button px-6 py-3 text-gray-600 hover:text-indigo-600 hover:border-indigo-600 border-b-2 border-transparent transition font-semibold"
                    data-tab="my-requests">
                <i class="fas fa-paper-plane mr-2"></i>Pending Requests
            </button>
            <button onclick="showTab('active-matches')"
                    class="tab-button px-6 py-3 text-gray-600 hover:text-indigo-600 hover:border-indigo-600 border-b-2 border-transparent transition font-semibold"
                    data-tab="active-matches">
                <i class="fas fa-comments mr-2"></i>Active Matches
                <span id="active-matches-badge" class="hidden ml-2 bg-green-500 text-white text-xs font-bold px-2 py-1 rounded-full">0</span>
            </button>
            </div>
        </div>

        <?php echo $__env->make('barangay.partials.tabs.resource-requests', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>
        <?php echo $__env->make('barangay.partials.tabs.online-donations', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>
        <?php echo $__env->make('barangay.partials.tabs.physical-donations', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>
        <?php echo $__env->make('barangay.partials.tabs.match-requests', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>
        <?php echo $__env->make('barangay.partials.tabs.pending-requests', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>
        <?php echo $__env->make('barangay.partials.tabs.active-matches', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>
        <?php echo $__env->make('barangay.partials.modals.respond-match-modal', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>
    </div>

    <?php echo $__env->make('barangay.partials.modals.record-donation-modal', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>
    <?php echo $__env->make('barangay.partials.modals.success-modal', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>
    <?php echo $__env->make('barangay.partials.modals.distribute-modal', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>
    <?php echo $__env->make('barangay.partials.modals.view-distribution-modal', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>
    <?php echo $__env->make('barangay.partials.modals.need-modal', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>

    <!-- Hidden Print Receipt -->
    <div id="printReceipt" style="display: none;"></div>

    <!-- External JavaScript Files -->
    <script src="<?php echo e(asset('js/barangay/utils.js')); ?>"></script>
    <script src="<?php echo e(asset('js/barangay/tabs.js')); ?>"></script>
    <script src="<?php echo e(asset('js/barangay/photo-upload.js')); ?>"></script>
    <script src="<?php echo e(asset('js/barangay/resource-needs.js')); ?>"></script>
    <script src="<?php echo e(asset('js/barangay/donations.js')); ?>"></script>
    <script src="<?php echo e(asset('js/barangay/modals.js')); ?>"></script>
    <script src="<?php echo e(asset('js/barangay/matching.js')); ?>"></script>
    <script src="<?php echo e(asset('js/barangay/init.js')); ?>"></script>

    <?php echo $__env->make('barangay.partials.modals.conversation-modal', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>
    <?php echo $__env->make('barangay.partials.modals.complete-match-modal', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>

</body>
</html><?php /**PATH C:\Users\Judd\BayanihanCebuBackEnd\resources\views/UserDashboards/barangaydashboard.blade.php ENDPATH**/ ?>
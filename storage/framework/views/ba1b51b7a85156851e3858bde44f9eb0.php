<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Track Your Physical Donation - BayanihanCebu</title>
    <?php echo app('Illuminate\Foundation\Vite')(['resources/css/app.css', 'resources/js/app.js']); ?>
    <style>
        /* Override any conflicting custom styles */
        * {
            box-sizing: border-box;
        }
        body {
            margin: 0;
            padding: 0;
        }
        .container {
            max-width: 100% !important;
            margin: 0 !important;
            padding: 0 !important;
        }
    </style>
</head>
<body class="bg-gray-100">

    <!-- Header -->
    <header class="bg-gradient-to-r from-blue-700 to-blue-900 text-white shadow-lg">
        <div class="max-w-7xl mx-auto px-6 py-4">
            <div class="flex justify-between items-center">
                <div>
                    <h1 class="text-xl font-bold">BayanihanCebu - Donation Tracking</h1>
                    <p class="text-blue-200 text-sm">Barangay <?php echo e($donation->barangay->name); ?></p>
                </div>
                <a href="<?php echo e(route('home')); ?>" class="px-4 py-2 bg-blue-600 hover:bg-blue-700 text-white text-sm font-medium rounded transition flex items-center">
                    <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/>
                    </svg>
                    Back to Map
                </a>
            </div>
        </div>
    </header>

    <!-- Main Content -->
    <main class="max-w-7xl mx-auto px-6 py-8">
        
        <!-- Tracking Code Card -->
        <div class="bg-white rounded-lg shadow-md p-6 mb-6">
            <div class="flex justify-between items-center flex-wrap gap-4">
                <div>
                    <p class="text-sm text-gray-600 mb-1">Tracking Code</p>
                    <h2 class="text-3xl font-bold text-gray-900"><?php echo e($donation->tracking_code); ?></h2>
                    <span class="inline-block mt-2 px-3 py-1 bg-purple-100 text-purple-800 text-xs font-semibold rounded">
                        Physical Donation
                    </span>
                </div>
                <div>
                    <?php
                        $statusClass = 'bg-gray-100 text-gray-800';
                        if ($donation->distribution_status === 'pending_distribution') {
                            $statusClass = 'bg-yellow-100 text-yellow-800';
                        } elseif ($donation->distribution_status === 'partially_distributed') {
                            $statusClass = 'bg-blue-100 text-blue-800';
                        } elseif ($donation->distribution_status === 'fully_distributed') {
                            $statusClass = 'bg-green-100 text-green-800';
                        }
                    ?>
                    <span class="px-4 py-2 text-sm font-semibold rounded <?php echo e($statusClass); ?>">
                        <?php echo e(strtoupper(str_replace('_', ' ', $donation->distribution_status))); ?>

                    </span>
                </div>
            </div>
        </div>

        <!-- Stats Cards -->
        <div class="grid grid-cols-1 md:grid-cols-4 gap-4 mb-6">
            <!-- Donor Name -->
            <div class="bg-white rounded-lg shadow-md p-5">
                <div class="flex items-center">
                    <div class="p-3 bg-purple-100 rounded-lg mr-4">
                        <svg class="w-6 h-6 text-purple-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/>
                        </svg>
                    </div>
                    <div>
                        <p class="text-sm text-gray-600">Donor Name</p>
                        <p class="text-lg font-bold text-gray-900"><?php echo e($donation->donor_name); ?></p>
                    </div>
                </div>
            </div>

            <!-- Category -->
            <div class="bg-white rounded-lg shadow-md p-5">
                <div class="flex items-center">
                    <div class="p-3 bg-blue-100 rounded-lg mr-4">
                        <svg class="w-6 h-6 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 7h.01M7 3h5c.512 0 1.024.195 1.414.586l7 7a2 2 0 010 2.828l-7 7a2 2 0 01-2.828 0l-7-7A1.994 1.994 0 013 12V7a4 4 0 014-4z"/>
                        </svg>
                    </div>
                    <div>
                        <p class="text-sm text-gray-600">Category</p>
                        <p class="text-lg font-bold text-gray-900 capitalize"><?php echo e($donation->category); ?></p>
                    </div>
                </div>
            </div>

            <!-- Estimated Value -->
            <div class="bg-white rounded-lg shadow-md p-5">
                <div class="flex items-center">
                    <div class="p-3 bg-green-100 rounded-lg mr-4">
                        <svg class="w-6 h-6 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                        </svg>
                    </div>
                    <div>
                        <p class="text-sm text-gray-600">Estimated Value</p>
                        <p class="text-lg font-bold text-gray-900">₱<?php echo e(number_format($donation->estimated_value, 2)); ?></p>
                    </div>
                </div>
            </div>

            <!-- Quantity -->
            <div class="bg-white rounded-lg shadow-md p-5">
                <div class="flex items-center">
                    <div class="p-3 bg-orange-100 rounded-lg mr-4">
                        <svg class="w-6 h-6 text-orange-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"/>
                        </svg>
                    </div>
                    <div>
                        <p class="text-sm text-gray-600">Quantity</p>
                        <p class="text-lg font-bold text-gray-900"><?php echo e($donation->quantity); ?></p>
                    </div>
                </div>
            </div>
        </div>


        <!-- Main Details Card -->
        <div class="bg-white rounded-lg shadow-md mb-6">
            <!-- Tabs -->
            <div class="border-b border-gray-200">
                <nav class="flex">
                    <button class="px-6 py-3 text-sm font-semibold text-blue-600 border-b-2 border-blue-600">
                        Donation Details
                    </button>
                </nav>
            </div>

            <!-- Tab Content -->
            <div class="p-6">
                <!-- Two Column Layout -->
                <div class="grid md:grid-cols-2 gap-8 mb-6">
                    <!-- Left Column: Donation Information -->
                    <div>
                        <h3 class="text-lg font-semibold text-gray-900 mb-4">Donation Information</h3>
                        <div class="space-y-3">
                            <div class="flex justify-between py-2 border-b border-gray-200">
                                <span class="text-gray-600">Donor Name:</span>
                                <span class="font-semibold text-gray-900"><?php echo e($donation->donor_name); ?></span>
                            </div>
                            <div class="flex justify-between py-2 border-b border-gray-200">
                                <span class="text-gray-600">Category:</span>
                                <span class="font-semibold text-gray-900 capitalize"><?php echo e($donation->category); ?></span>
                            </div>
                            <div class="flex justify-between py-2 border-b border-gray-200">
                                <span class="text-gray-600">Quantity:</span>
                                <span class="font-semibold text-gray-900"><?php echo e($donation->quantity); ?></span>
                            </div>
                            <div class="flex justify-between py-2 border-b border-gray-200">
                                <span class="text-gray-600">Estimated Value:</span>
                                <span class="font-semibold text-gray-900">₱<?php echo e(number_format($donation->estimated_value, 2)); ?></span>
                            </div>
                            <div class="flex justify-between py-2">
                                <span class="text-gray-600">Date Donated:</span>
                                <span class="font-semibold text-gray-900"><?php echo e($donation->recorded_at->format('M d, Y h:i A')); ?></span>
                            </div>
                        </div>
                    </div>

                    <!-- Right Column: Beneficiary Information -->
                    <div>
                        <h3 class="text-lg font-semibold text-gray-900 mb-4">Beneficiary Information</h3>
                        <div class="space-y-3">
                            <div class="flex justify-between py-2 border-b border-gray-200">
                                <span class="text-gray-600">Barangay:</span>
                                <span class="font-semibold text-gray-900"><?php echo e($donation->barangay->name); ?></span>
                            </div>
                            <div class="flex justify-between py-2 border-b border-gray-200">
                                <span class="text-gray-600">Intended Recipients:</span>
                                <span class="font-semibold text-gray-900"><?php echo e($donation->intended_recipients); ?></span>
                            </div>
                            <div class="flex justify-between py-2">
                                <span class="text-gray-600">Recorded By:</span>
                                <span class="font-semibold text-gray-900"><?php echo e($donation->recorder->full_name ?? 'BDRRMC Officer'); ?></span>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Items Description -->
                <div class="bg-gray-50 rounded-lg p-4 mb-6">
                    <h4 class="font-semibold text-gray-900 mb-2">Items Donated</h4>
                    <p class="text-gray-700"><?php echo e($donation->items_description); ?></p>
                    <?php if($donation->notes): ?>
                        <div class="mt-3 pt-3 border-t border-gray-300">
                            <p class="text-sm text-gray-600"><strong>Notes:</strong> <?php echo e($donation->notes); ?></p>
                        </div>
                    <?php endif; ?>
                </div>

                <!-- Blockchain Status -->
                <div class="rounded-lg p-4 mb-6 border-2
                    <?php echo e($donation->blockchain_status === 'confirmed' ? 'bg-green-50 border-green-300' : ''); ?>

                    <?php echo e($donation->blockchain_status === 'failed' ? 'bg-red-50 border-red-300' : ''); ?>

                    <?php echo e(!$donation->blockchain_status || $donation->blockchain_status === 'pending' ? 'bg-yellow-50 border-yellow-300' : ''); ?>">
                    <div class="flex items-start">
                        <div class="mr-4">
                            <?php if($donation->blockchain_status === 'confirmed'): ?>
                                <svg class="w-8 h-8 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                                </svg>
                            <?php elseif($donation->blockchain_status === 'failed'): ?>
                                <svg class="w-8 h-8 text-red-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 14l2-2m0 0l2-2m-2 2l-2-2m2 2l2 2m7-2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                                </svg>
                            <?php else: ?>
                                <svg class="w-8 h-8 text-yellow-600 animate-spin" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"/>
                                </svg>
                            <?php endif; ?>
                        </div>
                        <div class="flex-1">
                            <h4 class="text-sm font-semibold mb-1
                                <?php echo e($donation->blockchain_status === 'confirmed' ? 'text-green-800' : ''); ?>

                                <?php echo e($donation->blockchain_status === 'failed' ? 'text-red-800' : ''); ?>

                                <?php echo e(!$donation->blockchain_status || $donation->blockchain_status === 'pending' ? 'text-yellow-800' : ''); ?>">
                                Blockchain Status: <?php echo e(strtoupper($donation->blockchain_status ?? 'PENDING')); ?>

                            </h4>
                            <p class="text-sm mb-2
                                <?php echo e($donation->blockchain_status === 'confirmed' ? 'text-green-700' : ''); ?>

                                <?php echo e($donation->blockchain_status === 'failed' ? 'text-red-700' : ''); ?>

                                <?php echo e(!$donation->blockchain_status || $donation->blockchain_status === 'pending' ? 'text-yellow-700' : ''); ?>">
                                <?php if($donation->blockchain_status === 'confirmed'): ?>
                                    This donation has been permanently recorded on the Lisk blockchain.
                                <?php elseif($donation->blockchain_status === 'failed'): ?>
                                    Blockchain recording failed. The system will retry automatically.
                                <?php else: ?>
                                    Your donation is being recorded on the blockchain. This may take a few minutes.
                                <?php endif; ?>
                            </p>
                            <?php if($donation->blockchain_tx_hash): ?>
                                <a href="https://sepolia-blockscout.lisk.com/tx/<?php echo e($donation->blockchain_tx_hash); ?>" 
                                   target="_blank" 
                                   class="inline-flex items-center text-sm text-blue-600 hover:text-blue-800 font-medium">
                                    View on Blockchain Explorer
                                    <svg class="w-4 h-4 ml-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 6H6a2 2 0 00-2 2v10a2 2 0 002 2h10a2 2 0 002-2v-4M14 4h6m0 0v6m0-6L10 14"/>
                                    </svg>
                                </a>
                            <?php endif; ?>
                            <?php if($donation->ipfs_hash): ?>
                                <a href="https://gateway.pinata.cloud/ipfs/<?php echo e($donation->ipfs_hash); ?>" 
                                   target="_blank" 
                                   class="inline-flex items-center text-sm text-purple-600 hover:text-purple-800 font-medium ml-4">
                                    View Photos on IPFS
                                    <svg class="w-4 h-4 ml-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 6H6a2 2 0 00-2 2v10a2 2 0 002 2h10a2 2 0 002-2v-4M14 4h6m0 0v6m0-6L10 14"/>
                                    </svg>
                                </a>
                            <?php endif; ?>
                        </div>
                    </div>
                </div>

                <!-- Distribution History or Pending -->
                <?php if($donation->distributions->count() > 0): ?>
                <div class="mb-6">
                    <h3 class="text-lg font-semibold text-gray-900 mb-4">Distribution History</h3>
                    <div class="space-y-4">
                        <?php $__currentLoopData = $donation->distributions; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $distribution): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                        <div class="bg-blue-50 rounded-lg p-4 border border-blue-200">
                            <div class="flex justify-between items-start mb-2">
                                <div>
                                    <h4 class="font-semibold text-gray-900"><?php echo e($distribution->distributed_to); ?></h4>
                                    <p class="text-sm text-gray-600">Quantity: <?php echo e($distribution->quantity_distributed); ?></p>
                                    <?php if($distribution->notes): ?>
                                        <p class="text-sm text-gray-600 mt-1"><?php echo e($distribution->notes); ?></p>
                                    <?php endif; ?>
                                </div>
                                <div class="text-right text-sm">
                                    <p class="text-gray-600 font-medium"><?php echo e($distribution->distributed_at->format('M d, Y')); ?></p>
                                    <p class="text-gray-500"><?php echo e($distribution->distributed_at->format('h:i A')); ?></p>
                                </div>
                            </div>
                            <div class="flex items-center text-xs text-gray-500 mt-2">
                                <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/>
                                </svg>
                                Distributed by: <?php echo e($distribution->distributor->full_name ?? 'BDRRMC Officer'); ?>

                            </div>
                        </div>
                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                    </div>
                </div>
                <?php else: ?>
                <div class="bg-yellow-50 rounded-lg p-6 border border-yellow-200 text-center mb-6">
                    <svg class="w-12 h-12 text-yellow-500 mx-auto mb-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/>
                    </svg>
                    <p class="text-sm text-yellow-800 font-medium">This donation is pending distribution to beneficiaries.</p>
                    <p class="text-xs text-yellow-700 mt-1">Check back soon for updates!</p>
                </div>
                <?php endif; ?>

                <!-- Timeline -->
                <div class="mt-8">
                    <h3 class="text-lg font-semibold text-gray-900 mb-4">Donation Timeline</h3>
                    <ol class="relative border-l-2 border-gray-300 ml-3 space-y-6">
                        <!-- Step 1: Received -->
                        <li class="ml-6">
                            <span class="absolute flex items-center justify-center w-8 h-8 bg-green-500 rounded-full -left-4 ring-4 ring-white">
                                <svg class="w-4 h-4 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
                                </svg>
                            </span>
                            <div>
                                <p class="font-semibold text-gray-900">Donation Received</p>
                                <p class="text-sm text-gray-600"><?php echo e($donation->recorded_at->format('M d, Y h:i A')); ?></p>
                            </div>
                        </li>
                        
                        <!-- Step 2: Blockchain -->
                        <li class="ml-6">
                            <span class="absolute flex items-center justify-center w-8 h-8 rounded-full -left-4 ring-4 ring-white
                                <?php echo e($donation->blockchain_status === 'confirmed' ? 'bg-green-500' : ''); ?>

                                <?php echo e($donation->blockchain_status === 'failed' ? 'bg-red-500' : ''); ?>

                                <?php echo e(!$donation->blockchain_status || $donation->blockchain_status === 'pending' ? 'bg-yellow-500' : ''); ?>">
                                <?php if($donation->blockchain_status === 'confirmed'): ?>
                                    <svg class="w-4 h-4 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
                                    </svg>
                                <?php else: ?>
                                    <svg class="w-4 h-4 text-white animate-spin" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"/>
                                    </svg>
                                <?php endif; ?>
                            </span>
                            <div>
                                <p class="font-semibold text-gray-900">Blockchain Verification</p>
                                <p class="text-sm text-gray-600">
                                    <?php if($donation->blockchain_recorded_at): ?>
                                        <?php echo e($donation->blockchain_recorded_at->format('M d, Y h:i A')); ?>

                                    <?php else: ?>
                                        In progress...
                                    <?php endif; ?>
                                </p>
                            </div>
                        </li>
                        
                        <!-- Step 3: Distribution -->
                        <li class="ml-6">
                            <span class="absolute flex items-center justify-center w-8 h-8 rounded-full -left-4 ring-4 ring-white
                                <?php echo e($donation->distributions->count() > 0 ? 'bg-green-500' : 'bg-gray-400'); ?>">
                                <?php if($donation->distributions->count() > 0): ?>
                                    <svg class="w-4 h-4 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
                                    </svg>
                                <?php else: ?>
                                    <svg class="w-4 h-4 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/>
                                    </svg>
                                <?php endif; ?>
                            </span>
                            <div>
                                <p class="font-semibold text-gray-900">Distribution to Beneficiaries</p>
                                <p class="text-sm text-gray-600">
                                    <?php if($donation->distributions->count() > 0): ?>
                                        <?php echo e($donation->distributions->first()->distributed_at->format('M d, Y h:i A')); ?>

                                    <?php else: ?>
                                        Pending
                                    <?php endif; ?>
                                </p>
                            </div>
                        </li>
                        
                        <!-- Step 4: Fully Distributed -->
                        <li class="ml-6">
                            <span class="absolute flex items-center justify-center w-8 h-8 rounded-full -left-4 ring-4 ring-white
                                <?php echo e($donation->distribution_status === 'fully_distributed' ? 'bg-green-500' : 'bg-gray-400'); ?>">
                                <?php if($donation->distribution_status === 'fully_distributed'): ?>
                                    <svg class="w-4 h-4 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
                                    </svg>
                                <?php else: ?>
                                    <svg class="w-4 h-4 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/>
                                    </svg>
                                <?php endif; ?>
                            </span>
                            <div>
                                <p class="font-semibold text-gray-900">Fully Distributed</p>
                                <p class="text-sm text-gray-600">
                                    <?php if($donation->distribution_status === 'fully_distributed'): ?>
                                        Completed
                                    <?php else: ?>
                                        Awaiting completion
                                    <?php endif; ?>
                                </p>
                            </div>
                        </li>
                    </ol>
                </div>

            </div>
        </div>

        <!-- Action Buttons -->
        <div class="flex flex-wrap gap-4 justify-center mb-6">
            <a href="<?php echo e(route('home')); ?>" class="px-6 py-3 bg-blue-600 hover:bg-blue-700 text-white font-semibold rounded-lg transition shadow-md">
                Return to Map
            </a>
            <button onclick="window.print()" class="px-6 py-3 bg-gray-600 hover:bg-gray-700 text-white font-semibold rounded-lg transition shadow-md">
                Print Receipt
            </button>
            <button onclick="copyTrackingCode()" class="px-6 py-3 bg-purple-600 hover:bg-purple-700 text-white font-semibold rounded-lg transition shadow-md">
                Copy Tracking Code
            </button>
        </div>

    </main>

    <script>
    function copyTrackingCode() {
        const trackingCode = '<?php echo e($donation->tracking_code); ?>';
        navigator.clipboard.writeText(trackingCode).then(() => {
            alert('Tracking code copied to clipboard!');
        }).catch(err => {
            console.error('Failed to copy:', err);
            alert('Failed to copy tracking code. Please copy manually: ' + trackingCode);
        });
    }
    </script>

</body>
</html><?php /**PATH C:\Users\Judd\BayanihanCebuBackEnd\resources\views/donations/track.blade.php ENDPATH**/ ?>
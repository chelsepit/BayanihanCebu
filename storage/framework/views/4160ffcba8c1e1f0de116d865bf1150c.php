


<div class="bg-white rounded-lg shadow-lg p-8 mb-6">
    
    <!-- Tracking Code -->
    <div class="flex items-center justify-between mb-6 pb-6 border-b">
        <div>
            <p class="text-sm text-gray-600 mb-1">Tracking Code</p>
            <p class="text-2xl font-bold text-gray-800"><?php echo e($donation->tracking_code); ?></p>
            <span class="inline-block mt-2 px-3 py-1 bg-blue-100 text-blue-800 text-xs font-semibold rounded-full">
                Online Donation
            </span>
        </div>
        <div class="text-right">
            <span class="px-4 py-2 
                <?php if($donation->verification_status === 'verified'): ?> bg-green-100 text-green-800
                <?php elseif($donation->verification_status === 'rejected'): ?> bg-red-100 text-red-800
                <?php else: ?> bg-yellow-100 text-yellow-800
                <?php endif; ?>
                font-semibold rounded-full">
                <?php echo e(strtoupper($donation->verification_status)); ?>

            </span>
        </div>
    </div>

    <!-- Donation Details -->
    <div class="grid md:grid-cols-2 gap-6 mb-6">
        <div>
            <h3 class="text-lg font-semibold text-gray-800 mb-4">Donation Details</h3>
            <div class="space-y-3">
                <div class="flex justify-between">
                    <span class="text-gray-600">Donor:</span>
                    <span class="font-semibold text-gray-800"><?php echo e($donation->getDonorDisplayName()); ?></span>
                </div>
                <div class="flex justify-between">
                    <span class="text-gray-600">Amount:</span>
                    <span class="font-semibold text-gray-800">₱<?php echo e(number_format($donation->amount, 2)); ?></span>
                </div>
                <div class="flex justify-between">
                    <span class="text-gray-600">Payment Method:</span>
                    <span class="font-semibold text-gray-800 uppercase"><?php echo e($donation->payment_method); ?></span>
                </div>
                <div class="flex justify-between">
                    <span class="text-gray-600">Date:</span>
                    <span class="font-semibold text-gray-800"><?php echo e($donation->created_at->format('M d, Y')); ?></span>
                </div>
            </div>
        </div>

        <div>
            <h3 class="text-lg font-semibold text-gray-800 mb-4">Beneficiary</h3>
            <div class="space-y-3">
                <div class="flex justify-between">
                    <span class="text-gray-600">Barangay:</span>
                    <span class="font-semibold text-gray-800"><?php echo e($donation->barangay->name); ?></span>
                </div>
                <?php if($donation->disaster): ?>
                <div class="flex justify-between">
                    <span class="text-gray-600">Disaster:</span>
                    <span class="font-semibold text-gray-800"><?php echo e($donation->disaster->title); ?></span>
                </div>
                <?php endif; ?>
                <?php if($donation->verified_at): ?>
                <div class="flex justify-between">
                    <span class="text-gray-600">Verified:</span>
                    <span class="font-semibold text-gray-800"><?php echo e($donation->verified_at->format('M d, Y')); ?></span>
                </div>
                <?php endif; ?>
            </div>
        </div>
    </div>

    <!-- Blockchain Status -->
    <div class="p-4 
        <?php if($donation->blockchain_status === 'confirmed'): ?> bg-green-50 border-green-200
        <?php elseif($donation->blockchain_status === 'failed'): ?> bg-red-50 border-red-200
        <?php else: ?> bg-yellow-50 border-yellow-200
        <?php endif; ?>
        rounded-lg border-2 mb-4">
        <div class="flex items-center">
            <span class="text-2xl mr-3">
                <?php if($donation->blockchain_status === 'confirmed'): ?> ✓
                <?php elseif($donation->blockchain_status === 'failed'): ?> ✗
                <?php else: ?> ⟳
                <?php endif; ?>
            </span>
            <div>
                <h4 class="font-semibold 
                    <?php if($donation->blockchain_status === 'confirmed'): ?> text-green-800
                    <?php elseif($donation->blockchain_status === 'failed'): ?> text-red-800
                    <?php else: ?> text-yellow-800
                    <?php endif; ?>">
                    Blockchain Status: <?php echo e(strtoupper($donation->blockchain_status ?? 'pending')); ?>

                </h4>
                <p class="text-sm 
                    <?php if($donation->blockchain_status === 'confirmed'): ?> text-green-600
                    <?php elseif($donation->blockchain_status === 'failed'): ?> text-red-600
                    <?php else: ?> text-yellow-600
                    <?php endif; ?>">
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
                       class="text-sm text-blue-600 hover:text-blue-800 inline-flex items-center mt-2">
                        View on Blockchain Explorer
                        <svg class="w-3 h-3 ml-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 6H6a2 2 0 00-2 2v10a2 2 0 002 2h10a2 2 0 002-2v-4M14 4h6m0 0v6m0-6L10 14"/>
                        </svg>
                    </a>
                <?php endif; ?>
            </div>
        </div>
    </div>

</div>

<!-- Action Buttons -->
<div class="flex gap-4 justify-center">
    <a href="<?php echo e(route('home')); ?>" class="px-6 py-3 bg-blue-600 hover:bg-blue-700 text-white font-semibold rounded-lg transition-colors">
        Return to Map
    </a>
    <button onclick="window.print()" class="px-6 py-3 bg-gray-100 hover:bg-gray-200 text-gray-800 font-semibold rounded-lg transition-colors">
        Print Receipt
    </button>
</div><?php /**PATH C:\Users\Judd\BayanihanCebuBackEnd\resources\views\donations\partials\track-online.blade.php ENDPATH**/ ?>
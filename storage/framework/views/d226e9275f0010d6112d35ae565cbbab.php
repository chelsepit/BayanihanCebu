<?php $__env->startSection('content'); ?>
<div class="container mx-auto px-4 py-8 max-w-2xl">
    
    <!-- Success Icon -->
    <div class="text-center mb-8">
        <div class="inline-flex items-center justify-center w-20 h-20 bg-green-100 rounded-full mb-4">
            <svg class="w-12 h-12 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
            </svg>
        </div>
        <h1 class="text-3xl font-bold text-gray-800 mb-2">Thank You for Your Donation!</h1>
        <p class="text-gray-600">Your contribution will make a real difference</p>
    </div>

    <!-- Donation Summary Card -->
    <div class="bg-white rounded-lg shadow-lg p-8 mb-6">
        
        <!-- Tracking Code (Prominent) -->
        <div class="bg-blue-50 border-2 border-blue-200 rounded-lg p-6 mb-6 text-center">
            <p class="text-sm text-gray-600 mb-2">Your Tracking Code</p>
            <p class="text-3xl font-bold text-blue-600 mb-2"><?php echo e($donation->tracking_code); ?></p>
            <p class="text-sm text-gray-600">Save this code to track your donation</p>
        </div>

        <!-- Donation Details -->
        <div class="space-y-4 mb-6">
            <div class="flex justify-between pb-3 border-b">
                <span class="text-gray-600">Amount Donated:</span>
                <span class="font-bold text-xl text-gray-800">₱<?php echo e(number_format($donation->amount, 2)); ?></span>
            </div>
            
            <div class="flex justify-between pb-3 border-b">
                <span class="text-gray-600">Beneficiary:</span>
                <span class="font-semibold text-gray-800"><?php echo e($donation->disaster->barangay->name); ?></span>
            </div>
            
            <div class="flex justify-between pb-3 border-b">
                <span class="text-gray-600">Disaster Type:</span>
                <span class="font-semibold text-gray-800"><?php echo e($donation->disaster->type_display); ?></span>
            </div>
            
            <div class="flex justify-between pb-3 border-b">
                <span class="text-gray-600">Date:</span>
                <span class="font-semibold text-gray-800"><?php echo e($donation->created_at->format('M d, Y \a\t h:i A')); ?></span>
            </div>

            <div class="flex justify-between">
                <span class="text-gray-600">Status:</span>
                <span class="px-3 py-1 bg-<?php echo e($donation->status_color); ?>-100 text-<?php echo e($donation->status_color); ?>-800 font-semibold rounded-full text-sm">
                    <?php echo e(ucfirst($donation->status)); ?>

                </span>
            </div>
        </div>

        <!-- Blockchain Info -->
        <?php if($donation->transaction_hash): ?>
            <div class="bg-green-50 border border-green-200 rounded-lg p-4 mb-6">
                <div class="flex items-start gap-3">
                    <svg class="w-6 h-6 text-green-600 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"/>
                    </svg>
                    <div class="flex-1">
                        <p class="font-semibold text-green-900 mb-1">Blockchain Verified</p>
                        <p class="text-sm text-green-800 mb-2">Your donation has been securely recorded on the Lisk blockchain</p>
                        <p class="text-xs text-green-700 font-mono break-all">
                            <?php echo e($donation->transaction_hash); ?>

                        </p>
                    </div>
                </div>
            </div>
        <?php endif; ?>

        <!-- What Happens Next -->
        <div class="bg-gray-50 rounded-lg p-6">
            <h3 class="font-semibold text-gray-800 mb-4">What Happens Next?</h3>
            <ol class="space-y-3">
                <li class="flex items-start gap-3">
                    <span class="flex-shrink-0 w-6 h-6 bg-blue-600 text-white rounded-full flex items-center justify-center text-sm font-semibold">1</span>
                    <span class="text-sm text-gray-700">Your donation is verified and recorded on the blockchain</span>
                </li>
                <li class="flex items-start gap-3">
                    <span class="flex-shrink-0 w-6 h-6 bg-blue-600 text-white rounded-full flex items-center justify-center text-sm font-semibold">2</span>
                    <span class="text-sm text-gray-700">Local BDRMC officers prepare the aid distribution</span>
                </li>
                <li class="flex items-start gap-3">
                    <span class="flex-shrink-0 w-6 h-6 bg-blue-600 text-white rounded-full flex items-center justify-center text-sm font-semibold">3</span>
                    <span class="text-sm text-gray-700">Aid is distributed directly to affected families</span>
                </li>
                <li class="flex items-start gap-3">
                    <span class="flex-shrink-0 w-6 h-6 bg-blue-600 text-white rounded-full flex items-center justify-center text-sm font-semibold">4</span>
                    <span class="text-sm text-gray-700">You receive updates via your tracking code</span>
                </li>
            </ol>
        </div>

    </div>

    <!-- Action Buttons -->
    <div class="flex flex-col sm:flex-row gap-4">
        <a href="<?php echo e(route('home')); ?>" class="flex-1 px-6 py-3 bg-blue-600 hover:bg-blue-700 text-white font-semibold rounded-lg transition-colors text-center">
            Return to Map
        </a>
        <a href="<?php echo e(route('donation.track')); ?>" onclick="event.preventDefault(); document.getElementById('track-form').submit();" class="flex-1 px-6 py-3 bg-gray-100 hover:bg-gray-200 text-gray-800 font-semibold rounded-lg transition-colors text-center">
            Track This Donation
        </a>
    </div>

    <!-- Hidden form for tracking -->
    <form id="track-form" action="<?php echo e(route('donation.track')); ?>" method="POST" style="display: none;">
        <?php echo csrf_field(); ?>
        <input type="hidden" name="tracking_code" value="<?php echo e($donation->tracking_code); ?>">
    </form>

    <!-- Share Section -->
    <div class="mt-8 text-center">
        <p class="text-gray-600 mb-4">Help spread the word and encourage others to donate</p>
        <div class="flex justify-center gap-4">
            <button class="px-4 py-2 bg-blue-500 hover:bg-blue-600 text-white rounded-lg transition-colors">
                Share on Facebook
            </button>
            <button class="px-4 py-2 bg-sky-500 hover:bg-sky-600 text-white rounded-lg transition-colors">
                Share on Twitter
            </button>
        </div>
    </div>

</div>
<?php $__env->stopSection(); ?>
<?php echo $__env->make('layouts.app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\Users\Judd\BayanihanCebuBackEnd\resources\views\donations\success.blade.php ENDPATH**/ ?>
<?php $__env->startSection('content'); ?>
<div class="container mx-auto px-4 py-8 max-w-4xl">
    
    <!-- Back Button -->
    <a href="<?php echo e(route('home')); ?>" class="inline-flex items-center text-blue-600 hover:text-blue-800 mb-6">
        <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/>
        </svg>
        Back to Map
    </a>

    <!-- Page Header -->
    <div class="text-center mb-8">
        <h1 class="text-3xl font-bold text-gray-800 mb-2">Track Your Donation</h1>
        <p class="text-gray-600">View real-time status and distribution details</p>
    </div>

    <?php if(isset($donation_type)): ?>
        <!-- UNIFIED TRACKING RESULTS -->
        <?php if($donation_type === 'online'): ?>
            <?php echo $__env->make('donations.partials.track-online', ['donation' => $donation], array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>
        <?php elseif($donation_type === 'physical'): ?>
            <?php echo $__env->make('donations.partials.track-physical', ['donation' => $donation], array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>
        <?php else: ?>
            <?php echo $__env->make('donations.partials.track-legacy', ['donation' => $donation], array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>
        <?php endif; ?>
    <?php else: ?>
        <!-- TRACKING FORM -->
        <div class="bg-white rounded-lg shadow-lg p-8 mb-6">
            <form action="<?php echo e(route('donation.track')); ?>" method="POST" class="space-y-4">
                <?php echo csrf_field(); ?>
                <div>
                    <label for="tracking_code" class="block text-sm font-semibold text-gray-700 mb-2">
                        Tracking Code
                    </label>
                    <input 
                        type="text" 
                        id="tracking_code" 
                        name="tracking_code"
                        class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent"
                        placeholder="Enter your tracking code"
                        required
                        autofocus
                        value="<?php echo e(old('tracking_code')); ?>"
                    >
                </div>

                <?php if(session('error')): ?>
                    <div class="p-4 bg-red-50 border border-red-200 rounded-lg">
                        <p class="text-red-800 text-sm"><?php echo e(session('error')); ?></p>
                    </div>
                <?php endif; ?>

                <button type="submit" class="w-full px-6 py-3 bg-blue-600 hover:bg-blue-700 text-white font-semibold rounded-lg">
                    Track Donation
                </button>
            </form>
        </div>
    <?php endif; ?>

</div>
<?php $__env->stopSection(); ?>
<?php echo $__env->make('layouts.app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\Users\Judd\BayanihanCebuBackEnd\resources\views\donations\track.blade.php ENDPATH**/ ?>
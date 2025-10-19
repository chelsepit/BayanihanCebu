<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>DonorTrack | Staff Login</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-gray-50 flex items-center justify-center min-h-screen">

    <div class="w-full max-w-md bg-white rounded-2xl shadow-lg p-8">
        <!-- Logo -->
        <div class="text-center mb-6">
            <img src="https://cdn-icons-png.flaticon.com/512/1077/1077012.png" alt="logo" class="w-14 mx-auto mb-2">
            <h1 class="text-2xl font-semibold text-gray-800">DonorTrack</h1>
            <p class="text-sm text-gray-500 -mt-1">Cebu Donations</p>
        </div>

        <!-- Tabs -->
        <div class="flex mb-6 border border-gray-200 rounded-lg overflow-hidden">
            <button class="w-1/2 py-2 text-sm font-medium bg-white text-blue-700 border-b-2 border-blue-600">
                Login
            </button>
            <a href="<?php echo e(route('register')); ?>" class="w-1/2 py-2 text-sm font-medium text-gray-500 hover:text-gray-700 text-center">
                Register
            </a>
        </div>

        <!-- Welcome Text -->
        <h2 class="text-xl font-bold text-center mb-1">Welcome Back</h2>
        <p class="text-center text-gray-500 text-sm mb-6">Staff and Admin access only</p>

        <!-- Login Form -->

        <form method="POST" action="<?php echo e(route('login.post')); ?>">
            <?php echo csrf_field(); ?>
            <div class="mb-4">
                <label for="email" class="block text-sm font-medium text-gray-700 mb-1">Email</label>
                <input   type="email" id="email" name="email" required
                       class="w-full border-gray-300 rounded-lg focus:ring-blue-500 focus:border-blue-500"
                       placeholder="staff@donortrack.ph">
            </div>

            <div class="mb-6">
                <label for="password" class="block text-sm font-medium text-gray-700 mb-1">Password</label>
                <input type="password" id="password" name="password" required
                       class="w-full border-gray-300 rounded-lg focus:ring-blue-500 focus:border-blue-500"
                       placeholder="••••••••">
            </div>

            <button type="submit"
                    class="w-full py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 flex items-center justify-center space-x-2">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none"
                     viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round"
                     stroke-linejoin="round" stroke-width="2"
                     d="M17 16l4-4m0 0l-4-4m4 4H7"/></svg>
                <span>Sign In</span>
            </button>


        </form>

        <?php if($errors->any()): ?>
    <div class="mt-3 rounded-lg border border-red-400 bg-red-50 text-red-700 text-sm px-4 py-3 flex items-center gap-2 animate-fade-in">
        <svg xmlns="http://www.w3.org/2000/svg" class="w-4 h-4 text-red-600" fill="none" viewBox="0 0 24 24" stroke="currentColor">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01M12 5a7 7 0 110 14a7 7 0 010-14z" />
        </svg>
        <div>
            <?php $__currentLoopData = $errors->all(); $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $error): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                <p><?php echo e($error); ?></p>
            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
        </div>
    </div>
<?php endif; ?>



        <div class="mt-4 text-center">
            <a href="#" class="text-sm text-blue-600 hover:underline">Forgot password?</a>
        </div>

        <div class="mt-4 text-center">
            <a href="<?php echo e(url('/')); ?>" class="text-sm text-gray-500 hover:text-gray-700">← Back to home</a>
        </div>
    </div>

</body>
</html>
<?php /**PATH C:\Users\Judd\BayanihanCebuBackEnd\resources\views\auth\login.blade.php ENDPATH**/ ?>
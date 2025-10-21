<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>DonorTrack | Forgot Password</title>
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

        <!-- Heading -->
        <h2 class="text-xl font-bold text-center mb-1">Forgot Password</h2>
        <p class="text-center text-gray-500 text-sm mb-6">
            Enter your email to receive a password reset link
        </p>

        <!-- Success Message -->
        @if (session('success'))
            <div class="mb-4 rounded-lg border border-green-400 bg-green-50 text-green-700 text-sm px-4 py-3 flex items-center gap-2">
                <svg xmlns="http://www.w3.org/2000/svg" class="w-5 h-5 text-green-600" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                </svg>
                <div>{{ session('success') }}</div>
            </div>
        @endif

        <!-- Error Message -->
        @if ($errors->any())
            <div class="mb-4 rounded-lg border border-red-400 bg-red-50 text-red-700 text-sm px-4 py-3 flex items-center gap-2">
                <svg xmlns="http://www.w3.org/2000/svg" class="w-5 h-5 text-red-600" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                </svg>
                <div>
                    @foreach ($errors->all() as $error)
                        <p>{{ $error }}</p>
                    @endforeach
                </div>
            </div>
        @endif

        <!-- Forgot Password Form -->
        <form method="POST" action="{{ route('password.email') }}">
            @csrf

            <div class="mb-6">
                <label for="email" class="block text-sm font-medium text-gray-700 mb-1">Email Address</label>
                <input type="email" id="email" name="email" required
                       value="{{ old('email') }}"
                       class="w-full border-gray-300 rounded-lg focus:ring-blue-500 focus:border-blue-500"
                       placeholder="your@email.com">
            </div>

            <button type="submit"
                    class="w-full py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 flex items-center justify-center space-x-2">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z" />
                </svg>
                <span>Send Reset Link</span>
            </button>
        </form>

        <!-- Back to Login -->
        <div class="mt-4 text-center">
            <a href="{{ route('login') }}" class="text-sm text-blue-600 hover:underline">← Back to login</a>
        </div>

        <div class="mt-4 text-center">
            <a href="{{ url('/') }}" class="text-sm text-gray-500 hover:text-gray-700">← Back to home</a>
        </div>
    </div>

</body>
</html>

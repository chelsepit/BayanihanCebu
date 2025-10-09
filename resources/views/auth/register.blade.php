<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>DonorTrack | Resident Registration</title>
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
            <a href="{{ route('login') }}" class="w-1/2 py-2 text-sm font-medium text-gray-500 hover:text-gray-700 text-center">
                Staff Login
            </a>
            <button class="w-1/2 py-2 text-sm font-medium bg-white text-green-700 border-b-2 border-green-500">
                Register
            </button>
        </div>

        <!-- Heading -->
        <h2 class="text-xl font-bold text-center mb-1">Resident Registration</h2>
        <p class="text-center text-gray-500 text-sm mb-6">
            Register to access full barangay donation data
        </p>

        <!-- Registration Form -->
        <form method="POST" action="{{ route('register') }}">
            @csrf

            <div class="mb-4">
                <label for="name" class="block text-sm font-medium text-gray-700 mb-1">Full Name</label>
                <input type="text" id="name" name="name" required
                       class="w-full border-gray-300 rounded-lg focus:ring-green-500 focus:border-green-500"
                       placeholder="Juan Dela Cruz">
            </div>

            <!-- Barangay Dropdown --> <!--palihug kog edit sa backend lamat -->
            <div class="mb-4">
                <label for="barangay" class="block text-sm font-medium text-gray-700 mb-1">Barangay</label>
                <select id="barangay" name="barangay" required
                        class="w-full border-gray-300 rounded-lg focus:ring-green-500 focus:border-green-500">
                    <option value="" disabled selected>Select your barangay</option>
                    <option value="Lahug">Lahug</option>
                    <option value="Guadalupe">Guadalupe</option>
                    <option value="Banilad">Banilad</option>
                    <option value="Mabolo">Mabolo</option>
                    <option value="Capitol Site">Capitol Site</option>
                    <option value="Talamban">Talamban</option>
                    <option value="Basak">Basak</option>
                    <option value="Labangon">Labangon</option>
                    <option value="Sambag II">Sambag II</option>
                    <option value="Luz">Luz</option>
                </select>
            </div>

            <div class="mb-4">
                <label for="email" class="block text-sm font-medium text-gray-700 mb-1">Email</label>
                <input type="email" id="email" name="email" required
                       class="w-full border-gray-300 rounded-lg focus:ring-green-500 focus:border-green-500"
                       placeholder="juan@example.com">
            </div>

            <div class="mb-4">
                <label for="password" class="block text-sm font-medium text-gray-700 mb-1">Password</label>
                <input type="password" id="password" name="password" required
                       class="w-full border-gray-300 rounded-lg focus:ring-green-500 focus:border-green-500"
                       placeholder="••••••••">
            </div>

            <!-- Verification Info -->
            <div class="bg-gray-50 border border-gray-200 rounded-lg p-3 mb-6">
                <p class="text-sm font-medium text-gray-700 mb-1">Verification Required</p>
                <p class="text-xs text-gray-500">
                    Your account will need to be verified by staff before gaining full access.
                </p>
            </div>

            <button type="submit"
                    class="w-full py-2 bg-green-600 text-white rounded-lg hover:bg-green-700 flex items-center justify-center space-x-2">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none"
                     viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                          d="M12 4v16m8-8H4" />
                </svg>
                <span>Register Account</span>
            </button>
        </form>

        <div class="mt-4 text-center">
            <a href="{{ url('/') }}" class="text-sm text-gray-500 hover:text-gray-700">← Back to home</a>
        </div>
    </div>

</body>
</html>

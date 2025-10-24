<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>BayanihanCebu | Registration</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-gray-50 flex items-center justify-center min-h-screen">

    <div class="w-full max-w-md bg-white rounded-2xl shadow-lg p-8">
        <!-- Logo -->
        <div class="text-center mb-6"> 
           <div class="logo-icon">
                <img src="{{ asset('images/logo-icon.png') }}" alt="Logo Icon" class="w-14 mx-auto mb-2">
            </div>
            <h1 class="text-2xl font-semibold text-gray-800">BayanihanCebu</h1>
            <p class="text-sm text-gray-500 -mt-1">Cebu City</p>
        </div>
            
        <!-- Tabs -->
        <div class="flex mb-6 border border-gray-200 rounded-lg overflow-hidden">
            <a href="{{ route('login') }}"
               class="w-1/2 py-2 text-sm font-medium text-gray-500 hover:text-gray-700 text-center">
                Login
            </a>
            <button class="w-1/2 py-2 text-sm font-medium bg-white text-[#CA6702] border-b-2 border-[#CA6702]">
                Register
            </button>
        </div>

        <!-- Heading -->
        <h2 class="text-xl font-bold text-center mb-1">Registration</h2>
        <p class="text-center text-gray-500 text-sm mb-6">
            Register to access full barangay donation data
        </p>

        <!-- Error Messages -->
        @if ($errors->any())
            <div class="mb-4 rounded-lg border border-red-400 bg-red-50 text-red-700 text-sm px-4 py-3">
                <div class="flex items-center gap-2 mb-2">
                    <svg xmlns="http://www.w3.org/2000/svg" class="w-5 h-5 text-red-600" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                    </svg>
                    <strong>Please fix the following errors:</strong>
                </div>
                <ul class="list-disc list-inside space-y-1">
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        <!-- ✅ Registration Form -->
        <form method="POST" action="{{ route('register.post') }}">
            @csrf

            <!-- Full Name -->
            <div class="mb-4">
                <label for="full_name" class="block text-sm font-medium text-gray-700 mb-1">Full Name</label>
                <input type="text" id="full_name" name="full_name" required
                       value="{{ old('full_name') }}"
                       class="w-full border-gray-300 rounded-lg focus:ring-[#CA6702] focus:border-[#CA6702]"
                       placeholder="Juan Dela Cruz">
                @error('full_name')
                    <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                @enderror
            </div>

            <!-- Email -->
            <div class="mb-4">
                <label for="email" class="block text-sm font-medium text-gray-700 mb-1">Email</label>
                <input type="email" id="email" name="email" required
                       value="{{ old('email') }}"
                       class="w-full border-gray-300 rounded-lg focus:ring-[#CA6702] focus:border-[#CA6702]"
                       placeholder="juan@donortrack.ph">
                @error('email')
                    <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                @enderror
            </div>

            <!-- Password -->
            <div class="mb-4">
                <label for="password" class="block text-sm font-medium text-gray-700 mb-1">Password</label>
                <div class="relative">
                    <input type="password" id="password" name="password" required
                           class="w-full border-gray-300 rounded-lg focus:ring-[#CA6702] focus:border-[#CA6702] pr-10"
                           placeholder="••••••••">
                    <button type="button" onclick="togglePassword('password', 'togglePasswordIcon')"
                            class="absolute inset-y-0 right-0 px-3 flex items-center text-gray-500 hover:text-gray-700">
                        <svg id="togglePasswordIcon" xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
                        </svg>
                    </button>
                </div>
                @error('password')
                    <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                @enderror
            </div>

            <!-- Confirm Password -->
            <div class="mb-4">
                <label for="password_confirmation" class="block text-sm font-medium text-gray-700 mb-1">
                    Confirm Password
                </label>
                <div class="relative">
                    <input type="password" id="password_confirmation" name="password_confirmation" required
                           class="w-full border-gray-300 rounded-lg focus:ring-[#CA6702] focus:border-[#CA6702] pr-10"
                           placeholder="••••••••">
                    <button type="button" onclick="togglePassword('password_confirmation', 'togglePasswordConfirmIcon')"
                            class="absolute inset-y-0 right-0 px-3 flex items-center text-gray-500 hover:text-gray-700">
                        <svg id="togglePasswordConfirmIcon" xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
                        </svg>
                    </button>
                </div>
            </div>

            <!-- Barangay Dropdown -->
            <div class="mb-6">
                <label for="barangay_id" class="block text-sm font-medium text-gray-700 mb-1">Barangay</label>
                <select id="barangay_id" name="barangay_id" required
                        class="w-full border-gray-300 rounded-lg focus:ring-[#CA6702] focus:border-[#CA6702]">
                    <option value="">Select your barangay</option>
                    @foreach($barangays as $barangay)
                        <option value="{{ $barangay->barangay_id }}" {{ old('barangay_id') == $barangay->barangay_id ? 'selected' : '' }}>
                            {{ $barangay->name }}
                        </option>
                    @endforeach
                </select>
                @error('barangay_id')
                    <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                @enderror
            </div>

            <!-- Submit Button -->
            <button type="submit"
                    class="w-full py-2 bg-[#CA6702] text-white rounded-lg hover:bg-[#BB3E03] flex items-center justify-center space-x-2">
                <span>Register</span>
            </button>
        </form>

        <!-- Back to Home -->
        <div class="mt-4 text-center">
            <a href="{{ url('/') }}" class="text-sm text-gray-500 hover:text-gray-700">← Back to home</a>
        </div>
    </div>

    <script>
        // Toggle password visibility
        function togglePassword(inputId, iconId) {
            const input = document.getElementById(inputId);
            const icon = document.getElementById(iconId);

            if (input.type === 'password') {
                input.type = 'text';
                // Change to "eye-off" icon
                icon.innerHTML = `
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13.875 18.825A10.05 10.05 0 0112 19c-4.478 0-8.268-2.943-9.543-7a9.97 9.97 0 011.563-3.029m5.858.908a3 3 0 114.243 4.243M9.878 9.878l4.242 4.242M9.88 9.88l-3.29-3.29m7.532 7.532l3.29 3.29M3 3l3.59 3.59m0 0A9.953 9.953 0 0112 5c4.478 0 8.268 2.943 9.543 7a10.025 10.025 0 01-4.132 5.411m0 0L21 21" />
                `;
            } else {
                input.type = 'password';
                // Change back to "eye" icon
                icon.innerHTML = `
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
                `;
            }
        }

        // Form validation for password mismatch
        document.querySelector('form').addEventListener('submit', function(e) {
            const password = document.getElementById('password').value;
            const passwordConfirm = document.getElementById('password_confirmation').value;

            if (password !== passwordConfirm) {
                e.preventDefault();

                // Clear only password fields
                document.getElementById('password').value = '';
                document.getElementById('password_confirmation').value = '';

                // Show error message
                alert('Passwords do not match. Please try again.');

                // Focus on password field
                document.getElementById('password').focus();
            }
        });
    </script>

</body>
</html>

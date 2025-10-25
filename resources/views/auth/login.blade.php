<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Bayanihan | Login</title>
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
            <button class="w-1/2 py-2 text-sm font-medium bg-white text-[#CA6702] border-b-2 border-[#CA6702]">
                Login
            </button>
            <a href="{{ route('register') }}" class="w-1/2 py-2 text-sm font-medium text-gray-500 hover:text-gray-700 text-center">
                Register
            </a>
        </div>

        <!-- Welcome Text -->
        <h2 class="text-xl font-bold text-center mb-1">Welcome To BayanihanCebu</h2>
        <p class="text-center text-gray-500 text-sm mb-6">We don’t just track help. We make it happen.</p>

        <!-- Success Message -->
        @if (session('success'))
            <div class="mb-4 rounded-lg border border-green-400 bg-green-50 text-green-700 text-sm px-4 py-3 flex items-center gap-2">
                <svg xmlns="http://www.w3.org/2000/svg" class="w-5 h-5 text-green-600" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                </svg>
                <div>{{ session('success') }}</div>
            </div>
        @endif

        <!-- Login Form -->

        <form method="POST" action="{{ route('login.post') }}">
            @csrf
            <div class="mb-4">
                <label for="email" class="block text-sm font-medium text-gray-700 mb-1">Email</label>
                <input   type="email" id="email" name="email" required
                       class="w-full border-gray-300 rounded-lg focus:ring-[#CA6702] focus:border-[#CA6702]"
                       placeholder="staff@donortrack.ph">
            </div>

            <div class="mb-6">
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
            </div>

            <button type="submit"
                    class="w-full py-2 bg-[#CA6702] text-white rounded-lg hover:bg-[#BB3E03] flex items-center justify-center space-x-2">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none"
                     viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round"
                     stroke-linejoin="round" stroke-width="2"
                     d="M17 16l4-4m0 0l-4-4m4 4H7"/></svg>
                <span>Sign In</span>
            </button>


        </form>

        @if ($errors->any())
    <div class="mt-3 rounded-lg border border-red-400 bg-red-50 text-red-700 text-sm px-4 py-3 flex items-center gap-2 animate-fade-in">
        <svg xmlns="http://www.w3.org/2000/svg" class="w-4 h-4 text-red-600" fill="none" viewBox="0 0 24 24" stroke="currentColor">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01M12 5a7 7 0 110 14a7 7 0 010-14z" />
        </svg>
        <div>
            @foreach ($errors->all() as $error)
                <p>{{ $error }}</p>
            @endforeach
        </div>
    </div>
@endif



        <div class="mt-4 text-center">
            <a href="{{ route('password.request') }}" class="text-sm text-[#CA6702] hover:underline">Forgot password?</a>
        </div>

        <div class="mt-4 text-center">
            <a href="{{ url('/') }}" class="text-sm text-gray-500 hover:text-gray-700">← Back to home</a>
        </div>
    </div>

   <div class="mt-4 p-3 bg-gray-50 rounded-lg border border-gray-200">
    <p class="text-xs font-semibold text-gray-700 mb-2">List of Accounts:</p>
    <div class="text-xs text-gray-600 space-y-0.5 text-left">
        <p><span class="font-medium">Ldrrmo:</span> ldrrmo@cebu.gov.ph <span class="font-medium">Pass:</span>ldrrmo123</p>
        <p><span class="font-medium">BDRRMC (Apas):</span> bdrrmcCC001.bayanihancebu.com <span class="font-medium">Pass:</span>bdrrmc123</p>
        <p><span class="font-medium">BDRRMC (Basak Pardo):</span> bdrrmcCC002.bayanihancebu.com <span class="font-medium">Pass:</span>bdrrmc123</p>
        <p><span class="font-medium">BDRRMC (Basak San Nicolas):</span> bdrrmcCC003.bayanihancebu.com <span class="font-medium">Pass:</span>bdrrmc123</p>
        <p><span class="font-medium">BDRRMC (Busay):</span> bdrrmcCC004.bayanihancebu.com <span class="font-medium">Pass:</span>bdrrmc123</p>
        <p><span class="font-medium">BDRRMC (Capitol Site):</span> bdrrmcCC005.bayanihancebu.com <span class="font-medium">Pass:</span>bdrrmc123</p>
        <p><span class="font-medium">BDRRMC (Mabolo):</span> bdrrmcCC006.bayanihancebu.com <span class="font-medium">Pass:</span>bdrrmc123</p>
        <p><span class="font-medium">BDRRMC (Tisa):</span> bdrrmcCC007.bayanihancebu.com <span class="font-medium">Pass:</span>bdrrmc123</p>
        <p><span class="font-medium">BDRRMC (Guadalupe):</span> bdrrmcCC008.bayanihancebu.com <span class="font-medium">Pass:</span>bdrrmc123</p>
        <p><span class="font-medium">BDRRMC (Bambad):</span> bdrrmcCC009.bayanihancebu.com <span class="font-medium">Pass:</span>bdrrmc123</p>
        <p><span class="font-medium">BDRRMC (Talamban):</span> bdrrmcCC010.bayanihancebu.com <span class="font-medium">Pass:</span>bdrrmc123</p>
        <p><span class="font-medium">BDRRMC (Lahug):</span> bdrrmcCC011.bayanihancebu.com <span class="font-medium">Pass:</span>bdrrmc123</p>
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
    </script>

</body>
</html>

{{-- Barangay Dashboard Header --}}
<div class="bg-[#0D47A1] text-white px-6 py-4 flex justify-between items-center">
    <div>
        <h1 class="text-xl font-semibold">BayanihanCebu - BDRRMC</h1>
        <p class="text-sm text-blue-200">Barangay {{ $barangay->name ?? 'Unknown' }}</p>
    </div>
    <div class="flex items-center gap-4">
        <div class="text-right">
            <p class="text-sm text-blue-200">Logged in as</p>
            <p class="font-medium">{{ session('user_name') }}</p>
        </div>
        <form action="{{ route('logout') }}" method="POST">
            @csrf
            <button type="submit" class="bg-white/20 hover:bg-white/30 px-4 py-2 rounded text-sm transition">
                Logout
            </button>
        </form>
    </div>
</div>

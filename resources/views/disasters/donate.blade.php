@extends('layouts.app')

@section('content')
<div class="container mx-auto px-4 py-8 max-w-3xl">
    
    <!-- Back Button -->
    <a href="{{ route('home') }}" class="inline-flex items-center text-blue-600 hover:text-blue-800 mb-6">
        <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/>
        </svg>
        Back to Map
    </a>

    <!-- Disaster Info Card -->
    <div class="bg-white rounded-lg shadow-md p-6 mb-8">
        <div class="flex items-start justify-between mb-4">
            <div>
                <h1 class="text-2xl font-bold text-gray-800 mb-2">{{ $disaster->title }}</h1>
                <p class="text-gray-600">{{ $disaster->barangay->name }}</p>
            </div>
            <span class="px-4 py-2 bg-{{ $disaster->severity_color }}-100 text-{{ $disaster->severity_color }}-800 text-sm font-semibold rounded-full">
                {{ ucfirst($disaster->severity) }}
            </span>
        </div>

        @if($disaster->description)
            <p class="text-gray-700 mb-4">{{ $disaster->description }}</p>
        @endif

        <div class="grid grid-cols-2 gap-4 mb-4">
            <div class="bg-gray-50 p-4 rounded">
                <p class="text-sm text-gray-600">Affected Families</p>
                <p class="text-2xl font-bold text-gray-800">{{ number_format($disaster->affected_families) }}</p>
            </div>
            <div class="bg-gray-50 p-4 rounded">
                <p class="text-sm text-gray-600">Donations Received</p>
                <p class="text-2xl font-bold text-green-600">₱{{ number_format($disaster->total_donations, 2) }}</p>
            </div>
        </div>

        @if($disaster->urgentNeeds->count() > 0)
            <div>
                <p class="text-sm font-semibold text-gray-700 mb-2">Urgent Needs:</p>
                <div class="flex gap-2 flex-wrap">
                    @foreach($disaster->urgentNeeds as $need)
                        <span class="px-3 py-1 bg-blue-100 text-blue-800 text-sm rounded-full">
                            {{ ucfirst($need->type) }}
                            @if($need->quantity_needed)
                                ({{ $need->quantity_fulfilled }}/{{ $need->quantity_needed }} {{ $need->unit }})
                            @endif
                        </span>
                    @endforeach
                </div>
            </div>
        @endif
    </div>

    <!-- Donation Form -->
    <div class="bg-white rounded-lg shadow-md p-6">
        <h2 class="text-xl font-bold text-gray-800 mb-6">Make a Donation</h2>

        <form action="{{ route('donation.process', $disaster->id) }}" method="POST" id="donationForm">
            @csrf

            <!-- Donation Type -->
            <div class="mb-6">
                <label class="block text-sm font-semibold text-gray-700 mb-2">Donation Type</label>
                <div class="flex gap-4">
                    <label class="flex items-center">
                        <input type="radio" name="donation_type" value="monetary" checked class="mr-2">
                        <span>Monetary</span>
                    </label>
                    <label class="flex items-center">
                        <input type="radio" name="donation_type" value="in-kind" class="mr-2">
                        <span>In-Kind</span>
                    </label>
                </div>
            </div>

            <!-- Amount -->
            <div class="mb-6">
                <label for="amount" class="block text-sm font-semibold text-gray-700 mb-2">
                    Amount (₱) <span class="text-red-500">*</span>
                </label>
                <input 
                    type="number" 
                    name="amount" 
                    id="amount" 
                    min="10" 
                    step="0.01"
                    class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 @error('amount') border-red-500 @enderror"
                    required
                    placeholder="Enter amount"
                    value="{{ old('amount') }}"
                >
                @error('amount')
                    <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                @enderror

                <!-- Preset Amounts -->
                <div class="flex gap-2 mt-3 flex-wrap">
                    <button type="button" onclick="setAmount(100)" class="px-4 py-2 bg-gray-100 hover:bg-gray-200 rounded text-sm">₱100</button>
                    <button type="button" onclick="setAmount(500)" class="px-4 py-2 bg-gray-100 hover:bg-gray-200 rounded text-sm">₱500</button>
                    <button type="button" onclick="setAmount(1000)" class="px-4 py-2 bg-gray-100 hover:bg-gray-200 rounded text-sm">₱1,000</button>
                    <button type="button" onclick="setAmount(5000)" class="px-4 py-2 bg-gray-100 hover:bg-gray-200 rounded text-sm">₱5,000</button>
                </div>
            </div>

            <!-- Anonymous Donation -->
            <div class="mb-6">
                <label class="flex items-center">
                    <input 
                        type="checkbox" 
                        name="is_anonymous" 
                        id="is_anonymous"
                        value="1"
                        class="mr-2"
                        onchange="toggleDonorFields()"
                    >
                    <span class="text-sm text-gray-700">Make this donation anonymous</span>
                </label>
            </div>

            <div id="donorFields">
                <!-- Donor Name -->
                <div class="mb-6">
                    <label for="donor_name" class="block text-sm font-semibold text-gray-700 mb-2">
                        Full Name <span class="text-red-500">*</span>
                    </label>
                    <input 
                        type="text" 
                        name="donor_name" 
                        id="donor_name" 
                        class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 @error('donor_name') border-red-500 @enderror"
                        placeholder="Enter your full name"
                        value="{{ old('donor_name', auth()->user()->name ?? '') }}"
                    >
                    @error('donor_name')
                        <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Donor Email -->
                <div class="mb-6">
                    <label for="donor_email" class="block text-sm font-semibold text-gray-700 mb-2">
                        Email Address <span class="text-red-500">*</span>
                    </label>
                    <input 
                        type="email" 
                        name="donor_email" 
                        id="donor_email" 
                        class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 @error('donor_email') border-red-500 @enderror"
                        placeholder="Enter your email"
                        value="{{ old('donor_email', auth()->user()->email ?? '') }}"
                    >
                    @error('donor_email')
                        <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Donor Phone -->
                <div class="mb-6">
                    <label for="donor_phone" class="block text-sm font-semibold text-gray-700 mb-2">
                        Phone Number (Optional)
                    </label>
                    <input 
                        type="tel" 
                        name="donor_phone" 
                        id="donor_phone" 
                        class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500"
                        placeholder="Enter your phone number"
                        value="{{ old('donor_phone') }}"
                    >
                </div>
            </div>

            <!-- Submit Button -->
            <button 
                type="submit" 
                class="w-full bg-blue-600 hover:bg-blue-700 text-white font-semibold py-3 px-6 rounded-lg transition-colors"
            >
                Proceed to Payment
            </button>
        </form>
    </div>

    <!-- Trust Badges -->
    <div class="mt-8 flex justify-center items-center gap-8 flex-wrap">
        <div class="flex items-center gap-2 text-gray-600">
            <svg class="w-5 h-5 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"/>
            </svg>
            <span class="text-sm">Secure Payment</span>
        </div>
        <div class="flex items-center gap-2 text-gray-600">
            <svg class="w-5 h-5 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"/>
            </svg>
            <span class="text-sm">Blockchain Verified</span>
        </div>
    </div>

</div>

<script>
function setAmount(value) {
    document.getElementById('amount').value = value;
}

function toggleDonorFields() {
    const isAnonymous = document.getElementById('is_anonymous').checked;
    const donorFields = document.getElementById('donorFields');
    const nameField = document.getElementById('donor_name');
    const emailField = document.getElementById('donor_email');
    
    if (isAnonymous) {
        donorFields.style.display = 'none';
        nameField.removeAttribute('required');
        emailField.removeAttribute('required');
    } else {
        donorFields.style.display = 'block';
        nameField.setAttribute('required', 'required');
        emailField.setAttribute('required', 'required');
    }
}
</script>
@endsection
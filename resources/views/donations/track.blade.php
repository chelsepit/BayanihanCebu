<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Track Your Donation - BayanihanCebu</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <style>
        @media print {
            header, .no-print {
                display: none !important;
            }
            body {
                background: white !important;
                margin: 0;
                padding: 0;
            }
        }
    </style>
</head>
<body>

    @if(isset($donation))
        {{-- Header for tracking results --}}
        <header style="background: linear-gradient(to right, #1d4ed8, #1e3a8a); color: white; box-shadow: 0 4px 6px rgba(0,0,0,0.1);">
            <div style="max-width: 1280px; margin: 0 auto; padding: 16px 24px;">
                <div style="display: flex; justify-content: space-between; align-items: center;">
                    <div>
                        <h1 style="font-size: 20px; font-weight: 700; margin: 0;">BayanihanCebu - Donation Tracking</h1>
                        <p style="color: #bfdbfe; font-size: 14px; margin: 4px 0 0 0;">Barangay {{ $donation->barangay->name }}</p>
                    </div>
                    <a href="{{ route('home') }}" style="background: #2563eb; color: white; text-decoration: none; padding: 8px 16px; border-radius: 8px; font-size: 14px; font-weight: 500; display: flex; align-items: center; transition: background 0.3s;" class="no-print">
                        <svg width="16" height="16" fill="currentColor" viewBox="0 0 24 24" style="margin-right: 8px;">
                            <path d="M10 20v-6h4v6h5v-8h3L12 3 2 12h3v8z"/>
                        </svg>
                        Back to Map
                    </a>
                </div>
            </div>
        </header>

        {{-- Show tracking results --}}
        @if($donation_type === 'physical')
            {{-- Physical Donation Tracking --}}
            @include('donations.partials.track-physical')
        @else
            {{-- Online Donation Tracking --}}
            @include('donations.partials.track-online')
        @endif
    @else
        {{-- Show tracking form --}}
        <div class="min-h-screen flex items-center justify-center px-4" style="background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);">
            <div class="max-w-2xl w-full">
                <div class="bg-white rounded-xl shadow-xl p-8">
                    <div class="text-center mb-8">
                        <div class="inline-flex items-center justify-center w-16 h-16 bg-purple-100 rounded-full mb-4">
                            <svg class="w-8 h-8 text-purple-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/>
                            </svg>
                        </div>
                        <h1 class="text-3xl font-bold text-gray-900 mb-2">Track Your Donation</h1>
                        <p class="text-gray-600">Enter your tracking code to see your donation's journey</p>
                    </div>

                    @if(session('error'))
                        <div class="mb-6 bg-red-50 border-l-4 border-red-500 p-4 rounded">
                            <div class="flex">
                                <svg class="w-5 h-5 text-red-500 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                                </svg>
                                <p class="text-red-700">{{ session('error') }}</p>
                            </div>
                        </div>
                    @endif

                    <form action="{{ route('donation.track') }}" method="POST" class="space-y-6">
                        @csrf
                        <div>
                            <label for="tracking_code" class="block text-sm font-medium text-gray-700 mb-2">
                                Tracking Code
                            </label>
                            <input
                                type="text"
                                name="tracking_code"
                                id="tracking_code"
                                required
                                class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-purple-500 focus:border-transparent transition"
                                placeholder="e.g., CC001-2025-00001"
                                value="{{ old('tracking_code') }}"
                            >
                            @error('tracking_code')
                                <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>

                        <button
                            type="submit"
                            class="w-full bg-gradient-to-r from-purple-600 to-blue-600 hover:from-purple-700 hover:to-blue-700 text-white font-bold py-3 px-6 rounded-lg transition-all duration-200 shadow-lg hover:shadow-xl transform hover:-translate-y-0.5 flex items-center justify-center"
                        >
                            <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/>
                            </svg>
                            Track Donation
                        </button>
                    </form>

                    <div class="mt-8 pt-6 border-t border-gray-200">
                        <div class="text-center">
                            <p class="text-sm text-gray-600 mb-4">Need help?</p>
                            <a href="{{ route('home') }}" class="inline-flex items-center text-blue-600 hover:text-blue-800 text-sm font-medium transition">
                                <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/>
                                </svg>
                                Back to Map
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    @endif

</body>
</html>

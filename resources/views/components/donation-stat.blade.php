@props(['label' => 'Label', 'value' => 'Value', 'color' => 'purple', 'icon' => 'user'])

@php
    $colorClasses = [
        'purple' => ['bg' => 'bg-purple-100', 'text' => 'text-purple-600'],
        'blue' => ['bg' => 'bg-blue-100', 'text' => 'text-blue-600'],
        'green' => ['bg' => 'bg-green-100', 'text' => 'text-green-600'],
        'orange' => ['bg' => 'bg-orange-100', 'text' => 'text-orange-600'],
    ];
    
    $colors = $colorClasses[$color] ?? $colorClasses['purple'];
    
    // Icon SVG paths
    $icons = [
        'user' => 'M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z',
        'tag' => 'M7 7h.01M7 3h5c.512 0 1.024.195 1.414.586l7 7a2 2 0 010 2.828l-7 7a2 2 0 01-2.828 0l-7-7A1.994 1.994 0 013 12V7a4 4 0 014-4z',
        'currency' => 'M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z',
        'package' => 'M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4',
    ];
    
    $iconPath = $icons[$icon] ?? $icons['user'];
@endphp

<div class="bg-white rounded-lg shadow-md p-5">
    <div class="flex items-center">
        <div class="p-3 {{ $colors['bg'] }} rounded-lg mr-4">
            <svg class="w-6 h-6 {{ $colors['text'] }}" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="{{ $iconPath }}"/>
            </svg>
        </div>
        <div>
            <p class="text-sm text-gray-600">{{ $label }}</p>
            <p class="text-lg font-bold text-gray-900">{{ $value }}</p>
        </div>
    </div>
</div>
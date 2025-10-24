@props(['color' => 'green', 'spin' => false])

@php
    $iconColors = [
        'green' => 'text-green-600',
        'red' => 'text-red-600',
        'yellow' => 'text-yellow-600',
    ];
    
    $colorClass = $iconColors[$color] ?? $iconColors['green'];
    $spinClass = $spin ? 'animate-spin' : '';
@endphp

@if($color === 'green')
    <svg class="w-8 h-8 {{ $colorClass }}" fill="none" stroke="currentColor" viewBox="0 0 24 24">
        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
    </svg>
@elseif($color === 'red')
    <svg class="w-8 h-8 {{ $colorClass }}" fill="none" stroke="currentColor" viewBox="0 0 24 24">
        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 14l2-2m0 0l2-2m-2 2l-2-2m2 2l2 2m7-2a9 9 0 11-18 0 9 9 0 0118 0z"/>
    </svg>
@else
    <svg class="w-8 h-8 {{ $colorClass }} {{ $spinClass }}" fill="none" stroke="currentColor" viewBox="0 0 24 24">
        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"/>
    </svg>
@endif
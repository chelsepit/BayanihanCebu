<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>BayanihanCebu - Transparent Disaster Relief for Cebu</title>

    <!-- Leaflet CSS -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/leaflet/1.9.4/leaflet.min.css" />

    @vite(['resources/css/app.css', 'resources/js/app.js'])
    
    <!-- External CSS -->
    <link rel="stylesheet" href="{{ asset('css/public/welcome.css') }}">
</head>
<body>

    @include('public.sections.hero')
    @include('public.sections.map')
    @include('public.sections.track')
    @include('public.sections.verified-donations')
    @include('public.sections.trust')

    <!-- Leaflet JS -->
    <script src="https://cdnjs.cloudflare.com/ajax/libs/leaflet/1.9.4/leaflet.min.js"></script>

    <!-- External JavaScript -->
    <script src="{{ asset('js/public/welcome-map.js') }}"></script>

@include('partials.footer')
</body>
</html>

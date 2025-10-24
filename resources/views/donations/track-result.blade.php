<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Track Your Donation - BayanihanCebu</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="bg-gradient-to-br from-gray-50 to-gray-100 min-h-screen py-8">
    
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        @include('donations.partials.track-physical')
    </div>

</body>
</html>
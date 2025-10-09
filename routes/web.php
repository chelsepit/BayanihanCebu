<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\BarangayMapController;

Route::get('/login', [AuthController::class, 'showLogin'])->name('login');
Route::get('/register', [AuthController::class, 'showRegister'])->name('register');
Route::post('/register', [AuthController::class, 'register'])->name('register.post');
Route::post('/login', [AuthController::class, 'login'])->name('login.post');

Route::get('/', function () {
    return view('welcome'); // or 'home' if that’s your file name
})->name('home');

// Dashboard routes
Route::get('/city/dashboard', fn() => view('UserDashboards.citydashboard'))->name('city.dashboard');
Route::get('/barangay/dashboard', fn() => view('UserDashboards.barangaydashboard'))->name('barangay.dashboard');
Route::get('/resident/dashboard', fn() => view('UserDashboards.residentdashboard'))->name('resident.dashboard');


Route::get('/barangay/map', [BarangayMapController::class, 'index'])->name('barangay.map');
Route::get('/api/barangay-map-data', [BarangayMapController::class, 'getMapData']);
Route::get('/api/barangay/{barangayId}', [BarangayMapController::class, 'getBarangayDetails']);

// Donation tracking page
Route::get('/donation/track', function () {
    return view('donation.track'); // placeholder view
})->name('donation.track');

// Barangay map page
Route::get('/barangay/map', function () {
    return view('barangay.map'); // placeholder view
})->name('barangay.map');

// Fundraisers page
Route::get('/fundraisers', function () {
    return view('fundraisers.index'); // placeholder view
})->name('fundraisers');


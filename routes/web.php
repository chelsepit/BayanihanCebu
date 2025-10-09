<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\BarangayMapController;
use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\AdminDashboardController;
use App\Http\Controllers\CityDashboardController;
use App\Http\Controllers\BarangayDashboardController;
use App\Http\Controllers\ResidentDashboardController;

Route::get('/login', [AuthController::class, 'showLogin'])->name('login');
Route::get('/register', [AuthController::class, 'showRegister'])->name('register');
Route::post('/register', [AuthController::class, 'register'])->name('register.post');
Route::post('/login', [LoginController::class, 'login'])->name('login.post');
Route::post('/logout', [LoginController::class, 'logout'])->name('logout');

// Home page
Route::get('/', function () {
    return view('welcome');
})->name('home');

// Public routes (remove these if they should be protected)
Route::get('/barangay/map', [BarangayMapController::class, 'index'])->name('barangay.map');
Route::get('/api/barangay-map-data', [BarangayMapController::class, 'getMapData']);
Route::get('/api/barangay/{barangayId}', [BarangayMapController::class, 'getBarangayDetails']);

// Donation tracking page
Route::get('/donation/track', function () {
    return view('donation.track');
})->name('donation.track');

// Fundraisers page
Route::get('/fundraisers', function () {
    return view('fundraisers.index');
})->name('fundraisers');

// Keep using auth.check instead of auth
Route::middleware(['auth.check', 'role:ldrrmo'])->group(function () {
    Route::get('/city/dashboard', [CityDashboardController::class, 'index'])->name('city.dashboard');
});

Route::middleware(['auth.check', 'role:bdrrmc'])->group(function () {
    Route::get('/barangay/dashboard', [BarangayDashboardController::class, 'index'])->name('barangay.dashboard');
});

Route::middleware(['auth.check', 'role:resident'])->group(function () {
    Route::get('/resident/dashboard', [ResidentDashboardController::class, 'index'])->name('resident.dashboard');
});

Route::middleware(['auth.check', 'role:admin'])->group(function () {
    Route::get('/admin/dashboard', [AdminDashboardController::class, 'index'])->name('admin.dashboard');
});

Route::middleware(['auth.check'])->group(function () {
    Route::get('/api/user', [LoginController::class, 'getCurrentUser']);
});
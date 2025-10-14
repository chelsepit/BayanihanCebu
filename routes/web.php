<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\BarangayMapController;
use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\AdminDashboardController;
use App\Http\Controllers\CityDashboardController;
use App\Http\Controllers\BarangayDashboardController;
use App\Http\Controllers\ResidentDashboardController;
use App\Http\Controllers\DisasterMapController;

Route::get('/login', [AuthController::class, 'showLogin'])->name('login');
Route::get('/register', [AuthController::class, 'showRegister'])->name('register');
Route::post('/register', [AuthController::class, 'register'])->name('register.post');
Route::post('/login', [LoginController::class, 'login'])->name('login.post');
Route::post('/logout', [LoginController::class, 'logout'])->name('logout');

Route::get('/', function () {
    return view('welcome');
})->name('home');

// Public routes (remove these if they should be protected)
Route::get('/barangay/map', [BarangayMapController::class, 'index'])->name('barangay.map');
Route::get('/api/barangay-map-data', [BarangayMapController::class, 'getMapData']);
Route::get('/api/barangay/{barangayId}', [BarangayMapController::class, 'getBarangayDetails']);

Route::get('/city/dashboard', [CityDashboardController::class, 'index'])->name('city.dashboard');
Route::get('/admin/dashboard', [AdminDashboardController::class, 'index'])->name('admin.dashboard');
Route::get('/barangay/dashboard', [BarangayDashboardController::class, 'index'])->name('barangay.dashboard');
Route::get('/resident/dashboard', [ResidentDashboardController::class, 'index'])->name('resident.dashboard');

// Donation tracking page
Route::get('/donation/track', function () {
    return view('donation.track');
})->name('donation.track');

// Fundraisers page
Route::get('/fundraisers', function () {
    return view('fundraisers.index');
})->name('fundraisers');



// Disaster Map Routes
Route::get('/', [DisasterMapController::class, 'index'])->name('home');

// Donation Routes
Route::post('/track-donation', [DisasterMapController::class, 'trackDonation'])->name('donation.track');
Route::get('/donate/{disaster}', [DisasterMapController::class, 'showDonateForm'])->name('disaster.donate');
Route::post('/donate/{disaster}', [DisasterMapController::class, 'processDonation'])->name('donation.process');
Route::get('/donation/success/{trackingCode}', [DisasterMapController::class, 'donationSuccess'])->name('donation.success');

// Statistics API
Route::get('/api/statistics', [DisasterMapController::class, 'statistics'])->name('api.statistics');
Route::get('/api/barangays', [DisasterMapController::class, 'apiBarangays'])->name('api.barangays');
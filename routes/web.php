<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\BarangayMapController;
use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\AdminDashboardController;
use App\Http\Controllers\CityDashboardController;
use App\Http\Controllers\BarangayDashboardController;
use App\Http\Controllers\ResidentDashboardController;

// ==================== PUBLIC ROUTES ====================

// Home page
Route::get('/', function () {
    return view('welcome');
})->name('home');

// Authentication routes
Route::get('/login', [AuthController::class, 'showLogin'])->name('login');
Route::get('/register', [AuthController::class, 'showRegister'])->name('register');
Route::post('/register', [AuthController::class, 'register'])->name('register.post');
Route::post('/login', [LoginController::class, 'login'])->name('login.post');
Route::post('/logout', [LoginController::class, 'logout'])->name('logout');

// Public map and donation tracking
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

// ==================== PROTECTED ROUTES ====================

// Get current user info (all authenticated users)
Route::middleware(['auth.check'])->group(function () {
    Route::get('/api/user', [LoginController::class, 'getCurrentUser']);
});

// ==================== LDRRMO ROUTES ====================
Route::middleware(['auth.check', 'role:ldrrmo'])->group(function () {
    Route::get('/city/dashboard', [CityDashboardController::class, 'index'])->name('city.dashboard');
});

// ==================== BDRRMC ROUTES ====================
Route::middleware(['auth.check', 'role:bdrrmc'])->group(function () {
    // Dashboard view
    Route::get('/barangay/dashboard', [BarangayDashboardController::class, 'index'])->name('barangay.dashboard');
    
    // Resource Needs APIs
    Route::get('/api/bdrrmc/needs', [BarangayDashboardController::class, 'getNeeds']);
    Route::post('/api/bdrrmc/needs', [BarangayDashboardController::class, 'createNeed']);
    Route::patch('/api/bdrrmc/needs/{id}', [BarangayDashboardController::class, 'updateNeed']);
    Route::delete('/api/bdrrmc/needs/{id}', [BarangayDashboardController::class, 'deleteNeed']);
    
    // Physical Donations APIs
    Route::get('/api/bdrrmc/physical-donations', [BarangayDashboardController::class, 'getPhysicalDonations']);
    Route::post('/api/bdrrmc/physical-donations', [BarangayDashboardController::class, 'recordDonation']);
    Route::get('/api/bdrrmc/physical-donations/{id}', [BarangayDashboardController::class, 'getDonationDetails']);
    Route::post('/api/bdrrmc/physical-donations/{id}/distribute', [BarangayDashboardController::class, 'recordDistribution']);
    
    // Online Donations (Read-only)
    Route::get('/api/bdrrmc/online-donations', [BarangayDashboardController::class, 'getOnlineDonations']);
    
    // Barangay Info
    Route::get('/api/bdrrmc/my-barangay', [BarangayDashboardController::class, 'getBarangayInfo']);
    Route::patch('/api/bdrrmc/my-barangay', [BarangayDashboardController::class, 'updateBarangayInfo']);
});

// ==================== RESIDENT ROUTES ====================
Route::middleware(['auth.check', 'role:resident'])->group(function () {
    Route::get('/resident/dashboard', [ResidentDashboardController::class, 'index'])->name('resident.dashboard');
});

// ==================== ADMIN ROUTES ====================
Route::middleware(['auth.check', 'role:admin'])->group(function () {
    Route::get('/admin/dashboard', [AdminDashboardController::class, 'index'])->name('admin.dashboard');
});
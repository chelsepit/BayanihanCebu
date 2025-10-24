<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\ResidentDashboardController;
use App\Http\Controllers\DonationController;
use App\Http\Controllers\PaymentController;
use App\Http\Controllers\CityDashboardController;
use App\Http\Controllers\BarangayDashboardController;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/

// ==================== RESIDENT DASHBOARD ROUTES ====================
Route::prefix('resident')->group(function () {
    Route::get('/resource-needs', [ResidentDashboardController::class, 'getResourceNeeds']); // CHANGED FROM urgent-needs
    Route::get('/barangays-map', [ResidentDashboardController::class, 'getBarangaysMap']);
    Route::get('/statistics', [ResidentDashboardController::class, 'getStatistics']);
});

// ==================== DONATION ROUTES (for residents) ====================
Route::post('/donations', [DonationController::class, 'store']);
Route::get('/donations/my', [DonationController::class, 'myDonations']);
Route::get('/donations/{id}', [DonationController::class, 'show']);
Route::post('/donations/track', [DonationController::class, 'track']);
Route::get('/donations/stats', [DonationController::class, 'getResidentStats']);
Route::get('/donations', [DonationController::class, 'index']);
Route::get('/donations/barangay/{barangayId}', [DonationController::class, 'getByBarangay']);
Route::post('/donations/physical/{id}/verify-blockchain', [DonationController::class, 'verifyPhysicalDonationBlockchain']);
// ==================== PAYMENT ROUTES (PayMongo) ====================
Route::post('/payments/create-intent', [PaymentController::class, 'createPaymentIntent']);
Route::post('/payments/attach-method', [PaymentController::class, 'attachPaymentMethod']);

// Webhook (no auth needed)
Route::post('/webhooks/paymongo', [PaymentController::class, 'webhook']);
Route::post('/payments/create-source', [PaymentController::class, 'createPaymentSource']);

// ==================== LDRRMO ROUTES ====================
Route::prefix('ldrrmo')->group(function () {
    Route::get('/overview', [CityDashboardController::class, 'getCityOverview']);
    Route::get('/barangays-comparison', [CityDashboardController::class, 'getBarangaysComparison']);
    Route::get('/analytics', [CityDashboardController::class, 'getAnalyticsData']);
    Route::get('/barangays-map', [CityDashboardController::class, 'getBarangaysMapData']); // FIXED
    Route::get('/fundraisers', [CityDashboardController::class, 'getActiveFundraisers']); // FIXED
    Route::get('/resource-needs', [CityDashboardController::class, 'getResourceNeeds']);
    Route::post('/find-matches/{needId}', [CityDashboardController::class, 'findMatches']);
    Route::get('/barangay-contact/{barangayId}', [CityDashboardController::class, 'getBarangayContact']);
});

// ==================== BDRRMC ROUTES ====================
Route::prefix('bdrrmc')->group(function () {
    // Resource Needs
    Route::get('/needs', [BarangayDashboardController::class, 'getNeeds']);
    Route::post('/needs', [BarangayDashboardController::class, 'createNeed']);
    Route::patch('/needs/{id}', [BarangayDashboardController::class, 'updateNeed']);
    Route::delete('/needs/{id}', [BarangayDashboardController::class, 'deleteNeed']);

    // Physical Donations
    Route::get('/physical-donations', [BarangayDashboardController::class, 'getPhysicalDonations']);
    Route::post('/physical-donations', [BarangayDashboardController::class, 'recordDonation']);
    Route::get('/physical-donations/{id}', [BarangayDashboardController::class, 'getDonationDetails']);
    Route::post('/physical-donations/{id}/distribute', [BarangayDashboardController::class, 'recordDistribution']);

    // Online Donations (Read-only for BDRRMC)
    Route::get('/online-donations', [BarangayDashboardController::class, 'getOnlineDonations']);

    // Statistics
    Route::get('/statistics', [BarangayDashboardController::class, 'getUpdatedStats']);

    // Barangay Info
    Route::get('/my-barangay', [BarangayDashboardController::class, 'getBarangayInfo']);
    Route::patch('/my-barangay', [BarangayDashboardController::class, 'updateBarangayInfo']);
});

    // ==================== DONATION VERIFICATION ROUTES (BDRRMC/LDRRMO) ====================
    Route::get('/donations/pending/{barangayId?}', [DonationController::class, 'getPendingVerifications']);
    Route::post('/donations/{id}/verify', [DonationController::class, 'verify']);

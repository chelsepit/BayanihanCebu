<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\BarangayMapController;
use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\AdminDashboardController;
use App\Http\Controllers\CityDashboardController;
use App\Http\Controllers\BarangayDashboardController;
use App\Http\Controllers\ResidentDashboardController;
use App\Http\Controllers\PublicMapController;
use App\Http\Controllers\DonationController;
use App\Http\Controllers\NotificationController;
use App\Http\Controllers\PaymentController;

// ==================== PUBLIC ROUTES ====================

// Home page - Public Map
Route::get('/', [PublicMapController::class, 'index'])->name('home');

// Authentication routes
Route::get('/login', [AuthController::class, 'showLogin'])->name('login');
Route::get('/register', [AuthController::class, 'showRegister'])->name('register');
Route::post('/register', [AuthController::class, 'register'])->name('register.post');
Route::post('/login', [LoginController::class, 'login'])->name('login.post');
Route::post('/logout', [LoginController::class, 'logout'])->name('logout');

// Password Reset routes
Route::get('/forgot-password', [AuthController::class, 'showForgotPassword'])->name('password.request');
Route::post('/forgot-password', [AuthController::class, 'sendResetLink'])->name('password.email');
Route::get('/reset-password/{token}', [AuthController::class, 'showResetPassword'])->name('password.reset');
Route::post('/reset-password', [AuthController::class, 'resetPassword'])->name('password.update');

// Public map and donation tracking
Route::get('/barangay/map', [BarangayMapController::class, 'index'])->name('barangay.map');
Route::get('/api/barangay-map-data', [BarangayMapController::class, 'getMapData']);
Route::get('/api/barangay/{barangayId}', [BarangayMapController::class, 'getBarangayDetails']);

// Donation tracking page
Route::get('/donation/track', function () {
    return view('donations.track');
})->name('donation.track.page');

// Track donation (POST)
Route::post('/donation/track', [PublicMapController::class, 'trackDonation'])->name('donation.track');

// Donation Routes
Route::get('/donate/{barangay:barangay_id}', [PublicMapController::class, 'showDonateForm'])->name('barangay.donate');
Route::post('/donate/{barangay:barangay_id}', [PublicMapController::class, 'processDonation'])->name('donation.process');
Route::get('/donation/success/{trackingCode}', [PublicMapController::class, 'donationSuccess'])->name('donation.success');


// Statistics API
Route::get('/api/statistics', [PublicMapController::class, 'statistics'])->name('api.statistics');
Route::get('/api/barangays', [PublicMapController::class, 'apiBarangays'])->name('api.barangays');

// ==================== PROTECTED ROUTES ====================

// Get current user info (all authenticated users)
Route::middleware(['auth.check'])->group(function () {
    Route::get('/api/user', [LoginController::class, 'getCurrentUser']);
});

// ==================== LDRRMO ROUTES ====================
Route::middleware(['auth.check', 'role:ldrrmo'])->group(function () {
    Route::get('/city/dashboard', [CityDashboardController::class, 'index'])->name('city.dashboard');
    Route::get('/api/ldrrmo/overview', [CityDashboardController::class, 'getCityOverview']);
    Route::get('/api/ldrrmo/barangays-map', [CityDashboardController::class, 'getBarangaysMapData']);
    Route::get('/api/ldrrmo/analytics', [CityDashboardController::class, 'getAnalyticsData']);
    Route::get('/api/ldrrmo/barangays-comparison', [CityDashboardController::class, 'getBarangaysComparison']);
    Route::get('/api/ldrrmo/barangays/{barangayId}', [CityDashboardController::class, 'getBarangayDetails']);
    Route::patch('/api/ldrrmo/barangays/{barangayId}/status', [CityDashboardController::class, 'updateBarangayStatus']);
    Route::get('/api/ldrrmo/recent-activity', [CityDashboardController::class, 'getRecentActivity']);

    Route::get('/api/ldrrmo/resource-needs', [CityDashboardController::class, 'getResourceNeeds']);
    Route::post('/api/ldrrmo/find-matches/{needId}', [CityDashboardController::class, 'findMatches']);
    Route::get('/api/ldrrmo/barangay-contact/{barangayId}', [CityDashboardController::class, 'getBarangayContact']);

    Route::middleware(['auth.check', 'role:ldrrmo'])->group(function () {

    Route::post('/api/ldrrmo/resource-needs/{needId}/verify', [CityDashboardController::class, 'verifyResourceNeed']);
    Route::post('/api/ldrrmo/resource-needs/{needId}/revert', [CityDashboardController::class, 'revertVerification']);
    });
    Route::middleware(['auth.check', 'role:ldrrmo'])->group(function () {
    Route::post('/api/ldrrmo/matches/initiate', [CityDashboardController::class, 'initiateMatch']);
    Route::get('/api/ldrrmo/matches', [CityDashboardController::class, 'getMyInitiatedMatches']);
    Route::post('/api/ldrrmo/matches/{id}/cancel', [CityDashboardController::class, 'cancelMatch']);
    Route::get('/api/ldrrmo/matches/statistics', [CityDashboardController::class, 'getMatchStatistics']);
    });
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

    // ==================== BDRRMC MATCHING ROUTES ====================
Route::middleware(['auth.check', 'role:bdrrmc'])->group(function () {
    // ... existing BDRRMC routes ...

    // Incoming Match Requests (Donor Side)
    Route::get('/api/bdrrmc/matches/incoming', [BarangayDashboardController::class, 'getIncomingMatches']);
    Route::post('/api/bdrrmc/matches/{id}/respond', [BarangayDashboardController::class, 'respondToMatch']);

    // My Match Requests (Requester Side)
    Route::get('/api/bdrrmc/matches/my-requests', [BarangayDashboardController::class, 'getMyRequests']);

    // Active Matches (Both Sides)
    Route::get('/api/bdrrmc/matches/active', [BarangayDashboardController::class, 'getActiveMatches']);

    // Conversation & Messaging
    Route::get('/api/bdrrmc/matches/{id}/conversation', [BarangayDashboardController::class, 'getMatchConversation']);
    Route::post('/api/bdrrmc/matches/{id}/messages', [BarangayDashboardController::class, 'sendMessage']);
    Route::post('/api/bdrrmc/matches/{id}/messages/mark-read', [BarangayDashboardController::class, 'markMessagesAsRead']);

    // Complete Match
    Route::post('/api/bdrrmc/matches/{id}/complete', [BarangayDashboardController::class, 'completeMatch']);
});
});

// ==================== RESIDENT ROUTES ====================
Route::middleware(['auth.check', 'role:resident'])->group(function () {
    Route::get('/resident/dashboard', [ResidentDashboardController::class, 'index'])->name('resident.dashboard');
});

// ==================== ADMIN ROUTES ====================
Route::middleware(['auth.check', 'role:admin'])->group(function () {
    Route::get('/admin/dashboard', [AdminDashboardController::class, 'index'])->name('admin.dashboard');
});


// ==================== DONATION ROUTES FOR LISK ==================== //
Route::post('/api/donations', [DonationController::class, 'store']);
Route::get('/api/donations', [DonationController::class, 'index']);
Route::get('/api/donations/barangay/{id}', [DonationController::class, 'getByBarangay']);


// Public routes (no login required)
Route::post('/api/donations/track', [DonationController::class, 'track']);
Route::get('/api/donations/resource-needs', [DonationController::class, 'getResourceNeeds']);
Route::middleware(['auth.check'])->group(function () {

    // Resident routes
    Route::middleware(['role:resident'])->group(function () {
        Route::post('/api/donations', [DonationController::class, 'store']);
        Route::get('/api/donations/my-donations', [DonationController::class, 'myDonations']);
        Route::get('/api/donations/my-stats', [DonationController::class, 'getResidentStats']);
    });

    // BDRRMC routes
    Route::middleware(['role:bdrrmc'])->group(function () {
        Route::get('/api/donations/barangay/{id}', [DonationController::class, 'getByBarangay']);
        Route::get('/api/donations/pending-verifications', [DonationController::class, 'getPendingVerifications']);
        Route::post('/api/donations/{id}/verify', [DonationController::class, 'verify']);
    });

    // LDRRMO/Admin routes
    Route::middleware(['role:ldrrmo,admin'])->group(function () {
        Route::get('/api/donations', [DonationController::class, 'index']);
        Route::get('/api/donations/all-pending-verifications', [DonationController::class, 'getPendingVerifications']);
        Route::post('/api/donations/{id}/verify', [DonationController::class, 'verify']);
    });
    // ==================== NOTIFICATION ROUTES (BOTH LDRRMO & BDRRMC) ====================
    Route::middleware(['auth.check'])->group(function () {
    // Notifications
    Route::get('/api/notifications', [NotificationController::class, 'getNotifications']);
    Route::get('/api/notifications/unread-count', [NotificationController::class, 'getUnreadCount']);
    Route::post('/api/notifications/{id}/read', [NotificationController::class, 'markAsRead']);
    Route::post('/api/notifications/mark-all-read', [NotificationController::class, 'markAllAsRead']);
    Route::delete('/api/notifications/{id}', [NotificationController::class, 'deleteNotification']);
    Route::get('/api/notifications/grouped', [NotificationController::class, 'getGroupedNotifications']);
    });

    Route::middleware(['auth.check', 'role:ldrrmo'])->group(function () {

    Route::post('/api/ldrrmo/resource-needs/{needId}/verify', [CityDashboardController::class, 'verifyResourceNeed']);
    Route::post('/api/ldrrmo/resource-needs/{needId}/revert', [CityDashboardController::class, 'revertVerification']);
    }); 
    Route::middleware(['auth.check', 'role:ldrrmo'])->group(function () {
    Route::post('/api/ldrrmo/matches/initiate', [CityDashboardController::class, 'initiateMatch']);
    Route::get('/api/ldrrmo/matches', [CityDashboardController::class, 'getMyInitiatedMatches']);
    Route::post('/api/ldrrmo/matches/{id}/cancel', [CityDashboardController::class, 'cancelMatch']);
    Route::get('/api/ldrrmo/matches/statistics', [CityDashboardController::class, 'getMatchStatistics']);
    Route::get('/api/ldrrmo/match-details/{needId}/{donationId}', [CityDashboardController::class, 'getMatchDetails']);

    // Conversation & Messaging (LDRRMO as Monitor)
    Route::get('/api/ldrrmo/matches/{id}/conversation', [CityDashboardController::class, 'getMatchConversation']);
    Route::post('/api/ldrrmo/matches/{id}/messages', [CityDashboardController::class, 'sendMessage']);
    });

    });
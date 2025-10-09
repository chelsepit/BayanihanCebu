<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;

Route::get('/login', [AuthController::class, 'showLogin'])->name('login');
Route::get('/register', [AuthController::class, 'showRegister'])->name('register');
Route::post('/register', [AuthController::class, 'register'])->name('register.post');
Route::post('/login', [AuthController::class, 'login'])->name('login.post');

Route::get('/', function () {
    return view('welcome'); // or 'home' if that’s your file name
})->name('home');





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


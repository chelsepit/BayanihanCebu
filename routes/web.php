<?php

use Illuminate\Support\Facades\Route;

// Home page
Route::get('/', function () {
    return view('welcome'); // or 'home' if that’s your file name
})->name('home');

// Login page
Route::get('/login', function () {
    return view('auth.login'); // create this later
})->name('login');

Route::get('/register', function () {
    return view('auth.register'); // create this later
})->name('register');

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


<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Barangay;
use Illuminate\Support\Facades\Hash;
use App\Models\User;
use Illuminate\Support\Str;


class AuthController extends Controller
{
    // Show login form
    public function showLogin()
    {
        return view('auth.login');
    }

    // Show registration form
    public function showRegister()
    {
        $barangays = Barangay::orderBy('name')->get();
        return view('auth.register', compact('barangays'));
    }

public function register(Request $request)
{
    $validated = $request->validate([
        'full_name' => 'required|string|max:100',
        'email' => 'required|email|unique:users,email',
        'password' => 'required|min:6|confirmed',
        'barangay_id' => 'required|exists:barangays,barangay_id',
    ]);

    \App\Models\User::create([
        'user_id' => 'U' . str_pad(rand(1, 9999), 4, '0', STR_PAD_LEFT),
        'full_name' => $validated['full_name'],
        'email' => $validated['email'],
        'password_hash' => bcrypt($validated['password']),
        'role' => 'resident', // default role
        'barangay_id' => $validated['barangay_id'],
    ]);

    return redirect()->route('login')->with('success', 'Account created successfully! You can now log in.');
}

    public function login(Request $request)
    {
        // login logic later
    }
}

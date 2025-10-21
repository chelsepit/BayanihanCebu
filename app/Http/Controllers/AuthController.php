<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Barangay;
use App\Models\User;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
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

    // Handle registration
    public function register(Request $request)
    {
        $validated = $request->validate([
            'full_name' => 'required|string|max:100',
            'email' => 'required|email|unique:users,email',
            'password' => 'required|min:6|confirmed',
            'barangay_id' => 'required|exists:barangays,barangay_id',
        ]);

        User::create([
            'user_id' => 'U' . str_pad(rand(1, 9999), 4, '0', STR_PAD_LEFT),
            'full_name' => $validated['full_name'],
            'email' => $validated['email'],
            'password_hash' => bcrypt($validated['password']),
            'role' => 'resident', // default role
            'barangay_id' => $validated['barangay_id'],
        ]);

        return redirect()->route('login')->with('success', 'Account created successfully! You can now log in.');
    }

    // Show forgot password form
    public function showForgotPassword()
    {
        return view('auth.forgot-password');
    }

    // Handle forgot password email
    public function sendResetLink(Request $request)
    {
        $request->validate([
            'email' => 'required|email|exists:users,email',
        ]);

        // Delete old reset tokens for this email
        DB::table('password_resets')->where('email', $request->email)->delete();

        // Create new reset token
        $token = Str::random(60);

        DB::table('password_resets')->insert([
            'email' => $request->email,
            'token' => Hash::make($token),
            'created_at' => now(),
        ]);

        // In a real application, you would send this via email
        // For now, we'll just display it
        return back()->with('success', 'Password reset link has been sent! Check your email. (Token: ' . $token . ')');
    }

    // Show reset password form
    public function showResetPassword($token)
    {
        return view('auth.reset-password', [
            'token' => $token,
            'email' => request('email')
        ]);
    }

    // Handle password reset
    public function resetPassword(Request $request)
    {
        $request->validate([
            'token' => 'required',
            'email' => 'required|email|exists:users,email',
            'password' => 'required|min:6|confirmed',
        ]);

        // Find the password reset record
        $resetRecord = DB::table('password_resets')
            ->where('email', $request->email)
            ->first();

        if (!$resetRecord) {
            return back()->withErrors(['email' => 'Invalid reset token.']);
        }

        // Verify the token
        if (!Hash::check($request->token, $resetRecord->token)) {
            return back()->withErrors(['email' => 'Invalid reset token.']);
        }

        // Check if token is expired (valid for 1 hour)
        if (now()->diffInMinutes($resetRecord->created_at) > 60) {
            DB::table('password_resets')->where('email', $request->email)->delete();
            return back()->withErrors(['email' => 'Reset token has expired. Please request a new one.']);
        }

        // Update the user's password
        DB::table('users')
            ->where('email', $request->email)
            ->update([
                'password_hash' => Hash::make($request->password),
                'updated_at' => now()
            ]);

        // Delete the reset token
        DB::table('password_resets')->where('email', $request->email)->delete();

        return redirect()->route('login')->with('success', 'Password reset successfully! You can now log in with your new password.');
    }
}
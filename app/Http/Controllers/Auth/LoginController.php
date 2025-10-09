<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class LoginController extends Controller
{
    /**
     * Show the login form
     */
    public function showLoginForm()
    {
        return view('auth.login');
    }

    /**
     * Handle login request
     */
    public function login(Request $request)
    {
        $request->validate([
            'email' => 'required|email',
            'password' => 'required',
        ]);

        // Find user by email
        $user = DB::table('users')
            ->where('email', $request->email)
            ->first();

        // Check if user exists and password is correct
        if (!$user || !Hash::check($request->password, $user->password_hash)) {
            return back()->withErrors([
                'email' => 'The provided credentials do not match our records.',
            ])->withInput($request->only('email'));
        }

        // Generate remember token (session token)
        $rememberToken = Str::random(60);

        // Update user's remember token in database
        DB::table('users')
            ->where('user_id', $user->user_id)
            ->update([
                'remember_token' => $rememberToken,
                'updated_at' => now()
            ]);

        // Store user information in session
        session([
            'user_id' => $user->user_id,
            'user_role' => $user->role,
            'user_name' => $user->full_name,
            'user_email' => $user->email,
            'barangay_id' => $user->barangay_id,
            'remember_token' => $rememberToken,
            'authenticated' => true
        ]);

        // Regenerate session to prevent fixation attacks
        $request->session()->regenerate();

        // Redirect based on user role
        return $this->redirectToDashboard($user->role);
    }

    /**
     * Redirect to appropriate dashboard based on role
     */
    protected function redirectToDashboard($role)
    {
        switch ($role) {
            case 'admin':
                return redirect()->intended('/admin/dashboard');
            case 'ldrrmo':
                return redirect()->intended('/city/dashboard');
            case 'bdrrmc':
                return redirect()->intended('/barangay/dashboard');
            case 'resident':
                return redirect()->intended('/resident/dashboard');
            default:
                return redirect('/');
        }
    }

    /**
     * Handle logout
     */
    public function logout(Request $request)
    {
        // Get user_id before clearing session
        $userId = session('user_id');

        // Clear remember token from database
        if ($userId) {
            DB::table('users')
                ->where('user_id', $userId)
                ->update([
                    'remember_token' => null,
                    'updated_at' => now()
                ]);
        }

        // Clear all session data
        $request->session()->flush();
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect('/')->with('message', 'You have been logged out successfully.');
    }

    /**
     * Get current authenticated user
     */
    public function getCurrentUser()
    {
        if (!session('authenticated')) {
            return null;
        }

        $user = DB::table('users')
            ->where('user_id', session('user_id'))
            ->where('remember_token', session('remember_token'))
            ->first();

        return $user;
    }
}
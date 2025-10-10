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
    // 1️⃣ Validate input fields
    $request->validate([
        'email' => 'required|email',
        'password' => 'required',
    ]);

    try {
        // 2️⃣ Find user by email
        $user = DB::table('users')->where('email', $request->email)->first();

        if (!$user) {
            return back()->withErrors([
                'email' => 'No account found with that email.',
            ])->withInput();
        }

        // 3️⃣ Check password
        if (!Hash::check($request->password, $user->password_hash)) {
            return back()->withErrors([
                'password' => 'Incorrect password.',
            ])->withInput();
        }

        // 4️⃣ Check if user is active (optional but recommended)
        if (isset($user->status) && $user->status !== 'active') {
            return back()->withErrors([
                'email' => 'Your account is inactive. Contact the administrator.',
            ])->withInput();
        }

        // 5️⃣ Generate token and store session
        $rememberToken = Str::random(60);

        DB::table('users')
            ->where('user_id', $user->user_id)
            ->update([
                'remember_token' => $rememberToken,
                'updated_at' => now(),
            ]);

        session([
            'user_id'       => $user->user_id,
            'role'          => $user->role,   // ✅ fixed name
            'user_name'     => $user->full_name,
            'user_email'    => $user->email,
            'barangay_id'   => $user->barangay_id,
            'remember_token'=> $rememberToken,
            'authenticated' => true,
        ]);

        $request->session()->regenerate();

        // 6️⃣ Redirect by role
        return $this->redirectToDashboard($user->role);

    } catch (\Exception $e) {
        // 7️⃣ Catch unexpected errors (DB down, session issues, etc.)
        return back()->withErrors([
            'email' => 'An unexpected error occurred. Please try again later.',
        ])->withInput();
    }
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
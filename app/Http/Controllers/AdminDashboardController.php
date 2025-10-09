<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class AdminDashboardController extends Controller
{
    public function index()
    {
        return view('UserDashboards.admindashboard');
    }
   /* public function index()
    {
        $userId = session('user_id');

        // Get user details
        $user = DB::table('users')
            ->where('user_id', $userId)
            ->first();

        // Get overall system statistics
        $systemStats = [
            'total_users' => DB::table('users')->count(),
            'total_admins' => DB::table('users')->where('role', 'admin')->count(),
            'total_ldrrmo' => DB::table('users')->where('role', 'ldrrmo')->count(),
            'total_bdrrmc' => DB::table('users')->where('role', 'bdrrmc')->count(),
            'total_residents' => DB::table('users')->where('role', 'resident')->count(),
            'total_barangays' => DB::table('barangays')->count(),
            'total_donations' => DB::table('donations')->count(),
            'total_amount' => DB::table('donations')->sum('amount'),
        ];

        // Get recent users
        $recentUsers = DB::table('users')
            ->orderBy('created_at', 'desc')
            ->limit(10)
            ->get();

        // Get all barangays
        $barangays = DB::table('barangays')->get();

        return view('UserDashboards.admindashboard', [
            'user' => $user,
            'systemStats' => $systemStats,
            'recentUsers' => $recentUsers,
            'barangays' => $barangays
        ]);
    }*/
}

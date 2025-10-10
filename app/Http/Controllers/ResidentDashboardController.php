<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

/**
 * Resident Dashboard Controller
 */
class ResidentDashboardController extends Controller
{
  /*  public function index()
    {
        $userId = session('user_id');
        $barangayId = session('barangay_id');

        // Get user details
        $user = DB::table('users')
            ->where('user_id', $userId)
            ->first();

        // Get barangay info
        $barangay = DB::table('barangays')
            ->where('barangay_id', $barangayId)
            ->first();

        // Get user's donation history
        $donations = DB::table('donations')
            ->where('donor_id', $userId)
            ->orderBy('created_at', 'desc')
            ->limit(10)
            ->get();

        // Get barangay statistics
        $barangayStats = DB::table('donations')
            ->where('barangay_id', $barangayId)
            ->select(
                DB::raw('COUNT(*) as total_donations'),
                DB::raw('SUM(amount) as total_amount')
            )
            ->first();

        return view('UserDashboards.residentdashboard', [
            'user' => $user,
            'barangay' => $barangay,
            'donations' => $donations,
            'barangayStats' => $barangayStats
        ]);
    }
}
    */

/**
 * BDRRMC (Barangay) Dashboard Controller
 */
    public function index()
    {
        return view('UserDashboards.residentdashboard');
    }
}
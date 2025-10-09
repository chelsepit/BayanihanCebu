<?php

namespace App\Http\Controllers;


use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class BarangayDashboardController extends Controller
{
    public function index()
    {
        return view('UserDashboards.barangaydashboard');
    }
   /* public function index()
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

        // Get donations for this barangay
        $donations = DB::table('donations')
            ->where('barangay_id', $barangayId)
            ->orderBy('created_at', 'desc')
            ->limit(20)
            ->get();

        // Get distribution statistics
        $stats = DB::table('donations')
            ->where('barangay_id', $barangayId)
            ->select(
                DB::raw('COUNT(*) as total_donations'),
                DB::raw('SUM(amount) as total_amount'),
                DB::raw('SUM(families_served) as total_families'),
                DB::raw('COUNT(CASE WHEN distribution_status = "active" THEN 1 END) as active_distributions'),
                DB::raw('COUNT(CASE WHEN distribution_status = "pending" THEN 1 END) as pending_distributions'),
                DB::raw('COUNT(CASE WHEN distribution_status = "completed" THEN 1 END) as completed_distributions')
            )
            ->first();

        // Get recent beneficiaries
        $recentBeneficiaries = DB::table('beneficiaries')
            ->where('barangay_id', $barangayId)
            ->orderBy('created_at', 'desc')
            ->limit(10)
            ->get();

        return view('UserDashboards.barangaydashboard', [
            'user' => $user,
            'barangay' => $barangay,
            'donations' => $donations,
            'stats' => $stats,
            'recentBeneficiaries' => $recentBeneficiaries
        ]);
    }*/
}

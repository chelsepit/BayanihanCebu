<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

/**
 * Resident Dashboard Controller
 */
class ResidentDashboardController extends Controller
{
    public function index()
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

/**
 * BDRRMC (Barangay) Dashboard Controller
 */
class BarangayDashboardController extends Controller
{
    public function index()
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
    }
}

/**
 * LDRRMO (City) Dashboard Controller
 */
class CityDashboardController extends Controller
{
    public function index()
    {
        $userId = session('user_id');

        // Get user details
        $user = DB::table('users')
            ->where('user_id', $userId)
            ->first();

        // Get all barangays with their statistics
        $barangays = DB::table('barangays')
            ->leftJoin('donations', 'barangays.barangay_id', '=', 'donations.barangay_id')
            ->select(
                'barangays.*',
                DB::raw('COUNT(donations.donation_id) as donation_count'),
                DB::raw('COALESCE(SUM(donations.amount), 0) as total_amount'),
                DB::raw('COALESCE(SUM(donations.families_served), 0) as families_served')
            )
            ->groupBy(
                'barangays.barangay_id',
                'barangays.barangay_id',
                'barangays.name',
                'barangays.city',
                'barangays.latitude',
                'barangays.longitude',
                'barangays.disaster_status',
                'barangays.needs_summary',
                'barangays.blockchain_address',
                'barangays.created_at',
                'barangays.updated_at'
            )
            ->get();

        // Get overall city statistics
        $cityStats = DB::table('donations')
            ->select(
                DB::raw('COUNT(*) as total_donations'),
                DB::raw('SUM(amount) as total_amount'),
                DB::raw('SUM(families_served) as total_families'),
                DB::raw('COUNT(DISTINCT barangay_id) as barangays_served')
            )
            ->first();

        // Get recent donations across all barangays
        $recentDonations = DB::table('donations')
            ->join('barangays', 'donations.barangay_id', '=', 'barangays.barangay_id')
            ->select('donations.*', 'barangays.name as barangay_name')
            ->orderBy('donations.created_at', 'desc')
            ->limit(15)
            ->get();

        // Get barangays needing attention (disaster status)
        $urgentBarangays = DB::table('barangays')
            ->where('disaster_status', '!=', 'safe')
            ->get();

        return view('UserDashboards.citydashboard', [
            'user' => $user,
            'barangays' => $barangays,
            'cityStats' => $cityStats,
            'recentDonations' => $recentDonations,
            'urgentBarangays' => $urgentBarangays
        ]);
    }
}

/**
 * Admin Dashboard Controller
 */
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
    } */
}
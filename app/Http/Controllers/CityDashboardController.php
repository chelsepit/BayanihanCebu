<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class CityDashboardController extends Controller
{

    public function index()
    {
        return view('UserDashboards.citydashboard');
    }
    /* public function index()
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
    } */
}

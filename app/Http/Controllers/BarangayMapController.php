<?php
// app/Http/Controllers/BarangayMapController.php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class BarangayMapController extends Controller
{
    public function index()
    {
        return view('barangay.map');
    }

    public function getMapData()
    {
        // Get all barangays with their donation statistics
        $barangays = DB::table('barangays')
            ->leftJoin('donations', 'barangays.barangay_id', '=', 'donations.barangay_id')
            ->select(
                'barangays.barangay_id',
                'barangays.name',
                'barangays.city',
                'barangays.latitude',
                'barangays.longitude',
                'barangays.disaster_status',
                DB::raw('COUNT(donations.donation_id) as donation_count'),
                DB::raw('COALESCE(SUM(donations.amount), 0) as total_amount')
            )
            ->groupBy(
                'barangays.barangay_id',
                'barangays.name',
                'barangays.city',
                'barangays.latitude',
                'barangays.longitude',
                'barangays.disaster_status'
            )
            ->get()
            ->map(function ($barangay) {
                // Determine status based on donations
                if ($barangay->donation_count > 0) {
                    // Check if there are active distributions
                    $hasActive = DB::table('donations')
                        ->where('barangay_id', $barangay->barangay_id)
                        ->where('distribution_status', 'active')
                        ->exists();
                    
                    $hasPending = DB::table('donations')
                        ->where('barangay_id', $barangay->barangay_id)
                        ->where('distribution_status', 'pending')
                        ->exists();
                    
                    if ($hasActive) {
                        $status = 'active';
                    } elseif ($hasPending) {
                        $status = 'pending';
                    } else {
                        $status = 'completed';
                    }
                } else {
                    $status = 'no_donations';
                }

                return [
                    'barangay_id' => $barangay->barangay_id,
                    'name' => $barangay->name,
                    'city' => $barangay->city,
                    'lat' => (float) $barangay->latitude,
                    'lng' => (float) $barangay->longitude,
                    'disaster_status' => $barangay->disaster_status,
                    'status' => $status,
                    'donations' => (int) $barangay->donation_count,
                    'total_amount' => (float) $barangay->total_amount,
                ];
            });

        // Get recent activity (barangays with most recent donations)
        $recentActivity = DB::table('barangays')
            ->join('donations', 'barangays.barangay_id', '=', 'donations.barangay_id')
            ->select(
                'barangays.name',
                'barangays.barangay_id',
                DB::raw('COUNT(donations.donation_id) as donation_count'),
                DB::raw('MAX(donations.created_at) as latest_donation')
            )
            ->groupBy('barangays.name', 'barangays.barangay_id')
            ->orderBy('latest_donation', 'desc')
            ->limit(5)
            ->get();

        // Get overall statistics
        $stats = [
            'total_donations' => DB::table('donations')->count(),
            'total_barangays' => DB::table('barangays')->count(),
            'total_amount' => DB::table('donations')->sum('amount'),
            'families_served' => DB::table('donations')->sum('families_served') ?? 0,
        ];

        return response()->json([
            'barangays' => $barangays,
            'recent_activity' => $recentActivity,
            'stats' => $stats,
        ]);
    }

    public function getBarangayDetails($barangayId)
    {
        $barangay = DB::table('barangays')
            ->where('barangay_id', $barangayId)
            ->first();

        if (!$barangay) {
            return response()->json(['error' => 'Barangay not found'], 404);
        }

        // Get donations for this barangay
        $donations = DB::table('donations')
            ->where('barangay_id', $barangayId)
            ->orderBy('created_at', 'desc')
            ->limit(10)
            ->get();

        // Get donation statistics
        $stats = DB::table('donations')
            ->where('barangay_id', $barangayId)
            ->select(
                DB::raw('COUNT(*) as total_donations'),
                DB::raw('SUM(amount) as total_amount'),
                DB::raw('SUM(families_served) as total_families')
            )
            ->first();

        return response()->json([
            'barangay' => $barangay,
            'donations' => $donations,
            'stats' => $stats,
        ]);
    }
}
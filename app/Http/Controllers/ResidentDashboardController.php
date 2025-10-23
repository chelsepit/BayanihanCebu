<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Models\Barangay;
use App\Models\ResourceNeed;
use App\Models\Disaster;

/**
 * Resident Dashboard Controller
 */
class ResidentDashboardController extends Controller
{
    /**
     * Display Resident Dashboard
     */
    public function index()
    {
        // Get all barangays with their resource needs
        $barangays = Barangay::with(['resourceNeeds' => function($query) {
            $query->where('status', '!=', 'fulfilled');
        }])->get();

        // Calculate total donations for each barangay
        $barangays->each(function($barangay) {
            $barangay->total_raised = \App\Models\Donation::where('barangay_id', $barangay->barangay_id)
                ->where('status', '!=', 'pending')
                ->sum('amount');
        });

        return view('UserDashboards.residentdashboard', compact('barangays'));
    }

    /**
     * Get all urgent needs for residents to view and donate
     */
    public function getUrgentNeeds()
    {
        try {
            $needs = ResourceNeed::with(['barangay'])
                ->whereIn('status', ['pending', 'partially_fulfilled'])
                ->orderByRaw("FIELD(urgency, 'critical', 'high', 'medium', 'low')")
                ->orderBy('created_at', 'desc')
                ->get()
                ->map(function($need) {
                    return [
                        'id' => $need->id,
                        'barangay_id' => $need->barangay_id,
                        'barangay_name' => $need->barangay->name,
                        'category' => $need->category,
                        'description' => $need->description,
                        'quantity' => $need->quantity,
                        'urgency' => $need->urgency,
                        'status' => $need->status,
                        'affected_families' => $need->barangay->affected_families ?? 0,
                        'disaster_status' => $need->barangay->disaster_status ?? 'safe',
                        'created_at' => $need->created_at->format('Y-m-d H:i:s'),
                    ];
                });

            // Calculate statistics
            $activeNeedsCount = $needs->whereIn('status', ['pending', 'partially_fulfilled'])->count();
            $affectedBarangays = $needs->pluck('barangay_id')->unique()->count();

            return response()->json([
                'success' => true,
                'needs' => $needs,
                'statistics' => [
                    'active_needs' => $activeNeedsCount,
                    'affected_barangays' => $affectedBarangays,
                ]
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error loading needs',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Get barangays map data (for map view)
     */
  /**
 * Get barangays map data (for map view)
 */
public function getBarangaysMap()
{
    try {
        $barangays = Barangay::all()->map(function ($barangay) {
        
            $resourceNeeds = ResourceNeed::where('barangay_id', $barangay->barangay_id)
                ->whereIn('status', ['pending', 'partially_fulfilled'])
                ->pluck('category')
                ->unique()
                ->toArray();

            return [
                'barangay_id' => $barangay->barangay_id,
                'name' => $barangay->name,
                'lat' => $barangay->latitude ?? 10.3157,
                'lng' => $barangay->longitude ?? 123.8854,
                'status' => $barangay->disaster_status ?? 'safe',
                'affected_families' => $barangay->affected_families ?? 0,
                'resource_needs' => $resourceNeeds
            ];
        });

        return response()->json($barangays);

    } catch (\Exception $e) {
        return response()->json([
            'success' => false,
            'message' => 'Error loading barangays',
            'error' => $e->getMessage()
        ], 500);
    }
}

    /**
     * Get statistics for resident dashboard
     */
    public function getStatistics()
    {
        try {
            $activeNeeds = ResourceNeed::whereIn('status', ['pending', 'partially_fulfilled'])->count();

            $affectedBarangays = Barangay::where('disaster_status', '!=', 'safe')->count();

            // If user is logged in, get their donation stats
            $userEmail = session('user_email');
            $userImpact = 0;

            if ($userEmail) {
                $userImpact = \App\Models\Donation::where('donor_email', $userEmail)
                    ->where('verification_status', 'verified')
                    ->sum('amount');
            }

            return response()->json([
                'success' => true,
                'active_needs' => $activeNeeds,
                'affected_barangays' => $affectedBarangays,
                'user_impact' => $userImpact,
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error loading statistics',
                'error' => $e->getMessage()
            ], 500);
        }
    }
}

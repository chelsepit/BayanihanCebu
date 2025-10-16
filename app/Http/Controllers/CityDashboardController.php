<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Models\Barangay;
use App\Models\Donation;
use App\Models\PhysicalDonation;
use App\Models\ResourceNeed;

class CityDashboardController extends Controller
{
    /**
     * Display LDRRMO Dashboard
     */
    public function index()
    {
        $userId = session('user_id');

        $user = DB::table('users')
            ->where('user_id', $userId)
            ->first();

        return view('UserDashboards.citydashboard', [
            'user' => $user
        ]);
    }

    /**
     * Get city-wide statistics
     */
    public function getCityOverview()
    {
        $onlineDonations = Donation::whereIn('status', ['confirmed', 'distributed', 'completed'])
            ->sum('amount');

        $physicalDonations = PhysicalDonation::whereNotNull('estimated_value')
            ->sum('estimated_value');

        $totalAffectedFamilies = Barangay::where('disaster_status', '!=', 'safe')
            ->sum('affected_families');

        $affectedBarangays = Barangay::where('disaster_status', '!=', 'safe')->count();

        $activeFundraisers = Barangay::where('disaster_status', '!=', 'safe')->count();

        $criticalBarangays = Barangay::whereIn('disaster_status', ['critical', 'emergency'])->count();

        $totalDonors = Donation::whereIn('status', ['confirmed', 'distributed', 'completed'])
            ->distinct('donor_email')
            ->count();

        return response()->json([
            'total_donations' => $onlineDonations + $physicalDonations,
            'online_donations' => $onlineDonations,
            'physical_donations' => $physicalDonations,
            'total_affected_families' => $totalAffectedFamilies,
            'affected_barangays' => $affectedBarangays,
            'total_barangays' => Barangay::count(),
            'active_fundraisers' => $activeFundraisers,
            'critical_barangays' => $criticalBarangays,
            'total_donors' => $totalDonors,
        ]);
    }

    /**
     * Get all barangays for map - UPDATED TO USE RESOURCE_NEEDS
     */
    public function getBarangaysMapData()
    {
        $barangays = Barangay::all()->map(function ($barangay) {
            // Get resource needs for this barangay
            $resourceNeeds = ResourceNeed::where('barangay_id', $barangay->barangay_id)
                ->where('status', '!=', 'fulfilled')
                ->pluck('category')
                ->unique()
                ->toArray();

            return [
                'barangay_id' => $barangay->barangay_id,
                'name' => $barangay->name,
                'city' => $barangay->city,
                'lat' => isset($barangay->latitude) ? (float) $barangay->latitude : 10.3157,
                'lng' => isset($barangay->longitude) ? (float) $barangay->longitude : 123.8854,
                'status' => $barangay->disaster_status,
                'disaster_type' => $barangay->disaster_type,
                'affected_families' => $barangay->affected_families,
                'needs_help' => $barangay->needsHelp(),
                'resource_needs' => $resourceNeeds
            ];
        });

        return response()->json($barangays);
    }

    /**
     * Get analytics data
     */
    public function getAnalyticsData()
    {
        $donationsByBarangay = DB::table('barangays')
            ->leftJoin('disasters', function($join) {
                $join->on('barangays.barangay_id', '=', 'disasters.barangay_id')
                     ->where('disasters.is_active', '=', true);
            })
            ->leftJoin('donations', function($join) {
                $join->on('disasters.id', '=', 'donations.disaster_id')
                     ->whereIn('donations.status', ['confirmed', 'distributed', 'completed']);
            })
            ->leftJoin('physical_donations', 'barangays.barangay_id', '=', 'physical_donations.barangay_id')
            ->select(
                'barangays.name',
                DB::raw('COALESCE(SUM(donations.amount), 0) as online_donations'),
                DB::raw('COALESCE(SUM(physical_donations.estimated_value), 0) as physical_donations'),
                DB::raw('COALESCE(SUM(donations.amount), 0) + COALESCE(SUM(physical_donations.estimated_value), 0) as total_donations')
            )
            ->groupBy('barangays.barangay_id', 'barangays.name')
            ->orderBy('total_donations', 'desc')
            ->limit(10)
            ->get();

        $disasterStatusDistribution = Barangay::select('disaster_status', DB::raw('count(*) as count'))
            ->groupBy('disaster_status')
            ->get()
            ->pluck('count', 'disaster_status');

        $affectedFamiliesByBarangay = Barangay::select('name', 'affected_families')
            ->where('affected_families', '>', 0)
            ->orderBy('affected_families', 'desc')
            ->limit(10)
            ->get();

        return response()->json([
            'donations_by_barangay' => $donationsByBarangay,
            'disaster_status_distribution' => $disasterStatusDistribution,
            'affected_families_by_barangay' => $affectedFamiliesByBarangay
        ]);
    }

    /**
     * Get barangays comparison
     */
    public function getBarangaysComparison()
    {
        $barangays = Barangay::all()->map(function ($barangay) {
            $onlineDonations = Donation::where('barangay_id', $barangay->barangay_id)
                ->whereIn('status', ['confirmed', 'distributed', 'completed'])
                ->sum('amount');

            $physicalDonations = PhysicalDonation::where('barangay_id', $barangay->barangay_id)
                ->sum('estimated_value');

            $resourceNeeds = ResourceNeed::where('barangay_id', $barangay->barangay_id)
                ->whereIn('status', ['pending', 'partially_fulfilled'])
                ->pluck('category')
                ->unique()
                ->toArray();

            return [
                'barangay_id' => $barangay->barangay_id,
                'name' => $barangay->name,
                'status' => $barangay->disaster_status,
                'disaster_type' => $barangay->disaster_type,
                'affected_families' => $barangay->affected_families,
                'donations_received' => $onlineDonations + $physicalDonations,
                'online_donations' => $onlineDonations,
                'physical_donations' => $physicalDonations,
                'resource_needs' => $resourceNeeds,
                'needs_help' => $barangay->needsHelp()
            ];
        });

        return response()->json($barangays);
    }

    /**
     * Get active fundraisers - barangays that need help
     */
    public function getActiveFundraisers()
    {
        $fundraisers = Barangay::where('disaster_status', '!=', 'safe')
            ->with('resourceNeeds')
            ->get()
            ->map(function ($barangay) {
                $totalDonations = Donation::where('barangay_id', $barangay->barangay_id)
                    ->whereIn('status', ['confirmed', 'distributed', 'completed'])
                    ->sum('amount');

                $goal = $barangay->affected_families * 10000;
                $progress = $goal > 0 ? min(100, ($totalDonations / $goal) * 100) : 0;

                $donorsCount = Donation::where('barangay_id', $barangay->barangay_id)
                    ->whereIn('status', ['confirmed', 'distributed', 'completed'])
                    ->distinct('donor_email')
                    ->count();

                // Get resource needs for this barangay
                $resourceNeeds = ResourceNeed::where('barangay_id', $barangay->barangay_id)
                    ->whereIn('status', ['pending', 'partially_fulfilled'])
                    ->get()
                    ->map(function ($need) {
                        return [
                            'category' => $need->category,
                            'quantity' => $need->quantity,
                            'urgency' => $need->urgency,
                            'status' => $need->status,
                            'description' => $need->description
                        ];
                    });

                return [
                    'id' => $barangay->barangay_id,
                    'title' => $barangay->disaster_type
                        ? ucfirst($barangay->disaster_type) . ' Relief - ' . $barangay->name
                        : 'Emergency Relief - ' . $barangay->name,
                    'barangay' => $barangay->name,
                    'disaster_type' => $barangay->disaster_type,
                    'status' => $barangay->disaster_status,
                    'description' => $barangay->needs_summary,
                    'affected_families' => $barangay->affected_families,
                    'goal' => $goal,
                    'raised' => $totalDonations,
                    'progress' => round($progress, 2),
                    'donors_count' => $donorsCount,
                    'resource_needs' => $resourceNeeds,
                ];
            });

        return response()->json($fundraisers);
    }

    /**
     * Get detailed barangay information
     */
    public function getBarangayDetails($barangayId)
    {
        $barangay = Barangay::where('barangay_id', $barangayId)->firstOrFail();

        $recentDonations = Donation::where('barangay_id', $barangayId)
            ->whereIn('status', ['confirmed', 'distributed', 'completed'])
            ->orderBy('created_at', 'desc')
            ->limit(10)
            ->get();

        $physicalDonations = PhysicalDonation::where('barangay_id', $barangayId)
            ->orderBy('recorded_at', 'desc')
            ->limit(10)
            ->get();

        $resourceNeeds = ResourceNeed::where('barangay_id', $barangayId)
            ->orderBy('created_at', 'desc')
            ->get();

        $totalOnlineDonations = Donation::where('barangay_id', $barangayId)
            ->whereIn('status', ['confirmed', 'distributed', 'completed'])
            ->sum('amount');

        $totalPhysicalDonations = PhysicalDonation::where('barangay_id', $barangayId)
            ->sum('estimated_value');

        $totalDonors = Donation::where('barangay_id', $barangayId)
            ->whereIn('status', ['confirmed', 'distributed', 'completed'])
            ->distinct('donor_email')
            ->count();

        return response()->json([
            'barangay' => $barangay,
            'current_situation' => [
                'status' => $barangay->disaster_status,
                'disaster_type' => $barangay->disaster_type,
                'description' => $barangay->needs_summary,
                'affected_families' => $barangay->affected_families,
            ],
            'statistics' => [
                'total_donations' => $totalOnlineDonations + $totalPhysicalDonations,
                'online_donations' => $totalOnlineDonations,
                'physical_donations' => $totalPhysicalDonations,
                'total_donors' => $totalDonors,
                'affected_families' => $barangay->affected_families,
            ],
            'recent_online_donations' => $recentDonations,
            'recent_physical_donations' => $physicalDonations,
            'resource_needs' => $resourceNeeds
        ]);
    }

    /**
     * Update barangay disaster status
     */
    public function updateBarangayStatus(Request $request, $barangayId)
    {
        $validated = $request->validate([
            'disaster_status' => 'required|in:safe,warning,critical,emergency',
            'affected_families' => 'sometimes|integer|min:0',
            'needs_summary' => 'nullable|string|max:1000'
        ]);

        $barangay = Barangay::where('barangay_id', $barangayId)->firstOrFail();
        $barangay->update($validated);

        return response()->json([
            'success' => true,
            'message' => 'Barangay status updated successfully',
            'data' => $barangay
        ]);
    }

    /**
     * Get recent activity
     */
    public function getRecentActivity()
    {
        $activities = collect();

        $recentDonations = Donation::with('barangay')
            ->whereIn('status', ['confirmed', 'distributed', 'completed'])
            ->orderBy('created_at', 'desc')
            ->limit(10)
            ->get()
            ->map(function ($donation) {
                return [
                    'type' => 'donation',
                    'barangay' => $donation->barangay->name ?? 'Unknown',
                    'message' => '₱' . number_format($donation->amount, 2) . ' donation received',
                    'timestamp' => $donation->created_at,
                ];
            });

        $recentNeeds = ResourceNeed::with('barangay')
            ->orderBy('created_at', 'desc')
            ->limit(10)
            ->get()
            ->map(function ($need) {
                return [
                    'type' => 'resource_need',
                    'barangay' => $need->barangay->name ?? 'Unknown',
                    'message' => ucfirst($need->category) . ' needed - ' . $need->urgency . ' urgency',
                    'timestamp' => $need->created_at,
                ];
            });

        $activities = $recentDonations->merge($recentNeeds)
            ->sortByDesc('timestamp')
            ->take(15)
            ->values();

        return response()->json($activities);
    }
}
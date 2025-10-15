<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Models\Barangay;
use App\Models\Disaster;
use App\Models\Donation;
use App\Models\PhysicalDonation;
use App\Models\ResourceNeed;
use App\Models\UrgentNeed;

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

        $activeFundraisers = Disaster::where('is_active', true)->count();

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
            $disaster = Disaster::where('barangay_id', $barangay->barangay_id)
                ->where('is_active', true)
                ->first();

            // ⭐ PRIORITY: Get needs from resource_needs table (BDRRMC)
            $urgentNeeds = ResourceNeed::where('barangay_id', $barangay->barangay_id)
                ->where('status', '!=', 'fulfilled')
                ->pluck('category')
                ->unique()
                ->toArray();

            // Fallback: If no resource needs, check urgent needs from disasters
            if (empty($urgentNeeds) && $disaster) {
                $urgentNeeds = UrgentNeed::where('disaster_id', $disaster->id)
                    ->where('is_fulfilled', false)
                    ->pluck('type')
                    ->toArray();
            }
            
            return [
                'barangay_id' => $barangay->barangay_id,
                'name' => $barangay->name,
                'city' => $barangay->city,
                'lat' => isset($barangay->latitude) ? (float) $barangay->latitude : 10.3157,
                'lng' => isset($barangay->longitude) ? (float) $barangay->longitude : 123.8854,
                'status' => $barangay->disaster_status,
                'affected_families' => $barangay->affected_families,
                'has_disaster' => $disaster ? true : false,
                'disaster_type' => $disaster ? $disaster->type : null,
                'urgent_needs' => $urgentNeeds
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
     * Get barangays comparison - UPDATED TO USE RESOURCE_NEEDS
     */
    public function getBarangaysComparison()
    {
        $barangays = Barangay::all()->map(function ($barangay) {
            $disaster = Disaster::where('barangay_id', $barangay->barangay_id)
                ->where('is_active', true)
                ->first();

            $onlineDonations = 0;
            if ($disaster) {
                $onlineDonations = Donation::where('disaster_id', $disaster->id)
                    ->whereIn('status', ['confirmed', 'distributed', 'completed'])
                    ->sum('amount');
            }

            $physicalDonations = PhysicalDonation::where('barangay_id', $barangay->barangay_id)
                ->sum('estimated_value');

            // ⭐ PRIORITY: Get needs from resource_needs (BDRRMC posted needs)
            $urgentNeeds = ResourceNeed::where('barangay_id', $barangay->barangay_id)
                ->whereIn('status', ['pending', 'partially_fulfilled'])
                ->pluck('category')
                ->unique()
                ->toArray();

            // Fallback: If no resource needs, check disaster urgent needs
            if (empty($urgentNeeds) && $disaster) {
                $urgentNeeds = UrgentNeed::where('disaster_id', $disaster->id)
                    ->where('is_fulfilled', false)
                    ->pluck('type')
                    ->toArray();
            }

            return [
                'barangay_id' => $barangay->barangay_id,
                'name' => $barangay->name,
                'status' => $barangay->disaster_status,
                'affected_families' => $barangay->affected_families,
                'donations_received' => $onlineDonations + $physicalDonations,
                'online_donations' => $onlineDonations,
                'physical_donations' => $physicalDonations,
                'urgent_needs' => array_unique($urgentNeeds),
                'has_active_disaster' => $disaster ? true : false
            ];
        });

        return response()->json($barangays);
    }

    /**
     * Get active fundraisers - UPDATED TO SHOW RESOURCE_NEEDS
     */
    public function getActiveFundraisers()
    {
        $fundraisers = Disaster::with(['barangay'])
            ->where('is_active', true)
            ->get()
            ->map(function ($disaster) {
                $totalDonations = Donation::where('disaster_id', $disaster->id)
                    ->whereIn('status', ['confirmed', 'distributed', 'completed'])
                    ->sum('amount');

                $goal = $disaster->affected_families * 10000;
                $progress = $goal > 0 ? min(100, ($totalDonations / $goal) * 100) : 0;

                $donorsCount = Donation::where('disaster_id', $disaster->id)
                    ->whereIn('status', ['confirmed', 'distributed', 'completed'])
                    ->distinct('donor_email')
                    ->count();

                // ⭐ Get resource needs for this barangay (BDRRMC needs)
                $resourceNeeds = ResourceNeed::where('barangay_id', $disaster->barangay_id)
                    ->whereIn('status', ['pending', 'partially_fulfilled'])
                    ->get()
                    ->map(function ($need) {
                        return [
                            'type' => $need->category,
                            'quantity_needed' => $need->quantity,
                            'urgency' => $need->urgency,
                            'status' => $need->status,
                            'description' => $need->description
                        ];
                    });

                // If no resource needs, fallback to urgent needs
                if ($resourceNeeds->isEmpty()) {
                    $resourceNeeds = UrgentNeed::where('disaster_id', $disaster->id)
                        ->get()
                        ->map(function ($need) {
                            return [
                                'type' => $need->type,
                                'quantity_needed' => $need->quantity_needed,
                                'unit' => $need->unit,
                                'quantity_fulfilled' => $need->quantity_fulfilled,
                                'is_fulfilled' => $need->is_fulfilled
                            ];
                        });
                }

                return [
                    'id' => $disaster->id,
                    'title' => $disaster->title,
                    'barangay' => $disaster->barangay->name,
                    'type' => $disaster->type,
                    'severity' => $disaster->severity,
                    'description' => $disaster->description,
                    'affected_families' => $disaster->affected_families,
                    'goal' => $goal,
                    'raised' => $totalDonations,
                    'progress' => round($progress, 2),
                    'donors_count' => $donorsCount,
                    'urgent_needs' => $resourceNeeds,
                    'started_at' => $disaster->started_at->format('Y-m-d'),
                    'days_active' => $disaster->started_at->diffInDays(now())
                ];
            });

        return response()->json($fundraisers);
    }

    /**
     * Get detailed barangay information - UPDATED TO USE RESOURCE_NEEDS
     */
    public function getBarangayDetails($barangayId)
    {
        $barangay = Barangay::where('barangay_id', $barangayId)->firstOrFail();

        $disaster = Disaster::where('barangay_id', $barangayId)
            ->where('is_active', true)
            ->first();

        $recentDonations = [];
        if ($disaster) {
            $recentDonations = Donation::where('disaster_id', $disaster->id)
                ->whereIn('status', ['confirmed', 'distributed', 'completed'])
                ->orderBy('created_at', 'desc')
                ->limit(10)
                ->get();
        }

        $physicalDonations = PhysicalDonation::where('barangay_id', $barangayId)
            ->orderBy('recorded_at', 'desc')
            ->limit(10)
            ->get();

        // ⭐ Get resource needs (from BDRRMC)
        $resourceNeeds = ResourceNeed::where('barangay_id', $barangayId)
            ->orderBy('created_at', 'desc')
            ->get();

        $totalOnlineDonations = $disaster ? 
            Donation::where('disaster_id', $disaster->id)
                ->whereIn('status', ['confirmed', 'distributed', 'completed'])
                ->sum('amount') : 0;

        $totalPhysicalDonations = PhysicalDonation::where('barangay_id', $barangayId)
            ->sum('estimated_value');

        $totalDonors = $disaster ?
            Donation::where('disaster_id', $disaster->id)
                ->whereIn('status', ['confirmed', 'distributed', 'completed'])
                ->distinct('donor_email')
                ->count() : 0;

        return response()->json([
            'barangay' => [
                'barangay_id' => $barangay->barangay_id,
                'name' => $barangay->name,
                'city' => $barangay->city,
                'district' => $barangay->district,
                'status' => $barangay->disaster_status,
                'affected_families' => $barangay->affected_families,
                'latitude' => $barangay->latitude,
                'longitude' => $barangay->longitude,
                'contact_person' => $barangay->contact_person,
                'contact_phone' => $barangay->contact_phone,
                'contact_email' => $barangay->contact_email,
                'needs_summary' => $barangay->needs_summary,
            ],
            'disaster' => $disaster ? [
                'id' => $disaster->id,
                'title' => $disaster->title,
                'type' => $disaster->type,
                'severity' => $disaster->severity,
                'description' => $disaster->description,
                'started_at' => $disaster->started_at->format('Y-m-d H:i:s'),
            ] : null,
            'statistics' => [
                'total_donations' => $totalOnlineDonations + $totalPhysicalDonations,
                'online_donations' => $totalOnlineDonations,
                'physical_donations' => $totalPhysicalDonations,
                'total_donors' => $totalDonors,
                'affected_families' => $barangay->affected_families,
            ],
            'recent_online_donations' => $recentDonations,
            'recent_physical_donations' => $physicalDonations,
            'resource_needs' => $resourceNeeds // ⭐ From BDRRMC
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
     * Get recent activity - UPDATED TO SHOW RESOURCE_NEEDS
     */
    public function getRecentActivity()
    {
        $activities = collect();

        $recentDonations = Donation::with(['disaster.barangay'])
            ->whereIn('status', ['confirmed', 'distributed', 'completed'])
            ->orderBy('created_at', 'desc')
            ->limit(10)
            ->get()
            ->map(function ($donation) {
                return [
                    'type' => 'donation',
                    'barangay' => $donation->disaster->barangay->name ?? 'Unknown',
                    'amount' => $donation->amount,
                    'donor' => $donation->donor_display_name,
                    'timestamp' => $donation->created_at->diffForHumans(),
                    'created_at' => $donation->created_at
                ];
            });

        $recentPhysical = PhysicalDonation::with('barangay')
            ->orderBy('recorded_at', 'desc')
            ->limit(10)
            ->get()
            ->map(function ($donation) {
                return [
                    'type' => 'physical_donation',
                    'barangay' => $donation->barangay->name,
                    'donor' => $donation->donor_name,
                    'category' => $donation->category,
                    'quantity' => $donation->quantity,
                    'timestamp' => $donation->recorded_at->diffForHumans(),
                    'created_at' => $donation->recorded_at
                ];
            });

        // ⭐ Show resource needs in activity
        $recentNeeds = ResourceNeed::with('barangay')
            ->orderBy('created_at', 'desc')
            ->limit(10)
            ->get()
            ->map(function ($need) {
                return [
                    'type' => 'resource_need',
                    'barangay' => $need->barangay->name,
                    'category' => $need->category,
                    'urgency' => $need->urgency,
                    'quantity' => $need->quantity,
                    'status' => $need->status,
                    'timestamp' => $need->created_at->diffForHumans(),
                    'created_at' => $need->created_at
                ];
            });

        $activities = $recentDonations
            ->merge($recentPhysical)
            ->merge($recentNeeds)
            ->sortByDesc('created_at')
            ->take(20)
            ->values();

        return response()->json($activities);
    }
}
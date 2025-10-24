<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use App\Models\Barangay;
use App\Models\Donation;
use App\Models\PhysicalDonation;
use App\Models\ResourceNeed;
use App\Models\ResourceMatch;
use App\Models\MatchNotification;
use App\Models\MatchConversation;

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
        try {
            $onlineDonations = Donation::whereIn('status', ['confirmed', 'distributed', 'completed'])
                ->sum('amount');

            $physicalDonations = PhysicalDonation::whereNotNull('estimated_value')
                ->sum('estimated_value');

            // ✅ UPDATED: Use donation_status instead of disaster_status
            // Affected = those still needing help (pending or in_progress)
            $totalAffectedFamilies = Barangay::whereIn('donation_status', ['pending', 'in_progress'])
                ->sum('affected_families');

            $affectedBarangays = Barangay::whereIn('donation_status', ['pending', 'in_progress'])->count();

            $activeFundraisers = Barangay::whereIn('donation_status', ['pending', 'in_progress'])->count();

            // ✅ UPDATED: Critical = those with pending requests (nobody checked yet)
            $criticalBarangays = Barangay::where('donation_status', 'pending')->count();

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
        } catch (\Exception $e) {
            Log::error('Error loading overview: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Error loading overview',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Get all barangays for map - UPDATED TO USE RESOURCE_NEEDS
     */
   /**
 * Get all barangays for LDRRMO map - Shows ALL barangays
 * ✅ UPDATED: Uses donation_status instead of disaster_status
 */
public function getBarangaysMapData()
{
    $barangays = Barangay::select([
        'barangay_id',
        'name',
        'donation_status', // ✅ CHANGED: from disaster_status
        'affected_families',
        'latitude as lat',
        'longitude as lng'
    ])
    ->with(['resourceNeeds' => function($query) {
        // Only get pending and high priority needs
        $query->where('status', '!=', 'fulfilled')
              ->orderBy('urgency', 'desc');
    }])
    ->get()
    ->map(function($barangay) {
        return [
            'id' => $barangay->barangay_id,
            'name' => $barangay->name,
            'status' => $barangay->status,
            'affected_families' => $barangay->affected_families,
            'lat' => $barangay->lat,
            'lng' => $barangay->lng,
            // Format resource needs for map display
            'resource_needs' => $barangay->resourceNeeds->map(function($need) {
                return [
                    'category' => $need->category,
                    'description' => $need->description,
                    'quantity' => $need->quantity,
                    'urgency' => $need->urgency,
                    'status' => $need->status
                ];
            })
        ];
    });

    return response()->json($barangays);
}
    /**
     * Get analytics data - FIXED VERSION WITH PAYMENT METHOD
     */
    public function getAnalyticsData()
    {
        try {
            // Donations by Barangay
            $donationsByBarangay = DB::table('barangays')
                ->leftJoin('donations', function($join) {
                    $join->on('barangays.barangay_id', '=', 'donations.barangay_id')
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

            // ✅ UPDATED: Donation Status Distribution (was Disaster Status Distribution)
            $donationStatusDistribution = Barangay::select('donation_status', DB::raw('count(*) as count'))
                ->groupBy('donation_status')
                ->get()
                ->pluck('count', 'donation_status')
                ->toArray();

            // Affected Families by Barangay
            $affectedFamiliesByBarangay = Barangay::select('name', 'affected_families')
                ->where('affected_families', '>', 0)
                ->orderBy('affected_families', 'desc')
                ->limit(10)
                ->get();

            // Payment Method Distribution (FIXED - Check if column exists)
            $paymentMethodDistribution = [];
            
            // Check if payment_method column exists in donations table
            $tableColumns = DB::getSchemaBuilder()->getColumnListing('donations');
            
            if (in_array('payment_method', $tableColumns)) {
                $paymentMethodDistribution = Donation::select('payment_method', DB::raw('count(*) as total'))
                    ->whereIn('status', ['confirmed', 'distributed', 'completed'])
                    ->whereNotNull('payment_method')
                    ->groupBy('payment_method')
                    ->get()
                    ->map(function($item) {
                        return [
                            'payment_method' => $item->payment_method ?? 'Unknown',
                            'total' => $item->total
                        ];
                    });
            } else {
                // If payment_method doesn't exist, return empty array or default data
                $paymentMethodDistribution = [
                    ['payment_method' => 'Online', 'total' => Donation::whereIn('status', ['confirmed', 'distributed', 'completed'])->count()],
                    ['payment_method' => 'Physical', 'total' => PhysicalDonation::count()]
                ];
            }

            // Resource needs statistics
            $totalResourceNeeds = ResourceNeed::count();
            $activeResourceNeeds = ResourceNeed::whereIn('status', ['pending', 'verified', 'matched'])->count();
            $fulfilledResourceNeeds = ResourceNeed::where('status', 'fulfilled')->count();

            // Calculate total donations
            $totalDonations = DB::table('donations')
                ->whereIn('status', ['confirmed', 'distributed', 'completed'])
                ->sum('amount') +
                DB::table('physical_donations')
                ->sum('estimated_value');

            return response()->json([
                'donations_by_barangay' => $donationsByBarangay,
                'donation_status_distribution' => $donationStatusDistribution, // ✅ CHANGED from disaster_status_distribution
                'affected_families_by_barangay' => $affectedFamiliesByBarangay,
                'payment_method_distribution' => $paymentMethodDistribution,
                'resource_needs_count' => $activeResourceNeeds,
                'total_resource_needs' => $totalResourceNeeds,
                'fulfilled_resource_needs' => $fulfilledResourceNeeds,
                'total_donations' => $totalDonations
            ]);
        } catch (\Exception $e) {
            Log::error('Error loading analytics: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Error loading analytics data',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    public function getBarangaysComparison()
    {
        try {
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
                    'donation_status' => $barangay->donation_status, // ✅ CHANGED from disaster_status
                    'disaster_type' => $barangay->disaster_type, // Keep for historical context
                    'affected_families' => $barangay->affected_families ?? 0,
                    'donations_received' => $onlineDonations + $physicalDonations,
                    'online_donations' => $onlineDonations,
                    'physical_donations' => $physicalDonations,
                    'resource_needs' => $resourceNeeds,
                    'needs_help' => $barangay->needsHelp()
                ];
            });

            return response()->json($barangays);
        } catch (\Exception $e) {
            Log::error('Error loading barangays comparison: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Error loading barangays comparison',
                'error' => $e->getMessage()
            ], 500);
        }
    }


    /**
     * Get detailed barangay information
     */
    public function getBarangayDetails($barangayId)
    {
        try {
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
                    'donation_status' => $barangay->donation_status, // ✅ CHANGED from disaster_status
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
        } catch (\Exception $e) {
            Log::error('Error loading barangay details: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Error loading barangay details',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Update barangay donation status
     * ✅ UPDATED: Changed from disaster_status to donation_status
     */
    public function updateBarangayStatus(Request $request, $barangayId)
    {
        try {
            $validated = $request->validate([
                'donation_status' => 'required|in:pending,in_progress,completed', // ✅ CHANGED
                'affected_families' => 'sometimes|integer|min:0',
                'needs_summary' => 'nullable|string|max:1000'
            ]);

            $barangay = Barangay::where('barangay_id', $barangayId)->firstOrFail();
            $barangay->update($validated);

            return response()->json([
                'success' => true,
                'message' => 'Barangay donation status updated successfully', // ✅ UPDATED message
                'data' => $barangay
            ]);
        } catch (\Exception $e) {
            Log::error('Error updating barangay donation status: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Error updating barangay donation status',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Get recent activity
     */
    public function getRecentActivity()
    {
        try {
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
                        'message' => ucfirst($need->category ?? 'Resource') . ' needed - ' . ($need->urgency ?? 'low') . ' urgency',
                        'timestamp' => $need->created_at,
                    ];
                });

            $activities = $recentDonations->merge($recentNeeds)
                ->sortByDesc('timestamp')
                ->take(15)
                ->values();

            return response()->json($activities);
        } catch (\Exception $e) {
            Log::error('Error loading recent activity: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Error loading recent activity',
                'error' => $e->getMessage()
            ], 500);
        }
    }

public function getResourceNeeds(Request $request)
{
    try {
        $filter = $request->query('filter', 'all');

        \Log::info('=== GET RESOURCE NEEDS ===');
        \Log::info('Filter: ' . $filter);

        $query = ResourceNeed::with('barangay')
            ->where(function($q) {
                $q->where('status', 'pending')
                  ->orWhere('status', 'partially_fulfilled');
            });

        // ✅ FIX: Handle NULL verification_status
        if ($filter === 'pending') {
            $query->where(function($q) {
                $q->where('verification_status', 'pending')
                  ->orWhereNull('verification_status'); // ✅ Include NULL values
            });
        } elseif ($filter === 'verified') {
            $query->where('verification_status', 'verified');
        } elseif ($filter === 'rejected') {
            $query->where('verification_status', 'rejected');
        }
        // If 'all', don't filter by verification_status

        $needs = $query->orderByRaw("FIELD(urgency, 'critical', 'high', 'medium', 'low')")
            ->orderBy('created_at', 'desc')
            ->get()
            ->map(function($need) {
                // Check if this need has an active match (pending or accepted)
                $hasActiveMatch = ResourceMatch::where('resource_need_id', $need->id)
                    ->whereIn('status', ['pending', 'accepted'])
                    ->exists();

                return [
                    'id' => $need->id,
                    'barangay_id' => $need->barangay_id,
                    'barangay_name' => $need->barangay->name ?? 'Unknown',
                    'category' => $need->category ?? 'General',
                    'item_name' => $need->category ?? 'General',
                    'description' => $need->description ?? '',
                    'quantity' => $need->quantity ?? '0',
                    'urgency' => $need->urgency ?? 'low',
                    'status' => $need->status ?? 'pending',
                    'verification_status' => $need->verification_status ?? 'pending', // ✅ Default to 'pending'
                    'verified_by' => $need->verified_by ?? null,
                    'verified_at' => $need->verified_at ? $need->verified_at->format('Y-m-d H:i:s') : null,
                    'rejection_reason' => $need->rejection_reason ?? null, // ✅ Add this
                    'affected_families' => $need->barangay->affected_families ?? 0,
                    'created_at' => $need->created_at ? $need->created_at->format('Y-m-d H:i:s') : null,
                    'has_active_match' => $hasActiveMatch, // ✅ NEW: Indicate if this need already has an active match
                ];
            })
            ->filter(function($need) {
                // ✅ Filter out needs that already have active matches
                return !$need['has_active_match'];
            })
            ->values(); // Reset array keys

        \Log::info('Total needs found: ' . $needs->count());

        return response()->json($needs);
    } catch (\Exception $e) {
        \Log::error('Error loading resource needs: ' . $e->getMessage());
        \Log::error($e->getTraceAsString());

        return response()->json([
            'success' => false,
            'message' => 'Error loading resource needs',
            'error' => $e->getMessage()
        ], 500);
    }
}


public function verifyResourceNeed(Request $request, $needId)
{
    try {
        $validated = $request->validate([
            'action' => 'required|in:verify,reject',
            'rejection_reason' => 'required_if:action,reject|nullable|string|max:500'
        ]);

        $need = ResourceNeed::findOrFail($needId);
        $userId = session('user_id');

        if ($validated['action'] === 'verify') {
            $need->verification_status = 'verified';
            $need->verified_by = $userId;
            $need->verified_at = now();
            $need->rejection_reason = null;
            $message = 'Resource need verified successfully';
        } else {
            $need->verification_status = 'rejected';
            $need->verified_by = $userId;
            $need->verified_at = now();
            $need->rejection_reason = $validated['rejection_reason'];
            $message = 'Resource need rejected';
        }

        $need->save();

        return response()->json([
            'success' => true,
            'message' => $message,
            'data' => [
                'id' => $need->id,
                'verification_status' => $need->verification_status,
                'verified_by' => $need->verified_by,
                'verified_at' => $need->verified_at ? $need->verified_at->format('Y-m-d H:i:s') : null,
            ]
        ]);
    } catch (\Exception $e) {
        Log::error('Error verifying resource need: ' . $e->getMessage());
        return response()->json([
            'success' => false,
            'message' => 'Error verifying resource need',
            'error' => $e->getMessage()
        ], 500);
    }
}

public function revertVerification($needId)
{
    try {
        $need = ResourceNeed::findOrFail($needId);
        
        $need->verification_status = 'pending';
        $need->verified_by = null;
        $need->verified_at = null;
        $need->rejection_reason = null;
        $need->save();

        return response()->json([
            'success' => true,
            'message' => 'Verification status reverted to pending',
            'data' => [
                'id' => $need->id,
                'verification_status' => $need->verification_status,
            ]
        ]);
    } catch (\Exception $e) {
        Log::error('Error reverting verification: ' . $e->getMessage());
        return response()->json([
            'success' => false,
            'message' => 'Error reverting verification',
            'error' => $e->getMessage()
        ], 500);
    }
}
    /**
     * Find matching donations for a specific resource need
     */
    public function findMatches($needId)
{
    try {
        $need = ResourceNeed::with('barangay')->findOrFail($needId);

        // Search for matching donations using distribution_status
        // ✅ FIXED: Include partially_distributed donations too
        $matches = PhysicalDonation::with('barangay')
            ->whereIn('distribution_status', ['pending_distribution', 'partially_distributed'])
            ->where('barangay_id', '!=', $need->barangay_id)
            ->get()
            ->filter(function($donation) use ($need) {
                // Check if this donation already has a pending/accepted match with this need
                $hasActiveMatch = ResourceMatch::where('resource_need_id', $need->id)
                    ->where('physical_donation_id', $donation->id)
                    ->whereIn('status', ['pending', 'accepted'])
                    ->exists();

                if ($hasActiveMatch) {
                    return false; // Skip this donation
                }

                $needCategory = strtolower(trim($need->category ?? ''));
                $donationItem = strtolower(trim($donation->items_description ?? ''));
                $donationCategory = strtolower(trim($donation->category ?? ''));

                return str_contains($donationItem, $needCategory) ||
                       str_contains($donationCategory, $needCategory) ||
                       str_contains($needCategory, $donationItem) ||
                       str_contains($needCategory, $donationCategory);
            })
                ->map(function($donation) use ($need) {
                    $matchScore = $this->calculateMatchScore($need, $donation);

                    return [
                        'donation' => [
                            'id' => $donation->id,
                            'items_description' => $donation->items_description ?? $donation->category ?? 'Unknown',
                            'item_name' => $donation->items_description ?? $donation->category ?? 'Unknown',
                            'quantity' => $donation->quantity ?? '0',
                            'status' => $donation->distribution_status ?? 'available',
                            'category' => $donation->category ?? 'general',
                            'donor_name' => $donation->donor_name ?? 'Anonymous',
                            'recorded_at' => $donation->recorded_at ? $donation->recorded_at->format('M d, Y') : 'N/A',
                        ],
                        'barangay' => [
                            'id' => $donation->barangay_id,
                            'name' => $donation->barangay->name ?? 'Unknown',
                        ],
                        'match_score' => $matchScore,
                        'can_fulfill' => ($donation->quantity ?? 0) >= $this->extractQuantityNumber($need->quantity ?? '0'),
                        'can_fully_fulfill' => ($donation->quantity ?? 0) >= $this->extractQuantityNumber($need->quantity ?? '0'),
                    ];
                })
                ->filter(function($match) {
                    return $match['match_score'] > 30;
                })
                ->sortByDesc('match_score')
                ->values();

            return response()->json([
                'success' => true,
                'need' => [
                    'id' => $need->id,
                    'barangay' => [
                        'id' => $need->barangay_id,
                        'name' => $need->barangay->name ?? 'Unknown',
                    ],
                    'category' => $need->category ?? 'Unknown',
                    'item_name' => $need->category ?? 'Unknown',
                    'description' => $need->description ?? '',
                    'quantity' => $need->quantity ?? '0',
                    'urgency' => $need->urgency ?? 'low',
                    'affected_families' => $need->barangay->affected_families ?? 0,
                ],
                'matches' => $matches,
                'total_matches' => $matches->count(),
            ]);

        } catch (\Exception $e) {
            Log::error('Error finding matches: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Error finding matches',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Calculate match score between need and donation
     */
    private function calculateMatchScore($need, $donation)
{
    $score = 0;

    // Item/Category match (50 points max)
    $needCategory = strtolower(trim($need->category ?? ''));
    $donationItem = strtolower(trim($donation->items_description ?? ''));
    $donationCategory = strtolower(trim($donation->category ?? ''));

    if ($needCategory === $donationItem || $needCategory === $donationCategory) {
        $score += 50;
    } elseif (str_contains($donationItem, $needCategory) || str_contains($needCategory, $donationItem)) {
        $score += 35;
    } elseif (str_contains($donationCategory, $needCategory) || str_contains($needCategory, $donationCategory)) {
        $score += 30;
    }

    // Quantity adequacy (30 points max)
    $needQuantity = $this->extractQuantityNumber($need->quantity ?? '0');
    $donationQuantity = $this->extractQuantityNumber($donation->quantity ?? '0'); // ✅ FIXED!

    if ($donationQuantity >= $needQuantity) {
        $quantityRatio = min($donationQuantity / max($needQuantity, 1), 2);
        $score += 30 * ($quantityRatio / 2);
    } else {
        $score += 15 * ($donationQuantity / max($needQuantity, 1));
    }

    // Urgency factor (20 points max)
    $urgencyScores = [
        'critical' => 20,
        'high' => 15,
        'medium' => 10,
        'low' => 5
    ];
    $score += $urgencyScores[$need->urgency ?? 'low'] ?? 0;

    return round(min(100, $score), 2);
}

    /**
     * Extract numeric quantity from string
     */
    private function extractQuantityNumber($quantityString)
    {
        if (is_numeric($quantityString)) {
            return (int) $quantityString;
        }
        
        preg_match('/(\d+)/', $quantityString, $matches);
        return isset($matches[1]) ? (int) $matches[1] : 0;
    }

    /**
     * Get barangay contact information
     */
    public function getBarangayContact($barangayId)
    {
        try {
            $barangay = Barangay::where('barangay_id', $barangayId)->firstOrFail();
            
            return response()->json([
                'success' => true,
                'barangay' => $barangay,
                'contact_info' => [
                    'name' => $barangay->name,
                    'contact_person' => $barangay->contact_person ?? 'Not Available',
                    'phone' => $barangay->contact_phone ?? 'Not Available',
                    'email' => $barangay->contact_email ?? 'Not Available',
                ]
            ]);

        } catch (\Exception $e) {
            Log::error('Error retrieving barangay contact: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Error retrieving barangay contact',
                'error' => $e->getMessage()
            ], 404);
        }
    }

    public function initiateMatch(Request $request)
{
    try {
        $validated = $request->validate([
            'resource_need_id' => 'required|exists:resource_needs,id',
            'physical_donation_id' => 'required|exists:physical_donations,id',
            'match_score' => 'nullable|numeric|min:0|max:100',
            'quantity_requested' => 'nullable|string|max:100',
            'can_fully_fulfill' => 'nullable|boolean',
        ]);

        $userId = session('user_id');

             $need = ResourceNeed::with('barangay')->findOrFail($validated['resource_need_id']);
        $donation = PhysicalDonation::with('barangay')->findOrFail($validated['physical_donation_id']);

                $existingMatch = ResourceMatch::where('resource_need_id', $need->id)
            ->where('physical_donation_id', $donation->id)
            ->whereIn('status', ['pending', 'accepted'])
            ->first();

        if ($existingMatch) {
            return response()->json([
                'success' => false,
                'message' => 'A match request already exists for this combination'
            ], 400);
        }

                $match = ResourceMatch::create([
            'resource_need_id' => $need->id,
            'requesting_barangay_id' => $need->barangay_id,
            'physical_donation_id' => $donation->id,
            'donating_barangay_id' => $donation->barangay_id,
            'match_score' => $validated['match_score'] ?? null,
            'quantity_requested' => $validated['quantity_requested'] ?? $need->quantity,
            'can_fully_fulfill' => $validated['can_fully_fulfill'] ?? false,
            'status' => 'pending',
            'initiated_by' => $userId,
            'initiated_at' => now(),
        ]);

        // Create notification for REQUESTING barangay (FYI)
        MatchNotification::create([
            'resource_match_id' => $match->id,
            'barangay_id' => $need->barangay_id,
            'type' => 'match_request',
            'title' => 'Resource Match Found!',
            'message' => "LDRRMO found a potential donor ({$donation->barangay->name}) for your {$need->category} request. Waiting for their response.",
        ]);

        // Create notification for DONATING barangay (ACTION REQUIRED)
        MatchNotification::create([
            'resource_match_id' => $match->id,
            'barangay_id' => $donation->barangay_id,
            'type' => 'match_request',
            'title' => 'Resource Request from ' . $need->barangay->name,
            'message' => "{$need->barangay->name} needs {$need->category} ({$need->quantity}). You have {$donation->quantity} available. Please accept or reject this request.",
        ]);

        // Create notification for LDRRMO user (tracking)
        MatchNotification::create([
            'resource_match_id' => $match->id,
            'user_id' => $userId,
            'type' => 'match_request',
            'title' => 'Match Request Sent',
            'message' => "Match request sent: {$need->barangay->name} ↔ {$donation->barangay->name} for {$need->category}. Awaiting response from donor.",
        ]);

        Log::info("Match initiated by LDRRMO", [
            'match_id' => $match->id,
            'need' => $need->barangay->name,
            'donor' => $donation->barangay->name,
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Match request sent successfully',
            'data' => [
                'match_id' => $match->id,
                'status' => $match->status,
                'requesting_barangay' => $need->barangay->name,
                'donating_barangay' => $donation->barangay->name,
            ]
        ]);

    } catch (\Exception $e) {
        Log::error('Error initiating match: ' . $e->getMessage());
        return response()->json([
            'success' => false,
            'message' => 'Error initiating match',
            'error' => $e->getMessage()
        ], 500);
    }
}


public function getMyInitiatedMatches(Request $request)
{
    try {
        $status = $request->query('status', 'all');

        $query = ResourceMatch::with([
            'resourceNeed.barangay',
            'physicalDonation.barangay',
            'requestingBarangay',
            'donatingBarangay',
            'conversation'
        ])->orderBy('initiated_at', 'desc');

        // Filter by status if specified
        if ($status !== 'all') {
            $query->where('status', $status);
        }

        $matches = $query->get()->map(function($match) {
            return [
                'id' => $match->id,
                'resource_need' => [
                    'id' => $match->resource_need_id,
                    'category' => $match->resourceNeed->category,
                    'quantity' => $match->resourceNeed->quantity,
                    'urgency' => $match->resourceNeed->urgency,
                ],
                'physical_donation' => [
                    'id' => $match->physical_donation_id,
                    'items' => $match->physicalDonation->items_description,
                    'quantity' => $match->physicalDonation->quantity,
                ],
                'requesting_barangay' => [
                    'id' => $match->requesting_barangay_id,
                    'name' => $match->requestingBarangay->name,
                ],
                'donating_barangay' => [
                    'id' => $match->donating_barangay_id,
                    'name' => $match->donatingBarangay->name,
                ],
                'match_score' => $match->match_score,
                'can_fully_fulfill' => $match->can_fully_fulfill,
                'status' => $match->status,
                'status_label' => $match->status_label,
                'status_color' => $match->status_color,
                'initiated_at' => $match->initiated_at->format('M d, Y h:i A'),
                'responded_at' => $match->responded_at ? $match->responded_at->format('M d, Y h:i A') : null,
                'response_message' => $match->response_message,
                'has_conversation' => $match->hasConversation(),
            ];
        });

        return response()->json($matches);

    } catch (\Exception $e) {
        Log::error('Error loading matches: ' . $e->getMessage());
        return response()->json([
            'success' => false,
            'message' => 'Error loading matches',
            'error' => $e->getMessage()
        ], 500);
    }
}


 //Cancel a match (LDRRMO only, before it's accepted)
 
public function cancelMatch($matchId)
{
    try {
        $match = ResourceMatch::with(['requestingBarangay', 'donatingBarangay'])->findOrFail($matchId);

        // Only allow cancellation if status is pending
        if ($match->status !== 'pending') {
            return response()->json([
                'success' => false,
                'message' => 'Only pending matches can be cancelled'
            ], 400);
        }

        $match->update(['status' => 'cancelled']);

        // Notify both barangays
        MatchNotification::create([
            'resource_match_id' => $match->id,
            'barangay_id' => $match->requesting_barangay_id,
            'type' => 'match_cancelled',
            'title' => 'Match Request Cancelled',
            'message' => 'LDRRMO cancelled the match request. They may find an alternative donor.',
        ]);

        MatchNotification::create([
            'resource_match_id' => $match->id,
            'barangay_id' => $match->donating_barangay_id,
            'type' => 'match_cancelled',
            'title' => 'Match Request Cancelled',
            'message' => 'LDRRMO cancelled this match request.',
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Match cancelled successfully'
        ]);

    } catch (\Exception $e) {
        Log::error('Error cancelling match: ' . $e->getMessage());
        return response()->json([
            'success' => false,
            'message' => 'Error cancelling match',
            'error' => $e->getMessage()
        ], 500);
    }
}


public function getMatchStatistics()
{
    try {
        $totalMatches = ResourceMatch::count();
        $pendingMatches = ResourceMatch::pending()->count();
        $acceptedMatches = ResourceMatch::accepted()->count();
        $completedMatches = ResourceMatch::completed()->count();
        $rejectedMatches = ResourceMatch::rejected()->count();
        $activeConversations = MatchConversation::active()->count();

        // ✅ Calculate success rate (accepted + completed / total)
        $successRate = $totalMatches > 0
            ? round(($acceptedMatches + $completedMatches) / $totalMatches * 100, 1)
            : 0;

        $stats = [
            'total_matches' => $totalMatches,
            'pending_matches' => $pendingMatches,
            'accepted_matches' => $acceptedMatches,
            'completed_matches' => $completedMatches,
            'rejected_matches' => $rejectedMatches,
            'active_conversations' => $activeConversations,
            'success_rate' => $successRate, // ✅ ADDED: Backend calculation for consistency
        ];

        return response()->json($stats);

    } catch (\Exception $e) {
        Log::error('Error loading match statistics: ' . $e->getMessage());
        return response()->json([
            'success' => false,
            'message' => 'Error loading statistics',
            'error' => $e->getMessage()
        ], 500);
    }
}

public function getMatchDetails($needId, $donationId)
{
    try {
        // Optimized: Only select needed columns and eager load barangay name
        $need = ResourceNeed::select('id', 'barangay_id', 'category', 'description', 'quantity', 'urgency', 'status')
            ->with('barangay:barangay_id,name')
            ->findOrFail($needId);

        $donation = PhysicalDonation::select('id', 'barangay_id', 'items_description', 'quantity', 'category', 'distribution_status', 'donor_name')
            ->with('barangay:barangay_id,name')
            ->findOrFail($donationId);

        // Calculate match score
        $matchScore = $this->calculateMatchScore($need, $donation);

        return response()->json([
            'success' => true,
            'need' => [
                'id' => $need->id,
                'barangay_id' => $need->barangay_id,
                'barangay_name' => $need->barangay->name ?? 'Unknown',
                'category' => $need->category ?? 'General',
                'description' => $need->description ?? '',
                'quantity' => $need->quantity ?? '0',
                'urgency' => $need->urgency ?? 'low',
                'status' => $need->status ?? 'pending',
            ],
            'donation' => [
                'id' => $donation->id,
                'barangay_id' => $donation->barangay_id,
                'barangay_name' => $donation->barangay->name ?? 'Unknown',
                'items_description' => $donation->items_description ?? '',
                'quantity' => $donation->quantity ?? '0',
                'category' => $donation->category ?? 'general',
                'distribution_status' => $donation->distribution_status ?? 'pending_distribution',
                'donor_name' => $donation->donor_name ?? 'Anonymous',
            ],
            'match_analysis' => [
                'match_score' => $matchScore,
                'can_fully_fulfill' => $this->extractQuantityNumber($donation->quantity) >= $this->extractQuantityNumber($need->quantity),
            ]
        ]);

    } catch (\Exception $e) {
        Log::error('Error loading match details: ' . $e->getMessage());
        return response()->json([
            'success' => false,
            'message' => 'Error loading match details',
            'error' => $e->getMessage()
        ], 500);
    }
}

/**
 * Get conversation for a match (LDRRMO as monitor)
 */
public function getMatchConversation($matchId)
{
    try {
        $match = ResourceMatch::with([
            'requestingBarangay:barangay_id,name',
            'donatingBarangay:barangay_id,name',
            'resourceNeed:id,category,quantity',
            'physicalDonation:id,items_description,quantity',
            'conversation.messages' => function($query) {
                $query->orderBy('created_at', 'asc');
            }
        ])->findOrFail($matchId);

        // Check if conversation exists
        if (!$match->conversation) {
            return response()->json([
                'success' => false,
                'message' => 'No conversation exists for this match yet'
            ], 404);
        }

        $conversation = $match->conversation;

        $messages = $conversation->messages->map(function($message) use ($match) {
            // Determine sender name
            $senderName = 'Unknown';
            $senderRole = 'unknown';

            // Check for system messages first
            if ($message->isSystemMessage()) {
                $senderName = 'System';
                $senderRole = 'system';
            } elseif ($message->sender_barangay_id === $match->requesting_barangay_id) {
                $senderName = $match->requestingBarangay->name . ' (Requesting)';
                $senderRole = 'requester';
            } elseif ($message->sender_barangay_id === $match->donating_barangay_id) {
                $senderName = $match->donatingBarangay->name . ' (Donating)';
                $senderRole = 'donor';
            } elseif ($message->sender_user_id && !$message->sender_barangay_id) {
                $senderName = 'LDRRMO';
                $senderRole = 'ldrrmo';
            }

            return [
                'id' => $message->id,
                'message' => $message->message,
                'message_type' => $message->message_type,
                'sender_name' => $senderName,
                'sender_role' => $senderRole,
                'is_mine' => $senderRole === 'ldrrmo', // LDRRMO's own messages
                'created_at' => $message->created_at->format('M d, Y h:i A'),
                'timestamp' => $message->created_at->toIso8601String(),
            ];
        });

        return response()->json([
            'success' => true,
            'match' => [
                'id' => $match->id,
                'status' => $match->status,
                'requesting_barangay' => $match->requestingBarangay->name,
                'donating_barangay' => $match->donatingBarangay->name,
                'resource_need' => $match->resourceNeed->category . ' (' . $match->resourceNeed->quantity . ')',
                'donation' => $match->physicalDonation->items_description . ' (' . $match->physicalDonation->quantity . ')',
            ],
            'conversation' => [
                'id' => $conversation->id,
                'status' => $conversation->status,
                'message_count' => $messages->count(),
                'messages' => $messages,
            ]
        ]);

    } catch (\Exception $e) {
        Log::error('Error loading conversation: ' . $e->getMessage());
        return response()->json([
            'success' => false,
            'message' => 'Error loading conversation',
            'error' => $e->getMessage()
        ], 500);
    }
}

/**
 * Send message in match conversation (LDRRMO as participant)
 */
public function sendMessage(Request $request, $matchId)
{
    try {
        $validated = $request->validate([
            'message' => 'required|string|max:1000'
        ]);

        $match = ResourceMatch::with('conversation')->findOrFail($matchId);

        // Create conversation if it doesn't exist (for accepted matches)
        if (!$match->conversation) {
            if ($match->status !== 'accepted') {
                return response()->json([
                    'success' => false,
                    'message' => 'Conversation can only be started for accepted matches'
                ], 400);
            }

            $conversation = MatchConversation::create([
                'resource_match_id' => $match->id,
                'status' => 'active'
            ]);
        } else {
            $conversation = $match->conversation;
        }

        // Create message from LDRRMO (no barangay_id, only user_id)
        $userId = session('user_id');

        $message = $conversation->messages()->create([
            'sender_user_id' => $userId,
            'sender_barangay_id' => null, // LDRRMO doesn't have barangay_id
            'message' => $validated['message'],
        ]);

        // Update conversation last_message_at
        $conversation->update([
            'last_message_at' => now()
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Message sent successfully',
            'data' => [
                'id' => $message->id,
                'message' => $message->message,
                'sender_name' => 'LDRRMO',
                'sender_role' => 'ldrrmo',
                'created_at' => $message->created_at->format('M d, Y h:i A'),
            ]
        ]);

    } catch (\Exception $e) {
        Log::error('Error sending message: ' . $e->getMessage());
        return response()->json([
            'success' => false,
            'message' => 'Error sending message',
            'error' => $e->getMessage()
        ], 500);
    }
}

/**
 * Get LDRRMO notifications
 */
public function getLdrrmoNotifications(Request $request)
{
    try {
        $userId = session('user_id');
        $limit = $request->query('limit', 50);
        $type = $request->query('type', 'all');

        $query = MatchNotification::with([
            'resourceMatch.resourceNeed',
            'resourceMatch.requestingBarangay',
            'resourceMatch.donatingBarangay'
        ])
        ->where('user_id', $userId)
        ->orderBy('created_at', 'desc')
        ->limit($limit);

        // Filter by type if specified
        if ($type !== 'all') {
            $query->where('type', $type);
        }

        $notifications = $query->get()->map(function($notification) {
            return [
                'id' => $notification->id,
                'type' => $notification->type,
                'title' => $notification->title,
                'message' => $notification->message,
                'is_read' => $notification->is_read,
                'match_id' => $notification->resource_match_id,
                'match_status' => $notification->resourceMatch->status ?? null,
                'action_url' => $notification->resource_match_id ? "view-match-{$notification->resource_match_id}" : null,
                'created_at' => $notification->created_at->format('M d, Y h:i A'),
                'time_ago' => $notification->created_at->diffForHumans(),
            ];
        });

        return response()->json($notifications);

    } catch (\Exception $e) {
        Log::error('Error loading LDRRMO notifications: ' . $e->getMessage());
        return response()->json([
            'success' => false,
            'message' => 'Error loading notifications',
            'error' => $e->getMessage()
        ], 500);
    }
}

/**
 * Get LDRRMO unread notification count
 */
public function getLdrrmoUnreadCount()
{
    try {
        $userId = session('user_id');

        $count = MatchNotification::where('user_id', $userId)
            ->where('is_read', false)
            ->count();

        return response()->json([
            'count' => $count
        ]);

    } catch (\Exception $e) {
        Log::error('Error getting LDRRMO unread count: ' . $e->getMessage());
        return response()->json([
            'success' => false,
            'message' => 'Error getting unread count',
            'error' => $e->getMessage()
        ], 500);
    }
}

/**
 * Mark LDRRMO notification as read
 */
public function markLdrrmoNotificationAsRead($notificationId)
{
    try {
        $userId = session('user_id');

        $notification = MatchNotification::where('id', $notificationId)
            ->where('user_id', $userId)
            ->firstOrFail();

        $notification->update([
            'is_read' => true,
            'read_at' => now()
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Notification marked as read'
        ]);

    } catch (\Exception $e) {
        Log::error('Error marking notification as read: ' . $e->getMessage());
        return response()->json([
            'success' => false,
            'message' => 'Error marking notification as read',
            'error' => $e->getMessage()
        ], 500);
    }
}

/**
 * Mark all LDRRMO notifications as read
 */
public function markAllLdrrmoNotificationsAsRead()
{
    try {
        $userId = session('user_id');

        MatchNotification::where('user_id', $userId)
            ->where('is_read', false)
            ->update([
                'is_read' => true,
                'read_at' => now()
            ]);

        return response()->json([
            'success' => true,
            'message' => 'All notifications marked as read'
        ]);

    } catch (\Exception $e) {
        Log::error('Error marking all notifications as read: ' . $e->getMessage());
        return response()->json([
            'success' => false,
            'message' => 'Error marking all notifications as read',
            'error' => $e->getMessage()
        ], 500);
    }
}

    /**
     * Get top 5 urgent requests sorted by urgency
     * ✅ FIXED: Now uses same filtering logic as Resource Needs tab for consistency
     */
    public function getUrgentRequests()
    {
        try {
            // ✅ FIX: Use same status filter as Resource Needs tab
            $allNeeds = ResourceNeed::with('barangay')
                ->where(function($q) {
                    $q->where('status', 'pending')
                      ->orWhere('status', 'partially_fulfilled');
                })
                // ✅ FIX: Sort by urgency (critical > high > medium > low), then NEWEST first
                ->orderByRaw("FIELD(urgency, 'critical', 'high', 'medium', 'low')")
                ->orderBy('created_at', 'desc')
                ->get();

            // ✅ FIX: Exclude requests with active matches (same as Resource Needs tab)
            $urgentRequests = $allNeeds->filter(function($need) {
                $hasActiveMatch = ResourceMatch::where('resource_need_id', $need->id)
                    ->whereIn('status', ['pending', 'accepted'])
                    ->exists();
                return !$hasActiveMatch;
            })
            ->take(5) // Top 5 after filtering
            ->map(function ($need) {
                return [
                    'id' => $need->id,
                    'barangay_id' => $need->barangay_id,
                    'barangay_name' => $need->barangay->name ?? 'Unknown',
                    'category' => $need->category,
                    'item_name' => $need->description, // Using description as item_name
                    'quantity_needed' => $need->quantity,
                    'unit' => '', // No separate unit field
                    'status' => $need->status,
                    'urgency_level' => $need->urgency ?? 'medium',
                    'description' => $need->description,
                    'created_at' => $need->created_at,
                    'verification_status' => $need->verification_status
                ];
            })
            ->values();

            return response()->json($urgentRequests);
        } catch (\Exception $e) {
            Log::error('Error loading urgent requests: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Error loading urgent requests',
                'error' => $e->getMessage()
            ], 500);
        }
    }
}
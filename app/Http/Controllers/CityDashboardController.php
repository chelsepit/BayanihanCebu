<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
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
        try {
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
 * Get all barangays for LDRRMO map - Shows ALL barangays (including Safe)
 */
public function getBarangaysMapData()
{
    $barangays = Barangay::select([
        'barangay_id',
        'name', 
        'disaster_status as status',
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

            // Disaster Status Distribution
            $disasterStatusDistribution = Barangay::select('disaster_status', DB::raw('count(*) as count'))
                ->groupBy('disaster_status')
                ->get()
                ->pluck('count', 'disaster_status')
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

            return response()->json([
                'donations_by_barangay' => $donationsByBarangay,
                'disaster_status_distribution' => $disasterStatusDistribution,
                'affected_families_by_barangay' => $affectedFamiliesByBarangay,
                'payment_method_distribution' => $paymentMethodDistribution
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

    /**
     * Get barangays comparison
     */
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
                    'status' => $barangay->disaster_status,
                    'disaster_type' => $barangay->disaster_type,
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
     * Get active fundraisers - barangays that need help
     */
    public function getActiveFundraisers()
    {
        try {
            $fundraisers = Barangay::where('disaster_status', '!=', 'safe')
                ->with('resourceNeeds')
                ->get()
                ->map(function ($barangay) {
                    $totalDonations = Donation::where('barangay_id', $barangay->barangay_id)
                        ->whereIn('status', ['confirmed', 'distributed', 'completed'])
                        ->sum('amount');

                    $goal = ($barangay->affected_families ?? 0) * 10000;
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
                                'category' => $need->category ?? 'General',
                                'quantity' => $need->quantity ?? '0',
                                'urgency' => $need->urgency ?? 'low',
                                'status' => $need->status ?? 'pending',
                                'description' => $need->description ?? ''
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
                        'severity' => $barangay->disaster_status, // Add severity
                        'description' => $barangay->needs_summary ?? 'Emergency assistance needed',
                        'affected_families' => $barangay->affected_families ?? 0,
                        'goal' => $goal,
                        'raised' => $totalDonations,
                        'progress' => round($progress, 2),
                        'donors_count' => $donorsCount,
                        'days_active' => $barangay->updated_at ? $barangay->updated_at->diffInDays(now()) : 0,
                        'resource_needs' => $resourceNeeds
                    ];
                });

            return response()->json($fundraisers);
        } catch (\Exception $e) {
            Log::error('Error loading fundraisers: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Error loading fundraisers',
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
     * Update barangay disaster status
     */
    public function updateBarangayStatus(Request $request, $barangayId)
    {
        try {
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
        } catch (\Exception $e) {
            Log::error('Error updating barangay status: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Error updating barangay status',
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

    /**
     * Get all open resource needs for matching
     */
    public function getResourceNeeds()
    {
        try {
            $needs = ResourceNeed::with('barangay')
                ->where(function($query) {
                    $query->where('status', 'pending')
                          ->orWhere('status', 'partially_fulfilled');
                })
                ->orderByRaw("FIELD(urgency, 'critical', 'high', 'medium', 'low')")
                ->orderBy('created_at', 'desc')
                ->get()
                ->map(function($need) {
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
                        'affected_families' => $need->barangay->affected_families ?? 0,
                        'created_at' => $need->created_at ? $need->created_at->format('Y-m-d H:i:s') : null,
                    ];
                });

            return response()->json($needs);
        } catch (\Exception $e) {
            Log::error('Error loading resource needs: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Error loading resource needs',
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
            $matches = PhysicalDonation::with('barangay')
                ->where('distribution_status', 'pending_distribution')
                ->where('barangay_id', '!=', $need->barangay_id)
                ->get()
                ->filter(function($donation) use ($need) {
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
                            'item_name' => $donation->items_description ?? $donation->category ?? 'Unknown',
                            'quantity' => $donation->quantity ?? 0,
                            'status' => $donation->distribution_status ?? 'pending_distribution',
                            'category' => $donation->category ?? 'general',
                        ],
                        'barangay' => [
                            'id' => $donation->barangay_id,
                            'name' => $donation->barangay->name ?? 'Unknown',
                        ],
                        'match_score' => $matchScore,
                        'can_fulfill' => ($donation->quantity ?? 0) >= $this->extractQuantityNumber($need->quantity ?? '0'),
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
        $donationQuantity = $donation->quantity ?? 0;

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
}
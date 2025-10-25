<?php

namespace App\Http\Controllers;

use App\Models\Barangay;
use App\Models\Donation;
use App\Models\PhysicalDonation;
use App\Models\ResourceNeed;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Services\PhysicalDonationBlockchainService;

class PublicMapController extends Controller
{
    /**
     * Display the public map
     */
    public function index()
    {
        $barangays = Barangay::with('resourceNeeds')
            ->orderBy('name')
            ->get();

        return view('welcome', compact('barangays'));
    }

    /**
     * Track a donation by tracking code
     * ✅ NOW SUPPORTS BOTH ONLINE AND PHYSICAL DONATIONS
     */


public function trackDonation(Request $request)
{
    $trackingCode = $request->input('tracking_code');
    
    $donation = Donation::where('tracking_code', $trackingCode)
        ->with(['barangay', 'disaster', 'verifier'])
        ->first();

    if (!$donation) {
        return back()->with('error', 'Invalid tracking code. Please try again.');
    }

    // Set default values for optional fields
    $donation->verification_status = $donation->verification_status ?? 'pending';
    $donation->blockchain_status = $donation->blockchain_status ?? 'pending';
    
    return view('donations.track', [
        'donation' => $donation,
        'donation_type' => 'online'
    ]);
}

    /**
     * Show donation form for a specific barangay
     */
    public function showDonateForm(Barangay $barangay)
    {
        $barangay->load('resourceNeeds');

        return view('barangays.donate', compact('barangay'));
    }

    /**
     * Process online donation from public
     */
    public function processDonation(Request $request, Barangay $barangay)
    {
        $validated = $request->validate([
            'amount' => 'required|numeric|min:100',
            'donor_name' => 'required_without:is_anonymous|string|max:255',
            'donor_email' => 'required_without:is_anonymous|email|max:255',
            'donor_phone' => 'nullable|string|max:20',
            'is_anonymous' => 'boolean',
            'payment_method' => 'required|in:gcash,paymaya,bank_transfer,metamask,crypto',
            'payment_reference' => 'nullable|string|max:100',
            'tx_hash' => 'nullable|string|max:66',
            'wallet_address' => 'nullable|string|max:42',
        ]);

        $donation = Donation::create([
            'barangay_id' => $barangay->barangay_id,
            'donor_name' => $validated['is_anonymous'] ?? false ? 'Anonymous' : $validated['donor_name'],
            'donor_email' => $validated['is_anonymous'] ?? false ? null : $validated['donor_email'],
            'donor_phone' => $validated['donor_phone'] ?? null,
            'amount' => $validated['amount'],
            'payment_method' => $validated['payment_method'],
            'is_anonymous' => $validated['is_anonymous'] ?? false,
            'payment_reference' => $validated['payment_reference'] ?? null,
            'tx_hash' => $validated['tx_hash'] ?? null,
            'wallet_address' => $validated['wallet_address'] ?? null,
            'blockchain_status' => 'pending',
        ]);

        return redirect()
            ->route('donation.success', $donation->tracking_code)
            ->with('success', 'Thank you for your donation!');
    }

    /**
     * Show donation success page
     */
    public function donationSuccess($trackingCode)
    {
        // Try online donations first
        $onlineDonation = Donation::where('tracking_code', $trackingCode)
            ->with(['barangay', 'disaster'])
            ->first();

        if ($onlineDonation) {
            return view('donations.success', [
                'donation' => $onlineDonation,
                'donation_type' => 'online',
            ]);
        }

        // Try physical donations
        $physicalDonation = PhysicalDonation::where('tracking_code', $trackingCode)
            ->with(['barangay', 'recorder'])
            ->first();

        if ($physicalDonation) {
            return view('donations.success-physical', [
                'donation' => $physicalDonation,
                'donation_type' => 'physical',
            ]);
        }

        // Not found
        abort(404, 'Donation not found');
    }

    /**
     * Get statistics for the map
     * ✅ UPDATED: Shows blockchain-verified donations, all affected families, active needs, and active matches
     */
    public function statistics()
    {
        // Total Donations: Only blockchain-verified donations (both online and physical)
        $totalOnlineVerified = Donation::where('blockchain_status', 'confirmed')
            ->whereNotNull('blockchain_tx_hash')
            ->sum('amount');

        $totalPhysicalVerified = PhysicalDonation::where('blockchain_status', 'confirmed')
            ->whereNotNull('blockchain_tx_hash')
            ->sum('estimated_value');

        $totalDonations = $totalOnlineVerified + $totalPhysicalVerified;

        // Total Affected Families: Sum from all barangays
        $totalAffectedFamilies = Barangay::sum('affected_families');

        // Active Family Needs: Count all verified (not fulfilled) resource needs
        $activeFamilyNeeds = ResourceNeed::where('status', '!=', 'fulfilled')
            ->where('verification_status', 'verified')
            ->count();

        // Active Matches: Count accepted matches from LDRRMO
        $activeMatches = \App\Models\ResourceMatch::where('status', 'accepted')->count();

        $stats = [
            'total_donations' => $totalDonations, // Only blockchain-verified
            'total_affected_families' => $totalAffectedFamilies, // Sum of all barangays
            'active_family_needs' => $activeFamilyNeeds, // Verified resource needs
            'active_matches' => $activeMatches, // Active matches

            // Legacy fields (for backwards compatibility)
            'total_barangays' => Barangay::count(),
            'affected_barangays' => Barangay::whereIn('donation_status', ['pending', 'in_progress'])->count(),
            'total_online_donations' => $totalOnlineVerified,
            'total_physical_donations' => $totalPhysicalVerified,
            // Count blockchain-verified donations (since emails are often NULL)
            'total_donors' => Donation::where('blockchain_status', 'confirmed')->whereNotNull('blockchain_tx_hash')->count() +
                              PhysicalDonation::where('blockchain_status', 'confirmed')->whereNotNull('blockchain_tx_hash')->count(),
            'urgent_needs' => ResourceNeed::whereIn('urgency', ['critical', 'high'])->count(),
        ];

        return response()->json($stats);
    }

    /**
     * API endpoint to get all barangays
     */
/**
 * API endpoint for Public Map - Shows ALL barangays with dynamic resource needs
 */
public function apiBarangays()
{
    $barangays = Barangay::with(['resourceNeeds' => function($query) {
            $query->where('status', '!=', 'fulfilled')
                  ->orderByRaw("FIELD(urgency, 'critical', 'high', 'medium', 'low')");
        }])
        ->orderBy('name')
        ->get()
        ->map(function ($barangay) {
            // Get active resource needs with full details
            $resourceNeeds = $barangay->resourceNeeds->map(function($need) {
                return [
                    'id' => $need->id,
                    'category' => $need->category,
                    'description' => $need->description,
                    'quantity' => $need->quantity,
                    'urgency' => $need->urgency,
                    'status' => $need->status,
                    'created_at' => $need->created_at ? $need->created_at->format('M d, Y') : null,
                ];
            });

            // Calculate highest urgency level for pin sizing
            $urgencyLevels = ['critical' => 4, 'high' => 3, 'medium' => 2, 'low' => 1];
            $highestUrgency = 'low';
            $highestValue = 0;

            foreach ($resourceNeeds as $need) {
                $value = $urgencyLevels[$need['urgency']] ?? 0;
                if ($value > $highestValue) {
                    $highestValue = $value;
                    $highestUrgency = $need['urgency'];
                }
            }

            return [
                'id' => $barangay->barangay_id,
                'name' => $barangay->name,
                'slug' => $barangay->slug,
                'donation_status' => $barangay->donation_status, // ✅ CHANGED from disaster_status
                'disaster_type' => $barangay->disaster_type,
                'latitude' => $barangay->latitude,
                'longitude' => $barangay->longitude,
                'affected_families' => $barangay->affected_families,
                'total_raised' => $barangay->total_raised ?? 0,
                'resource_needs' => $resourceNeeds,
                'resource_needs_count' => $resourceNeeds->count(),
                'highest_urgency' => $highestUrgency,
                'city' => $barangay->city ?? 'Cebu City',
            ];
        });

    return response()->json($barangays);
}

    /**
     * Verify physical donation from tracking page
     */
    public function verifyPhysicalDonation($trackingCode)
    {
        try {
            $donation = PhysicalDonation::where('tracking_code', $trackingCode)->first();

            if (!$donation) {
                return response()->json([
                    'success' => false,
                    'error' => 'Donation not found'
                ], 404);
            }

            // Check if blockchain recording is complete
            if ($donation->blockchain_status !== 'confirmed' || !$donation->blockchain_tx_hash) {
                return response()->json([
                    'success' => false,
                    'status' => 'not_ready',
                    'message' => 'Blockchain recording is still in progress. Please wait and try again.'
                ]);
            }

            // Verify blockchain integrity
            $result = $donation->verifyBlockchainIntegrity();

            return response()->json($result);

        } catch (\Exception $e) {
            Log::error('Error verifying physical donation from tracking page', [
                'tracking_code' => $trackingCode,
                'error' => $e->getMessage()
            ]);

            return response()->json([
                'success' => false,
                'error' => 'Verification failed: ' . $e->getMessage()
            ], 500);
        }
    }
}

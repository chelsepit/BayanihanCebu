<?php

namespace App\Http\Controllers;

use App\Models\Barangay;
use App\Models\OnlineDonation;
use App\Models\PhysicalDonation;
use App\Models\ResourceNeed;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

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
        $request->validate([
            'tracking_code' => 'required|string',
        ]);

        $trackingCode = $request->tracking_code;

        // Try online donations first
        $onlineDonation = OnlineDonation::where('tracking_code', $trackingCode)
            ->with(['barangay', 'disaster', 'verifier'])
            ->first();

        if ($onlineDonation) {
            return view('donations.track', [
                'donation' => $onlineDonation,
                'donation_type' => 'online',
            ]);
        }

        // Try physical donations
        $physicalDonation = PhysicalDonation::where('tracking_code', $trackingCode)
            ->with(['barangay', 'recorder', 'distributions.distributor'])
            ->first();

        if ($physicalDonation) {
            return view('donations.track', [
                'donation' => $physicalDonation,
                'donation_type' => 'physical',
            ]);
        }

        // Not found
        return back()->with('error', 'Tracking code not found. Please check and try again.');
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

        $donation = OnlineDonation::create([
            'target_barangay_id' => $barangay->barangay_id,
            'donor_name' => $validated['is_anonymous'] ?? false ? 'Anonymous' : $validated['donor_name'],
            'donor_email' => $validated['is_anonymous'] ?? false ? null : $validated['donor_email'],
            'donor_phone' => $validated['donor_phone'] ?? null,
            'amount' => $validated['amount'],
            'payment_method' => $validated['payment_method'],
            'is_anonymous' => $validated['is_anonymous'] ?? false,
            'payment_reference' => $validated['payment_reference'] ?? null,
            'tx_hash' => $validated['tx_hash'] ?? null,
            'wallet_address' => $validated['wallet_address'] ?? null,
            'verification_status' => 'pending',
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
        $onlineDonation = OnlineDonation::where('tracking_code', $trackingCode)
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
     */
    public function statistics()
    {
        $stats = [
            'total_barangays' => Barangay::count(),
            'affected_barangays' => Barangay::where('disaster_status', '!=', 'safe')->count(),
            'total_online_donations' => OnlineDonation::where('verification_status', 'verified')->sum('amount'),
            'total_physical_donations' => PhysicalDonation::sum('estimated_value'),
            'total_donors' => OnlineDonation::distinct('donor_email')->count() + PhysicalDonation::distinct('donor_email')->count(),
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
                'status' => $barangay->disaster_status,
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
}
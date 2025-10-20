<?php

namespace App\Http\Controllers;

use App\Models\Barangay;
use App\Models\Donation;
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
     */
    public function trackDonation(Request $request)
    {
        $request->validate([
            'tracking_code' => 'required|string',
        ]);

        $donation = Donation::with(['barangay', 'user'])
            ->where('tracking_code', $request->tracking_code)
            ->first();

        if (!$donation) {
            return back()->with('error', 'Tracking code not found. Please check and try again.');
        }

        return view('donations.track', compact('donation'));
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
     * Process donation
     */
    public function processDonation(Request $request, Barangay $barangay)
    {
        $validated = $request->validate([
            'amount' => 'required|numeric|min:10',
            'donor_name' => 'required_without:is_anonymous|string|max:255',
            'donor_email' => 'required_without:is_anonymous|email|max:255',
            'donor_phone' => 'nullable|string|max:20',
            'is_anonymous' => 'boolean',
            'donation_type' => 'required|in:monetary,in-kind',
        ]);

        $donation = Donation::create([
            'barangay_id' => $barangay->barangay_id,
            'user_id' => Auth::check() ? Auth::user()->user_id : null,
            'amount' => $validated['amount'],
            'donation_type' => $validated['donation_type'],
            'donor_name' => $validated['is_anonymous'] ?? false ? null : $validated['donor_name'],
            'donor_email' => $validated['is_anonymous'] ?? false ? null : $validated['donor_email'],
            'donor_phone' => $validated['donor_phone'] ?? null,
            'is_anonymous' => $validated['is_anonymous'] ?? false,
            'status' => 'pending',
        ]);

        // Here you would integrate with payment gateway
        // For now, we'll just confirm the donation
        $donation->confirm('0x' . bin2hex(random_bytes(32)));

        return redirect()
            ->route('donation.success', $donation->tracking_code)
            ->with('success', 'Thank you for your donation!');
    }

    /**
     * Show donation success page
     */
    public function donationSuccess($trackingCode)
    {
        $donation = Donation::with('barangay')
            ->where('tracking_code', $trackingCode)
            ->firstOrFail();

        return view('donations.success', compact('donation'));
    }

    /**
     * Get disaster statistics
     */
    public function statistics()
    {
        $stats = [
            'total_barangays_needing_help' => Barangay::needsHelp()->count(),
            'total_affected_families' => Barangay::needsHelp()->sum('affected_families'),
            'total_donations' => Donation::whereIn('status', ['confirmed', 'distributed', 'completed'])->sum('amount'),
            'total_donors' => Donation::whereIn('status', ['confirmed', 'distributed', 'completed'])->distinct('donor_email')->count(),
            'barangays_affected' => Barangay::needsHelp()->count(),
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
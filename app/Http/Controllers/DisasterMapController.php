<?php

namespace App\Http\Controllers;

use App\Models\Barangay;
use App\Models\Disaster;
use App\Models\Donation;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class DisasterMapController extends Controller
{
    /**
     * Display the disaster map
     */
    public function index()
    {
        // CHANGED: currentDisaster.urgentNeeds → resourceNeeds
        $barangays = Barangay::with(['resourceNeeds' => function($query) {
            $query->where('status', '!=', 'fulfilled')
                  ->orderBy('urgency', 'desc');
        }])
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

        $donation = Donation::with(['disaster.barangay', 'user'])
            ->where('tracking_code', $request->tracking_code)
            ->first();

        if (!$donation) {
            return back()->with('error', 'Tracking code not found. Please check and try again.');
        }

        return view('donations.track', compact('donation'));
    }

    /**
     * Show donation form for a specific disaster
     */
    public function showDonateForm(Disaster $disaster)
    {
        // CHANGED: urgentNeeds → resourceNeeds
        $disaster->load(['barangay', 'resourceNeeds']);

        return view('disasters.donate', compact('disaster'));
    }

    /**
     * Process donation
     */
    public function processDonation(Request $request, Disaster $disaster)
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
            'disaster_id' => $disaster->id,
            'user_id' => Auth::check() ? Auth::user()->user_id : null,
            'amount' => $validated['amount'],
            'donation_type' => $validated['donation_type'],
            'donor_name' => $validated['is_anonymous'] ?? false ? null : $validated['donor_name'],
            'donor_email' => $validated['is_anonymous'] ?? false ? null : $validated['donor_email'],
            'donor_phone' => $validated['donor_phone'] ?? null,
            'is_anonymous' => $validated['is_anonymous'] ?? false,
            'status' => 'pending',
        ]);

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
        $donation = Donation::with(['disaster.barangay'])
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
            'total_disasters' => Disaster::active()->count(),
            'total_affected_families' => Disaster::active()->sum('affected_families'),
            'total_donations' => Donation::confirmed()->sum('amount'),
            'total_donors' => Donation::confirmed()->distinct('donor_email')->count(),
            'barangays_affected' => Barangay::withActiveDisasters()->count(),
        ];

        return response()->json($stats);
    }

    /**
     * API endpoint to get all barangays with disasters
     */
    public function apiBarangays()
    {
        // CHANGED: currentDisaster.urgentNeeds → resourceNeeds
        $barangays = Barangay::with(['resourceNeeds' => function($query) {
            $query->where('status', '!=', 'fulfilled')
                  ->orderBy('urgency', 'desc');
        }])
        ->orderBy('name')
        ->get()
        ->map(function ($barangay) {
            return [
                'id' => $barangay->barangay_id,
                'name' => $barangay->name,
                'slug' => $barangay->slug,
                'status' => $barangay->disaster_status,
                'latitude' => $barangay->latitude,
                'longitude' => $barangay->longitude,
                'has_disaster' => $barangay->disaster_status !== 'safe',
                // CHANGED: urgent_needs → resource_needs
                'resource_needs' => $barangay->resourceNeeds->map(function($need) {
                    return [
                        'category' => $need->category,
                        'quantity' => $need->quantity,
                        'urgency' => $need->urgency,
                    ];
                }),
            ];
        });

        return response()->json($barangays);
    }
}
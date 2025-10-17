<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Models\ResourceNeed;
use App\Models\PhysicalDonation;
use App\Models\DistributionLog;
use App\Models\Barangay;
use App\Models\OnlineDonation;

class BarangayDashboardController extends Controller
{
    /**
     * Display BDRRMC Dashboard
     */
    public function index()
    {
        $userId = session('user_id');
        $barangayId = session('barangay_id');

        // Get barangay info
        $barangay = Barangay::where('barangay_id', $barangayId)->first();

        // Get updated statistics including online donations
        $stats = $this->calculateStats($barangayId);

        return view('UserDashboards.barangaydashboard', compact('barangay', 'stats'));
    }

    /**
     * Calculate statistics for the dashboard
     */
    private function calculateStats($barangayId)
    {
        $physicalCount = PhysicalDonation::where('barangay_id', $barangayId)->count();
        $physicalValue = PhysicalDonation::where('barangay_id', $barangayId)->sum('estimated_value');

        $onlineCount = OnlineDonation::where('target_barangay_id', $barangayId)
            ->where('verification_status', 'verified')->count();
        $onlineValue = OnlineDonation::where('target_barangay_id', $barangayId)
            ->where('verification_status', 'verified')->sum('amount');

        $activeRequests = ResourceNeed::where('barangay_id', $barangayId)
            ->where('status', 'pending')->count();

        $verifiedDonations = OnlineDonation::where('target_barangay_id', $barangayId)
            ->where('blockchain_status', 'confirmed')->count();

        $barangay = Barangay::where('barangay_id', $barangayId)->first();

        return [
            'affected_families' => $barangay->affected_families ?? 0,
            'total_donations' => $physicalCount + $onlineCount,
            'total_value' => $physicalValue + $onlineValue,
            'active_requests' => $activeRequests,
            'verified_donations' => $verifiedDonations,
            'physical_donations' => $physicalCount,
            'online_donations' => $onlineCount,
        ];
    }

    // ==================== RESOURCE NEEDS APIs ====================

    /**
     * Get all resource needs for the barangay
     */
    public function getNeeds()
    {
        $barangayId = session('barangay_id');

        $needs = ResourceNeed::where('barangay_id', $barangayId)
            ->orderBy('created_at', 'desc')
            ->get();

        return response()->json($needs);
    }

    /**
     * Create a new resource need
     */
    public function createNeed(Request $request)
    {
        $barangayId = session('barangay_id');

        $validated = $request->validate([
            'category' => 'required|in:food,water,medical,shelter,clothing,other',
            'description' => 'required|string|max:1000',
            'quantity' => 'required|string|max:100',
            'urgency' => 'required|in:low,medium,high,critical',
        ]);

        $need = ResourceNeed::create([
            'barangay_id' => $barangayId,
            'category' => $validated['category'],
            'description' => $validated['description'],
            'quantity' => $validated['quantity'],
            'urgency' => $validated['urgency'],
            'status' => 'pending',
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Resource need created successfully',
            'data' => $need
        ], 201);
    }

    /**
     * Update a resource need
     */
    public function updateNeed(Request $request, $id)
    {
        $barangayId = session('barangay_id');

        $need = ResourceNeed::where('id', $id)
            ->where('barangay_id', $barangayId)
            ->firstOrFail();

        $validated = $request->validate([
            'category' => 'sometimes|in:food,water,medical,shelter,clothing,other',
            'description' => 'sometimes|string|max:1000',
            'quantity' => 'sometimes|string|max:100',
            'urgency' => 'sometimes|in:low,medium,high,critical',
            'status' => 'sometimes|in:pending,partially_fulfilled,fulfilled',
        ]);

        $need->update($validated);

        return response()->json([
            'success' => true,
            'message' => 'Resource need updated successfully',
            'data' => $need
        ]);
    }

    /**
     * Delete a resource need
     */
    public function deleteNeed($id)
    {
        $barangayId = session('barangay_id');

        $need = ResourceNeed::where('id', $id)
            ->where('barangay_id', $barangayId)
            ->firstOrFail();

        $need->delete();

        return response()->json([
            'success' => true,
            'message' => 'Resource need deleted successfully'
        ]);
    }

    // ==================== PHYSICAL DONATIONS APIs ====================

    /**
     * Get all physical donations for the barangay
     */
    public function getPhysicalDonations()
    {
        $barangayId = session('barangay_id');

        $donations = PhysicalDonation::where('barangay_id', $barangayId)
            ->with(['recorder', 'distributions'])
            ->orderBy('recorded_at', 'desc')
            ->get();

        return response()->json($donations);
    }

    /**
     * Record a new physical donation
     */
    public function recordDonation(Request $request)
    {
        $barangayId = session('barangay_id');
        $userId = session('user_id');

        $validated = $request->validate([
            'donor_name' => 'required|string|max:100',
            'donor_contact' => 'required|string|max:20',
            'donor_email' => 'nullable|email|max:100',
            'donor_address' => 'required|string|max:500',
            'category' => 'required|in:food,water,medical,shelter,clothing,other',
            'items_description' => 'required|string|max:1000',
            'quantity' => 'required|string|max:100',
            'estimated_value' => 'nullable|numeric|min:0',
            'intended_recipients' => 'required|string|max:100',
            'notes' => 'nullable|string|max:500',
        ]);

        // Generate tracking code
        $trackingCode = PhysicalDonation::generateTrackingCode($barangayId);

        $donation = PhysicalDonation::create([
            'barangay_id' => $barangayId,
            'tracking_code' => $trackingCode,
            'donor_name' => $validated['donor_name'],
            'donor_contact' => $validated['donor_contact'],
            'donor_email' => $validated['donor_email'],
            'donor_address' => $validated['donor_address'],
            'category' => $validated['category'],
            'items_description' => $validated['items_description'],
            'quantity' => $validated['quantity'],
            'estimated_value' => $validated['estimated_value'],
            'intended_recipients' => $validated['intended_recipients'],
            'notes' => $validated['notes'],
            'distribution_status' => 'pending_distribution',
            'recorded_by' => $userId,
            'recorded_at' => now(),
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Donation recorded successfully',
            'data' => $donation,
            'tracking_code' => $trackingCode
        ], 201);
    }

    /**
     * Get single donation details
     */
    public function getDonationDetails($id)
    {
        $barangayId = session('barangay_id');

        $donation = PhysicalDonation::where('id', $id)
            ->where('barangay_id', $barangayId)
            ->with(['recorder', 'distributions.distributor'])
            ->firstOrFail();

        return response()->json($donation);
    }

    /**
     * Record distribution of a donation
     */
    public function recordDistribution(Request $request, $id)
    {
        $barangayId = session('barangay_id');
        $userId = session('user_id');

        $donation = PhysicalDonation::where('id', $id)
            ->where('barangay_id', $barangayId)
            ->firstOrFail();

        $validated = $request->validate([
            'distributed_to' => 'required|string|max:200',
            'quantity_distributed' => 'required|string|max:100',
            'notes' => 'nullable|string|max:500',
            'distribution_status' => 'required|in:partially_distributed,fully_distributed',
            'photo_urls' => 'required|array|min:5|max:5',
            'photo_urls.*' => 'required|string',
        ]);

        // Create distribution log
        $distributionLog = DistributionLog::create([
            'physical_donation_id' => $donation->id,
            'distributed_to' => $validated['distributed_to'],
            'quantity_distributed' => $validated['quantity_distributed'],
            'distributed_by' => $userId,
            'distributed_at' => now(),
            'notes' => $validated['notes'],
            'photo_urls' => $validated['photo_urls'],
        ]);

        // Update donation status
        $donation->update([
            'distribution_status' => $validated['distribution_status']
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Distribution recorded successfully',
            'data' => [
                'donation' => $donation,
                'distribution' => $distributionLog
            ]
        ]);
    }

    // ==================== ONLINE DONATIONS (READ-ONLY FOR BDRRMC) ====================

    /**
     * Get online donations for the barangay (READ-ONLY for BDRRMC)
     */
    public function getOnlineDonations()
    {
        $barangayId = session('barangay_id');

        $donations = OnlineDonation::where('target_barangay_id', $barangayId)
            ->with(['verifier'])
            ->orderBy('created_at', 'desc')
            ->get()
            ->map(function($donation) {
                return [
                    'id' => $donation->id,
                    'tracking_code' => $donation->tracking_code,
                    'donor_name' => $donation->getDonorDisplayName(),
                    'donor_email' => $donation->is_anonymous ? null : $donation->donor_email,
                    'amount' => $donation->amount,
                    'payment_method' => $donation->payment_method,
                    'verification_status' => $donation->verification_status,
                    'verified_at' => $donation->verified_at ? $donation->verified_at->format('M d, Y') : null,
                    'blockchain_status' => $donation->blockchain_status,
                    'blockchain_verified' => $donation->blockchain_status === 'confirmed',
                    'tx_hash' => $donation->tx_hash,
                    'blockchain_tx_hash' => $donation->blockchain_tx_hash,
                    'explorer_url' => $donation->explorer_url,
                    'created_at' => $donation->created_at->format('M d, Y H:i'),
                ];
            });

        // Calculate statistics
        $stats = [
            'total_online_donations' => $donations->sum('amount'),
            'total_count' => $donations->count(),
            'verified_count' => $donations->where('verification_status', 'verified')->count(),
            'blockchain_verified_count' => $donations->where('blockchain_verified', true)->count(),
            'pending_count' => $donations->where('verification_status', 'pending')->count(),
        ];

        return response()->json([
            'success' => true,
            'donations' => $donations,
            'statistics' => $stats,
        ]);
    }

    /**
     * Get updated statistics including online donations
     */
    public function getUpdatedStats()
    {
        $barangayId = session('barangay_id');
        $stats = $this->calculateStats($barangayId);

        return response()->json($stats);
    }

    // ==================== BARANGAY INFO ====================

    /**
     * Get barangay information
     */
    public function getBarangayInfo()
    {
        $barangayId = session('barangay_id');

        $barangay = Barangay::where('barangay_id', $barangayId)->firstOrFail();

        return response()->json($barangay);
    }

    /**
     * Update barangay information
     */
    public function updateBarangayInfo(Request $request)
    {
        try {
            $barangayId = session('barangay_id');

            $validated = $request->validate([
                'disaster_status' => 'required|in:safe,warning,critical,emergency',
                'disaster_type' => 'nullable|in:flood,fire,earthquake,typhoon,landslide,other',
                'affected_families' => 'required|integer|min:0',
                'needs_summary' => 'nullable|string|max:1000',
                'contact_person' => 'nullable|string|max:100',
                'contact_phone' => 'nullable|string|max:20',
            ]);

            $barangay = Barangay::where('barangay_id', $barangayId)->firstOrFail();

            // If status is safe, clear disaster_type and affected_families
            if ($validated['disaster_status'] === 'safe') {
                $validated['disaster_type'] = null;
                $validated['affected_families'] = 0;
                $validated['needs_summary'] = null;
            }

            $barangay->update($validated);

            return response()->json([
                'success' => true,
                'message' => 'Barangay information updated successfully',
                'data' => $barangay
            ]);

        } catch (\Illuminate\Validation\ValidationException $e) {
            return response()->json([
                'success' => false,
                'message' => 'Validation failed',
                'errors' => $e->errors()
            ], 422);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error updating barangay information',
                'error' => $e->getMessage()
            ], 500);
        }
    }
}

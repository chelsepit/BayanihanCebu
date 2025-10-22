<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Models\OnlineDonation;
use App\Models\PhysicalDonation;
use App\Models\Barangay;
use App\Models\Disaster;
use App\Models\ResourceNeed;

class DonationController extends Controller
{
    /**
     * Store a new online donation (from resident)
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'donor_name' => 'required|string|max:100',
            'donor_email' => 'nullable|email|max:100',
            'donor_phone' => 'nullable|string|max:20',
            'target_barangay_id' => 'required|exists:barangays,barangay_id',
            'disaster_id' => 'nullable|exists:disasters,id',
            'amount' => 'required|numeric|min:100',
            'payment_method' => 'required|in:gcash,paymaya,bank_transfer,metamask,crypto',
            'is_anonymous' => 'boolean',

            // For MetaMask/Crypto
            'tx_hash' => 'nullable|string|max:66',
            'wallet_address' => 'nullable|string|max:42',
            'explorer_url' => 'nullable|string',

            // For Manual Payment
            'payment_reference' => 'nullable|string|max:100',
            'payment_proof' => 'nullable|image|max:5120', // 5MB max
        ]);

        try {
            DB::beginTransaction();

            // Handle payment proof upload
            $paymentProofUrl = null;
            if ($request->hasFile('payment_proof')) {
                $file = $request->file('payment_proof');
                $filename = 'payment_proof_' . time() . '_' . uniqid() . '.' . $file->getClientOriginalExtension();
                $file->move(public_path('uploads/payment_proofs'), $filename);
                $paymentProofUrl = '/uploads/payment_proofs/' . $filename;
            }

            // Create donation record
            $donation = OnlineDonation::create([
                'donor_name' => $validated['donor_name'],
                'donor_email' => $validated['donor_email'] ?? null,
                'donor_phone' => $validated['donor_phone'] ?? null,
                'target_barangay_id' => $validated['target_barangay_id'],
                'disaster_id' => $validated['disaster_id'] ?? null,
                'amount' => $validated['amount'],
                'payment_method' => $validated['payment_method'],
                'is_anonymous' => $validated['is_anonymous'] ?? false,

                // MetaMask/Crypto
                'tx_hash' => $validated['tx_hash'] ?? null,
                'wallet_address' => $validated['wallet_address'] ?? null,
                'explorer_url' => $validated['explorer_url'] ?? null,

                // Manual Payment
                'payment_reference' => $validated['payment_reference'] ?? null,
                'payment_proof_url' => $paymentProofUrl,

                // Status
                'verification_status' => 'pending',
                'blockchain_status' => 'pending',
            ]);

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => 'Donation submitted successfully',
                'tracking_code' => $donation->tracking_code,
                'donation' => $donation,
            ], 201);

        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                'success' => false,
                'message' => 'Failed to process donation',
                'error' => $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Get all donations (for LDRRMO/Admin)
     */
    public function index(Request $request)
    {
        $query = OnlineDonation::with(['barangay', 'disaster', 'verifier'])
            ->orderBy('created_at', 'desc');

        if ($request->has('status')) {
            $query->where('verification_status', $request->status);
        }

        if ($request->has('barangay_id')) {
            $query->where('target_barangay_id', $request->barangay_id);
        }

        $donations = $query->paginate(20);

        return response()->json($donations);
    }

    /**
     * Get donations by barangay (for BDRRMC)
     */
    public function getByBarangay($barangayId)
    {
        $donations = OnlineDonation::where('target_barangay_id', $barangayId)
            ->with(['disaster', 'verifier'])
            ->orderBy('created_at', 'desc')
            ->get();

        return response()->json([
            'success' => true,
            'donations' => $donations,
        ]);
    }

    /**
     * Get pending verifications
     */
    public function getPendingVerifications(Request $request, $barangayId = null)
    {
        $query = OnlineDonation::where('verification_status', 'pending')
            ->with(['barangay', 'disaster']);

        if ($barangayId) {
            $query->where('target_barangay_id', $barangayId);
        }

        $donations = $query->orderBy('created_at', 'desc')->get();

        return response()->json([
            'success' => true,
            'donations' => $donations,
        ]);
    }

    /**
     * Verify donation (BDRRMC/LDRRMO)
     */
    public function verify(Request $request, $id)
    {
        $validated = $request->validate([
            'status' => 'required|in:verified,rejected',
            'rejection_reason' => 'required_if:status,rejected|string|max:500',
        ]);

        $donation = OnlineDonation::findOrFail($id);

        $donation->update([
            'verification_status' => $validated['status'],
            'verified_by' => session('user_id'),
            'verified_at' => now(),
            'rejection_reason' => $validated['rejection_reason'] ?? null,
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Donation ' . $validated['status'] . ' successfully',
            'donation' => $donation->fresh(['barangay', 'disaster', 'verifier']),
        ]);
    }

    /**
     * Get resident's own donations
     */
    public function myDonations(Request $request)
    {
        $userId = session('user_id');
        $email = session('email');

        $donations = OnlineDonation::where(function($query) use ($userId, $email) {
                $query->where('user_id', $userId)
                      ->orWhere('donor_email', $email);
            })
            ->with(['barangay', 'disaster'])
            ->orderBy('created_at', 'desc')
            ->get()
            ->map(function ($donation) {
                return [
                    'tracking_code' => $donation->tracking_code,
                    'barangay' => $donation->barangay->name,
                    'disaster' => $donation->disaster ? $donation->disaster->title : 'General Donation',
                    'amount' => $donation->amount,
                    'payment_method' => $donation->payment_method,
                    'verification_status' => $donation->verification_status,
                    'blockchain_status' => $donation->blockchain_status,
                    'blockchain_verified' => $donation->blockchain_status === 'confirmed',
                    'explorer_url' => $donation->explorer_url,
                    'created_at' => $donation->created_at->format('M d, Y'),
                ];
            });

        return response()->json([
            'success' => true,
            'donations' => $donations,
            'total_donated' => $donations->sum('amount'),
            'total_donations' => $donations->count(),
        ]);
    }

    /**
     * Get resident statistics
     */
    public function getResidentStats(Request $request)
    {
        $userId = session('user_id');
        $email = session('email');

        $stats = [
            'total_donations' => OnlineDonation::where(function($query) use ($userId, $email) {
                    $query->where('user_id', $userId)
                          ->orWhere('donor_email', $email);
                })->count(),
            'total_amount' => OnlineDonation::where(function($query) use ($userId, $email) {
                    $query->where('user_id', $userId)
                          ->orWhere('donor_email', $email);
                })->sum('amount'),
            'verified_donations' => OnlineDonation::where(function($query) use ($userId, $email) {
                    $query->where('user_id', $userId)
                          ->orWhere('donor_email', $email);
                })->where('verification_status', 'verified')->count(),
        ];

        return response()->json($stats);
    }

    /**
     * Get urgent needs for donation page
     */
    public function getUrgentNeeds()
    {
        $needs = ResourceNeed::where('urgency', 'critical')
            ->orWhere('urgency', 'high')
            ->with('barangay')
            ->orderBy('urgency', 'desc')
            ->orderBy('created_at', 'desc')
            ->limit(10)
            ->get();

        return response()->json([
            'success' => true,
            'needs' => $needs,
        ]);
    }

    /**
     * Track donation by tracking code (PUBLIC - no login required)
     * ✅ NOW SUPPORTS BOTH ONLINE AND PHYSICAL DONATIONS
     */
    public function track(Request $request)
    {
        $validated = $request->validate([
            'tracking_code' => 'required|string'
        ]);

        $trackingCode = $validated['tracking_code'];

        // Try to find in online donations first
        $onlineDonation = OnlineDonation::where('tracking_code', $trackingCode)
            ->with(['barangay', 'disaster', 'verifier'])
            ->first();

        if ($onlineDonation) {
            return response()->json([
                'success' => true,
                'donation_type' => 'online',
                'donation' => [
                    'tracking_code' => $onlineDonation->tracking_code,
                    'donor_name' => $onlineDonation->getDonorDisplayName(),
                    'barangay_name' => $onlineDonation->barangay->name,
                    'disaster_title' => $onlineDonation->disaster ? $onlineDonation->disaster->title : 'General Donation',
                    'amount' => $onlineDonation->amount,
                    'payment_method' => $onlineDonation->payment_method,
                    'verification_status' => $onlineDonation->verification_status,
                    'verified_at' => $onlineDonation->verified_at ? $onlineDonation->verified_at->format('M d, Y h:i A') : null,
                    'verified_by' => $onlineDonation->verifier ? $onlineDonation->verifier->full_name : null,
                    'blockchain_status' => $onlineDonation->blockchain_status,
                    'blockchain_tx_hash' => $onlineDonation->blockchain_tx_hash,
                    'blockchain_recorded_at' => $onlineDonation->blockchain_recorded_at ? $onlineDonation->blockchain_recorded_at->format('M d, Y h:i A') : null,
                    'explorer_url' => $onlineDonation->explorer_url,
                    'created_at' => $onlineDonation->created_at->format('M d, Y h:i A'),
                ],
            ]);
        }

        // If not found in online donations, try physical donations
        $physicalDonation = PhysicalDonation::where('tracking_code', $trackingCode)
            ->with(['barangay', 'recorder', 'distributions.distributor'])
            ->first();

        if ($physicalDonation) {
            return response()->json([
                'success' => true,
                'donation_type' => 'physical',
                'donation' => [
                    'tracking_code' => $physicalDonation->tracking_code,
                    'donor_name' => $physicalDonation->donor_name,
                    'donor_contact' => $physicalDonation->donor_contact,
                    'donor_email' => $physicalDonation->donor_email,
                    'barangay_name' => $physicalDonation->barangay->name,
                    'category' => ucfirst($physicalDonation->category),
                    'items_description' => $physicalDonation->items_description,
                    'quantity' => $physicalDonation->quantity,
                    'estimated_value' => $physicalDonation->estimated_value,
                    'intended_recipients' => $physicalDonation->intended_recipients,
                    'distribution_status' => $physicalDonation->distribution_status,
                    'recorded_by' => $physicalDonation->recorder ? $physicalDonation->recorder->full_name : null,
                    'recorded_at' => $physicalDonation->recorded_at->format('M d, Y h:i A'),
                    'blockchain_status' => $physicalDonation->blockchain_status ?? 'pending',
                    'blockchain_tx_hash' => $physicalDonation->blockchain_tx_hash ?? null,
                    'blockchain_recorded_at' => $physicalDonation->blockchain_recorded_at ? $physicalDonation->blockchain_recorded_at->format('M d, Y h:i A') : null,
                    'ipfs_hash' => $physicalDonation->ipfs_hash ?? null,
                    'distributions' => $physicalDonation->distributions->map(function($dist) {
                        return [
                            'id' => $dist->id,
                            'distributed_to' => $dist->distributed_to,
                            'quantity_distributed' => $dist->quantity_distributed,
                            'distributed_by' => $dist->distributor ? $dist->distributor->full_name : null,
                            'distributed_at' => $dist->distributed_at->format('M d, Y h:i A'),
                            'notes' => $dist->notes,
                            'has_photos' => !empty($dist->photo_urls),
                        ];
                    }),
                    'created_at' => $physicalDonation->created_at->format('M d, Y h:i A'),
                ],
            ]);
        }

        $stats = [
            'total_donated' => OnlineDonation::where('donor_email', $userEmail)->sum('amount'),
            'total_donations' => OnlineDonation::where('donor_email', $userEmail)->count(),
            'verified_donations' => OnlineDonation::where('donor_email', $userEmail)
                ->where('verification_status', 'verified')->count(),
            'blockchain_verified' => OnlineDonation::where('donor_email', $userEmail)
                ->where('blockchain_status', 'confirmed')->count(),
            'families_helped' => OnlineDonation::where('donor_email', $userEmail)
                ->where('verification_status', 'verified')
                ->join('barangays', 'online_donations.target_barangay_id', '=', 'barangays.barangay_id')
                ->sum('barangays.affected_families'),
        ];

        return response()->json($stats);
    }

}

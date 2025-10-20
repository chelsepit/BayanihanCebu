<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Models\OnlineDonation;
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

                // Auto-verify MetaMask/Crypto, others need manual verification
                'verification_status' => in_array($validated['payment_method'], ['metamask', 'crypto']) ? 'verified' : 'pending',
                'verified_at' => in_array($validated['payment_method'], ['metamask', 'crypto']) ? now() : null,
            ]);

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => 'Donation recorded successfully',
                'data' => [
                    'tracking_code' => $donation->tracking_code,
                    'donation_id' => $donation->id,
                    'verification_status' => $donation->verification_status,
                ]
            ], 201);

        } catch (\Exception $e) {
            DB::rollBack();

            return response()->json([
                'success' => false,
                'message' => 'Failed to record donation',
                'error' => $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Get all donations (for admin/LDRRMO)
     */
    public function index()
    {
        $donations = OnlineDonation::with(['barangay', 'disaster', 'verifier'])
            ->orderBy('created_at', 'desc')
            ->get()
            ->map(function($donation) {
                return [
                    'id' => $donation->id,
                    'tracking_code' => $donation->tracking_code,
                    'donor_name' => $donation->getDonorDisplayName(),
                    'donor_email' => $donation->is_anonymous ? null : $donation->donor_email,
                    'barangay_name' => $donation->barangay->name,
                    'amount' => $donation->amount,
                    'payment_method' => $donation->payment_method,
                    'verification_status' => $donation->verification_status,
                    'blockchain_status' => $donation->blockchain_status,
                    'tx_hash' => $donation->tx_hash,
                    'blockchain_tx_hash' => $donation->blockchain_tx_hash,
                    'explorer_url' => $donation->explorer_url,
                    'created_at' => $donation->created_at->format('Y-m-d H:i:s'),
                ];
            });

        return response()->json($donations);
    }

    /**
     * Get donations by barangay (for BDRRMC)
     */
    public function getByBarangay($barangayId)
    {
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
                    'blockchain_status' => $donation->blockchain_status,
                    'tx_hash' => $donation->tx_hash,
                    'blockchain_tx_hash' => $donation->blockchain_tx_hash,
                    'explorer_url' => $donation->explorer_url,
                    'created_at' => $donation->created_at->format('Y-m-d H:i:s'),
                ];
            });

        return response()->json($donations);
    }

    /**
     * Get resident's donation history (for logged-in resident)
     */
    public function myDonations(Request $request)
    {
        $userEmail = session('user_email');

        $donations = OnlineDonation::where('donor_email', $userEmail)
            ->with(['barangay', 'disaster'])
            ->orderBy('created_at', 'desc')
            ->get()
            ->map(function($donation) {
                return [
                    'id' => $donation->id,
                    'tracking_code' => $donation->tracking_code,
                    'barangay_name' => $donation->barangay->name,
                    'disaster_title' => $donation->disaster ? $donation->disaster->title : 'General Donation',
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
     * Track donation by tracking code (PUBLIC - no login required)
     */
    public function track(Request $request)
    {
        $validated = $request->validate([
            'tracking_code' => 'required|string'
        ]);

        $donation = OnlineDonation::where('tracking_code', $validated['tracking_code'])
            ->with(['barangay', 'disaster', 'verifier'])
            ->first();

        if (!$donation) {
            return response()->json([
                'success' => false,
                'message' => 'Donation not found with this tracking code'
            ], 404);
        }

        return response()->json([
            'success' => true,
            'donation' => [
                'tracking_code' => $donation->tracking_code,
                'donor_name' => $donation->getDonorDisplayName(),
                'barangay_name' => $donation->barangay->name,
                'disaster_title' => $donation->disaster ? $donation->disaster->title : 'General Donation',
                'amount' => $donation->amount,
                'payment_method' => $donation->payment_method,
                'verification_status' => $donation->verification_status,
                'verified_at' => $donation->verified_at ? $donation->verified_at->format('M d, Y H:i') : null,
                'blockchain_status' => $donation->blockchain_status,
                'blockchain_verified' => $donation->blockchain_status === 'confirmed',
                'tx_hash' => $donation->tx_hash,
                'blockchain_tx_hash' => $donation->blockchain_tx_hash,
                'explorer_url' => $donation->explorer_url,
                'created_at' => $donation->created_at->format('M d, Y H:i'),
            ]
        ]);
    }

    /**
     * Get pending donations for verification (BDRRMC/LDRRMO)
     */
    public function getPendingVerifications($barangayId = null)
    {
        $query = OnlineDonation::where('verification_status', 'pending')
            ->whereIn('payment_method', ['gcash', 'paymaya', 'bank_transfer'])
            ->with(['barangay']);

        if ($barangayId) {
            $query->where('target_barangay_id', $barangayId);
        }

        $donations = $query->orderBy('created_at', 'asc')->get();

        return response()->json([
            'success' => true,
            'pending_count' => $donations->count(),
            'donations' => $donations->map(function($donation) {
                return [
                    'id' => $donation->id,
                    'tracking_code' => $donation->tracking_code,
                    'donor_name' => $donation->donor_name,
                    'donor_email' => $donation->donor_email,
                    'donor_phone' => $donation->donor_phone,
                    'barangay_name' => $donation->barangay->name,
                    'amount' => $donation->amount,
                    'payment_method' => $donation->payment_method,
                    'payment_reference' => $donation->payment_reference,
                    'payment_proof_url' => $donation->payment_proof_url,
                    'created_at' => $donation->created_at->format('M d, Y H:i'),
                ];
            })
        ]);
    }

    /**
     * Verify donation (BDRRMC/LDRRMO only)
     */
    public function verify(Request $request, $id)
    {
        $validated = $request->validate([
            'action' => 'required|in:approve,reject',
            'rejection_reason' => 'required_if:action,reject|string|max:500'
        ]);

        $donation = OnlineDonation::findOrFail($id);

        if ($donation->verification_status !== 'pending') {
            return response()->json([
                'success' => false,
                'message' => 'This donation has already been processed'
            ], 400);
        }

        try {
            if ($validated['action'] === 'approve') {
                $donation->update([
                    'verification_status' => 'verified',
                    'verified_by' => session('user_id'),
                    'verified_at' => now(),
                ]);

                $message = 'Donation verified successfully';
            } else {
                $donation->update([
                    'verification_status' => 'rejected',
                    'verified_by' => session('user_id'),
                    'verified_at' => now(),
                    'rejection_reason' => $validated['rejection_reason'],
                ]);

                $message = 'Donation rejected';
            }

            // TODO: Send email notification to donor

            return response()->json([
                'success' => true,
                'message' => $message
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error processing verification',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Get donation statistics for resident
     */
    public function getResidentStats()
    {
        $userEmail = session('user_email');

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

    /**
     * Get all urgent needs for resident dashboard
     */
   /**
 * Get all urgent needs for resident dashboard
 */
public function getUrgentNeeds()
{
    $needs = ResourceNeed::with(['barangay'])
        ->whereIn('urgency', ['high', 'critical'])
        ->where('status', '!=', 'fulfilled')
        ->orderByRaw("FIELD(urgency, 'critical', 'high')")
        ->orderBy('created_at', 'desc')
        ->limit(10)
        ->get()
        ->map(function($need) {
            return [
                'id' => $need->id,
                'barangay_id' => $need->barangay_id,
                'barangay_name' => $need->barangay->name,
                'category' => $need->category,
                'description' => $need->description,
                'quantity' => $need->quantity,
                'urgency' => $need->urgency,
                'affected_families' => $need->barangay->affected_families,
                'disaster_status' => $need->barangay->disaster_status,
            ];
        });

    return response()->json($needs);
}
}

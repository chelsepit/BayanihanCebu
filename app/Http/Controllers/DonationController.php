<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class DonationController extends Controller
{
    public function store(Request $request)
    {
        // ✅ Validate input
        $validated = $request->validate([
            'donor_name' => 'required|string|max:100',
            'donor_email' => 'nullable|email|max:100',
            'target_barangay_id' => 'required|exists:barangays,barangay_id',
            'amount' => 'required|numeric|min:0',
            'payment_method' => 'required|in:gcash,metamask,bank_transfer',
            'tx_hash' => 'nullable|string|max:66',
            'wallet_address' => 'nullable|string|max:42',
            'blockchain_status' => 'required|in:pending,confirmed,failed',
            'explorer_url' => 'nullable|string'
        ]);

        try {
            // Insert donation record
            DB::table('online_donations')->insert([
                'donor_name' => $validated['donor_name'],
                'donor_email' => $validated['donor_email'] ?? null,
                'target_barangay_id' => $validated['target_barangay_id'],
                'amount' => $validated['amount'],
                'payment_method' => $validated['payment_method'],
                'tx_hash' => $validated['tx_hash'] ?? null,
                'wallet_address' => $validated['wallet_address'] ?? null,
                'blockchain_status' => $validated['blockchain_status'],
                'explorer_url' => $validated['explorer_url'] ?? null,
                'created_at' => now(),
                'updated_at' => now(),
            ]);

            return response()->json([
                'success' => true,
                'message' => 'Donation recorded successfully',
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to record donation',
                'error' => $e->getMessage(),
            ], 500);
        }
    }

    public function index()
    {
        // ✅ Retrieve all donations with barangay names
        $donations = DB::table('online_donations')
            ->join('barangays', 'online_donations.target_barangay_id', '=', 'barangays.barangay_id')
            ->select('online_donations.*', 'barangays.name as barangay_name')
            ->orderBy('online_donations.created_at', 'desc')
            ->get();

        return response()->json($donations);
    }

    public function getByBarangay($barangayId)
    {
        // ✅ Retrieve all donations for a specific barangay
        $donations = DB::table('online_donations')
            ->where('target_barangay_id', $barangayId)
            ->orderBy('created_at', 'desc')
            ->get();

        return response()->json($donations);
    }
}

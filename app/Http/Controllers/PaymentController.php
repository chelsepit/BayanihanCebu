<?php

namespace App\Http\Controllers;

use App\Jobs\RecordDonationOnChain;
use App\Models\Donation;
use App\Services\PaymongoService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;

class PaymentController extends Controller
{
    /**
     * Create PayMongo payment source
     */
    public function createPaymentSource(Request $request, PaymongoService $paymongo)
    {
        $validated = $request->validate([
            'barangay_id' => 'required|exists:barangays,barangay_id',
            'amount' => 'required|numeric|min:100',
            'donation_type' => 'required|in:monetary,in-kind',
            'payment_method' => 'required|in:gcash,paymaya,grab_pay,card',
            'donor_name' => 'required|string|max:255',
            'donor_email' => 'nullable|email',
            'donor_phone' => 'nullable|string',
        ]);

        try {
            // Create donation record first (status: pending)
            $donation = Donation::create([
                'barangay_id' => $validated['barangay_id'],
                'user_id' => Auth::id(), // Fixed: changed from donor_id to user_id
                'amount' => $validated['amount'],
                'donation_type' => $validated['donation_type'],
                'donor_name' => $validated['donor_name'],
                'donor_email' => $validated['donor_email'],
                'donor_phone' => $validated['donor_phone'],
                'payment_status' => 'pending',
                'status' => 'pending',
                'tracking_code' => $this->generateTrackingCode(),
                'is_anonymous' => false,
            ]);

            // Prepare billing information (only include non-empty values)
            $billing = ['name' => $validated['donor_name']];

            if (!empty($validated['donor_email'])) {
                $billing['email'] = $validated['donor_email'];
            }

            if (!empty($validated['donor_phone'])) {
                $billing['phone'] = $validated['donor_phone'];
            }

            // Create PayMongo Source
            $sourceData = $paymongo->createSource([
                'type' => $validated['payment_method'],
                'amount' => $validated['amount'] * 100, // Convert to centavos
                'currency' => 'PHP',
                'redirect' => [
                    'success' => route('payment.success', ['donation_id' => $donation->id]),
                    'failed' => route('payment.failed', ['donation_id' => $donation->id]),
                ],
                'billing' => $billing,
            ]);

            // Save PayMongo source ID
            $donation->update([
                'payment_source_id' => $sourceData['id'],
            ]);

            return response()->json([
                'success' => true,
                'data' => [
                    'donation_id' => $donation->id,
                    'tracking_code' => $donation->tracking_code,
                    'checkout_url' => $sourceData['attributes']['redirect']['checkout_url'] ?? null,
                ],
            ]);

        } catch (\Exception $e) {
            Log::error('Payment source creation failed: ' . $e->getMessage());

            return response()->json([
                'success' => false,
                'message' => 'Failed to create payment. Please try again.',
                'error' => $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Handle successful payment
     */
    public function success(Request $request)
    {
        $donationId = $request->query('donation_id');
        $donation = Donation::findOrFail($donationId);

        // Update payment status
        $donation->update([
            'payment_status' => 'paid',
            'status' => 'confirmed',
        ]);

        // Dispatch blockchain recording job
        RecordDonationOnChain::dispatch($donation);

        Log::info('Payment successful, blockchain recording job dispatched', [
            'donation_id' => $donation->id,
            'tracking_code' => $donation->tracking_code
        ]);

        // Redirect back to dashboard with success indicator
        return redirect('/resident-dashboard?donation_id=' . $donationId . '#donations');
    }

    /**
     * Handle failed payment
     */
    public function failed(Request $request)
    {
        $donationId = $request->query('donation_id');
        $donation = Donation::findOrFail($donationId);

        $donation->update([
            'payment_status' => 'failed',
            'status' => 'failed',
        ]);

        return redirect('/resident-dashboard')->with('error', 'Payment failed. Please try again.');
    }

    /**
     * Generate unique tracking code
     */
    private function generateTrackingCode()
    {
        do {
            $code = 'DON-' . strtoupper(substr(uniqid(), -8));
        } while (Donation::where('tracking_code', $code)->exists());

        return $code;
    }
}

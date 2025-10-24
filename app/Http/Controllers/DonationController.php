<?php

namespace App\Http\Controllers;

use App\Models\Donation;
use App\Models\Barangay;
use App\Services\PaymongoService;
use App\Jobs\RecordDonationOnChain;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\DB;

class DonationController extends Controller
{
    protected $paymongo;

    public function __construct(PaymongoService $paymongo)
    {
        $this->paymongo = $paymongo;
    }

    /**
     * Create payment checkout session (Step 1: User initiates payment)
     */
    public function createPayment(Request $request)
    {
        $validated = $request->validate([
            'barangay_id' => 'required|exists:barangays,barangay_id',
            'amount' => 'required|numeric|min:100',
            'donation_type' => 'required|in:monetary', // Only monetary donations accepted
            'payment_method' => 'required|in:gcash,paymaya,grab_pay',
            'donor_name' => 'required|string|max:255',
            'donor_email' => 'nullable|email',
            'donor_phone' => 'nullable|string',
            'is_anonymous' => 'boolean',
        ]);

        // CRITICAL: Ensure user is authenticated (using custom session-based auth)
        if (!session('authenticated') || !session('user_id')) {
            return response()->json([
                'success' => false,
                'message' => 'You must be logged in to make a donation.',
                'redirect' => route('login')
            ], 401);
        }

        $userId = session('user_id');

        Log::info('Creating donation for authenticated user', [
            'user_id' => $userId,
            'donor_name' => $validated['donor_name'],
        ]);

        DB::beginTransaction();
        try {
            // Generate tracking code
            $trackingCode = $this->generateTrackingCode();

            // Create donation record (status: pending, payment_status: pending)
            $donation = Donation::create([
                'barangay_id' => $validated['barangay_id'],
                'user_id' => $userId, // CRITICAL: Use verified user_id
                'tracking_code' => $trackingCode,
                'amount' => $validated['amount'],
                'donation_type' => $validated['donation_type'],
                'donor_name' => $validated['donor_name'],
                'donor_email' => $validated['donor_email'] ?? null,
                'donor_phone' => $validated['donor_phone'] ?? null,
                'is_anonymous' => $validated['is_anonymous'] ?? false,
                'payment_status' => 'pending',
                'status' => 'pending',
            ]);

            // Get barangay info
            $barangay = Barangay::where('barangay_id', $validated['barangay_id'])->first();

            // Create PayMongo Checkout Session
            $checkoutData = $this->paymongo->createCheckoutSession([
                'description' => "Donation to {$barangay->name}",
                'line_items' => [
                    [
                        'name' => "Donation to {$barangay->name}",
                        'quantity' => 1,
                        'amount' => intval($validated['amount'] * 100), // Convert to centavos
                        'currency' => 'PHP',
                    ]
                ],
                'payment_method_types' => [$validated['payment_method']],
                'success_url' => route('donations.success') . '?session_id={CHECKOUT_SESSION_ID}',
                'cancel_url' => route('donations.cancel'),
                'reference_number' => $trackingCode,
                'metadata' => [
                    'donation_id' => $donation->id,
                    'tracking_code' => $trackingCode,
                    'barangay_id' => $validated['barangay_id'],
                    'barangay_name' => $barangay->name,
                ],
            ]);

            // Save checkout session ID
            $donation->update([
                'payment_session_id' => $checkoutData['id'],
                'checkout_url' => $checkoutData['attributes']['checkout_url'],
            ]);

            DB::commit();

            Log::info('Payment checkout session created', [
                'donation_id' => $donation->id,
                'tracking_code' => $trackingCode,
                'session_id' => $checkoutData['id'],
            ]);

            return response()->json([
                'success' => true,
                'data' => [
                    'donation_id' => $donation->id,
                    'tracking_code' => $trackingCode,
                    'checkout_url' => $checkoutData['attributes']['checkout_url'],
                    'session_id' => $checkoutData['id'],
                ],
            ]);

        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Payment creation failed', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Failed to create payment. Please try again.',
                'error' => config('app.debug') ? $e->getMessage() : 'Payment processing error',
            ], 500);
        }
    }

    /**
     * Create payment checkout session for PUBLIC/ANONYMOUS users (NO AUTH REQUIRED)
     */
    public function createPaymentPublic(Request $request)
    {
        $validated = $request->validate([
            'barangay_id' => 'required|exists:barangays,barangay_id',
            'amount' => 'required|numeric|min:100',
            'donation_type' => 'required|in:monetary',
            'payment_method' => 'required|in:gcash,paymaya,grab_pay',
            'donor_name' => 'required|string|max:255',
            'donor_email' => 'nullable|email',
            'donor_phone' => 'nullable|string',
            'is_anonymous' => 'boolean',
        ]);

        Log::info('Creating PUBLIC donation (no auth required)', [
            'donor_name' => $validated['donor_name'],
            'is_anonymous' => $validated['is_anonymous'] ?? false,
        ]);

        DB::beginTransaction();
        try {
            // Generate tracking code
            $trackingCode = $this->generateTrackingCode();

            // Create donation record WITHOUT user_id (public/anonymous donation)
            $donation = Donation::create([
                'barangay_id' => $validated['barangay_id'],
                'user_id' => null, // NULL for anonymous/public donations
                'tracking_code' => $trackingCode,
                'amount' => $validated['amount'],
                'donation_type' => $validated['donation_type'],
                'donor_name' => $validated['donor_name'],
                'donor_email' => $validated['donor_email'] ?? null,
                'donor_phone' => $validated['donor_phone'] ?? null,
                'is_anonymous' => $validated['is_anonymous'] ?? false,
                'payment_status' => 'pending',
                'status' => 'pending',
            ]);

            // Get barangay info
            $barangay = Barangay::where('barangay_id', $validated['barangay_id'])->first();

            // Create PayMongo Checkout Session
            $checkoutData = $this->paymongo->createCheckoutSession([
                'description' => "Donation to {$barangay->name}",
                'line_items' => [
                    [
                        'name' => "Donation to {$barangay->name}",
                        'quantity' => 1,
                        'amount' => intval($validated['amount'] * 100), // Convert to centavos
                        'currency' => 'PHP',
                    ]
                ],
                'payment_method_types' => [$validated['payment_method']],
                'success_url' => route('donations.success') . '?session_id={CHECKOUT_SESSION_ID}',
                'cancel_url' => url('/') . '#track',
                'reference_number' => $trackingCode,
                'metadata' => [
                    'donation_id' => $donation->id,
                    'tracking_code' => $trackingCode,
                    'barangay_id' => $validated['barangay_id'],
                    'barangay_name' => $barangay->name,
                    'is_anonymous' => $validated['is_anonymous'] ?? false,
                ],
            ]);

            // Save checkout session ID
            $donation->update([
                'payment_session_id' => $checkoutData['id'],
                'checkout_url' => $checkoutData['attributes']['checkout_url'],
            ]);

            DB::commit();

            Log::info('Public payment checkout session created', [
                'donation_id' => $donation->id,
                'tracking_code' => $trackingCode,
                'session_id' => $checkoutData['id'],
            ]);

            return response()->json([
                'success' => true,
                'data' => [
                    'donation_id' => $donation->id,
                    'tracking_code' => $trackingCode,
                    'checkout_url' => $checkoutData['attributes']['checkout_url'],
                    'session_id' => $checkoutData['id'],
                ],
            ]);

        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Public payment creation failed', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Failed to create payment. Please try again.',
                'error' => config('app.debug') ? $e->getMessage() : 'Payment processing error',
            ], 500);
        }
    }

    /**
     * Handle successful payment redirect (Step 2: User returned after payment)
     */
    public function success(Request $request)
    {
        $sessionId = $request->query('session_id');

        // Handle case where PayMongo sends placeholder instead of actual session ID
        // This happens in test mode - check all recent pending donations
        if (!$sessionId || $sessionId === '{CHECKOUT_SESSION_ID}') {
            Log::info('PayMongo redirect without session ID - checking ALL recent pending donations');

            // Find ALL recent pending donations (last 10 minutes)
            $recentDonations = Donation::where('payment_status', 'pending')
                ->whereNotNull('payment_session_id')
                ->where('created_at', '>', now()->subMinutes(10))
                ->orderBy('created_at', 'desc')
                ->get();

            Log::info('Found ' . $recentDonations->count() . ' pending donations to check');

            // Check each donation with PayMongo to find the paid one
            foreach ($recentDonations as $donation) {
                try {
                    $session = $this->paymongo->getCheckoutSession($donation->payment_session_id);

                    // Check if payment was completed
                    $payments = $session['attributes']['payments'] ?? [];
                    $paymentIntent = $session['attributes']['payment_intent'] ?? null;

                    $isPaid = false;
                    $paymentId = null;

                    // Check payments array
                    if (!empty($payments) && isset($payments[0]['attributes']['status'])) {
                        $isPaid = $payments[0]['attributes']['status'] === 'paid';
                        $paymentId = $payments[0]['id'] ?? null;
                    }

                    // Or check payment intent status
                    if (!$isPaid && $paymentIntent && isset($paymentIntent['attributes']['status'])) {
                        $isPaid = $paymentIntent['attributes']['status'] === 'succeeded';
                    }

                    if ($isPaid) {
                        // Found the paid donation!
                        $donation->update([
                            'payment_status' => 'paid',
                            'status' => 'confirmed',
                            'payment_id' => $paymentId,
                            'paid_at' => now(),
                        ]);

                        Log::info('✅ Payment confirmed via fallback check', [
                            'donation_id' => $donation->id,
                            'tracking_code' => $donation->tracking_code,
                            'amount' => $donation->amount,
                        ]);

                        // Dispatch blockchain recording job
                        RecordDonationOnChain::dispatch($donation);

                        // Redirect based on whether this is an authenticated user or anonymous donor
                        if ($donation->user_id) {
                            // Authenticated user - redirect to their dashboard
                            return redirect()->route('resident.dashboard')
                                ->with('success', "Thank you for your PHP {$donation->amount} donation! Tracking Code: {$donation->tracking_code}")
                                ->with('show_donations_tab', true);
                        } else {
                            // Anonymous donor - redirect to homepage with tracking code
                            return redirect('/#track')
                                ->with('success', "Thank you for your PHP {$donation->amount} donation!")
                                ->with('tracking_code', $donation->tracking_code)
                                ->with('show_tracking', true);
                        }
                    }
                } catch (\Exception $e) {
                    Log::error('Error checking donation session', [
                        'donation_id' => $donation->id,
                        'error' => $e->getMessage(),
                    ]);
                    continue;
                }
            }

            // If we get here, no paid donation found
            Log::warning('No paid donation found in recent pending donations');

            // Check if user is authenticated
            if (session('authenticated')) {
                return redirect()->route('resident.dashboard')
                    ->with('info', 'Payment verification in progress. Please refresh the page in a moment.')
                    ->with('show_donations_tab', true);
            } else {
                return redirect('/#track')
                    ->with('info', 'Payment verification in progress. Check your email for tracking code.');
            }
        }

        try {
            // Retrieve checkout session from PayMongo
            $session = $this->paymongo->getCheckoutSession($sessionId);

            // Find donation by session ID
            $donation = Donation::where('payment_session_id', $sessionId)->first();

            if (!$donation) {
                Log::error('Donation not found for session', ['session_id' => $sessionId]);

                // Redirect based on auth status
                if (session('authenticated')) {
                    return redirect()->route('resident.dashboard')->with('error', 'Donation record not found');
                } else {
                    return redirect('/#track')->with('error', 'Donation record not found. Please contact support.');
                }
            }

            // Check payment status
            $paymentStatus = $session['attributes']['payment_status'] ?? 'unpaid';

            if ($paymentStatus === 'paid') {
                // Payment confirmed - update donation
                $donation->update([
                    'payment_status' => 'paid',
                    'status' => 'confirmed',
                    'payment_id' => $session['attributes']['payments'][0]['id'] ?? null,
                    'paid_at' => now(),
                ]);

                Log::info('✅ Payment confirmed via success page', [
                    'donation_id' => $donation->id,
                    'tracking_code' => $donation->tracking_code,
                    'amount' => $donation->amount,
                ]);

                // Dispatch blockchain recording job
                RecordDonationOnChain::dispatch($donation);

                // Redirect based on whether this is an authenticated user or anonymous donor
                if ($donation->user_id) {
                    // Authenticated user - redirect to their dashboard
                    return redirect()->route('resident.dashboard')
                        ->with('success', "Thank you for your PHP {$donation->amount} donation! Tracking Code: {$donation->tracking_code}")
                        ->with('show_donations_tab', true);
                } else {
                    // Anonymous donor - redirect to homepage with tracking code
                    return redirect('/#track')
                        ->with('success', "Thank you for your PHP {$donation->amount} donation!")
                        ->with('tracking_code', $donation->tracking_code)
                        ->with('show_tracking', true);
                }
            }

            // Payment not yet confirmed
            if ($donation->user_id) {
                return redirect()->route('resident.dashboard')
                    ->with('info', 'Payment is being processed. Please check back in a few minutes.')
                    ->with('show_donations_tab', true);
            } else {
                return redirect('/#track')
                    ->with('info', 'Payment is being processed. Use your tracking code to check status.');
            }

        } catch (\Exception $e) {
            Log::error('Success page error', [
                'session_id' => $sessionId,
                'error' => $e->getMessage(),
            ]);

            // Redirect based on auth status
            if (session('authenticated')) {
                return redirect()->route('resident.dashboard')
                    ->with('info', 'Unable to verify payment status. If you completed payment, it will be confirmed shortly.')
                    ->with('show_donations_tab', true);
            } else {
                return redirect('/#track')
                    ->with('info', 'Unable to verify payment status. If you completed payment, check back in a few minutes.');
            }
        }
    }

    /**
     * Handle cancelled payment
     */
    public function cancel(Request $request)
    {
        return redirect()->route('resident.dashboard')->with('info', 'Payment was cancelled. You can try again anytime.');
    }

    /**
     * PayMongo Webhook Handler (Step 3: Automatic payment confirmation)
     * This is the key to automatic Lisk recording
     */
    public function webhook(    Request $request)
    {
        try {
            // Log ALL webhook requests for debugging
            Log::info('🔔 PayMongo webhook RECEIVED', [
                'headers' => $request->headers->all(),
                'body' => $request->all(),
            ]);

            // Verify webhook signature (skip if no secret configured for testing)
            $signature = $request->header('Paymongo-Signature');
            $webhookSecret = config('services.paymongo.webhook_secret');

            if ($webhookSecret && $signature) {
                $payload = $request->getContent();
                $expectedSignature = hash_hmac('sha256', $payload, $webhookSecret);

                if (!hash_equals($expectedSignature, $signature)) {
                    Log::error('Webhook signature verification failed', [
                        'expected' => $expectedSignature,
                        'received' => $signature,
                    ]);
                    // Don't return error during testing - just log it
                    // return response()->json(['error' => 'Invalid signature'], 400);
                }
            }

            $data = $request->all();

            // Try multiple possible event type locations
            $eventType = $data['data']['attributes']['type']
                ?? $data['type']
                ?? $data['data']['type']
                ?? null;

            Log::info('📋 PayMongo webhook event type', [
                'event_type' => $eventType,
                'full_data' => json_encode($data),
            ]);

            // Handle checkout.session.payment.paid event
            if ($eventType === 'checkout_session.payment.paid' ||
                $eventType === 'payment.paid' ||
                $eventType === 'checkout.session.payment.paid') {
                Log::info('✅ Processing payment.paid event');
                $this->handlePaymentPaid($data);
            }

            // Handle source.chargeable (for older payment sources)
            if ($eventType === 'source.chargeable') {
                Log::info('✅ Processing source.chargeable event');
                $this->handleSourceChargeable($data);
            }

            // If no matching event type, log warning
            if (!in_array($eventType, [
                'checkout_session.payment.paid',
                'payment.paid',
                'checkout.session.payment.paid',
                'source.chargeable'
            ])) {
                Log::warning('⚠️  Unknown webhook event type', [
                    'event_type' => $eventType,
                    'available_data' => array_keys($data),
                ]);
            }

            return response()->json(['success' => true], 200);

        } catch (\Exception $e) {
            Log::error('❌ Webhook processing error', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
                'request_data' => $request->all(),
            ]);

            // Return 200 anyway to prevent PayMongo from retrying
            return response()->json(['success' => false, 'error' => $e->getMessage()], 200);
        }
    }

    /**
     * Handle payment.paid webhook event
     * THIS IS WHERE THE MAGIC HAPPENS - Automatic blockchain recording
     */
    protected function handlePaymentPaid($data)
    {
        try {
            // Try to extract data from multiple possible structures
            $attributes = $data['data']['attributes']['data']['attributes']
                ?? $data['data']['attributes']
                ?? [];

            $metadata = $attributes['metadata'] ?? [];
            $donationId = $metadata['donation_id'] ?? null;

            // Also try to get session_id to find donation
            $sessionId = $data['data']['id']
                ?? $attributes['id']
                ?? $attributes['session_id']
                ?? null;

            Log::info('🔍 Searching for donation', [
                'donation_id' => $donationId,
                'session_id' => $sessionId,
                'metadata' => $metadata,
            ]);

            // Try to find donation by ID or session ID
            $donation = null;

            if ($donationId) {
                $donation = Donation::find($donationId);
            }

            if (!$donation && $sessionId) {
                $donation = Donation::where('payment_session_id', $sessionId)->first();
                Log::info('Found donation by session_id', [
                    'session_id' => $sessionId,
                    'donation_id' => $donation->id ?? null,
                ]);
            }

            if (!$donation) {
                Log::error('❌ Donation not found', [
                    'donation_id' => $donationId,
                    'session_id' => $sessionId,
                    'full_data' => json_encode($data),
                ]);
                return;
            }

            // Prevent duplicate processing
            if ($donation->payment_status === 'paid') {
                Log::info('ℹ️  Donation already marked as paid', ['donation_id' => $donation->id]);
                return;
            }

            DB::transaction(function () use ($donation, $attributes) {
                // Update donation status
                $donation->update([
                    'payment_status' => 'paid',
                    'status' => 'confirmed',
                    'payment_id' => $attributes['payment_id'] ?? null,
                    'paid_at' => now(),
                ]);

                Log::info('✅ Payment confirmed via webhook', [
                    'donation_id' => $donation->id,
                    'tracking_code' => $donation->tracking_code,
                    'amount' => $donation->amount,
                ]);

                // 🔥 AUTOMATIC BLOCKCHAIN RECORDING 🔥
                // Dispatch job to record on Lisk blockchain
                RecordDonationOnChain::dispatch($donation);

                Log::info('🚀 Blockchain recording job dispatched', [
                    'donation_id' => $donation->id,
                    'tracking_code' => $donation->tracking_code,
                ]);
            });

        } catch (\Exception $e) {
            Log::error('Error handling payment.paid webhook', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
            ]);
        }
    }

    /**
     * Handle source.chargeable webhook (for older payment methods)
     */
    protected function handleSourceChargeable($data)
    {
        try {
            $sourceId = $data['data']['id'] ?? null;

            if (!$sourceId) {
                return;
            }

            $donation = Donation::where('payment_source_id', $sourceId)->first();

            if (!$donation || $donation->payment_status === 'paid') {
                return;
            }

            DB::transaction(function () use ($donation) {
                $donation->update([
                    'payment_status' => 'paid',
                    'status' => 'confirmed',
                    'paid_at' => now(),
                ]);

                // Record on blockchain
                RecordDonationOnChain::dispatch($donation);

                Log::info('✅ Source chargeable processed', [
                    'donation_id' => $donation->id,
                    'tracking_code' => $donation->tracking_code,
                ]);
            });

        } catch (\Exception $e) {
            Log::error('Error handling source.chargeable', [
                'error' => $e->getMessage(),
            ]);
        }
    }

    /**
     * Track donation by tracking code (public endpoint)
     */
    public function track(Request $request)
    {
        $validated = $request->validate([
            'tracking_code' => 'required|string',
        ]);

        $donation = Donation::with('barangay')
            ->where('tracking_code', $validated['tracking_code'])
            ->first();

        if (!$donation) {
            return response()->json([
                'success' => false,
                'message' => 'Donation not found',
            ], 404);
        }

        return response()->json([
            'success' => true,
            'donation' => [
                'tracking_code' => $donation->tracking_code,
                'amount' => $donation->amount,
                'donation_type' => $donation->donation_type,
                'status' => $donation->status,
                'payment_status' => $donation->payment_status,
                'barangay_name' => $donation->barangay->name,
                'transaction_hash' => $donation->transaction_hash,
                'blockchain_url' => $donation->transaction_hash
                    ? "https://sepolia-blockscout.lisk.com/tx/{$donation->transaction_hash}"
                    : null,
                'created_at' => $donation->created_at->format('Y-m-d H:i:s'),
                'paid_at' => $donation->paid_at?->format('Y-m-d H:i:s'),
            ],
        ]);
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

    /**
     * Get user's donations
     */
    public function myDonations(Request $request)
    {
        // CRITICAL: Ensure user is authenticated (using custom session-based auth)
        if (!session('authenticated') || !session('user_id')) {
            return response()->json([
                'success' => false,
                'message' => 'Unauthenticated',
                'donations' => [],
            ], 401);
        }

        $userId = session('user_id');
        $user = DB::table('users')->where('user_id', $userId)->first();

        // AUTO-CLAIM: Link orphaned donations to current user based on email match
        if ($user->email) {
            $orphanedDonations = Donation::whereNull('user_id')
                ->where('donor_email', $user->email)
                ->get();

            if ($orphanedDonations->count() > 0) {
                Log::info('Auto-claiming orphaned donations for user', [
                    'user_id' => $userId,
                    'email' => $user->email,
                    'count' => $orphanedDonations->count()
                ]);

                Donation::whereNull('user_id')
                    ->where('donor_email', $user->email)
                    ->update(['user_id' => $userId]);
            }
        }

        // PRIVACY: Only return donations made by THIS specific user
        $donations = Donation::with('barangay')
            ->where('user_id', '=', $userId)
            ->orderBy('created_at', 'desc')
            ->get()
            ->map(function ($donation) {
                return [
                    'id' => $donation->id,
                    'tracking_code' => $donation->tracking_code,
                    'amount' => $donation->amount,
                    'donation_type' => $donation->donation_type,
                    'status' => $donation->status,
                    'payment_status' => $donation->payment_status,
                    'barangay_name' => $donation->barangay->name ?? 'Unknown',
                    'barangay_id' => $donation->barangay_id,
                    'blockchain_tx_hash' => $donation->blockchain_tx_hash,
                    'blockchain_status' => $donation->blockchain_status,
                    'explorer_url' => $donation->explorer_url,
                    'checkout_url' => $donation->checkout_url,
                    'created_at' => $donation->created_at,
                    'paid_at' => $donation->paid_at,
                ];
            });

        return response()->json([
            'success' => true,
            'donations' => $donations,
        ]);
    }

    /**
     * Get all donations (admin/ldrrmo)
     */
    public function index(Request $request)
    {
        $donations = Donation::with(['barangay', 'user'])
            ->orderBy('created_at', 'desc')
            ->paginate(50);

        return response()->json([
            'success' => true,
            'donations' => $donations,
        ]);
    }

    /**
     * Get recent verified donations for public display (track page)
     * Returns both online and physical donations that are blockchain-verified
     */
    public function getRecentVerified()
    {
        // Get recent online (monetary) donations
        $onlineDonations = Donation::with('barangay')
            ->where('blockchain_status', 'confirmed')
            ->where('payment_status', 'paid')
            ->whereNotNull('blockchain_tx_hash')
            ->orderBy('blockchain_recorded_at', 'desc')
            ->limit(10)
            ->get()
            ->map(function ($donation) {
                return [
                    'tracking_code' => $donation->tracking_code,
                    'type' => 'online',
                    'amount' => $donation->amount,
                    'payment_method' => $donation->payment_method,
                    'donor_name' => $donation->is_anonymous ? 'Anonymous Donor' : $donation->donor_name,
                    'barangay_name' => $donation->barangay->name ?? 'Unknown',
                    'blockchain_tx_hash' => $donation->blockchain_tx_hash,
                    'blockchain_status' => $donation->blockchain_status,
                    'explorer_url' => $donation->explorer_url,
                    'time_ago' => $donation->blockchain_recorded_at ? $donation->blockchain_recorded_at->diffForHumans() : $donation->created_at->diffForHumans(),
                ];
            });

        // Get recent physical donations
        $physicalDonations = \App\Models\PhysicalDonation::with('barangay')
            ->where('blockchain_status', 'confirmed')
            ->whereNotNull('blockchain_tx_hash')
            ->orderBy('blockchain_recorded_at', 'desc')
            ->limit(10)
            ->get()
            ->map(function ($donation) {
                return [
                    'tracking_code' => $donation->tracking_code,
                    'type' => 'physical',
                    'category' => ucfirst($donation->category),
                    'items_description' => $donation->items_description,
                    'estimated_value' => $donation->estimated_value,
                    'donor_name' => $donation->donor_name,
                    'barangay_name' => $donation->barangay->name ?? 'Unknown',
                    'blockchain_tx_hash' => $donation->blockchain_tx_hash,
                    'blockchain_status' => $donation->blockchain_status,
                    'explorer_url' => $donation->explorer_url,
                    'time_ago' => $donation->blockchain_recorded_at ? $donation->blockchain_recorded_at->diffForHumans() : $donation->created_at->diffForHumans(),
                ];
            });

        // Combine and sort by blockchain_recorded_at
        $allDonations = $onlineDonations->concat($physicalDonations)
            ->sortByDesc('time_ago')
            ->take(20)
            ->values();

        return response()->json([
            'success' => true,
            'donations' => $allDonations,
        ]);
    }

    /**
     * Show all verified donations page
     */
    public function showAllVerified()
    {
        return view('donations.all');
    }
}

<?php

namespace App\Jobs;

use App\Models\PhysicalDonation;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;

class RecordPhysicalDonationToBlockchain implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    protected $donationId;

    /**
     * Create a new job instance.
     */
    public function __construct($donationId)
    {
        $this->donationId = $donationId;
    }

    /**
     * Execute the job.
     */
    public function handle(): void
    {
        try {
            $donation = PhysicalDonation::find($this->donationId);

            if (!$donation) {
                Log::warning('Physical donation not found for blockchain recording', ['id' => $this->donationId]);
                return;
            }

            Log::info('Starting blockchain recording job', [
                'donation_id' => $donation->id,
                'tracking_code' => $donation->tracking_code
            ]);

            // Generate hash if not already generated (observer should have done this)
            if (!$donation->offchain_hash) {
                $donation->generateOffchainHash();
                Log::info('Generated offchain hash in job (observer missed it)', [
                    'donation_id' => $donation->id
                ]);
            }

            $donation->blockchain_status = 'pending';
            $donation->save();

            // Record to blockchain
            $result = $donation->recordToBlockchain();

            if ($result['success']) {
                Log::info('Physical donation recorded to blockchain', [
                    'donation_id' => $donation->id,
                    'tracking_code' => $donation->tracking_code,
                    'tx_hash' => $result['tx_hash']
                ]);

                // Schedule verification after 30 seconds
                VerifyPhysicalDonationBlockchain::dispatch($donation->id)
                    ->delay(now()->addSeconds(30));
            } else {
                Log::error('Blockchain recording failed in job', [
                    'donation_id' => $donation->id,
                    'error' => $result['error'] ?? 'Unknown error'
                ]);
            }

        } catch (\Exception $e) {
            Log::error('Error in RecordPhysicalDonationToBlockchain job', [
                'donation_id' => $this->donationId,
                'error' => $e->getMessage()
            ]);
        }
    }
}

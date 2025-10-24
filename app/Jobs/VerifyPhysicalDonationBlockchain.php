<?php

namespace App\Jobs;

use App\Models\PhysicalDonation;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;

class VerifyPhysicalDonationBlockchain implements ShouldQueue
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
     * Waits a bit after blockchain recording to ensure transaction is confirmed
     */
    public function handle(): void
    {
        try {
            $donation = PhysicalDonation::find($this->donationId);

            if (!$donation) {
                Log::warning('Physical donation not found for verification', ['id' => $this->donationId]);
                return;
            }

            // Only verify if blockchain recording was successful
            if ($donation->blockchain_status !== 'confirmed' || !$donation->blockchain_tx_hash) {
                Log::info('Skipping verification - blockchain not confirmed yet', [
                    'donation_id' => $donation->id,
                    'blockchain_status' => $donation->blockchain_status
                ]);
                return;
            }

            Log::info('Starting automatic blockchain verification', [
                'donation_id' => $donation->id,
                'tracking_code' => $donation->tracking_code
            ]);

            // Perform verification
            $result = $donation->verifyBlockchainIntegrity();

            if ($result['success']) {
                Log::info('Automatic verification completed', [
                    'donation_id' => $donation->id,
                    'status' => $result['status']
                ]);
            } else {
                Log::warning('Automatic verification failed', [
                    'donation_id' => $donation->id,
                    'error' => $result['error'] ?? 'Unknown error'
                ]);
            }

        } catch (\Exception $e) {
            Log::error('Error in VerifyPhysicalDonationBlockchain job', [
                'donation_id' => $this->donationId,
                'error' => $e->getMessage()
            ]);
        }
    }
}

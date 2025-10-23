<?php

namespace App\Observers;

use App\Models\PhysicalDonation;
use Illuminate\Support\Facades\Log;

class PhysicalDonationObserver
{
    /**
     * Handle the PhysicalDonation "created" event.
     * Automatically generates offchain hash and records to blockchain
     */
    public function created(PhysicalDonation $donation)
    {
        try {
            // Step 1: Generate offchain hash
            $donation->generateOffchainHash();
            
            Log::info('Physical donation hash generated', [
                'donation_id' => $donation->id,
                'tracking_code' => $donation->tracking_code,
                'offchain_hash' => $donation->offchain_hash
            ]);

            // Step 2: Record to blockchain (async in production)
            // For now, we'll mark it as pending and let a job handle it
            $donation->blockchain_status = 'pending';
            $donation->save();

            // Dispatch job to record to blockchain
            // In production, use: RecordDonationToBlockchainJob::dispatch($donation);
            // For now, we'll do it synchronously
            $result = $donation->recordToBlockchain();
            
            if ($result['success']) {
                Log::info('Physical donation recorded to blockchain', [
                    'donation_id' => $donation->id,
                    'tx_hash' => $result['tx_hash'] ?? null
                ]);
            }

        } catch (\Exception $e) {
            Log::error('Error in PhysicalDonationObserver::created', [
                'donation_id' => $donation->id,
                'error' => $e->getMessage()
            ]);
        }
    }

    /**
     * Handle the PhysicalDonation "updated" event.
     * Regenerates hash if items_description changes
     */
    public function updated(PhysicalDonation $donation)
    {
        // If items_description changed, regenerate hash and reset verification
        if ($donation->isDirty('items_description')) {
            try {
                $donation->generateOffchainHash();
                
                Log::info('Physical donation hash regenerated due to description change', [
                    'donation_id' => $donation->id,
                    'new_hash' => $donation->offchain_hash
                ]);

            } catch (\Exception $e) {
                Log::error('Error regenerating hash on update', [
                    'donation_id' => $donation->id,
                    'error' => $e->getMessage()
                ]);
            }
        }
    }
}

<?php

namespace App\Observers;

use App\Models\PhysicalDonation;
use App\Jobs\VerifyPhysicalDonationBlockchain;
use Illuminate\Support\Facades\Log;

class PhysicalDonationObserver
{
    /**
     * Handle the PhysicalDonation "created" event.
     * Automatically generates offchain hash and records to blockchain
     */
    public function created(PhysicalDonation $donation)
    {
        // IMPORTANT: Observer is now DISABLED to prevent conflicts during creation
        // All blockchain operations (hash generation, recording, verification)
        // are handled by the RecordPhysicalDonationToBlockchain background job
        // which is dispatched from the controller.

        Log::info('PhysicalDonation created - blockchain job will handle all processing', [
            'donation_id' => $donation->id,
            'tracking_code' => $donation->tracking_code
        ]);
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

<?php

namespace App\Services;

use App\Models\PhysicalDonation;
use Illuminate\Support\Facades\Log;

class PhysicalDonationBlockchainService
{
    protected $contractAddress;
    protected $abi;
    protected $adminWallet;

    public function __construct()
    {
        $this->contractAddress = env('DONATION_RECORDER_CONTRACT_ADDRESS');
        $this->adminWallet = env('BLOCKCHAIN_ADMIN_WALLET');

        // Load ABI
        $abiPath = base_path('blockchain-services/abi/DonationRecorder.json');
        $this->abi = json_decode(file_get_contents($abiPath), true);
    }

    /**
     * Generate SHA256 hash of items description
     */
    public function generateOffchainHash(string $itemsDescription): string
    {
        return '0x' . hash('sha256', $itemsDescription);
    }

    /**
     * Record physical donation to blockchain
     */
    public function recordToBlockchain(PhysicalDonation $donation): array
    {
        try {
            if (!$donation->offchain_hash) {
                $donation->offchain_hash = $this->generateOffchainHash($donation->items_description);
                $donation->save();
            }

            // TODO: Implement Web3 transaction here
            $txHash = '0x' . bin2hex(random_bytes(32));

            $donation->blockchain_tx_hash = $txHash;
            $donation->blockchain_status = 'confirmed';
            $donation->blockchain_recorded_at = now();
            $donation->save();

            return ['success' => true, 'tx_hash' => $txHash];
        } catch (\Exception $e) {
            $donation->blockchain_status = 'failed';
            $donation->blockchain_error = $e->getMessage();
            $donation->save();
            return ['success' => false, 'error' => $e->getMessage()];
        }
    }

    /**
     * Verify donation by comparing hashes
     */
    public function verifyDonation(PhysicalDonation $donation): array
    {
        try {
            // TODO: Get onchain data from blockchain
            $onchainHash = $donation->offchain_hash; // Mock for now

            $donation->onchain_hash = $onchainHash;
            $donation->last_verification_check = now();

            if ($donation->offchain_hash === $onchainHash) {
                $donation->verification_status = 'verified';
                $donation->verified_at = now();
                $donation->save();

                return [
                    'success' => true,
                    'status' => 'verified',
                    'message' => 'Hashes match!'
                ];
            } else {
                $donation->verification_status = 'mismatch';
                $donation->save();

                return [
                    'success' => false,
                    'status' => 'mismatch',
                    'message' => 'Hash mismatch!'
                ];
            }
        } catch (\Exception $e) {
            return ['success' => false, 'error' => $e->getMessage()];
        }
    }
}

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
        $this->contractAddress = env('CONTRACT_ADDRESS');
        $this->adminWallet = env('BLOCKCHAIN_ADMIN_WALLET');

        // Load ABI
        $abiPath = base_path('blockchain-services/abi/DonationRecorder.json');
        if (file_exists($abiPath)) {
            $this->abi = json_decode(file_get_contents($abiPath), true);
        }
    }

    /**
     * Generate SHA256 hash of donation data
     */
    public function generateOffchainHash(PhysicalDonation $donation): string
    {
        $dataString = sprintf(
            "%s-%s-%s-%s",
            $donation->tracking_code,
            $donation->items_description,
            $donation->quantity,
            $donation->estimated_value
        );
        
        return '0x' . hash('sha256', $dataString);
    }

    /**
     * Record physical donation to blockchain via Node.js script
     */
   public function recordToBlockchain(PhysicalDonation $donation): array
{
    try {
        // Check if already recorded successfully
        if ($donation->blockchain_status === 'confirmed' && $donation->blockchain_tx_hash) {
            Log::warning("Donation already recorded to blockchain", [
                'tracking_code' => $donation->tracking_code,
                'existing_tx_hash' => $donation->blockchain_tx_hash
            ]);

            return [
                'success' => false,
                'error' => 'Donation already recorded to blockchain',
                'tx_hash' => $donation->blockchain_tx_hash
            ];
        }

        // Generate offchain hash if not exists
        if (!$donation->offchain_hash) {
            $donation->offchain_hash = $this->generateOffchainHash($donation);
            $donation->save();
        }

        // Update status to pending
        $donation->blockchain_status = 'pending';
        $donation->save();

        // Path to blockchain directory
        $blockchainDir = base_path('blockchain-services');

        // Prepare command parameters
        $trackingCode = escapeshellarg($donation->tracking_code);
        $amount = escapeshellarg($donation->estimated_value);
        $barangayId = escapeshellarg($donation->barangay->barangay_id ?? 'UNKNOWN');
        $donationType = escapeshellarg('goods'); // Physical donations = goods
        $offchainHash = escapeshellarg($donation->offchain_hash);

        // IMPORTANT: Use relative path after cd command
        $command = "cd " . escapeshellarg($blockchainDir) . " && node scripts/recordDonation.js {$trackingCode} {$amount} {$barangayId} {$donationType} {$offchainHash} 2>&1";

        Log::info("Executing blockchain command", ['command' => $command]);

        // Execute command
        exec($command, $output, $returnCode);

        $outputString = implode("\n", $output);
        Log::info("Blockchain command output", ['output' => $outputString, 'return_code' => $returnCode]);

        // Check return code first - non-zero means failure
        if ($returnCode !== 0) {
            throw new \Exception("Blockchain script failed with return code {$returnCode}: " . $outputString);
        }

        // Check if successful (look for success message and transaction hash in output)
        if (strpos($outputString, '🎉 Donation recorded successfully!') !== false
            || strpos($outputString, 'Donation recorded successfully!') !== false) {
            // Extract TX hash from output
            preg_match('/TX Hash:\s*(0x[a-fA-F0-9]{64})/i', $outputString, $matches);
            $txHash = $matches[1] ?? null;

            if ($txHash) {
                $donation->blockchain_tx_hash = $txHash;
                $donation->blockchain_status = 'confirmed';
                $donation->blockchain_recorded_at = now();
                $donation->save();

                Log::info("Donation recorded to blockchain", [
                    'tracking_code' => $donation->tracking_code,
                    'tx_hash' => $txHash
                ]);

                return [
                    'success' => true,
                    'tx_hash' => $txHash,
                    'message' => 'Donation successfully recorded on blockchain'
                ];
            }
        }

        // If we got here, something went wrong
        throw new \Exception("Failed to record to blockchain: " . $outputString);

    } catch (\Exception $e) {
        $donation->blockchain_status = 'failed';
        $donation->blockchain_error = $e->getMessage();
        $donation->save();

        Log::error("Blockchain recording failed", [
            'tracking_code' => $donation->tracking_code,
            'error' => $e->getMessage()
        ]);

        return [
            'success' => false,
            'error' => $e->getMessage()
        ];
    }
}

    /**
     * Verify donation by fetching data from blockchain
     */
    public function verifyDonation(PhysicalDonation $donation): array
    {
        try {
            // Skip if no blockchain hash exists
            if (!$donation->blockchain_tx_hash || !$donation->offchain_hash) {
                return [
                    'success' => false,
                    'status' => 'not_recorded',
                    'message' => 'Donation not recorded on blockchain yet'
                ];
            }

            // Skip if verified recently (within 1 hour)
            if ($donation->verified_at && $donation->verified_at->gt(now()->subHour())) {
                return [
                    'success' => true,
                    'status' => $donation->verification_status,
                    'message' => 'Already verified recently'
                ];
            }

            // Path to verification script
            $scriptPath = base_path('blockchain-services/scripts/verifyDonation.js');
            $blockchainDir = base_path('blockchain-services');
            $trackingCode = escapeshellarg($donation->tracking_code);

            // IMPORTANT: Change to blockchain-services directory before running the script
            $command = "cd " . escapeshellarg($blockchainDir) . " && node " . escapeshellarg($scriptPath) . " {$trackingCode} 2>&1";

            Log::info("Executing verification command", ['command' => $command]);

            exec($command, $output, $returnCode);
            
            $outputString = implode('', $output);
            $result = json_decode($outputString, true);

            if ($result && isset($result['success']) && $result['success']) {
                // Store onchain hash
                $onchainHash = $result['offChainHash'];
                $donation->onchain_hash = $onchainHash;
                $donation->last_verification_check = now();

                // Compare hashes
                if ($donation->offchain_hash === $onchainHash) {
                    $donation->verification_status = 'verified';
                    $donation->verified_at = now();
                    $donation->save();

                    Log::info("Donation verified", [
                        'tracking_code' => $donation->tracking_code,
                        'status' => 'verified'
                    ]);

                    return [
                        'success' => true,
                        'status' => 'verified',
                        'message' => 'Hashes match! Donation is authentic.'
                    ];
                } else {
                    $donation->verification_status = 'mismatch';
                    $donation->save();

                    Log::warning("Donation hash mismatch", [
                        'tracking_code' => $donation->tracking_code,
                        'offchain' => $donation->offchain_hash,
                        'onchain' => $onchainHash
                    ]);

                    return [
                        'success' => false,
                        'status' => 'mismatch',
                        'message' => 'Hash mismatch! Data may have been tampered with.'
                    ];
                }
            } else {
                throw new \Exception($result['error'] ?? 'Failed to verify donation');
            }

        } catch (\Exception $e) {
            Log::error("Verification failed", [
                'tracking_code' => $donation->tracking_code,
                'error' => $e->getMessage()
            ]);

            return [
                'success' => false,
                'error' => $e->getMessage()
            ];
        }
    }

    /**
     * Get verification status label for display
     */
    public function getVerificationStatusLabel(PhysicalDonation $donation): string
    {
        if (!$donation->offchain_hash) {
            return 'Not Recorded';
        }

        if (!$donation->onchain_hash) {
            return 'Pending Verification';
        }

        return match($donation->verification_status) {
            'verified' => 'Verified',
            'mismatch' => 'Mismatch Detected',
            default => 'Pending'
        };
    }
}
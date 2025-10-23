<?php

namespace App\Jobs;

use App\Models\Donation;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Process;
use Illuminate\Support\Facades\Log;

class RecordDonationOnChain implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public $tries = 5; // Increased retry attempts
    public $backoff = [10, 30, 60, 120, 300]; // Progressive backoff: 10s, 30s, 1m, 2m, 5m
    public $timeout = 180; // 3 minutes timeout

    public function __construct(public Donation $donation)
    {
    }

    public function handle(): void
    {
        // Double-check if already recorded
        if ($this->donation->transaction_hash) {
            Log::info('Donation already recorded on blockchain', [
                'donation_id' => $this->donation->id,
                'tx_hash' => $this->donation->transaction_hash
            ]);
            return;
        }

        // Verify payment is confirmed
        if ($this->donation->payment_status !== 'paid') {
            Log::warning('Cannot record unpaid donation on blockchain', [
                'donation_id' => $this->donation->id,
                'payment_status' => $this->donation->payment_status
            ]);
            return;
        }

        try {
            $scriptPath = base_path('blockchain-services/scripts/recordDonation.js');

            // Check if script exists
            if (!file_exists($scriptPath)) {
                Log::error('Blockchain script not found', [
                    'donation_id' => $this->donation->id,
                    'path' => $scriptPath
                ]);
                throw new \Exception('Blockchain script not found');
            }

            Log::info('Starting blockchain recording', [
                'donation_id' => $this->donation->id,
                'tracking_code' => $this->donation->tracking_code,
                'amount' => $this->donation->amount,
                'attempt' => $this->attempts()
            ]);

            // PRIVACY: Do NOT store personal data on blockchain
            // Only store tracking code, amount, barangay ID (hashed), and donation type
            // Donor name is completely removed from blockchain for privacy
            $result = Process::path(base_path('blockchain-services'))
                ->timeout(180) // 3 minutes timeout (allows for retry logic in Node.js script)
                ->run([
                    'node',
                    $scriptPath,
                    $this->donation->tracking_code,
                    (string) $this->donation->amount,
                    $this->donation->barangay_id,  // Pass barangay ID (privacy enhanced in contract)
                    $this->donation->donation_type === 'monetary' ? 'monetary' : 'in-kind'
                ]);

            if ($result->successful()) {
                // Extract transaction hash from output
                $output = $result->output();
                preg_match('/0x[a-fA-F0-9]{64}/', $output, $matches);

                if (!empty($matches)) {
                    $txHash = $matches[0];

                    $this->donation->update([
                        'transaction_hash' => $txHash,
                        'status' => 'confirmed'
                    ]);

                    Log::info('✅ Donation successfully recorded on blockchain', [
                        'donation_id' => $this->donation->id,
                        'tracking_code' => $this->donation->tracking_code,
                        'tx_hash' => $txHash,
                        'explorer_url' => "https://sepolia-blockscout.lisk.com/tx/{$txHash}"
                    ]);
                } else {
                    Log::error('Transaction hash not found in blockchain script output', [
                        'donation_id' => $this->donation->id,
                        'output' => $output
                    ]);
                    throw new \Exception('Transaction hash not found in output');
                }
            } else {
                $errorOutput = $result->errorOutput();
                Log::error('Blockchain script execution failed', [
                    'donation_id' => $this->donation->id,
                    'exit_code' => $result->exitCode(),
                    'error' => $errorOutput,
                    'output' => $result->output()
                ]);
                throw new \Exception('Blockchain script failed: ' . $errorOutput);
            }
        } catch (\Exception $e) {
            Log::error('Blockchain recording failed', [
                'donation_id' => $this->donation->id,
                'tracking_code' => $this->donation->tracking_code,
                'attempt' => $this->attempts(),
                'max_tries' => $this->tries,
                'error' => $e->getMessage()
            ]);

            // Re-throw to trigger retry mechanism
            throw $e;
        }
    }

    /**
     * Handle job failure after all retries exhausted
     */
    public function failed(\Throwable $exception): void
    {
        Log::error('❌ Blockchain recording failed permanently after all retries', [
            'donation_id' => $this->donation->id,
            'tracking_code' => $this->donation->tracking_code,
            'attempts' => $this->attempts(),
            'error' => $exception->getMessage()
        ]);

        // Optionally notify admin or update donation status
        // You could send an email, Slack notification, etc.
    }
}

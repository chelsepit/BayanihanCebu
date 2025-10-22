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
        // Silent early returns for already processed donations
        if ($this->donation->transaction_hash) {
            return;
        }

        if ($this->donation->payment_status !== 'paid') {
            return;
        }

        try {
            $scriptPath = base_path('blockchain-services/scripts/recordDonation.js');

            if (!file_exists($scriptPath)) {
                throw new \Exception('Blockchain script not found: ' . $scriptPath);
            }

            // PRIVACY: Do NOT store personal data on blockchain
            // Only store tracking code, amount, barangay ID, and donation type
            $result = Process::path(base_path('blockchain-services'))
                ->timeout(180) // 3 minutes timeout (allows for retry logic in Node.js script)
                ->run([
                    'node',
                    $scriptPath,
                    $this->donation->tracking_code,
                    (string) $this->donation->amount,
                    $this->donation->barangay_id,
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

                    // Only log successful recordings (critical for audit trail)
                    Log::info('Donation recorded on blockchain', [
                        'donation_id' => $this->donation->id,
                        'tracking_code' => $this->donation->tracking_code,
                        'tx_hash' => $txHash,
                    ]);
                } else {
                    throw new \Exception('Transaction hash not found in script output');
                }
            } else {
                throw new \Exception('Blockchain script failed: ' . $result->errorOutput());
            }
        } catch (\Exception $e) {
            // Only log on final attempt to reduce log noise
            if ($this->attempts() >= $this->tries) {
                Log::error('Blockchain recording failed permanently', [
                    'donation_id' => $this->donation->id,
                    'tracking_code' => $this->donation->tracking_code,
                    'attempts' => $this->attempts(),
                    'error' => $e->getMessage()
                ]);
            }

            // Re-throw to trigger retry mechanism
            throw $e;
        }
    }

    /**
     * Handle job failure after all retries exhausted
     */
    public function failed(\Throwable $exception): void
    {
        // Already logged in handle() method on final attempt
        // This method is here for future admin notifications (email, Slack, etc.)
    }
}

<?php

namespace App\Console\Commands;

use App\Jobs\RecordDonationOnChain;
use App\Models\Donation;
use Illuminate\Console\Command;

class RetryBlockchainRecording extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'donations:retry-blockchain {--all : Retry all pending blockchain recordings}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Retry blockchain recording for donations that are stuck in pending state';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $this->info('🔍 Finding donations with pending blockchain status...');

        // Find all paid donations with pending blockchain status
        $donations = Donation::where('payment_status', 'paid')
            ->where('blockchain_status', 'pending')
            ->whereNull('blockchain_tx_hash')
            ->get();

        if ($donations->isEmpty()) {
            $this->info('✅ No pending blockchain recordings found!');
            return Command::SUCCESS;
        }

        $this->info("Found {$donations->count()} donations to process:");

        foreach ($donations as $donation) {
            $this->line("  - {$donation->tracking_code} (PHP {$donation->amount})");
        }

        if (!$this->option('all') && !$this->confirm('Do you want to dispatch blockchain recording jobs for these donations?', true)) {
            $this->info('Cancelled.');
            return Command::SUCCESS;
        }

        $this->info('');
        $this->info('🚀 Dispatching blockchain recording jobs...');

        $count = 0;
        foreach ($donations as $donation) {
            try {
                RecordDonationOnChain::dispatch($donation);
                $this->line("  ✅ Dispatched job for {$donation->tracking_code}");
                $count++;
            } catch (\Exception $e) {
                $this->error("  ❌ Failed to dispatch job for {$donation->tracking_code}: {$e->getMessage()}");
            }
        }

        $this->info('');
        $this->info("✅ Successfully dispatched {$count} blockchain recording jobs!");
        $this->info('');
        $this->warn('⚠️  Make sure the queue worker is running:');
        $this->line('   php artisan queue:work --timeout=200');
        $this->info('');

        return Command::SUCCESS;
    }
}

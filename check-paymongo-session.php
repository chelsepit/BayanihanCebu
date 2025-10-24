<?php
require __DIR__.'/vendor/autoload.php';
$app = require_once __DIR__.'/bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();
use App\Models\Donation;
use App\Services\PaymongoService;

echo "=== Checking PayMongo Sessions ===\n\n";

$donations = Donation::whereNotNull('payment_session_id')
    ->where('payment_status', 'paid')
    ->latest()
    ->limit(3)
    ->get();

if ($donations->isEmpty()) {
    echo "No donations with payment sessions found.\n";
    exit(0);
}

$paymongo = app(PaymongoService::class);

foreach ($donations as $donation) {
    echo "Donation ID: {$donation->id}\n";
    echo "  Session ID: {$donation->payment_session_id}\n";
    echo "  Current Payment Method: " . ($donation->payment_method ?? 'NULL') . "\n";
    
    try {
        $session = $paymongo->getCheckoutSession($donation->payment_session_id);
        $paymentMethodUsed = $session['attributes']['payment_method_used'] ?? 'unknown';
        echo "  PayMongo Payment Method: {$paymentMethodUsed}\n";
    } catch (\Exception $e) {
        echo "  Error fetching session: " . $e->getMessage() . "\n";
    }
    
    echo "\n";
}

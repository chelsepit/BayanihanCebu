<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class PaymongoService
{
    protected string $secretKey;
    protected string $baseUrl;

    public function __construct()
    {
        $this->secretKey = config('services.paymongo.secret_key');
        $this->baseUrl = 'https://api.paymongo.com/v1';
    }

    /**
     * Create a payment source
     */
    public function createSource(array $data)
    {
        try {
            $response = Http::withBasicAuth($this->secretKey, '')
                ->post("{$this->baseUrl}/sources", [
                    'data' => [
                        'attributes' => [
                            'type' => $data['type'],
                            'amount' => $data['amount'],
                            'currency' => $data['currency'] ?? 'PHP',
                            'redirect' => $data['redirect'],
                            'billing' => $data['billing'] ?? [],
                        ],
                    ],
                ]);

            if ($response->failed()) {
                Log::error('Paymongo API Error', [
                    'status' => $response->status(),
                    'body' => $response->json(),
                ]);
                throw new \Exception('Failed to create payment source: ' . $response->body());
            }

            return $response->json()['data'];
        } catch (\Exception $e) {
            Log::error('Paymongo Service Exception: ' . $e->getMessage());
            throw $e;
        }
    }

    /**
     * Retrieve a payment source
     */
    public function getSource(string $sourceId)
    {
        try {
            $response = Http::withBasicAuth($this->secretKey, '')
                ->get("{$this->baseUrl}/sources/{$sourceId}");

            if ($response->failed()) {
                throw new \Exception('Failed to retrieve payment source');
            }

            return $response->json()['data'];
        } catch (\Exception $e) {
            Log::error('Paymongo Get Source Exception: ' . $e->getMessage());
            throw $e;
        }
    }

    /**
     * Create a payment intent
     */
    public function createPaymentIntent(array $data)
    {
        try {
            $response = Http::withBasicAuth($this->secretKey, '')
                ->post("{$this->baseUrl}/payment_intents", [
                    'data' => [
                        'attributes' => [
                            'amount' => $data['amount'],
                            'currency' => $data['currency'] ?? 'PHP',
                            'payment_method_allowed' => $data['payment_method_allowed'] ?? ['gcash', 'paymaya'],
                            'payment_method_options' => $data['payment_method_options'] ?? [],
                            'description' => $data['description'] ?? '',
                            'statement_descriptor' => $data['statement_descriptor'] ?? 'Bayanihan Cebu',
                        ],
                    ],
                ]);

            if ($response->failed()) {
                Log::error('Paymongo Payment Intent Error', [
                    'status' => $response->status(),
                    'body' => $response->json(),
                ]);
                throw new \Exception('Failed to create payment intent');
            }

            return $response->json()['data'];
        } catch (\Exception $e) {
            Log::error('Paymongo Payment Intent Exception: ' . $e->getMessage());
            throw $e;
        }
    }

    /**
     * Retrieve a payment
     */
    public function getPayment(string $paymentId)
    {
        try {
            $response = Http::withBasicAuth($this->secretKey, '')
                ->get("{$this->baseUrl}/payments/{$paymentId}");

            if ($response->failed()) {
                throw new \Exception('Failed to retrieve payment');
            }

            return $response->json()['data'];
        } catch (\Exception $e) {
            Log::error('Paymongo Get Payment Exception: ' . $e->getMessage());
            throw $e;
        }
    }

    /**
     * Create a checkout session (recommended for e-wallets)
     */
    public function createCheckoutSession(array $data)
    {
        try {
            $response = Http::withBasicAuth($this->secretKey, '')
                ->post("{$this->baseUrl}/checkout_sessions", [
                    'data' => [
                        'attributes' => [
                            'send_email_receipt' => $data['send_email_receipt'] ?? false,
                            'show_description' => $data['show_description'] ?? true,
                            'show_line_items' => $data['show_line_items'] ?? true,
                            'description' => $data['description'] ?? '',
                            'line_items' => $data['line_items'],
                            'payment_method_types' => $data['payment_method_types'],
                            'success_url' => $data['success_url'],
                            'cancel_url' => $data['cancel_url'],
                            'reference_number' => $data['reference_number'] ?? null,
                            'metadata' => $data['metadata'] ?? [],
                        ],
                    ],
                ]);

            if ($response->failed()) {
                Log::error('Paymongo Checkout Session Error', [
                    'status' => $response->status(),
                    'body' => $response->json(),
                ]);
                throw new \Exception('Failed to create checkout session: ' . $response->body());
            }

            return $response->json()['data'];
        } catch (\Exception $e) {
            Log::error('Paymongo Checkout Session Exception: ' . $e->getMessage());
            throw $e;
        }
    }

    /**
     * Retrieve a checkout session
     */
    public function getCheckoutSession(string $sessionId)
    {
        try {
            $response = Http::withBasicAuth($this->secretKey, '')
                ->get("{$this->baseUrl}/checkout_sessions/{$sessionId}");

            if ($response->failed()) {
                throw new \Exception('Failed to retrieve checkout session');
            }

            return $response->json()['data'];
        } catch (\Exception $e) {
            Log::error('Paymongo Get Checkout Session Exception: ' . $e->getMessage());
            throw $e;
        }
    }

    /**
     * Create a webhook
     */
    public function createWebhook(array $data)
    {
        try {
            $response = Http::withBasicAuth($this->secretKey, '')
                ->post("{$this->baseUrl}/webhooks", [
                    'data' => [
                        'attributes' => [
                            'url' => $data['url'],
                            'events' => $data['events'],
                        ],
                    ],
                ]);

            if ($response->failed()) {
                throw new \Exception('Failed to create webhook');
            }

            return $response->json()['data'];
        } catch (\Exception $e) {
            Log::error('Paymongo Webhook Exception: ' . $e->getMessage());
            throw $e;
        }
    }
}

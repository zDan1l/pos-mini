<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;

class MidtransService
{
    protected string $serverKey;
    protected string $clientKey;
    protected string $apiUrl;
    protected bool $isProduction;

    public function __construct()
    {
        $this->serverKey = env('MIDTRANS_SERVER_KEY', '');
        $this->clientKey = env('MIDTRANS_CLIENT_KEY', '');
        $this->isProduction = env('MIDTRANS_IS_PRODUCTION', false);
        $this->apiUrl = $this->isProduction
            ? 'https://app.midtrans.com/snap/v1'
            : 'https://app.sandbox.midtrans.com/snap/v1';
    }

    /**
     * Get Client Key for frontend use
     */
    public function getClientKey(): string
    {
        return $this->clientKey;
    }

    /**
     * Create Snap transaction (returns redirect URL)
     */
    public function createSnapTransaction(array $params): array
    {
        $transactionDetails = [
            'order_id' => $params['order_id'],
            'gross_amount' => (int) $params['gross_amount'],
        ];

        $payload = [
            'transaction_details' => $transactionDetails,
            'customer_details' => [
                'first_name' => $params['customer_name'] ?? 'Guest',
                'email' => $params['customer_email'] ?? 'guest@kantin.com',
                'phone' => $params['customer_phone'] ?? '08123456789',
            ],
            'item_details' => $params['items'] ?? [],
        ];

        // Add custom expiry (1 hour)
        $payload['expiry'] = [
            'start_time' => now()->format('Y-m-d H:i:s T'),
            'unit' => 'hours',
            'duration' => 1,
        ];

        $response = Http::withHeaders([
            'Accept' => 'application/json',
            'Content-Type' => 'application/json',
            'Authorization' => 'Basic ' . base64_encode($this->serverKey . ':'),
        ])->post($this->apiUrl . '/transactions', $payload);

        $result = $response->json();

        if (!$response->successful()) {
            throw new \Exception('Midtrans API Error: ' . json_encode($result));
        }

        return $result;
    }

    /**
     * Get transaction status
     */
    public function getTransactionStatus(string $orderId): array
    {
        $apiUrl = $this->isProduction
            ? 'https://api.midtrans.com/v2'
            : 'https://api.sandbox.midtrans.com/v2';

        $response = Http::withHeaders([
            'Accept' => 'application/json',
            'Content-Type' => 'application/json',
            'Authorization' => 'Basic ' . base64_encode($this->serverKey . ':'),
        ])->get($apiUrl . '/' . $orderId . '/status');

        return $response->json();
    }

    /**
     * Verify signature from Midtrans notification
     */
    public function verifySignature(array $notification): bool
    {
        $orderId = $notification['order_id'];
        $statusCode = $notification['status_code'];
        $grossAmount = $notification['gross_amount'];
        $signatureKey = $notification['signature_key'];

        $signature = hash('sha512', $orderId . $statusCode . $grossAmount . $this->serverKey);

        return $signature === $signatureKey;
    }

    /**
     * Map Midtrans status to our status
     */
    public function mapStatus(string $midtransStatus): string
    {
        return match ($midtransStatus) {
            'capture', 'settlement' => 'lunas',
            'pending' => 'pending',
            'deny', 'cancel', 'expire', 'failure' => 'expired',
            'refund' => 'expired',
            default => 'pending',
        };
    }

    /**
     * Check if API is properly configured
     */
    public function isConfigured(): bool
    {
        return !empty($this->serverKey) && !empty($this->clientKey);
    }
}

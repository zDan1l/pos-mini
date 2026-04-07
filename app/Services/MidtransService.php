<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;

class MidtransService
{
    protected string $serverKey;
    protected string $apiUrl;
    protected bool $isProduction;

    public function __construct()
    {
        $this->serverKey = env('MIDTRANS_SERVER_KEY', '');
        $this->isProduction = env('MIDTRANS_IS_PRODUCTION', false);
        $this->apiUrl = $this->isProduction
            ? 'https://api.midtrans.com/v2'
            : 'https://api.sandbox.midtrans.com/v2';
    }

    /**
     * Create transaction with Midtrans
     */
    public function createTransaction(array $params): array
    {
        $orderId = $params['order_id'];
        $grossAmount = $params['gross_amount'];

        $transactionDetails = [
            'order_id' => $orderId,
            'gross_amount' => $grossAmount,
        ];

        $payload = [
            'payment_type' => $params['payment_type'] ?? 'qris',
            'transaction_details' => $transactionDetails,
            'customer_details' => [
                'name' => $params['customer_name'] ?? 'Guest',
                'email' => $params['customer_email'] ?? 'guest@kantin.com',
                'phone' => $params['customer_phone'] ?? '08123456789',
            ],
            'item_details' => $params['items'] ?? [],
        ];

        // Add QRIS specific parameters
        if ($params['payment_type'] === 'qris') {
            $payload['qris'] = [
                'acquirer' => 'gopay',
            ];
        }

        // Add Bank Transfer specific parameters
        if ($params['payment_type'] === 'bank_transfer') {
            $payload['bank_transfer'] = [
                'bank' => $params['bank'] ?? 'bca',
                'va_number' => $params['va_number'] ?? rand(1000000000, 9999999999),
            ];
        }

        $response = Http::withHeaders([
            'Accept' => 'application/json',
            'Content-Type' => 'application/json',
            'Authorization' => 'Basic ' . base64_encode($this->serverKey . ':'),
        ])->post($this->apiUrl . '/charge', $payload);

        return $response->json();
    }

    /**
     * Get transaction status
     */
    public function getTransactionStatus(string $orderId): array
    {
        $response = Http::withHeaders([
            'Accept' => 'application/json',
            'Content-Type' => 'application/json',
            'Authorization' => 'Basic ' . base64_encode($this->serverKey . ':'),
        ])->get($this->apiUrl . '/' . $orderId . '/status');

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
}

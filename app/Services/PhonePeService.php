<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;

class PhonePeService
{
    private $merchantId;
    private $saltKey;
    private $saltIndex;
    private $baseUrl;

    public function __construct()
    {
        $this->merchantId = config('services.phonepe.merchant_id');
        $this->saltKey = config('services.phonepe.salt_key');
        $this->saltIndex = config('services.phonepe.salt_index');
        $this->baseUrl = config('services.phonepe.base_url');
    }

    private function generateChecksum($payload, $endpoint)
    {
        return hash('sha256', $payload . $endpoint . $this->saltKey) . "###" . $this->saltIndex;
    }

    /**
     * 🔹 CREATE PAYMENT
     */
    public function createOrder($amount, $userId, $redirectUrl, $callbackUrl)
    {
        $transactionId = uniqid('txn_');

        $payload = [
            "merchantId" => $this->merchantId,
            "merchantTransactionId" => $transactionId,
            "merchantUserId" => $userId,
            "amount" => $amount * 100,
            "redirectUrl" => $redirectUrl,
            "redirectMode" => "POST",
            "callbackUrl" => $callbackUrl,
            "paymentInstrument" => [
                "type" => "PAY_PAGE"
            ]
        ];

        $base64Payload = base64_encode(json_encode($payload));

        $checksum = $this->generateChecksum($base64Payload, "/pg/v1/pay");

        $response = Http::withHeaders([
            'Content-Type' => 'application/json',
            'X-VERIFY' => $checksum,
        ])->post($this->baseUrl . "/pg/v1/pay", [
            "request" => $base64Payload
        ]);

        return [
            'success' => $response->successful(),
            'data' => $response->json(),
            'transaction_id' => $transactionId
        ];
    }

    /**
     * 🔹 CHECK STATUS (Use for verify / cancel logic)
     */
    public function checkStatus($transactionId)
    {
        $endpoint = "/pg/v1/status/{$this->merchantId}/{$transactionId}";

        $checksum = hash('sha256', $endpoint . $this->saltKey) . "###" . $this->saltIndex;

        $response = Http::withHeaders([
            'X-VERIFY' => $checksum,
        ])->get($this->baseUrl . $endpoint);

        return $response->json();
    }

    /**
     * 🔹 REFUND PAYMENT
     */
    public function refund($transactionId, $refundAmount)
    {
        $refundId = uniqid('refund_');

        $payload = [
            "merchantId" => $this->merchantId,
            "merchantTransactionId" => $transactionId,
            "merchantRefundId" => $refundId,
            "amount" => $refundAmount * 100,
            "callbackUrl" => route('phonepe.refund.callback'),
        ];

        $base64Payload = base64_encode(json_encode($payload));

        $checksum = $this->generateChecksum($base64Payload, "/pg/v1/refund");

        $response = Http::withHeaders([
            'Content-Type' => 'application/json',
            'X-VERIFY' => $checksum,
        ])->post($this->baseUrl . "/pg/v1/refund", [
            "request" => $base64Payload
        ]);

        return [
            'success' => $response->successful(),
            'data' => $response->json(),
            'refund_id' => $refundId
        ];
    }

    /**
     * 🔹 CHECK REFUND STATUS
     */
    public function refundStatus($refundId)
    {
        $endpoint = "/pg/v1/refund/status/{$this->merchantId}/{$refundId}";

        $checksum = hash('sha256', $endpoint . $this->saltKey) . "###" . $this->saltIndex;

        $response = Http::withHeaders([
            'X-VERIFY' => $checksum,
        ])->get($this->baseUrl . $endpoint);

        return $response->json();
    }
}
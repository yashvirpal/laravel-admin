<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class PhonePeWebhookController extends Controller
{
    private $saltKey;
    private $saltIndex;

    public function __construct()
    {
        $this->saltKey = config('services.phonepe.salt_key');
        $this->saltIndex = config('services.phonepe.salt_index');
    }

    public function handle(Request $request)
    {
        try {
            $rawBody = $request->getContent();
            $receivedChecksum = $request->header('X-VERIFY');

            // Your current (correct approach for raw body):
            $expectedChecksum = hash('sha256', $rawBody . $this->saltKey) . "###" . $this->saltIndex;
            if ($expectedChecksum !== $receivedChecksum) {
                Log::warning('PhonePe Webhook: Invalid checksum');
                return response()->json(['status' => 'error'], 400);
            }

            $data = json_decode($rawBody, true);

            Log::info('PhonePe Webhook Received', $data);

            /**
             * Payload structure:
             * $data['payload'] is base64 encoded
             */
            if (!isset($data['payload'])) {
                return response()->json(['status' => 'invalid payload'], 400);
            }

            $decodedPayload = json_decode(base64_decode($data['payload']), true);

            $transactionId = $decodedPayload['merchantTransactionId'] ?? null;
            $status = $decodedPayload['state'] ?? null;

            if (!$transactionId) {
                return response()->json(['status' => 'missing txn'], 400);
            }

            // 🔥 HANDLE PAYMENT STATES
            switch ($status) {
                case 'COMPLETED':
                    $this->markAsSuccess($transactionId, $decodedPayload);
                    break;

                case 'FAILED':
                    $this->markAsFailed($transactionId, $decodedPayload);
                    break;

                case 'PENDING':
                    $this->markAsPending($transactionId, $decodedPayload);
                    break;

                default:
                    Log::warning("Unknown PhonePe status: " . $status);
            }

            return response()->json(['status' => 'ok']);
        } catch (\Exception $e) {
            Log::error('PhonePe Webhook Error: ' . $e->getMessage());
            return response()->json(['status' => 'error'], 500);
        }
    }

    /**
     * ✅ SUCCESS
     */
    private function markAsSuccess($txnId, $data)
    {
        // Example:
        // Order::where('txn_id', $txnId)->update(['status' => 'paid']);

        Log::info("Payment SUCCESS: {$txnId}");
    }

    /**
     * ❌ FAILED
     */
    private function markAsFailed($txnId, $data)
    {
        // Order::where('txn_id', $txnId)->update(['status' => 'failed']);

        Log::info("Payment FAILED: {$txnId}");
    }

    /**
     * ⏳ PENDING
     */
    private function markAsPending($txnId, $data)
    {
        // Optional: store pending state

        Log::info("Payment PENDING: {$txnId}");
    }
    public function refundCallback(Request $request)
    {
        Log::info('Refund webhook', $request->all());

        // Handle refund success/failure
    }
}
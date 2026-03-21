<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Log;

class PhonePeService
{
    private string $clientId;
    private string $clientVersion;
    private string $clientSecret;
    private string $tokenUrl;
    private string $baseUrl;

    public function __construct()
    {
        $this->clientId      = config('services.phonepe.client_id');
        $this->clientVersion = (string) config('services.phonepe.client_version', '1');
        $this->clientSecret  = config('services.phonepe.client_secret');
        $this->tokenUrl      = config('services.phonepe.token_url');
        $this->baseUrl       = rtrim(config('services.phonepe.base_url'), '/');
    }

    // ─────────────────────────────────────────────
    // 🔐 GET OAuth TOKEN
    // ─────────────────────────────────────────────

    private function getAccessToken(bool $forceRefresh = false): string
    {
        if ($forceRefresh) {
            Cache::forget('phonepe_access_token');
        }

        return Cache::remember('phonepe_access_token', now()->addHours(23), function () {
            $response = Http::withHeaders([
                    'Content-Type' => 'application/x-www-form-urlencoded',
                ])
                ->timeout(15)
                ->asForm()
                ->post($this->tokenUrl, [
                    'client_id'      => $this->clientId,
                    'client_version' => $this->clientVersion,
                    'client_secret'  => $this->clientSecret,
                    'grant_type'     => 'client_credentials',
                ]);

            Log::info('PhonePe OAuth response', [
                'status' => $response->status(),
                'body'   => $response->json(),
            ]);

            if (! $response->successful() || empty($response->json('access_token'))) {
                throw new \Exception('PhonePe OAuth failed: ' . $response->body());
            }

            return $response->json('access_token');
        });
    }

    // ─────────────────────────────────────────────
    // 🔑 AUTH HEADERS
    // ─────────────────────────────────────────────

    private function authHeaders(bool $forceRefresh = false): array
    {
        return [
            'Content-Type'  => 'application/json',
            'Authorization' => 'O-Bearer ' . $this->getAccessToken($forceRefresh),
        ];
    }

    // ─────────────────────────────────────────────
    // 🌐 MAKE AUTHENTICATED REQUEST (auto-retry on 401)
    // ─────────────────────────────────────────────

    private function makeRequest(string $method, string $endpoint, array $payload = []): \Illuminate\Http\Client\Response
    {
        $url = $this->baseUrl . $endpoint;

        $response = Http::withHeaders($this->authHeaders())
            ->timeout(30)
            ->{$method}($url, $payload ?: null);

        // ✅ If 401 — token expired, refresh once and retry
        if ($response->status() === 401) {
            Log::warning('PhonePe 401 — refreshing token and retrying', ['url' => $url]);

            $response = Http::withHeaders($this->authHeaders(forceRefresh: true))
                ->timeout(30)
                ->{$method}($url, $payload ?: null);
        }

        return $response;
    }

    // ─────────────────────────────────────────────
    // 💳 CREATE PAYMENT
    // ─────────────────────────────────────────────

    public function createOrder($amount, $userId, $redirectUrl, $callbackUrl = null): array
    {
        $merchantOrderId = 'txn_' . uniqid();

        $payload = [
            'merchantOrderId' => $merchantOrderId,
            'amount'          => (int) ($amount * 100),
            'expireAfter'     => 1200,
            'paymentFlow'     => [
                'type'    => 'PG_CHECKOUT',
                'message' => 'Order Payment',
                'merchantUrls' => [
                    'redirectUrl' => $redirectUrl,
                ],
            ],
        ];

        Log::info('PhonePe createOrder request', [
            'url'     => $this->baseUrl . '/checkout/v2/pay',
            'payload' => $payload,
        ]);

        try {
            $response = $this->makeRequest('post', '/checkout/v2/pay', $payload);

            Log::info('PhonePe createOrder response', [
                'status' => $response->status(),
                'body'   => $response->json(),
            ]);

            $redirectUrlFromPhonePe = $response->json('redirectUrl') ?? null;

            return [
                'success'        => $response->successful() && ! empty($redirectUrlFromPhonePe),
                'data'           => $response->json(),
                'redirect_url'   => $redirectUrlFromPhonePe,
                'transaction_id' => $merchantOrderId,
            ];

        } catch (\Illuminate\Http\Client\ConnectionException $e) {
            Log::error('PhonePe connection failed', ['error' => $e->getMessage()]);

            return [
                'success'        => false,
                'data'           => null,
                'redirect_url'   => null,
                'transaction_id' => $merchantOrderId,
                'error'          => 'Connection failed: ' . $e->getMessage(),
            ];

        } catch (\Exception $e) {
            Log::error('PhonePe unexpected error', ['error' => $e->getMessage()]);

            return [
                'success'        => false,
                'data'           => null,
                'redirect_url'   => null,
                'transaction_id' => $merchantOrderId,
                'error'          => $e->getMessage(),
            ];
        }
    }

    // ─────────────────────────────────────────────
    // 🔍 CHECK ORDER STATUS
    // ─────────────────────────────────────────────

    public function checkStatus(string $merchantOrderId): array
    {
        Log::info('PhonePe checkStatus', ['transaction_id' => $merchantOrderId]);

        try {
            $response = $this->makeRequest('get', '/checkout/v2/order/' . $merchantOrderId . '/status');

            Log::info('PhonePe checkStatus response', [
                'status' => $response->status(),
                'body'   => $response->json(),
            ]);

            return $response->json() ?? [];

        } catch (\Exception $e) {
            Log::error('PhonePe checkStatus error', ['error' => $e->getMessage()]);
            return ['state' => 'FAILED', 'error' => $e->getMessage()];
        }
    }

    // ─────────────────────────────────────────────
    // 💸 INITIATE REFUND
    // ─────────────────────────────────────────────

    public function refund(string $merchantOrderId, $refundAmount): array
    {
        $merchantRefundId = 'refund_' . uniqid();

        $payload = [
            'merchantRefundId' => $merchantRefundId,
            'merchantOrderId'  => $merchantOrderId,
            'amount'           => (int) ($refundAmount * 100),
        ];

        Log::info('PhonePe refund request', [
            'url'     => $this->baseUrl . '/checkout/v2/refund',
            'payload' => $payload,
        ]);

        try {
            $response = $this->makeRequest('post', '/checkout/v2/refund', $payload);

            Log::info('PhonePe refund response', [
                'status' => $response->status(),
                'body'   => $response->json(),
            ]);

            return [
                'success'   => $response->successful(),
                'data'      => $response->json(),
                'refund_id' => $merchantRefundId,
            ];

        } catch (\Exception $e) {
            Log::error('PhonePe refund error', ['error' => $e->getMessage()]);

            return [
                'success'   => false,
                'data'      => null,
                'refund_id' => $merchantRefundId,
                'error'     => $e->getMessage(),
            ];
        }
    }

    // ─────────────────────────────────────────────
    // 🔍 CHECK REFUND STATUS
    // ─────────────────────────────────────────────

    public function refundStatus(string $merchantRefundId): array
    {
        try {
            $response = $this->makeRequest('get', '/checkout/v2/refund/' . $merchantRefundId . '/status');
            return $response->json() ?? [];
        } catch (\Exception $e) {
            Log::error('PhonePe refundStatus error', ['error' => $e->getMessage()]);
            return ['error' => $e->getMessage()];
        }
    }

    // ─────────────────────────────────────────────
    // 🔄 FORCE REFRESH TOKEN
    // ─────────────────────────────────────────────

    public function refreshToken(): void
    {
        $this->getAccessToken(forceRefresh: true);
    }
}
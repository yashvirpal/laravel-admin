<?php
// app/Listeners/MergeGuestCart.php

namespace App\Listeners;

use Illuminate\Auth\Events\Login;
use App\Services\CartService;
use Illuminate\Support\Facades\Log;

class MergeGuestCart
{
    protected CartService $cartService;

    public function __construct(CartService $cartService)
    {
        $this->cartService = $cartService;
    }

    /**
     * Handle the event.
     */
    public function handle(Login $event): void
    {
        $user = $event->user;
        $sessionId = session()->getId();

        Log::info('Login event fired, attempting cart merge', [
            'user_id' => $user->id,
            'session_id' => $sessionId
        ]);

        try {
            $this->cartService->mergeGuestCart($sessionId, $user->id);
            Log::info('Cart merge completed successfully');
        } catch (\Exception $e) {
            Log::error('Cart merge failed', [
                'user_id' => $user->id,
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
        }
    }
}
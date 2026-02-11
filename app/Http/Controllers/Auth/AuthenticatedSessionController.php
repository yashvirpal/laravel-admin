<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Http\Requests\Auth\LoginRequest;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;
use Illuminate\Validation\ValidationException;

use App\Facades\CartServiceFacade as CartFacade;

class AuthenticatedSessionController extends Controller
{
    /**
     * Display the login view.
     */
    public function create(): View
    {
        return view('auth.login');
    }

    /**
     * Handle an incoming authentication request.
     */


    // app/Http/Controllers/Auth/AuthenticatedSessionController.php (or your login controller)

    public function store(LoginRequest $request): RedirectResponse|\Illuminate\Http\JsonResponse
    {
        try {
            // CRITICAL: Get session ID BEFORE any authentication
            $guestSessionId = session()->getId();

            \Log::info('=== LOGIN STARTED ===', [
                'guest_session_id' => $guestSessionId
            ]);

            // Check if guest cart exists
            $guestCart = \App\Models\Cart::where('session_id', $guestSessionId)
                ->whereNull('user_id')
                ->first();

            \Log::info('Guest cart check', [
                'exists' => $guestCart ? 'yes' : 'no',
                'cart_id' => $guestCart?->id,
                'items_count' => $guestCart?->items()->count()
            ]);

            // Authenticate user
            $request->authenticate();

            $userId = auth()->id();

            \Log::info('User authenticated', [
                'user_id' => $userId
            ]);

            // Merge cart BEFORE regenerating session
            if ($guestCart && $guestCart->items()->count() > 0) {
                \Log::info('Merging guest cart before session regeneration', [
                    'guest_cart_id' => $guestCart->id,
                    'guest_session_id' => $guestSessionId,
                    'user_id' => $userId
                ]);

                try {
                    $cartService = app(\App\Services\CartService::class);
                    $cartService->mergeGuestCart($guestSessionId, $userId);
                    \Log::info('✅ Cart merge successful');
                } catch (\Exception $e) {
                    \Log::error('❌ Cart merge failed', [
                        'error' => $e->getMessage(),
                        'trace' => $e->getTraceAsString()
                    ]);
                }
            } else {
                \Log::info('No guest cart to merge or cart is empty');
            }

            // NOW regenerate session (this changes the session ID)
            $request->session()->regenerate();

            \Log::info('Session regenerated', [
                'new_session_id' => session()->getId()
            ]);

            if ($request->ajax()) {
                return response()->json([
                    'success' => true,
                    'message' => 'Login successful.',
                    'redirect_url' => route('dashboard'),
                ]);
            }

            return redirect()->intended(route('dashboard', absolute: false));

        } catch (ValidationException $e) {
            if ($request->ajax()) {
                return response()->json([
                    'success' => false,
                    'errors' => $e->errors(),
                ], 422);
            }

            throw $e;
        }
    }
    // public function store(LoginRequest $request): RedirectResponse|\Illuminate\Http\JsonResponse
    // {
    //     try {
    //         $request->authenticate(); // handles email + password
    //         $request->session()->regenerate();

    //         if ($request->ajax()) {
    //             return response()->json([
    //                 'success' => true,
    //                 'message' => 'Login successful.',
    //                 'redirect_url' => route('dashboard'),
    //             ]);
    //         }



    //         return redirect()->intended(route('dashboard', absolute: false));

    //     } catch (ValidationException $e) {

    //         if ($request->ajax()) {
    //             return response()->json([
    //                 'success' => false,
    //                 'errors' => $e->errors(),
    //             ], 422);
    //         }

    //         throw $e;
    //     }
    // }

    // public function store(LoginRequest $request): RedirectResponse
    // {
    //     $request->authenticate();

    //     $request->session()->regenerate();

    //     return redirect()->intended(route('dashboard', absolute: false));
    // }

    /**
     * Destroy an authenticated session.
     */
    public function destroy(Request $request): RedirectResponse
    {
        Auth::guard('web')->logout();

        $request->session()->invalidate();

        $request->session()->regenerateToken();

        return redirect('/');
    }
}

<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Password;
use Illuminate\View\View;

use Illuminate\Validation\ValidationException;

class PasswordResetLinkController extends Controller
{
    /**
     * Display the password reset link request view.
     */
    public function create(): View
    {
        return view('frontend.auth.forgot-password');
        //return view('auth.forgot-password');
    }

    /**
     * Handle an incoming password reset link request.
     *
     * @throws \Illuminate\Validation\ValidationException
     */


    public function store(Request $request)
    {
        try {
            $request->validate([
                'email' => ['required', 'email'],
            ]);

            $status = Password::sendResetLink(
                $request->only('email')
            );

            if ($status === Password::RESET_LINK_SENT) {

                //  AJAX response
                if ($request->ajax()) {
                    return response()->json([
                        'success' => true,
                        'message' => 'Password reset link has been sent to your email address.',
                    ], 200);
                }

                //  Normal redirect
                return back()->with('status', __($status));
            }

            //  Failed case
            throw ValidationException::withMessages([
                'email' => [__($status)],
            ]);

        } catch (ValidationException $e) {

            if ($request->ajax()) {
                return response()->json([
                    'success' => false,
                    'errors' => $e->errors(),
                ], 422);
            }

            throw $e;

        } catch (\Exception $e) {

            if ($request->ajax()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Something went wrong. Please try again later.',
                    'error'=>$e->getMessage(),
                ], 500);
            }

            return back()->with('error', 'Something went wrong.');
        }
    }

    // public function store(Request $request): RedirectResponse
    // {
    //     $request->validate([
    //         'email' => ['required', 'email'],
    //     ]);

    //     // We will send the password reset link to this user. Once we have attempted
    //     // to send the link, we will examine the response then see the message we
    //     // need to show to the user. Finally, we'll send out a proper response.
    //     $status = Password::sendResetLink(
    //         $request->only('email')
    //     );

    //     return $status == Password::RESET_LINK_SENT
    //                 ? back()->with('status', __($status))
    //                 : back()->withInput($request->only('email'))
    //                     ->withErrors(['email' => __($status)]);
    // }
}

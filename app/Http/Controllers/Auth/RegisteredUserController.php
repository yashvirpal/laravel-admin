<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Auth\Events\Registered;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules;
use Illuminate\View\View;
use Illuminate\Validation\ValidationException;
use Illuminate\Http\JsonResponse;

use Illuminate\Support\Facades\Mail;


class RegisteredUserController extends Controller
{
    /**
     * Display the registration view.
     */
    public function create(): View
    {
        //return view('auth.register');
        return view('frontend.auth.register');
    }

    /**
     * Handle an incoming registration request.
     *
     * @throws \Illuminate\Validation\ValidationException
     */


    public function store(Request $request): JsonResponse|RedirectResponse
    {
        try {
            //  Validation
            $validated = $request->validate([
                'name' => ['required', 'string', 'max:255'],
                'email' => ['required', 'email', 'max:255', 'unique:users,email'],
                'phone' => ['required', 'string', 'unique:users,phone'],
                'country_code' => ['nullable', 'string', 'max:5'],
                'password' => ['required', 'confirmed', Rules\Password::defaults()],
            ]);

            //  Create User
            $user = User::create([
                'name' => $validated['name'],
                'email' => $validated['email'],
                'password' => Hash::make($validated['password']),
                'phone' => $validated['phone'],
                'country_code' => $validated['country_code'] ?? '91',
                'status' => 1,
            ]);

            //  Events + Login
            event(new Registered($user));
            Auth::login($user);

            Mail::to($user->email)->send(new \App\Mail\WelcomeMail($user));

            //  AJAX Response
            if ($request->ajax()) {
                return response()->json([
                    'success' => true,
                    'message' => 'Account created successfully.',
                    'redirect_url' => route('profile.dashboard'),
                ], 200);
            }

            //  Normal Redirect
            return redirect()->route('profile.dashboard');

        } catch (ValidationException $e) {

            //  Validation errors (AJAX)
            if ($request->ajax()) {
                return response()->json([
                    'success' => false,
                    'errors' => $e->errors(),
                ], 422);
            }

            throw $e;

        } catch (\Exception $e) {
            \Log::error('Registration error: ' . $e->getMessage());
            //  Any other error
            if ($request->ajax()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Something went wrong. Please try again.',
                    'errors' => $e->getMessage(),
                ], 500);
            }

            return back()->with('error', 'Something went wrong. Please try again.');
        }
    }

    // public function store(Request $request): RedirectResponse
    // {
    //     $request->validate([
    //         'name' => ['required', 'string', 'max:255'],
    //         'email' => ['required', 'string', 'lowercase', 'email', 'max:255', 'unique:'.User::class],
    //         'password' => ['required', 'confirmed', Rules\Password::defaults()],
    //     ]);

    //     $user = User::create([
    //         'name' => $request->name,
    //         'email' => $request->email,
    //         'password' => Hash::make($request->password),
    //     ]);

    //     event(new Registered($user));

    //     Auth::login($user);

    //     return redirect(route('dashboard', absolute: false));
    // }
}

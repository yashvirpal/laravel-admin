<?php

use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;
use Illuminate\Auth\AuthenticationException;
use Illuminate\Session\TokenMismatchException;


return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__ . '/../routes/web.php',
        commands: __DIR__ . '/../routes/console.php',
        health: '/up',
    )
    ->withMiddleware(function (Middleware $middleware): void {
        $middleware->alias([
            'admin' =>  \App\Http\Middleware\RedirectIfNotAdmin::class,
            'admin.guest' =>  \App\Http\Middleware\RedirectIfAdmin::class,
        ]);
    })
    ->withExceptions(function (Exceptions $exceptions): void {
        //
        $exceptions->render(function (AuthenticationException $e, $request) {
            // If expecting JSON → return JSON
            if ($request->expectsJson()) {
                return response()->json([
                    'status' => false,
                    'message' => 'Please login first.',
                ], 401);
            }

            // Otherwise redirect to login
            return redirect()->guest(route('login'));
        });
        $exceptions->render(function (TokenMismatchException $e, $request) {
            if ($request->expectsJson()) {
                return response()->json([
                    'status' => false,
                    'message' => 'Session expired, please refresh and try again.',
                ], 419);
            }

            // For normal requests, redirect back or to login
            return redirect()
                ->back()
                ->withInput()
                ->with('error', 'Session expired, please try again.');
        });
    })->create();

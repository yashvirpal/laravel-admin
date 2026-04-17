<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

class RedirectIfNotAdmin
{
    /**
     * Handle an incoming request.
     */
    public function handle(Request $request, Closure $next): Response
    {
        // If not logged in as admin
        if (!Auth::guard('admin')->check()) {
            //return redirect()->route('admin.login');
            return redirect()->guest(route('admin.login'));

        }

        // If already logged in, continue to dashboard or requested page
        return $next($request);
    }
}

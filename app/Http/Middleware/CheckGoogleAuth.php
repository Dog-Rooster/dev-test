<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class CheckGoogleAuth
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        // Check if a Google access token is in session
        if (session()->has('google_access_token')) {
            return $next($request);
        }

        // Save the intedted page to session
        session([
            'intendedPage' => $request->fullUrl()
        ]);

        return redirect()->route('google.auth');
    }
}

<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;
use Google\Client as GoogleClient;

class CheckGoogleAuth
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        // Check if Google access token in session exists
        if (session()->has('google_access_token')) {
            $client = new GoogleClient;
            $client->setAccessToken(session('google_access_token'));
            // Refresh token if existing access token is expired
            if ($client->isAccessTokenExpired()) {
                $client->setAccessToken(session('google_access_token'));
                $newAccessToken = $client->fetchAccessTokenWithRefreshToken($client->getRefreshToken());
                session([
                    'google_access_token' => $newAccessToken
                ]);
            }

            return $next($request);
        }

        // Save the intended route to session so auth callback will know where to redirect
        session([
            'intendedRoute' => $request->fullUrl()
        ]);

        return redirect()->route('google.auth');
    }
}

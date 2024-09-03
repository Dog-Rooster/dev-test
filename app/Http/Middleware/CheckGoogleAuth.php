<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Google\Client as GoogleClient;

class CheckGoogleAuth
{
    protected $googleClient;

    public function __construct(GoogleClient $googleClient)
    {
        $this->googleClient = $googleClient;
    }

    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle(Request $request, Closure $next)
    {
        // Check if the Google token is set in the session
        if (!session()->has('google_token')) {
            // Redirect to an error page or show a message if the token is not set
            return redirect()->route('auth.google');

        }

        return $next($request);
    }

}

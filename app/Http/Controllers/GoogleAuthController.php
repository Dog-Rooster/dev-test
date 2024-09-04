<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Google\Client as GoogleClient;

class GoogleAuthController extends Controller
{
    protected $googleClient;

    public function __construct(GoogleClient $googleClient)
    {
        $this->googleClient = $googleClient;
    }

    public function redirectToGoogle()
    {

        $authUrl = $this->googleClient->createAuthUrl();

        return redirect($authUrl);
    }

    public function handleGoogleCallback(Request $request)
    {
        // Authenticate the user with the authorization code
        $this->googleClient->authenticate($request->input('code'));

        // Get the access token
        $token = $this->googleClient->getAccessToken();

        // Store the token in the session
        session(['google_token' => $token]);

        return redirect('/')->with('success', 'Google Calendar connected!');
    }
}

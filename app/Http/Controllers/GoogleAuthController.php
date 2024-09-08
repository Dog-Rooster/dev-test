<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Google\Client as GoogleClient;
use Google_Service_Calendar;

class GoogleAuthController extends Controller
{
    private $client;

    public function __construct(GoogleClient $googleClient)
    {
        // Setup Google client
        $this->client = $googleClient;
        $this->client->setApplicationName(config('app.name'));
        $this->client->setClientId(config('services.google.client_id'));
        $this->client->setClientSecret(config('services.google.client_secret'));
        $this->client->setRedirectUri(config('services.google.redirect_uri'));
        $this->client->setScopes(config('services.google.scopes'));
        $this->client->setApprovalPrompt(config('services.google.approval_prompt'));
        $this->client->setAccessType(config('services.google.access_type'));
        $this->client->setIncludeGrantedScopes(config('services.google.include_granted_scopes'));
    }
    
    /**
     * Authenticate and retrieve access token for user
     * @return \Illuminate\Http\RedirectResponse|\Illuminate\Routing\Redirector
     */
    public function createAuthUrl()
    {
        // Creates an auth URL so an Google access token can be generated for the user
        return redirect($this->client->createAuthUrl());
    }

    /**
     * Handle Google oauth callback
     * @param \Illuminate\Http\Request $request
     * @return \Illuminate\Http\RedirectResponse|\Illuminate\Routing\Redirector
     */
    public function handleAuthCallback(Request $request)
    {
        // Save access token to session
        session([
            'google_access_token' => $this->client->fetchAccessTokenWithAuthCode($request->get('code'))
        ]);

        return redirect(session('intended_route'));
    }
}

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
        $this->client = $googleClient;
        $this->client->setApplicationName(env('APP_NAME'));
        // TODO : Move auth config to env; PRIO : 2
        $this->client->setAuthConfig(base_path() . '/storage/google/config.json');
        $this->client->setAccessType('offline');
        $this->client->setApprovalPrompt('force');
        $this->client->setScopes(Google_Service_Calendar::CALENDAR);
    }

    /**
     * Creates an auth URL so an Google access token can be generated for the user
     * @return \Illuminate\Http\RedirectResponse|\Illuminate\Routing\Redirector
     */
    public function createAuthUrl()
    {
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

        // Redirect to the intedted page
        return redirect(session('intendedPage'));
    }
}

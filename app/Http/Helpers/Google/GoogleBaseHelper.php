<?php

namespace App\Http\Helpers\Google;

use Google\Client as GoogleClient;

class GoogleBaseHelper
{
    protected $client;

    private $service;

    public function __construct(GoogleClient $googleClient)
    {
        $this->client = $googleClient;
        $this->client->setAccessToken(session('google_access_token'));
        // Check if access token is expired and refresh if true
        if ($this->client->isAccessTokenExpired()) {
            // TODO : Fix issues with refreshing token ; PRIO : 1
            $this->client->fetchAccessTokenWithRefreshToken($this->client->getRefreshToken());
            session(['google_access_token' => $this->client->getAccessToken()]);
            $this->client->setAccessToken(session('google_access_token'));
        }
    }
}

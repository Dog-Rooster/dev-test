<?php

namespace App\Http\Helpers\Google;

use Google\Client as GoogleClient;

class GoogleBaseHelper
{
    protected $client;

    private $service;

    public function __construct(GoogleClient $googleClient, $accessToken = [])
    {
        $this->client = $googleClient;
        if (empty($accessToken)) {
            $accessToken = session('google_access_token');
        }
        $this->client->setAccessToken($accessToken);
        // Check if access token is expired and refresh if true
        // if ($this->client->isAccessTokenExpired()) {
        //     // TODO : Fix issues with refreshing token ; PRIO : 1
        //     $this->client->fetchAccessTokenWithRefreshToken($this->client->getRefreshToken());
        //     session(['google_access_token' => $this->client->getAccessToken()]);
        //     $this->client->setAccessToken(session('google_access_token'));
        // }
    }
}

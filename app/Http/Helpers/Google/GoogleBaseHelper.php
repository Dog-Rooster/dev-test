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
    }
}

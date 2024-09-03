<?php

namespace App\Providers;

use Google_Service_Calendar;
use Illuminate\Support\ServiceProvider;
use Google\Client as GoogleClient;
class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        $this->app->singleton(GoogleClient::class, function ($app) {
            $client = new GoogleClient();
            $client->setClientId(env('GOOGLE_CLIENT_ID'));
            $client->setClientSecret(env('GOOGLE_CLIENT_SECRET'));
            $client->setRedirectUri(env('GOOGLE_REDIRECT_URI'));
            $client->addScope(Google_Service_Calendar::CALENDAR);
            return $client;
        });
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        //
    }
}

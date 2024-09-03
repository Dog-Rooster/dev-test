<?php

namespace App\Providers;

use App\Models\Booking;
use App\Observers\BookingObserver;
use App\Services\GoogleCalendarService;
use App\Services\IcsGeneratorService;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        $this->app->singleton(IcsGeneratorService::class, function ($app) {
            return new IcsGeneratorService();
        });
        $this->app->singleton(GoogleCalendarService::class, function ($app) {
            return new GoogleCalendarService();
        });
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        Booking::observe(BookingObserver::class);
    }
}

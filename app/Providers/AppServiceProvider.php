<?php

namespace App\Providers;

use Carbon\Carbon;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\ServiceProvider;


class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        // Make sure booking date is a weekday
        Validator::extend('weekday', function ($attribute, $value, $parameters, $validator) {
            return Carbon::parse($value)->isWeekday();
        });
    }
}

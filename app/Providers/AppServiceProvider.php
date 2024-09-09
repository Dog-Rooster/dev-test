<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use App\Services\BookingService;
use App\Services\EventService;
use App\Services\TimeSlotService;
use App\Repositories\EloquentBookingRepository;
use App\Repositories\EloquentEventRepository;

use App\Contracts\BookingRepositoryInterface;
use App\Contracts\TimeSlotServiceInterface;
use App\Contracts\EventServiceInterface;
use App\Contracts\EventRepositoryInterface;
use App\Contracts\BookingServiceInterface;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        $this->app->bind(BookingServiceInterface::class, BookingService::class);
        $this->app->bind(EventServiceInterface::class, EventService::class);
        $this->app->bind(TimeSlotServiceInterface::class, TimeSlotService::class);
        $this->app->bind(BookingRepositoryInterface::class, EloquentBookingRepository::class);
        $this->app->bind(EventRepositoryInterface::class, EloquentEventRepository::class);
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        //
    }
}

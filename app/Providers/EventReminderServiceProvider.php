<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\Schedule;
use App\Models\Booking;
use Illuminate\Support\Facades\Mail;
use Carbon\Carbon;

class EventReminderServiceProvider extends ServiceProvider
{
    public function boot()
    {
        Schedule::call(function () {
            $bookings = Booking::with('event')
                               ->where('booking_date', now()->toDateString())
                               ->where('booking_time', '>=', now()->addHour()->format('H:i:s'))
                               ->where('booking_time', '<=', now()->addHour()->addMinute()->format('H:i:s'))
                               ->get();

            foreach ($bookings as $booking) {
                Mail::send('emails.reminder', ['booking' => $booking], function ($message) use ($booking) {
                    $message->to($booking->attendee_email)
                            ->subject('Event Reminder: ' . $booking->event->name);
                });
            }
        })->everyMinute();
    }

}

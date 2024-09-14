<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Booking;
use Illuminate\Support\Facades\Mail;
use App\Mail\EventReminder; // Make sure you have this Mailable

class SendEventReminders extends Command
{
    protected $signature = 'events:send-reminders';
    protected $description = 'Send reminders for events starting in 1 hour';

    public function __construct()
    {
        parent::__construct();
    }

    public function handle()
    {
        $now = now();
        $oneHourLater = $now->copy()->addHour();

        $bookings = Booking::whereBetween('booking_time', [$now, $oneHourLater])
            ->get();

        foreach ($bookings as $booking) {
            Mail::to($booking->attendee_email)->send(new EventReminder($booking));
        }

        $this->info('Event reminders sent successfully!');
    }
}

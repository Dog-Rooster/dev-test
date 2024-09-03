<?php

namespace App\Console\Commands;

use App\Models\Booking;
use App\Notifications\EventReminderNotification;
use Carbon\Carbon;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Notification;

class SendEventReminders extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'app:send-event-reminders';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Send reminders for events starting in 1 hour';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $now = Carbon::now('UTC');
        $oneHourLater = $now->copy()->addHour();

        $bookings = Booking::with('event')
            ->where('notification_sent', false)
            ->whereBetween('start_datetime', [$now, $oneHourLater])
            ->get();

        foreach ($bookings as $booking) {
            // Assuming 'attendee_email' is the column storing the attendee's email address
            Notification::route('mail', $booking->attendee_email)
                ->notify(new EventReminderNotification($booking));
            // Mark the event as notified
            $booking->notification_sent = true;
            $booking->save();
        }
        $this->info("{$now} : {$bookings->count()} Event reminders sent.");
    }
}

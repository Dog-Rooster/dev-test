<?php

namespace App\Console\Commands;

use App\Models\Booking;
use App\Notifications\BookingReminderNotification;
use Carbon\Carbon;
use Illuminate\Console\Command;

class SendEventBookingReminders extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'app:send-event-booking-reminders';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Sends reminder notifications for booked events';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        // Get the current time in UTC
        $now = Carbon::now('UTC');

        // Get all bookings for today and check if they are 1 hour before the event time, considering time zone
        $bookings = Booking::whereDate('booking_date', $now->format('Y-m-d'))
            ->where('has_sent_reminder', '!=', true)
            ->get();

        if($bookings->count() > 0) {
            foreach ($bookings as $booking) {
                // Convert the booking time to the attendee's time zone
                $startDateTime = Carbon::createFromFormat('Y-m-d H:i:s', "{$booking->booking_date} {$booking->booking_time}", $booking->timezone);

                // Convert the start time to UTC for comparison
                $eventStartInUTC = $startDateTime->setTimezone('UTC');
                $oneHourBeforeEvent = $eventStartInUTC->copy()->subHour();

                // Compare the current time in UTC with the event start time thats also in UTC
                if ($now->greaterThanOrEqualTo($oneHourBeforeEvent) && $now->lessThan($eventStartInUTC)) {
                    $booking->update(['has_sent_reminder' => true]);
                    $booking->notify(new BookingReminderNotification($booking));
                }
            }
        }
    }
}

<?php

namespace App\Observers;

use App\Notifications\NotifyHourBeforeEvent;
use Illuminate\Support\Facades\Notification;
use App\Models\Booking;
use Carbon\Carbon;

class BookingObserver
{
    /**
     * Handle the Booking "created" event.
     */
    public function created(Booking $booking): void
    {
        $delay = 0;
        // Get the number of minutes until event booking starts
        $bookingSlot = Carbon::parse($booking->bookingDate->booking_date . ' ' . $booking->booking_time);
        $minutesUntilNotification = (Carbon::now())->diffInMinutes($bookingSlot, true);
        // Add a delay to the booking notification
        if ($minutesUntilNotification > 60) {
            $delay = $minutesUntilNotification - 60;
        }
        // Notify booking attendee through email
        Notification::route('mail', [
            $booking->attendee_email => $booking->attendee_name
        ])->notify((new NotifyHourBeforeEvent($booking))->delay($delay));
    }
}

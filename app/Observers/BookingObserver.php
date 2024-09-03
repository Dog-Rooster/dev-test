<?php

namespace App\Observers;

use App\Mail\EventConfirmationMail;
use App\Models\Booking;
use App\Services\IcsGeneratorService;
use DateTime;
use DateTimeZone;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;

class BookingObserver
{
    protected $icsGeneratorService;

    public function __construct(IcsGeneratorService $icsGeneratorService)
    {
        $this->icsGeneratorService = $icsGeneratorService;
    }
    /**
     * Handle the Booking "created" event.
     */
    public function created(Booking $booking): void
    {
        // Generate DateTime String
        $startDateTime = new DateTime($booking->booking_date . 'T' . $booking->booking_time, new DateTimeZone($booking->timezone));
        $endDateTime = clone $startDateTime;
        $endDateTime->modify("+{$booking->event->duration} minutes");
        $startDateTimeStr = $startDateTime->format('Y-m-d\TH:i:s');
        $endDateTimeStr = $endDateTime->format('Y-m-d\TH:i:s');

        // Generate ICS file
        [$icsContent, $icsFileName] = $this->icsGeneratorService->generateIcsFile(
            $booking->event->name,
            $booking->event->description,
            $startDateTimeStr,
            $endDateTimeStr,
            $booking->timezone
        );

        // Send confirmation email with ICS attachment
        Mail::to($booking->attendee_email)->send(new EventConfirmationMail($icsContent, $icsFileName));
    }

    /**
     * Handle the Booking "updated" event.
     */
    public function updated(Booking $booking): void
    {
        //
    }

    /**
     * Handle the Booking "deleted" event.
     */
    public function deleted(Booking $booking): void
    {
        //
    }

    /**
     * Handle the Booking "restored" event.
     */
    public function restored(Booking $booking): void
    {
        //
    }

    /**
     * Handle the Booking "force deleted" event.
     */
    public function forceDeleted(Booking $booking): void
    {
        //
    }
}

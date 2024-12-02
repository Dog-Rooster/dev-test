<?php

namespace App\Jobs;

use App\Models\Booking;
use Carbon\Carbon;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Queue\Queueable;
use Spatie\GoogleCalendar\Event as SpatieEvent;

class CreateCalendarEvent implements ShouldQueue
{
    use Queueable;

    public Booking $booking;

    /**
     * Create a new job instance.
     */
    public function __construct(Booking $booking)
    {
        $this->booking = $booking;
    }

    /**
     * Execute the job.
     */
    public function handle(): void
    {
        $startDateTime = Carbon::createFromFormat('Y-m-d H:i:s', $this->booking->booking_date . ' ' . $this->booking->booking_time, $this->booking->timezone);
        $startDateTimeUtc = $startDateTime->setTimezone('UTC');
        $event = new SpatieEvent();
        $event->name = $this->booking->event->name;
        $event->description  = $this->booking->event->description ;
        $event->startDateTime = $startDateTimeUtc;
        $event->endDateTime = $startDateTimeUtc->copy()->addMinutes($this->booking->event->duration);
        $event->timezone = 'UTC';
        $event->save();
    }
}

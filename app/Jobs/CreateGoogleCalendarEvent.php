<?php

namespace App\Jobs;

use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Foundation\Queue\Queueable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Google\Client as GoogleClient;
use App\Http\Helpers\Google\GoogleCalendarHelper;
use App\Models\Booking;

class CreateGoogleCalendarEvent implements ShouldQueue
{
    use Queueable, SerializesModels;

    /**
     * Create a new job instance.
     */
    public function __construct(
        private Booking $booking,
        private $timeSlot,
        private $accessToken
    ) {}

    /**
     * Execute the job.
     */
    public function handle(): void
    {
        (new GoogleCalendarHelper(new GoogleClient, $this->accessToken))->createEvent($this->booking, $this->timeSlot);
    }
}

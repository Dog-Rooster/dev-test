<?php

namespace App\Http\Helpers\Google;

use Carbon\Carbon;
use Google\Client as GoogleClient;
use Google\Service\Calendar as GoogleCalendar;
use Google_Service_Calendar_Event;
use App\Models\Booking;

class GoogleCalendarHelper extends GoogleBaseHelper
{
    public function __construct(GoogleClient $googleClient, $accessToken)
    {
        parent::__construct($googleClient, $accessToken);
        $this->service = new GoogleCalendar($this->client);
    }

    /**
     * Create an event in user's primary Google calendar
     * @param \App\Models\Booking $booking
     * @param mixed $timeSlot
     * @return void
     */
    public function createEvent(Booking $booking, $timeSlot)
    {
        $event = new Google_Service_Calendar_Event([
            'summary' => $booking->event->name,
            'description' => $booking->attendee_name . ' has booked an event (' . $booking->event->name . ') on ' . $timeSlot . '.',
            'start' => [
                'dateTime' => Carbon::parse($timeSlot)->toIso8601String(),
                'timeZone' => 'UTC'
            ],
            'end' => [
                'dateTime' => Carbon::parse($timeSlot)->addMinutes($booking->event->duration)->toIso8601String(),
                'timeZone' => 'UTC'
            ],
            'attendees' => [
                [
                    'email' => $booking->attendee_email
                ]
            ]
        ]);

        $this->service->events->insert('primary', $event);
    }
}

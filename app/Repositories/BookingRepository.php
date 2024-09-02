<?php

namespace App\Repositories;

use App\Mail\EventConfirmationMail;
use App\Models\Booking;
use App\Models\Event;
use App\Repositories\Interfaces\BookingRepositoryInterface;
use App\Services\GoogleCalendarService;
use App\Services\IcsGeneratorService;
use DateTime;
use DateTimeZone;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;

class BookingRepository implements BookingRepositoryInterface
{
    protected $googleCalendarService;
    protected $icsGeneratorService;

    public function __construct(
        GoogleCalendarService $googleCalendarService,
        IcsGeneratorService $icsGeneratorService,
    )
    {
        $this->googleCalendarService = $googleCalendarService;
        $this->icsGeneratorService = $icsGeneratorService;
    }
    public function all()
    {
        $bookings = Booking::with('event')->get();
        return $bookings;
    }

    public function bookEvent(Request $request, Event $event){
        $title = $event->name;
        $description = $event->description;
        $duration = $event->duration;
        $attendeeName = $request->input('attendee_name');
        $attendeeEmail = $request->input('attendee_email');
        $bookingDate = $request->input('booking_date');
        $bookingTime = $request->input('booking_time');
        $timezone = $request->input('booking_timezone');

        // Generate DateTime String
        $startDateTime = new DateTime($bookingDate . 'T' . $bookingTime, new DateTimeZone($timezone));
        $endDateTime = clone $startDateTime;
        $endDateTime->modify("+{$duration} minutes");
        $startDateTimeStr = $startDateTime->format('Y-m-d\TH:i:s');
        $endDateTimeStr = $endDateTime->format('Y-m-d\TH:i:s');

        $startDateTimeUTC = clone $startDateTime; // Clone to avoid modifying the original DateTime object
        $startDateTimeUTC->setTimezone(new DateTimeZone('UTC'));
        $endDateTimeUTC = clone $endDateTime; // Clone to avoid modifying the original DateTime object
        $endDateTimeUTC->setTimezone(new DateTimeZone('UTC'));
        $startDateTimeStrUTC = $startDateTimeUTC->format('Y-m-d H:i:s');
        $endDateTimeStrUTC = $endDateTimeUTC->format('Y-m-d H:i:s');

        //TODO check collision
        if($this->canBookNewEvent()){
            $descriptionGoogleCalendar = $description.' with '.$attendeeName . ' (' . $attendeeEmail.')';
            $googleevents = $this->googleCalendarService->createEvent($title, $descriptionGoogleCalendar, $duration, $attendeeName, $attendeeEmail, $startDateTimeStr, $endDateTimeStr, $timezone);

            Log::Info($googleevents->getId());
            if($googleevents->getId()){
                $booking = new Booking();
                $booking->attendee_name = $attendeeName;
                $booking->attendee_email = $attendeeEmail;
                $booking->event_id = $event->id;
                $booking->booking_date = $bookingDate;
                $booking->booking_time = $bookingTime;
                $booking->timezone = $timezone;
                $booking->start_datetime = $startDateTimeStrUTC;
                $booking->end_datetime = $endDateTimeStrUTC;

                $booking->save();

                // Generate ICS file
                [$icsContent, $icsFileName] = $this->icsGeneratorService->generateIcsFile(
                    $title,
                    $description,
                    $startDateTimeStr,
                    $endDateTimeStr,
                    $timezone
                );

                // Send confirmation email with ICS attachment
                Mail::to($attendeeEmail)->send(new EventConfirmationMail($icsContent, $icsFileName));

                return $booking;
            }
            else {
                return null;
            }
        }
        else{
            return null;
        }
    }

    private function canBookNewEvent(){
        return true;
    }
}

<?php

namespace App\Http\Controllers;

use App\Models\Booking;
use App\Models\BookingDate;
use App\Models\Event;
use App\Models\Timezone;
use Carbon\Carbon;
use Illuminate\Http\Request;
use App\Http\Helpers\Google\GoogleCalendarHelper;
use Google\Client as GoogleClient;
use DateTimeZone;
use Date;

class BookingController extends Controller
{
    public function index()
    {
        $bookings = Booking::with('event')->get();

        return view('bookings.index', compact('bookings'));
    }

    public function store(Request $request, $eventId)
    {
        // TODO: Add backend validation for double booking; PRIO: 2

        // Convert selected timeslot to UTC timezone for storing
        $bookingTimeslot = Carbon::createFromFormat('Y-m-d H:i', 
            $request->input('booking_date') . ' ' . $request->input('booking_time'), 
            $request->input('booking_timezone'))
            ->setTimezone('UTC');

        $bookingDate = new BookingDate();
        // Save booking date if not yet existing in database
        $bookingDateId = $bookingDate->firstOrCreate([
            'booking_date' => $bookingTimeslot->toDateString()
        ])->id;

        $timezone = new Timezone();
        // Save timezone if not yet existing in database
        $timezoneId = $timezone->firstOrCreate([
            'timezone' => $request->input('booking_timezone')
        ])->id;

        // Set new booking data
        $booking = new Booking();
        $booking->attendee_name = $request->input('attendee_name');
        $booking->attendee_email = $request->input('attendee_email');

        $booking->event_id = $eventId;

        $booking->booking_date_id = $bookingDateId;
        $booking->booking_time = $bookingTimeslot->toTimeString();

        $booking->timezone_id = $timezoneId;

        // TODO : Move to jobs; PRIO : 1
        // Create a new event in Google calendar
        $calendarHelper = new GoogleCalendarHelper(new GoogleClient);
        $calendarHelper->createEvent($booking, $bookingTimeslot);

        $booking->save();

        // Set thank you page booking date and time to original date and time
        $booking->booking_date =  $request->input('booking_date');
        $booking->booking_time = $request->input('booking_time');
        return view('bookings.thank-you', ['booking' => $booking]);
    }

    public function create(Request $request, $eventId)
    {
        // TODO: Move to resource; PRIO: 2
        $event = Event::findOrFail($eventId);
        $selectedDate = $request->input('booking_date', now()->toDateString());
        $selectedTimezone = $request->input('booking_timezone', env('APP_TIMEZONE', 'UTC'));

        // Get all time slots
        $timeSlots = $this->generateTimeSlots($selectedDate, $selectedTimezone, $event);
        // Get all available timezones
        $timezones = DateTimeZone::listIdentifiers(DateTimeZone::ALL);

        return view('bookings.calendar', compact('event', 'timeSlots', 'timezones', 'selectedDate', 'selectedTimezone'));
    }

    /**
     * Generate time slots to be selected by the user
     * @param mixed $date
     * @param mixed $timezone
     * @param mixed $event
     * @return array<bool|string>[]
     */
    private function generateTimeSlots($date, $timezone, $event)
    {
        $startOfDay = Carbon::parse($date)->startOfDay();
        $endOfDay = Carbon::parse($date)->startOfDay()->addHours(24);
        $interval = $event->duration; // Set interval to duration of event

        // Get all bookings for a specific event a day before and after the selected data
        $bookings = Booking::select('booking_date', 'booking_time')
            ->join('booking_dates', 'bookings.booking_date_id', '=', 'booking_dates.id')
            ->whereBetween('booking_date', [Carbon::parse($date)->subHours(24), Carbon::parse($date)->addHours(24)])
            ->get()
            ->all();

        // Since time slots are stored in UTC timezone, 
        // convert all booking time slots to the selected timezone and store them in an array
        $bookedTimeSlots = [];
        foreach ($bookings as $booking) {
            $bookedTimeSlots[] = Carbon::parse($booking->booking_date . ' ' . $booking->booking_time)->setTimezone($timezone)->toDateTimeString();
        }
        
        // Store selectable time slots in an array
        $timeSlots = [];
        while ($startOfDay < $endOfDay) {
            $end = $startOfDay->copy()->addMinutes($interval);

            // Convert from selected timezone to UTC
            $utcDate = Carbon::createFromFormat('Y-m-d H:i:s', 
                $startOfDay, 
                $timezone)
            ->setTimezone('UTC');

            // Skip adding time slot if datetime is restricted
            if ($this->checkIfDateIsRestricted($utcDate)) {
                $timeSlots[] = [
                    'time' => $startOfDay->format('H:i'),
                    // If time slot is already booked (time slot is in bookedTimeSlots array), 
                    // select button for the time slot will be disabled
                    'booked' => in_array($startOfDay, $bookedTimeSlots),
                    // If time slot is already in the past, 
                    // select button for the time slot will be disabled
                    'past' => $utcDate->isPast()
                ];
            }

            $startOfDay = $end;
        }
        
        return $timeSlots;
    }

    /**
     * Check if date is a weekday and between 08:00 - 17:00
     * @param mixed $date
     * @return bool
     */
    private function checkIfDateIsRestricted($date)
    {
        return $date->isWeekday() && $date->isBetween(Date::createFromTimeString($date->toDateString() . '08:00'), Date::createFromTimeString($date->toDateString() . '17:00'));
    }
}

<?php

namespace App\Http\Controllers;

use App\Models\Booking;
use App\Models\Event;
use Carbon\Carbon;
use Illuminate\Http\Request;
use DateTimeZone;

class BookingController extends Controller
{
    public function index()
    {
        $bookings = Booking::with('event')->get();

        return view('bookings.index', compact('bookings'));
    }

    public function store(Request $request, $eventId)
    {
        // TODO: Add timezone to booking table; PRIO: 1
        // TODO: Add backend validation for double booking; PRIO: 2
        $bookingTimeslot = Carbon::createFromFormat('Y-m-d H:i', 
            $request->input('booking_date') . ' ' . $request->input('booking_time'), 
            $request->input('booking_timezone'))->setTimezone('UTC');

        $booking = new Booking();
        $booking->attendee_name = $request->input('attendee_name');
        $booking->attendee_email = $request->input('attendee_email');
        $booking->event_id = $eventId;
        $booking->booking_date = $bookingTimeslot->toDateString();
        $booking->booking_time = $bookingTimeslot->toTimeString();
        $booking->save();

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

        $timeSlots = $this->generateTimeSlots($selectedDate, $selectedTimezone, $event);
        $timezones = DateTimeZone::listIdentifiers(DateTimeZone::ALL);

        return view('bookings.calendar', compact('event', 'timeSlots', 'timezones', 'selectedDate', 'selectedTimezone'));
    }

    private function generateTimeSlots($date, $timezone, $event)
    {
        $startOfDay = Carbon::parse($date)->startOfDay();
        $endOfDay = Carbon::parse($date)->startOfDay()->addHours(24);
        $interval = $event->duration;
        $bookings = Booking::select('booking_date', 'booking_time')
            ->whereBetween('booking_date', [Carbon::parse($date)->subHours(24), Carbon::parse($date)->addHours(24)])
            ->get()
            ->all();

        $bookedTimeSlots = [];
        foreach ($bookings as $booking) {
            $bookedTimeSlots[] = Carbon::parse($booking->booking_date . ' ' . $booking->booking_time)->setTimezone($timezone)->toDateTimeString();
        }
        
        $timeSlots = [];
        while ($startOfDay < $endOfDay) {
            $end = $startOfDay->copy()->addMinutes($interval);

            $timeSlots[] = [
                'time' => $startOfDay->format('H:i'),
                'booked' => in_array($startOfDay, $bookedTimeSlots)
            ];

            $startOfDay = $end;
        }
        
        return $timeSlots;
    }
}

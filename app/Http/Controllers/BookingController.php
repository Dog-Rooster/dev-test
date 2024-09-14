<?php

namespace App\Http\Controllers;

use App\Http\Requests\BookingRequest;
use App\Mail\CalendarInvite;
use App\Models\Booking;
use App\Models\Event;
use App\Services\CalendarService;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;

class BookingController extends Controller
{
    protected $calendarService;

    public function __construct(CalendarService $calendarService)
    {
        $this->calendarService = $calendarService;
    }

    public function index()
    {
        $bookings = Booking::with('event')->get();

        return view('bookings.index', compact('bookings'));
    }

    public function store(BookingRequest $request, $eventId)
    {
        //return $request;
        $event = Event::findOrFail($eventId);
        $eventName = Event::select('name')->where('id', $eventId)->first();
        $timeZone = $request->time_zone;
        $bookingDateTime = Carbon::createFromFormat('Y-m-d H:i', $request->booking_date . ' ' . $request->booking_time, $timeZone);
        $bookingDateTimeUtc = $bookingDateTime->copy()->setTimezone('UTC');
        $formattedBookingTime = $bookingDateTimeUtc->format('H:i');

        $existingBooking = Booking::where('event_id', $eventId)
            ->where('booking_date', $request->booking_date)
            ->where('booking_time', $formattedBookingTime)
            ->first();

        if ($existingBooking) {
            return redirect()->back()->withErrors([
                'booking_conflict' => 'This event is already booked for the selected date and time. Please choose another slot.',
            ])->withInput();
        }

        $booking = Booking::create([
            'attendee_name' => $request->attendee_name,
            'attendee_email' => $request->attendee_email,
            'event_id' => $eventId,
            'booking_date' => $request->booking_date,
            'booking_time' => $formattedBookingTime,
        ]);

        $summary = "Booking for " . $request->attendee_name;
        $dateTimeString = $request->booking_date . 'T' . $formattedBookingTime . ':00';
        $startDateTime = Carbon::createFromFormat('Y-m-d\TH:i:s', $dateTimeString, 'Asia/Manila');
        $endDateTime = $startDateTime->copy()->addMinutes(30);
        $attendees = [$request->attendee_email];
        $timezone = $request->time_zone;

        $eventDetails = [
            'summary' => $summary,
            'description' => $eventName->name,
            'start' => [
                'dateTime' => $startDateTime->format('Y-m-d\TH:i:sP'),
                'timeZone' => $timezone,
            ],
            'end' => [
                'dateTime' => $endDateTime->format('Y-m-d\TH:i:sP'),
                'timeZone' => $timezone,
            ],
        ];

        $googleEvent = $this->calendarService->createEvent($eventDetails);

        $icsContent = $this->calendarService->createICS($summary, $startDateTime, $endDateTime, $attendees);

        Mail::to($request->attendee_email)->queue(new CalendarInvite($icsContent, $eventName->name));

        return view('bookings.thank-you', ['booking' => $booking]);
    }

    public function create(Request $request, $eventId)
    {
        $event = Event::findOrFail($eventId);
        $selectedDate = $request->input('booking_date', now()->toDateString());
        $currentTimeZone = $request->time_zone;
        $currentTimeZone = $currentTimeZone ?? 'UTC';

        $existingBooking = Booking::select('booking_time')
            ->where('event_id', $eventId)
            ->where('booking_date', $selectedDate)
            ->pluck('booking_time')
            ->toArray();

        $existingBooking = array_map(function ($time) use ($currentTimeZone) {
            return Carbon::createFromFormat('H:i', $time, 'UTC')
                ->setTimezone($currentTimeZone)
                ->format('H:i');
        }, $existingBooking);

        $timeZones = \DateTimeZone::listIdentifiers();

        $timeSlots = $this->generateTimeSlots($selectedDate, $existingBooking, $currentTimeZone);


        return view('bookings.calendar', compact('event', 'timeSlots', 'selectedDate', 'timeZones'));
    }

    private function generateTimeSlots($date, $existingBooking, $currentTimeZone)
    {
        $startOfDay = Carbon::parse($date, $currentTimeZone)->startOfDay();
        $endOfDay = $startOfDay->copy()->addHours(24);
        $interval = 30; // 30 minutes per time block

        $timeSlots = [];

        while ($startOfDay < $endOfDay) {
            $end = $startOfDay->copy()->addMinutes($interval);
            $slotTime = $startOfDay->format('H:i');
            $isBooked = in_array($slotTime, $existingBooking);

            $timeSlots[] = [
                'time' => $slotTime,
                'available' => $isBooked ? 'disabled' : '',
                'text' => $isBooked ? 'Full' : 'Select',
                'opacity' => $isBooked ? 'opacity-25' : ''
            ];

            $startOfDay = $end;
        }

        return $timeSlots;
    }
}

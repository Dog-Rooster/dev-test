<?php

namespace App\Http\Controllers;

use App\Models\Booking;
use App\Models\Event;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Google\Service\Calendar;
use Google\Service\Calendar\Event as GoogleEvent;
use Google\Service\Calendar\EventDateTime;
use Illuminate\Support\Facades\Auth;


class BookingController extends Controller
{
    public function index()
    {
        // Ensure the user is authenticated
        if (!Auth::check()) {
            return redirect()->route('login'); // Redirect to login if not authenticated
        }

        // Retrieve bookings for the authenticated user
        $user = Auth::user();
        $bookings = Booking::where('attendee_email', $user->email)->get();

        return view('bookings.index', compact('bookings'));
    }

    public function store(Request $request, $eventId)
    {
        $event = Event::findOrFail($eventId);
        $booking = new Booking();
        $booking->attendee_name = $request->input('attendee_name');
        $booking->attendee_email = $request->input('attendee_email');
        $booking->event_id = $eventId;
        $booking->booking_date = $request->input('booking_date');
        $booking->booking_time = $request->input('booking_time');
        $booking->timezone = $request->input('timezone');
        $booking->save();

        // Now integrate with Google Calendar
        try {
            // Get the authenticated user
            $user = Auth::user();

            // Create a new Google client
            $client = new \Google\Client();
            $client->setAccessToken($user->google_token);

            // Check if the token has expired, and refresh it if necessary
            // dd('test', $user->google_token, $client->isAccessTokenExpired());
            if ($client->isAccessTokenExpired()) {
                $userRefreshToken = $user->google_refresh_token;
                $test = $client->fetchAccessTokenWithRefreshToken($userRefreshToken);

                $user->google_token = $client->getAccessToken();
                $user->save();
            }

            // Create a new Google Calendar service instance
            $calendarService = new Calendar($client);

            $startDateTime = $booking->booking_date . 'T' . $booking->booking_time;
            $endDateTime = date('Y-m-d\TH:i:s', strtotime($startDateTime . ' +' . $event->duration . ' minutes'));
            // Create a new Google Calendar event
            $googleEvent = new GoogleEvent([
                'summary' => $event->name,
                'location' => 'Sample',
                'description' => $event->description,
                'start' => new EventDateTime([
                    'dateTime' => $startDateTime,
                    'timeZone' => $this->getTimezoneName( $request->input('timezone') ),
                ]),
                'end' => new EventDateTime([
                    'dateTime' => $endDateTime,
                    'timeZone' => $this->getTimezoneName( $request->input('timezone') ),
                ]),
            ]);
            // Insert the event into the user's primary Google Calendar
            $createdEvent = $calendarService->events->insert('primary', $googleEvent);

            // Store the Google Calendar event ID in the booking
            $booking->google_calendar_event_id = $createdEvent->id;
            $booking->save();

        } catch (\Exception $e) {
            // Handle errors, e.g., log the error and notify the user that calendar integration failed
            \Log::error('Google Calendar API error: ' . $e->getMessage());
        }

        return view('bookings.thank-you', ['booking' => $booking]);
    }

    public function update(Request $request, $eventId, $bookingId)
    {
        // Find the event and the booking
        $event = Event::findOrFail($eventId);
        $booking = Booking::findOrFail($bookingId);

        // Update booking details
        $booking->attendee_name = $request->input('attendee_name');
        $booking->attendee_email = $request->input('attendee_email');
        $booking->booking_date = $request->input('booking_date');
        $booking->booking_time = $request->input('booking_time');
        $booking->timezone = $request->input('timezone');
        $booking->save();

        // Now integrate with Google Calendar
        try {
            // Get the authenticated user
            $user = Auth::user();

            // Create a new Google client
            $client = new \Google\Client();
            $client->setAccessToken($user->google_token);

            // Check if the token has expired, and refresh it if necessary
            if ($client->isAccessTokenExpired()) {
                $userRefreshToken = $user->google_refresh_token;
                $client->fetchAccessTokenWithRefreshToken($userRefreshToken);
                $user->google_token = $client->getAccessToken();
                $user->save();
            }

            // Create a new Google Calendar service instance
            $calendarService = new \Google_Service_Calendar($client);
            if ($booking->google_calendar_event_id) {
                // Retrieve the existing Google Calendar event
                $googleEvent = $calendarService->events->get('primary', $booking->google_calendar_event_id);

                // Update the event details
                $startDateTime = $booking->booking_date . 'T' . $booking->booking_time;
                $endDateTime = date('Y-m-d\TH:i:s', strtotime($startDateTime . ' +' . $event->duration . ' minutes'));

                $googleEvent->setSummary($event->name);
                $googleEvent->setDescription($event->description);
                $googleEvent->setStart(new \Google_Service_Calendar_EventDateTime([
                    'dateTime' => $startDateTime,
                    'timeZone' => $this->getTimezoneName( $request->input('timezone') ),
                ]));
                $googleEvent->setEnd(new \Google_Service_Calendar_EventDateTime([
                    'dateTime' => $endDateTime,
                    'timeZone' => $this->getTimezoneName( $request->input('timezone') ),
                ]));

                // Update the event in Google Calendar
                $updatedEvent = $calendarService->events->update('primary', $googleEvent->getId(), $googleEvent);

            } else {
                // If no Google Calendar event exists, create a new one
                $startDateTime = $booking->booking_date . 'T' . $booking->booking_time;
                $endDateTime = date('Y-m-d\TH:i:s', strtotime($startDateTime . ' +' . $event->duration . ' minutes'));

                $googleEvent = new \Google_Service_Calendar_Event([
                    'summary' => $event->name,
                    'description' => $event->description,
                    'start' => new \Google_Service_Calendar_EventDateTime([
                        'dateTime' => $startDateTime,
                        'timeZone' => $this->getTimezoneName( $request->input('timezone') ),
                    ]),
                    'end' => new \Google_Service_Calendar_EventDateTime([
                        'dateTime' => $endDateTime,
                        'timeZone' => $this->getTimezoneName( $request->input('timezone') ),
                    ]),
                ]);

                // Insert the event into the user's primary Google Calendar
                $newEvent = $calendarService->events->insert('primary', $googleEvent);

                // Store the new Google Calendar event ID in the booking record
                $booking->google_calendar_event_id = $newEvent->getId();
                $booking->save();
            }

        } catch (\Exception $e) {
            // Handle errors, e.g., log the error and notify the user that calendar integration failed
            \Log::error('Google Calendar API error: ' . $e->getMessage());
        }

        return redirect()->route('bookings.index')->with('success', 'Booking updated successfully!');
    }

    public function create(Request $request, $eventId)
    {
        $timezone = $request->query('timezone');

        $selectedTimezoneName = $this->getTimezoneName( $timezone );

        $event = Event::findOrFail($eventId);

        $selectedDate = $request->input('booking_date', now()->toDateString());

        $timeSlots = $this->generateTimeSlots($selectedDate);

        return view('bookings.create', compact('event', 'timeSlots', 'selectedDate', 'selectedTimezoneName', ));
    }

    public function createCalendar(Request $request, $eventId)
    {
        $event = Event::findOrFail($eventId);

        $selectedDate = $request->input('booking_date', now()->toDateString());

        $bookedTimes = Booking::whereDate('booking_date', $selectedDate)->pluck('booking_time')->toArray();

        $timeSlots = $this->generateTimeSlots($selectedDate, $bookedTimes);

        $routeName = 'bookings.create';

        $routeParams = [$event->id];

        return view('bookings.calendar', compact('event', 'timeSlots', 'selectedDate', 'routeName', 'routeParams'));
    }

    public function edit(Request $request, $eventID, $bookingID)
    {
        $timezone = $request->query('timezone');

        $selectedTimezoneName = $this->getTimezoneName( $timezone );

        $booking = Booking::findOrFail($bookingID);

        $event = $booking->event;

        $selectedDate = $booking->booking_date;

        $selectedTime = $booking->booking_time;

        $timeSlots = $this->generateTimeSlots($selectedDate);

        return view('bookings.edit', compact('booking', 'event', 'timeSlots', 'selectedDate', 'selectedTime', 'selectedTimezoneName'));
    }

    public function editCalendar($eventID, $bookingID)
    {
        $booking = Booking::findOrFail($bookingID);

        $event = Event::findOrFail($eventID);

        $selectedDate = $booking->booking_date;

        $selectedTime = $booking->booking_time;

        $bookedTimes = Booking::whereDate('booking_date', $selectedDate)->pluck('booking_time')->toArray();

        $timeSlots = $this->generateTimeSlots($selectedDate, $bookedTimes);

        $routeName = 'bookings.edit';

        $routeParams = [$eventID, $bookingID];

        return view('bookings.calendar', compact('booking', 'event', 'timeSlots', 'selectedDate', 'selectedTime', 'routeName', 'routeParams'));
    }

    private function generateTimeSlots($date, $bookedTimes = [])
    {
        $startOfDay = Carbon::parse($date)->startOfDay();
        $endOfDay = Carbon::parse($date)->startOfDay()->addHours(24);
        $interval = 30; // 30 minutes per time block

        $timeSlots = [];

        while ($startOfDay < $endOfDay) {
            $end = $startOfDay->copy()->addMinutes($interval);

            $time = $startOfDay->format('H:i:00');

            $timeSlots[] = [
                'time' => $time,
                'is_booked' => in_array($time, $bookedTimes),
            ];

            $startOfDay = $end;
        }

        return $timeSlots;
    }

    public function getTimeSlots(Request $request)
    {
        $date = $request->input('date');

        $bookedTimes = Booking::whereDate('booking_date', $date)->pluck('booking_time')->toArray();

        $startOfDay = Carbon::parse($date)->startOfDay();
        $endOfDay = Carbon::parse($date)->startOfDay()->addHours(24);
        $interval = 30; // 30 minutes per time block

        $timeSlots = [];

        while ($startOfDay < $endOfDay) {
            $end = $startOfDay->copy()->addMinutes($interval);

            $time = $startOfDay->format('H:i:00');

            $timeSlots[] = [
                'time' => $time,
                'is_booked' => in_array($time, $bookedTimes),
            ];

            $startOfDay = $end;
        }

        return $timeSlots;
    }
    public function generateTimeZones()
    {
        $timezones = \DateTimeZone::listIdentifiers(\DateTimeZone::ALL);

        $formattedTimezones = array_map(function ($key, $value) {
            return [
                'value' => $key,
                'text' => $value,
            ];
        }, array_keys($timezones), $timezones);

        return response()->json($formattedTimezones);
    }

    private function getTimezoneName($timezone)
    {
        // Fetch timezones
        $timezones = $this->generateTimeZones()->original;

        // Find the text for the selected timezone value
        $selectedTimezoneName = collect($timezones)->firstWhere('value', $timezone);

        $selectedTimezoneName = $selectedTimezoneName['text'];

        return $selectedTimezoneName;
    }
}

<?php

namespace App\Http\Controllers;

use App\Models\Booking;
use App\Models\Event;
use Carbon\Carbon;
use Google_Service_Calendar_Event;
use Illuminate\Http\Request;
use Google\Client as GoogleClient;
use Google\Service\Calendar as GoogleCalendar;
class BookingController extends Controller
{
    protected $googleClient;

    /**
     * @param GoogleClient $googleClient
     */
    public function __construct(GoogleClient $googleClient)
    {
        $this->googleClient = $googleClient;
        // Retrieve the token from the session
        $token = session('google_token');

        if ($token) {
            // Set the access token to the Google client
            $this->googleClient->setAccessToken($token);

            // Refresh the token if it's expired
            if ($this->googleClient->isAccessTokenExpired()) {
                $this->googleClient->refreshToken($this->googleClient->getRefreshToken());
                session(['google_token' => $this->googleClient->getAccessToken()]);
            }
        }

    }
    public function index()
    {
        $bookings = Booking::with('event')->get();

        return view('bookings.index', compact('bookings'));
    }

    public function store(Request $request, $eventId)
    {
        try {
            $event = Event::findOrFail($eventId);

            // Handle Timezone
            $userTimeZone = $request->input('timezone');
            $bookingDateTime = Carbon::parse($request->input('booking_date') . ' ' . $request->input('booking_time'), $userTimeZone)
                                     ->setTimezone('UTC');
            // Collision Detection
            if ($this->isTimeSlotTaken($eventId, $bookingDateTime)) {
                return back()->withErrors(['time_slot' => 'This time slot is already taken. Please choose another.']);
            }

            // Check if the time slot is in the past
            if ($bookingDateTime->isPast()) {
                return redirect()->route('bookings.create', ['event' => $eventId])
                                 ->withErrors(['time_slot' => 'The selected time slot is already in the past. Please choose a future time slot.']);
            }

            /**
             * Create a new Booking instance and populate its attributes.
             *
             * @param Request $request The incoming request object.
             * @param int $eventId The ID of the event being booked.
             * @param Carbon\Carbon $bookingDateTime The date and time of the booking.
             *
             * @return Booking The newly created Booking instance.
             */
            $booking = new Booking();

            // Set the attendee's name from the request input.
            $booking->attendee_name = $request->input('attendee_name');

            // Set the attendee's email from the request input.
            $booking->attendee_email = $request->input('attendee_email');

            // Associate the booking with the specified event.
            $booking->event_id = $eventId;

            // Set the booking date from the parsed booking date and time.
            $booking->booking_date = $bookingDateTime->toDateString();

            // Set the booking time from the parsed booking date and time.
            $booking->booking_time = $bookingDateTime->toTimeString();


            // Add to Google Calendar
            $this->addBookingToGoogleCalendar($booking, $event, $userTimeZone);

            $booking->save();

            return view('bookings.thank-you', ['booking' => $booking]);

        } catch (\Exception $e) {
            return back()->withErrors(['error' => $e->getMessage()]);
        }
    }


    /**
     * Checks if a time slot is taken for a specific event.
     *
     * @param int $eventId The ID of the event to check.
     * @param Carbon\Carbon $bookingDateTime The date and time to check.
     *
     * @return bool True if the time slot is taken, false otherwise.
     */
    private function isTimeSlotTaken($eventId, $bookingDateTime)
    {
        // Check if there's an existing booking for this event at the same time
        // We use the exists() method to check if any records match the given criteria
        return Booking::where('event_id', $eventId)
                      // Filter by booking date
                      ->where('booking_date', $bookingDateTime->toDateString())
                      // Filter by booking time
                      ->where('booking_time', $bookingDateTime->toTimeString())
                      // Check if any records exist
                      ->exists();
    }

    /**
     * Adds a booking to the Google Calendar.
     *
     * @param Booking $booking The booking to add to the calendar.
     * @param Event $event The event associated with the booking.
     * @param string $userTimeZone The time zone of the user.
     */
    private function addBookingToGoogleCalendar(Booking $booking, Event $event, $userTimeZone)
    {
        // Create a new instance of the Google Calendar service using the authenticated client.
        $service = new GoogleCalendar($this->googleClient);

        // Create a new Google Calendar event.
        $googleEvent = new Google_Service_Calendar_Event([
            // Set the summary of the event to the name of the event.
            'summary' => $event->name,
            // Set the description of the event to include the attendee's name.
            'description' => 'Booking for ' . $booking->attendee_name,
            // Set the start time of the event.
            'start' => [
                // Format the start time as a string in the format 'YYYY-MM-DDTHH:MM:SS'.
                'dateTime' => $booking->booking_date . 'T' . $booking->booking_time,
                // Set the time zone of the event.
                'timeZone' => $userTimeZone,
            ],
            // Set the end time of the event.
            'end' => [
                // Parse the start time and add 30 minutes to get the end time.
                'dateTime' => Carbon::parse($booking->booking_date . ' ' . $booking->booking_time, $userTimeZone)->addMinutes(30)->toIso8601String(),
                // Set the time zone of the event.
                'timeZone' => $userTimeZone,
            ],
        ]);

        // Insert the new event into the primary calendar.
        $service->events->insert('primary', $googleEvent);
    }

    public function create(Request $request, $eventId)
    {
        $event = Event::findOrFail($eventId);
        $selectedDate = $request->input('booking_date', now()->toDateString());
        $userTimeZone = $request->input('timezone', 'UTC');

        $timeSlots = $this->generateTimeSlots($selectedDate, $userTimeZone);

        return view('bookings.calendar', compact('event', 'timeSlots', 'selectedDate', 'userTimeZone'));
    }

    private function generateTimeSlots($date, $userTimeZone)
    {
        $startOfDay = Carbon::parse($date, $userTimeZone)->startOfDay();
        $endOfDay = Carbon::parse($date, $userTimeZone)->endOfDay();
        $interval = 30; // 30 minutes per time block

        $timeSlots = [];

        while ($startOfDay < $endOfDay) {
            $timeSlots[] = [
                'time' => $startOfDay->format('H:i'),
            ];
            $startOfDay->addMinutes($interval);
        }

        return $timeSlots;
    }
}

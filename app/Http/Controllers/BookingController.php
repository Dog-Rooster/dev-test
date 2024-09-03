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

            $booking = new Booking();
            $booking->attendee_name = $request->input('attendee_name');
            $booking->attendee_email = $request->input('attendee_email');
            $booking->event_id = $eventId;
            $booking->booking_date = $bookingDateTime->toDateString();
            $booking->booking_time = $bookingDateTime->toTimeString();


            // Add to Google Calendar
            $this->addBookingToGoogleCalendar($booking, $event, $userTimeZone);

            $booking->save();

            return view('bookings.thank-you', ['booking' => $booking]);

        } catch (\Exception $e) {
            return back()->withErrors(['error' => $e->getMessage()]);
        }
    }

    private function isTimeSlotTaken($eventId, $bookingDateTime)
    {
        // Check if there's an existing booking for this event at the same time
        return Booking::where('event_id', $eventId)
                      ->where('booking_date', $bookingDateTime->toDateString())
                      ->where('booking_time', $bookingDateTime->toTimeString())
                      ->exists();
    }
    private function addBookingToGoogleCalendar(Booking $booking, Event $event, $userTimeZone) {

        $service = new GoogleCalendar($this->googleClient);

        $googleEvent = new Google_Service_Calendar_Event([
            'summary' => $event->name,
            'description' => 'Booking for ' . $booking->attendee_name,
            'start' => [
                'dateTime' => $booking->booking_date . 'T' . $booking->booking_time,
                'timeZone' => $userTimeZone,
            ],
            'end' => [
                'dateTime' => Carbon::parse($booking->booking_date . ' ' . $booking->booking_time, $userTimeZone)->addMinutes(30)->toIso8601String(),
                'timeZone' => $userTimeZone,
            ],
        ]);


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

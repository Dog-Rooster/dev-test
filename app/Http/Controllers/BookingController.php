<?php

namespace App\Http\Controllers;

use App\Http\Requests\BookingRequest;
use App\Jobs\CreateCalendarEvent;
use App\Models\Booking;
use App\Models\Event;
use App\Notifications\BookingsNotification;

class BookingController extends Controller
{
    public function index()
    {
        $bookings = Booking::with('event')->get();
        return view('bookings.index', compact('bookings'));
    }

    public function store(BookingRequest $request, $eventId)
    {
        $event = Event::findOrFail($eventId);
        $booking = $event->bookings()->create($request->validated());

        // Dispatch a job to create the Google Calendar event
        CreateCalendarEvent::dispatch($booking);

        // Dispatch email notification
        $booking->notify(new BookingsNotification($booking));

        return view('bookings.thank-you', ['booking' => $booking]);
    }

    public function create($eventId)
    {
        $event = Event::findOrFail($eventId);
        return view('bookings.calendar', compact('event'));
    }
}

<?php

namespace App\Repositories;

use App\Models\Booking;
use Illuminate\Support\Facades\Cache;
use App\Contracts\BookingRepositoryInterface;

class EloquentBookingRepository implements BookingRepositoryInterface
{
    public function create(array $data)
    {
        $booking = new Booking();
        $booking->attendee_name = $data['attendee_name'];
        $booking->attendee_email = $data['attendee_email'];
        $booking->event_id = $data['event_id'];
        $booking->booking_date = $data['booking_date'];
        $booking->booking_time = $data['booking_time'];
        $booking->timezone = $data['timezone'];
        $booking->save();

        return $booking;
    }

    public function find($bookingId)
    {
        return Booking::findOrFail($bookingId);
    }

    public function getBookedSlots($eventId, $date)
    {
        $timeslot = Booking::where('event_id', $eventId)
                           ->where('booking_date', $date)
                           ->pluck('booking_time');
        return $timeslot;
    }
}

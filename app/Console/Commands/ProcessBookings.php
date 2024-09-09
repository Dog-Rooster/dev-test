<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;

use App\Models\Booking;
use App\Models\Event;
use App\Mail\BookingConfirmation;
use Sabre\VObject\Component\VEvent;
use Sabre\VObject\Component\VCalendar;
use Carbon\Carbon;
use Illuminate\Support\Facades\Mail;

class ProcessBookings extends Command
{
    protected $signature = 'bookings:process';
    protected $description = 'Process and send booking confirmations';

    public function __construct()
    {
        parent::__construct();
    }

    public function handle()
    {
        $cacheKey = 'pending_bookings';
        $bookings = Booking::where('email_is_sent', false)->get();

        if(!empty($bookings)){
            foreach ($bookings as $booking) {
    
                $selectedTimeZone = $booking->timezone;
                $bookingDateTime = Carbon::createFromFormat('Y-m-d H:i', $booking->booking_date . ' ' . date('H:i',strtotime($booking->booking_time)) , $selectedTimeZone);
                $endTime = $bookingDateTime->copy()->addMinutes($booking->event->duration);
    
                $calendar = new VCalendar();
                $iCalEvent = $calendar->add('VEVENT', [
                    'SUMMARY' => $booking->event->name,
                    'DESCRIPTION' => $booking->event->description,
                    'DTSTART' => $bookingDateTime->format('Ymd\THis'),
                    'DTEND' => $endTime->format('Ymd\THis'),
                    'UID' => uniqid() . '@example.com',
                    'SEQUENCE' => 1,
                ]);
    
                $icsFile = $calendar->serialize(); 
    
                Mail::to($booking->attendee_email)->send(new BookingConfirmation($icsFile, $booking->event, $booking));
    
                $booking->email_is_sent = true;
                $booking->save();
            }
        }
    }
}

<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class EventReminder extends Mailable
{
    use Queueable, SerializesModels;

    public $booking;

    /**
     * Create a new message instance.
     *
     * @param  Booking  $booking
     * @return void
     */
    public function __construct($booking)
    {
        $this->booking = $booking;
    }

    public function build()
    {
        return $this->view('emails.event_reminder')
            ->subject('Upcoming Event Reminder');
    }
}

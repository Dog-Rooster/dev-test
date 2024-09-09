<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class BookingConfirmation extends Mailable
{
    use Queueable, SerializesModels;

    public $event;
    public $icsFile;
    public $booking;

    /**
     * Create a new message instance.
     *
     * @param string $icsFile
     * @param \App\Models\Event $event
     */
    public function __construct(string $icsFile, $event, $booking)
    {
        $this->icsFile = $icsFile;
        $this->event = $event;
        $this->booking = $booking;
    }

    /**
     * Build the message.
     *
     * @return $this
     */
    public function build()
    {
        return $this->view('emails.booking_confirmation')
                    ->subject('Booking Confirmation')
                    ->attachData($this->icsFile, 'booking.ics', [
                        'mime' => 'text/calendar',
                    ]);
    }
}

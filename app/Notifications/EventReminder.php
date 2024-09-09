<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Carbon\Carbon;

class EventReminder extends Notification implements ShouldQueue
{
    use Queueable;

    protected $booking;

    public function __construct($booking)
    {
        $this->booking = $booking;
    }

    public function via($notifiable)
    {
        return ['mail'];
    }

    public function toMail($notifiable)
    {

        $formattedDate = Carbon::parse($this->booking->booking_date)->format('F j, Y'); 
        $formattedTime = Carbon::parse($this->booking->booking_time)->format('g:i A'); 

        return (new MailMessage)
                    ->subject('Event Reminder - '. $this->booking->event->name)
                    ->greeting('Hello ' . $this->booking->attendee_name . ',')
                    ->line('This is a reminder for your upcoming event.')
                    ->line('Event: ' . $this->booking->event->name)
                    ->line('Description: ' . $this->booking->event->description)
                    ->line('Event Date: ' . $formattedDate)
                    ->line('Event Time: ' . $formattedTime)
                    ->line('Duration: ' . $this->booking->event->duration)
                    ->line('This is just a reminder that your event will start in one hour.')
                    ->line('Thank you for using our application!')
                    ->salutation('Best regards, Demo Booking');
    }
}


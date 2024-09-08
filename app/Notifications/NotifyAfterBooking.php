<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;
use App\Models\Booking;
use Illuminate\Support\Facades\Log;
use Carbon\Carbon;
use Spatie\IcalendarGenerator\Components\Calendar;
use Spatie\IcalendarGenerator\Components\Event;

class NotifyAfterBooking extends Notification
{
    use Queueable;

    /**
     * Create a new notification instance.
     */
    public function __construct(
        private Booking $booking,
        private $bookingSlot
    ) {}

    /**
     * Get the notification's delivery channels.
     *
     * @return array<int, string>
     */
    public function via(object $notifiable): array
    {
        return ['mail'];
    }

    /**
     * Get the mail representation of the notification.
     */
    public function toMail(object $notifiable): MailMessage
    {
        // Create ics file and attach to email
        $icsFile = Calendar::create()
            ->event(Event::create($this->booking->event->name . ' Event Notification')
                ->period(Carbon::parse($this->bookingSlot), Carbon::parse($this->bookingSlot)->addMinutes($this->booking->event->duration))
                ->attendee($this->booking->attendee_email)
        )->get();
        Log::info('Booking confirmation sent to ' . $this->booking->attendee_email . '. | Booking id : ' . $this->booking->id . '.');
        return (new MailMessage)
                    ->subject('Event Confirmation')
                    ->greeting($this->booking->event->name . ' Event Confirmation')
                    ->line('Hello ' . $this->booking->attendee_name . ',')
                    ->line('Your booking for ' . $this->booking->event->name . ' on ' . $this->bookingSlot . ' has been confirmed.')
                    ->line('Please check the attached .ics file.')
                    ->line('Thank you and we are looking forward to seeing you!')
                    ->attachData($icsFile, 'calendar.ics', [
                        'mime' => 'text/calendar',
                    ]);
    }

    /**
     * Get the array representation of the notification.
     *
     * @return array<string, mixed>
     */
    public function toArray(object $notifiable): array
    {
        return [
            //
        ];
    }
}

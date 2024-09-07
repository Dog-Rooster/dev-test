<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;
use App\Models\Booking;
use Illuminate\Support\Facades\Log;

class NotifyHourBeforeEvent extends Notification implements ShouldQueue
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
        Log::info('Booking notification sent to ' . $this->booking->attendee_email . '. | Booking id : ' . $this->booking->id . '.');
        return (new MailMessage)
                    ->subject('Event Notification')
                    ->greeting($this->booking->event->name . ' Event Notification')
                    ->line('Hello ' . $this->booking->attendee_name . ',')
                    ->line('This email is to notify you of you upcoming ' . $this->booking->event->name . ' event on ' . $this->bookingSlot . '.')
                    ->line('Again, thank you and we are looking forward to seeing you!');
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

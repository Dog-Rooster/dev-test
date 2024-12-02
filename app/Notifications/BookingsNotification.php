<?php

namespace App\Notifications;

use App\Models\Booking;
use Carbon\Carbon;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;
use Spatie\IcalendarGenerator\Components\Calendar;
use Spatie\IcalendarGenerator\Components\Event as SpatieIcalendarEvent;

class BookingsNotification extends Notification implements ShouldQueue
{
    use Queueable;

    public Booking $booking;

    /**
     * Create a new notification instance.
     */
    public function __construct(Booking $booking)
    {
        $this->booking = $booking;
    }

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
        return (new MailMessage)
            ->subject('Booking Confirmation')
            ->line('Your event has been successfully booked.')
            ->line('Event Name: ' . $this->booking->event->name)
            ->line('Date: ' . $this->booking->booking_date)
            ->line('Time: ' . $this->booking->booking_time)
            ->line('Timezone: ' . $this->booking->timezone)
            ->attachData($this->generateIcsContent(), 'event.ics', [
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

    private function generateIcsContent()
    {
        $startDateTime = Carbon::createFromFormat('Y-m-d H:i:s', $this->booking->booking_date . ' ' . $this->booking->booking_time, $this->booking->timezone);
        $startDateTimeUtc = $startDateTime->setTimezone('UTC');
        $endDateTimeUtc = $startDateTimeUtc->copy()->addMinutes($this->booking->event->duration);
        return Calendar::create()
            ->event(SpatieIcalendarEvent::create()
                ->name($this->booking->event->name)
                ->description($this->booking->event->description)
                ->startsAt($startDateTimeUtc)
                ->endsAt($endDateTimeUtc)
            )
            ->get();
    }
}

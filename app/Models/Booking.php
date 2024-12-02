<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Notifications\Notifiable;
use Illuminate\Notifications\Notification;

class Booking extends Model
{
    use HasFactory;

    use Notifiable;

    protected $table = 'bookings';

    protected $fillable = [
        'attendee_name',
        'attendee_email',
        'booking_date',
        'booking_time',
        'timezone',
        'has_sent_reminder',
    ];

    public function routeNotificationForMail(Notification $notification): string
    {
        return $this->attendee_email;
    }

    public function event()
    {
        return $this->belongsTo(Event::class);
    }
}

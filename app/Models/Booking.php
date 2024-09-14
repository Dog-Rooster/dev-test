<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Booking extends Model
{
    use HasFactory;
    protected $table = 'bookings';
    protected $fillable = ['attendee_name', 'attendee_email', 'event_id', 'booking_date', 'booking_time'];

    public function event()
    {
        return $this->belongsTo(Event::class);
    }
}

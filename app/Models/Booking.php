<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Attributes\ObservedBy;

class Booking extends Model
{
    use HasFactory;
    protected $table = 'bookings';

    public function event()
    {
        return $this->belongsTo(Event::class);
    }

    public function bookingDate()
    {
        return $this->belongsTo(BookingDate::class);
    }
}

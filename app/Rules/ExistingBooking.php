<?php

namespace App\Rules;

use App\Models\BookingDate;
use App\Models\Booking;
use Closure;
use Illuminate\Contracts\Validation\DataAwareRule;
use Illuminate\Contracts\Validation\ValidationRule;

class ExistingBooking implements ValidationRule, DataAwareRule
{    
    /**
     * All of the data under validation.
     *
     * @var array<string, mixed>
    */
    protected $data = [];


    /**
     * Set the data under validation.
     *
     * @param  array<string, mixed>  $data
    */
    public function setData(array $data): static
    {
        $this->data = $data;

        return $this;
    }

    /**
     * Run the validation rule.
     *
     * @param  \Closure(string): \Illuminate\Translation\PotentiallyTranslatedString  $fail
     */
    public function validate(string $attribute, mixed $value, Closure $fail): void
    {
        $bookingDate = BookingDate::where('booking_date', $value)
            ->first();
        // If booking date is not in booking_dates table, then immediately pass the rule
        if (isset($bookingDate->id)) {
            // Check if booking with provided event_id, booking_date_id, and booking_time exists in the database
            $bookingExists = Booking::where('event_id', $this->data['event_id'])
                ->where('booking_date_id', $bookingDate->id)
                ->where('booking_time', $this->data['booking_time'])
                ->exists();
            if ($bookingExists) {
                $fail('A booking with in the same time slot already exists.');
            }
        }
    }
}

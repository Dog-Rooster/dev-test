<?php

namespace App\Rules;

use Carbon\Carbon;
use Closure;
use Illuminate\Contracts\Validation\DataAwareRule;
use Illuminate\Contracts\Validation\ValidationRule;

class IsDatePast implements ValidationRule, DataAwareRule
{
    /**
     * All of the data under validation.
     *
     * @var array<string, mixed>
    */
    protected $timeSlot;


    /**
     * Set the data under validation.
     *
     * @param  array<string, mixed>  $data
    */
    public function setData(array $data): static
    {
        $this->timeSlot = Carbon::parse($data['booking_date'] . ' ' . $data['booking_time']);

        return $this;
    }

    /**
     * Run the validation rule.
     *
     * @param  \Closure(string): \Illuminate\Translation\PotentiallyTranslatedString  $fail
     */
    public function validate(string $attribute, mixed $value, Closure $fail): void
    {
        // Check if provided time slot is in the past
        $isPast = $this->timeSlot->isPast();
        if ($isPast) {
            $fail('The provided time slot is a past date.');
        }
    }
}

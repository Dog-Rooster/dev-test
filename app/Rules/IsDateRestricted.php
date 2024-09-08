<?php

namespace App\Rules;

use Carbon\Carbon;
use Closure;
use Illuminate\Contracts\Validation\DataAwareRule;
use Illuminate\Contracts\Validation\ValidationRule;
use Date;

class IsDateRestricted implements ValidationRule, DataAwareRule
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
        // Check if provided time slot is not a weekday and between 08:00 - 17:00
        $isRestricted = !($this->timeSlot->isWeekday() &&
         $this->timeSlot->isBetween(Date::createFromTimeString($this->timeSlot->toDateString() . '08:00'), Date::createFromTimeString($this->timeSlot->toDateString() . '17:00')));
        if ($isRestricted) {
            $fail('The provided time slot is restricted. It may either be during a weekend or not between 08:00 and 17:00.');
        }
    }
}

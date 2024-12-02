<?php

namespace App\Livewire\Booking;

use Carbon\Carbon;
use Livewire\Component;

class DateAndTimePicker extends Component
{

    public $type;
    public $name;
    public $id;
    public $schedule;
    public $message;
    public $validationErrors = false;
    public $required = false;
    public $currentValue;


    public function updatedSchedule($value)
    {
        $this->validateSelectedSchedule($value);
    }

    private function validateSelectedSchedule($value)
    {
        $this->validationErrors = false;
        switch ($this->type) {
            case 'date':
                $selected = Carbon::parse($value);

                // Validate if selected date is a weekday Mon-Fri
                $dayOfWeek = $selected->dayOfWeek;
                if($dayOfWeek == 0 || $dayOfWeek == 6){
                    $this->schedule = null;
                    $this->validationErrors = true;
                    $this->message = 'You cannot book on weekends';
                    return;
                }

                // Validate if the selected date is in the future
                $today = Carbon::today();
                if ($selected->lt($today)) {
                    $this->schedule = null;
                    $this->validationErrors = true;
                    $this->message = 'The selected date has already passed!';
                    return;
                }

                break;
            case 'time':
                $selected = Carbon::createFromFormat('H:i', $value);
                $startTime = Carbon::createFromTime(8, 0);
                $endTime = Carbon::createFromTime(17, 0);

                // Validate if the selected time is outside the allowed range 8am-5pm
                if ($selected->lt($startTime) || $selected->gt($endTime)) {
                    $this->schedule = null;
                    $this->validationErrors = true;
                    $this->message = 'Please select a time between 8:00 AM and 5:00 PM.';
                    return;
                }
                break;
        }

    }

    public function render()
    {
        return view('livewire.booking.date-and-time-picker');
    }
}

<?php

namespace App\Http\Requests;

use Carbon\Carbon;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Validator;
use Spatie\GoogleCalendar\Event;

class BookingRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'attendee_name' => ['required', 'string', 'max:100'],
            'attendee_email' => ['required', 'string', 'email', 'max:255'],
            'booking_date' => ['required', 'date', 'date_format:Y-m-d', 'after_or_equal:today', 'weekday'],
            'booking_time' => ['required', 'date_format:H:i', 'after:07:59', 'before:17:00'],
            'timezone' => ['required', 'timezone:all'],
        ];
    }

    public function after()
    {
        return [
            function (Validator $validator) {
                if ($this->hasConflictSchedule()) {
                    $validator->errors()->add(
                        'schedule_conflict',
                        'Sorry, there\'s a conflict with another schedule. Please choose another date and time'
                    );
                }
            }
        ];
    }

    private function hasConflictSchedule()
    {
        $bookedEvents = Event::get();
        if($bookedEvents->count() > 0) {
            $selectedStartDateTime = $this->input('booking_date') . ' ' . $this->input('booking_time'); // Guest selected schedule
            $eventDuration = (int) $this->input('event_duration'); // Event duration
            $selectedTimeZone = $this->input('timezone');  // Guest selected timezone
            $startDateTime = Carbon::createFromFormat('Y-m-d H:i', $selectedStartDateTime, $selectedTimeZone); // Formatted guest selected schedule
            $startDateTimeUtc = $startDateTime->setTimezone('UTC'); // Convert to UTC
            $endDateTimeUtc = $startDateTimeUtc->copy()->addMinutes($eventDuration); // Calculate end time based on event duration

            // Loop through all booked events from Google Calendar
            foreach ($bookedEvents as $bookedEvent) {
                $eventStart = Carbon::parse($bookedEvent->startDateTime);
                $eventEnd = Carbon::parse($bookedEvent->endDateTime);

                // Check if schedule conflicts
                if (($startDateTimeUtc < $eventEnd) && ($endDateTimeUtc > $eventStart)) {
                    return true;
                }
            }
        }
        return false;
    }
}

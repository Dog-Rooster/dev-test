<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreBookingRequest extends FormRequest
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
        // TODO : Add validation if time slot with the same event id already exists; PRIO : 1
        return [
            'event_id' => ['required', 'exists:events,id'],
            'attendee_name' => ['required', 'max:128'],
            'attendee_email' => ['required', 'email', 'max:64'],
            'booking_date' => ['required', 'date_format:Y-m-d'],
            'booking_time' => ['required', 'date_format:H:i'],
            'booking_timezone' => ['required', 'timezone:all']
        ];
    }
}

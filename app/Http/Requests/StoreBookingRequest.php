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
    public function rules()
    {
        return [
            'attendee_name' => 'required|string|max:255',
            'attendee_email' => 'required|email|max:255',
            'booking_date' => 'required',
            'booking_time' => 'required',
            'timezone' => 'required',
        ];
    }

    public function messages()
    {
        return [
            'attendee_name.required' => 'Attendee name is required.',
            'attendee_email.required' => 'Attendee email is required.',
            'attendee_email.email' => 'The email address must be a valid email format.',
            'booking_date.required' => 'Booking date is required.',
            'booking_date.date' => 'Booking date must be a valid date.',
            'booking_time.required' => 'Booking time is required.',
            'timezone.required' => 'Timezone is required.',
        ];
    }

}

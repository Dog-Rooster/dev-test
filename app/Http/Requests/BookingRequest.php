<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Log;

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
            'attendee_name' => 'required|string',
            'attendee_email' => 'required|email',
            'booking_date' => 'required|date',
            'booking_time' => 'required',
            'time_zone' => 'nullable|string',
        ];
    }

    public function messages()
    {
        return [
            'attendee_name.required' => 'Attendee name is required',
            'attendee_email.required' => 'Attendee email is required',
            'booking_date.required' => 'The booking date is required.',
            'booking_time.required' => 'The booking time is required.',
        ];
    }

    protected function prepareForValidation()
    {
        $this->merge([
            'time_zone' => $this->input('time_zone') ?? 'UTC',
        ]);
    }
}

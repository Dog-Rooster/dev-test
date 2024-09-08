<?php

namespace App\Http\Requests;

use App\Rules\ExistingBooking;
use App\Rules\IsDatePast;
use App\Rules\IsDateRestricted;
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
        return [
            'event_id' => ['bail', 'required', 'exists:events,id'],
            'attendee_name' => ['bail', 'required', 'max:128'],
            'attendee_email' => ['bail', 'required', 'email', 'max:64'],
            'booking_date' => ['bail', 'required', 'date_format:Y-m-d', new IsDateRestricted, new IsDatePast, new ExistingBooking],
            'booking_time' => ['bail', 'required', 'date_format:H:i'],
            'booking_timezone' => ['bail', 'required', 'timezone:all']
        ];
    }
}

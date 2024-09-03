<?php

namespace Database\Factories;

use App\Models\Booking;
use App\Models\Event;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

class BookingFactory extends Factory
{
    protected $model = Booking::class;

    public function definition()
    {
        return [
            'event_id' => Event::factory(),
            'booking_time' => $this->faker->dateTimeBetween('now', '+1 month')->format('H:i'),
            'booking_date' => $this->faker->date(),
            'attendee_name' => $this->faker->name(),
            'attendee_email' => $this->faker->email(),
            'timezone' => $this->faker->timezone(),
            'start_datetime' => $this->faker->datetime()->format('Y-m-d H:i:s'),
            'end_datetime' => $this->faker->dateTimeBetween('now', '+1 hour')->format('Y-m-d H:i:s'),
            'notification_sent' => false,
        ];
    }
}

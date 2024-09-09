<?php

namespace Tests\Feature;

use Tests\TestCase;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithoutMiddleware;
use App\Models\Booking;
use App\Models\Event;

class BookingControllerTest extends TestCase
{
    use RefreshDatabase, WithoutMiddleware;

    public function testIndex()
    {
        $response = $this->get('/bookings');

        $response->assertStatus(200);
        $response->assertViewIs('bookings.index');
    }

    public function testStore()
    {
        $event = Event::factory()->create();

        $response = $this->post('/bookings/store/' . $event->id, [
            'attendee_name' => 'Jane Doe',
            'attendee_email' => 'jane@example.com',
            'booking_date' => '2024-09-08',
            'booking_time' => '11:00',
            'timezone' => 'Asia/Manila',
        ]);

        $response->assertStatus(200);
        $response->assertViewIs('bookings.thank-you');

        $this->assertDatabaseHas('bookings', [
            'attendee_name' => 'Jane Doe',
            'attendee_email' => 'jane@example.com',
            'event_id' => $event->id,
            'booking_date' => '2024-09-08',
            'booking_time' => '11:00',
        ]);
    }

    public function testBookingThankYou()
    {
        $booking = Booking::factory()->create();

        $response = $this->get('/bookings/thank-you/' . $booking->id);

        $response->assertStatus(200);
        $response->assertViewIs('bookings.thank-you');
    }

    public function testCreate()
    {
        $event = Event::factory()->create();

        $response = $this->get('/bookings/create/' . $event->id);

        $response->assertStatus(200);
        $response->assertViewIs('bookings.calendar');
    }
}


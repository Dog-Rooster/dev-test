<?php

namespace Tests\Feature;

use Illuminate\Support\Facades\Log;
use Mockery;
use Tests\TestCase;
use App\Models\Event;
use App\Models\User;
use App\Models\Booking;
use Illuminate\Foundation\Testing\RefreshDatabase;

class BookingFeatureTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        // Mock GoogleCalendarService
        $this->mockGoogleCalendarService(true);
    }

    protected function mockGoogleCalendarService($result)
    {
        $mock = Mockery::mock('App\Services\GoogleCalendarService');
        $mock->shouldReceive('createEvent')
            ->andReturn($result); // or any appropriate return value
        $this->app->instance('App\Services\GoogleCalendarService', $mock);
    }

    public function testUserCanCreateBooking()
    {
        $user = User::factory()->create();
        $event = Event::factory()->create();

        $response = $this->actingAs($user)->post('/events/' . $event->id . '/book', [
            'event_id' => $event->id,
            'booking_time' => '08:00',
            'booking_date' => '2024-09-03',
            'attendee_email' => 'test@test.com',
            'attendee_name' => 'Best Tester',
            'booking_timezone' => 'America/New_York',

        ]);
        $response->assertSee('Thank You!');
        $response->assertSee($event->name);
    }

    public function testUserCanNotCreateBookingBecauseGoogleCalendarServiceFailed()
    {
        $this->mockGoogleCalendarService(false);

        $user = User::factory()->create();
        $event = Event::factory()->create();

        $response = $this->actingAs($user)->post('/events/' . $event->id . '/book', [
            'event_id' => $event->id,
            'booking_time' => '08:00',
            'booking_date' => '2024-09-03',
            'attendee_email' => 'test@test.com',
            'attendee_name' => 'Best Tester',
            'booking_timezone' => 'America/New_York',

        ]);
        Log::Info($response->getContent());
        $response->assertSee('Failed!');
        $response->assertSee('Google Calendar Event Failed');
    }

    public function testUserCanNotCreateBookingBecasueBookingTimeIsRestricted()
    {
        $user = User::factory()->create();
        $event = Event::factory()->create();

        $response = $this->actingAs($user)->post('/events/' . $event->id . '/book', [
            'event_id' => $event->id,
            'booking_time' => '07:00',
            'booking_date' => '2024-09-03',
            'attendee_email' => 'test@test.com',
            'attendee_name' => 'Best Tester',
            'booking_timezone' => 'America/New_York',

        ]);
        $response->assertSee('Booking datetime is restricted');

        $response = $this->actingAs($user)->post('/events/' . $event->id . '/book', [
            'event_id' => $event->id,
            'booking_time' => '18:00',
            'booking_date' => '2024-09-03',
            'attendee_email' => 'test@test.com',
            'attendee_name' => 'Best Tester',
            'booking_timezone' => 'America/New_York',

        ]);
        $response->assertSee('Booking datetime is restricted');

        $response = $this->actingAs($user)->post('/events/' . $event->id . '/book', [
            'event_id' => $event->id,
            'booking_time' => '08:00',
            'booking_date' => '2024-09-07',
            'attendee_email' => 'test@test.com',
            'attendee_name' => 'Best Tester',
            'booking_timezone' => 'America/New_York',

        ]);
        $response->assertSee('Booking datetime is restricted');
    }

    public function testUserCanNotCreateBookingBecasueCollisionDetected()
    {
        $user = User::factory()->create();
        $event = Event::factory()->create();

        $response = $this->actingAs($user)->post('/events/' . $event->id . '/book', [
            'event_id' => $event->id,
            'booking_time' => '08:00',
            'booking_date' => '2024-09-03',
            'attendee_email' => 'test@test.com',
            'attendee_name' => 'Best Tester',
            'booking_timezone' => 'America/New_York',

        ]);
        $response->assertSee('Thank You!');

        $response = $this->actingAs($user)->post('/events/' . $event->id . '/book', [
            'event_id' => $event->id,
            'booking_time' => '08:20',
            'booking_date' => '2024-09-03',
            'attendee_email' => 'test@test.com',
            'attendee_name' => 'Best Tester',
            'booking_timezone' => 'America/New_York',

        ]);
        $response->assertSee('Collision detected with old Booking');
    }

}

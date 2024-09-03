<?php

namespace Tests\Feature;

use Mockery;
use Tests\TestCase;
use App\Models\Event;
use Illuminate\Foundation\Testing\RefreshDatabase;

class EventFeatureTest extends TestCase
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

    public function testEventListPageDisplaysEvents()
    {
        $event = Event::factory()->create();

        $response = $this->get('/');
        $response->assertStatus(200);
        $response->assertSee('Event List');
        $response->assertSee($event->name);
    }
}

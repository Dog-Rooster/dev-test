<?php

namespace Tests\Feature;

// use Illuminate\Foundation\Testing\RefreshDatabase;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Log;
use Mockery;
use Tests\TestCase;

class ExampleTest extends TestCase
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
    /**
     * A basic test example.
     */
    public function test_the_application_returns_a_successful_response(): void
    {
        $response = $this->get('/');
        $response->assertStatus(200);
    }
}

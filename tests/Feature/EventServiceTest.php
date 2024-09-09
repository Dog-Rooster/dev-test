<?php

namespace Tests\Unit;

use Tests\TestCase;
use App\Services\EventService;
use App\Repositories\EventRepositoryInterface;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Mockery;

class EventServiceTest extends TestCase
{
    use RefreshDatabase;

    protected $eventService;
    protected $eventRepository;

    protected function setUp(): void
    {
        parent::setUp();

        $this->eventRepository = Mockery::mock(EventRepositoryInterface::class);
        $this->eventService = new EventService($this->eventRepository);
    }

    public function testGetAllEvents()
    {
        $events = [
            (object)['id' => 1, 'name' => 'Event 1'],
            (object)['id' => 2, 'name' => 'Event 2'],
        ];

        $this->eventRepository->shouldReceive('getAll')
            ->once()
            ->andReturn($events);

        $result = $this->eventService->getAllEvents();

        $this->assertCount(2, $result);
        $this->assertEquals('Event 1', $result[0]->name);
    }
}

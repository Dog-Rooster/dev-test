<?php

namespace Tests\Unit;

use Tests\TestCase;
use App\Services\BookingService;
use App\Repositories\BookingRepositoryInterface;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Mockery;

class BookingServiceTest extends TestCase
{
    use RefreshDatabase;

    protected $bookingService;
    protected $bookingRepository;

    protected function setUp(): void
    {
        parent::setUp();

        $this->bookingRepository = Mockery::mock(BookingRepositoryInterface::class);
        $this->bookingService = new BookingService($this->bookingRepository);
    }

    public function testCreateBooking()
    {
        $data = [
            'attendee_name' => 'John Doe',
            'attendee_email' => 'john@example.com',
            'event_id' => 1,
            'booking_date' => '2024-09-08',
            'booking_time' => '10:00',
            'timezone' => 'Asia/Manila',
        ];

        $this->bookingRepository->shouldReceive('create')
            ->once()
            ->with($data)
            ->andReturn((object)$data);

        $booking = $this->bookingService->createBooking($data);

        $this->assertEquals($data['attendee_name'], $booking->attendee_name);
    }

    public function testGetBooking()
    {
        $bookingId = 1;
        $data = [
            'id' => $bookingId,
            'attendee_name' => 'John Doe',
            'attendee_email' => 'john@example.com',
            'event_id' => 1,
            'booking_date' => '2024-09-08',
            'booking_time' => '10:00',
            'timezone' => 'Asia/Manila',
        ];

        $this->bookingRepository->shouldReceive('find')
            ->once()
            ->with($bookingId)
            ->andReturn((object)$data);

        $booking = $this->bookingService->getBooking($bookingId);

        $this->assertEquals($data['attendee_name'], $booking->attendee_name);
    }
}

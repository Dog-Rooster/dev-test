<?php
namespace App\Services;

use App\Contracts\BookingServiceInterface;
use App\Contracts\BookingRepositoryInterface;

class BookingService implements BookingServiceInterface
{
    protected $bookingRepository;

    public function __construct(BookingRepositoryInterface $bookingRepository)
    {
        $this->bookingRepository = $bookingRepository;
    }

    public function createBooking(array $data)
    {
        return $this->bookingRepository->create($data);
    }

    public function getBooking($bookingId)
    {
        return $this->bookingRepository->find($bookingId);
    }
}
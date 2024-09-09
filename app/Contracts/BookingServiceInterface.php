<?php
namespace App\Contracts;

interface BookingServiceInterface
{
    public function createBooking(array $data);
    public function getBooking($bookingId);
}
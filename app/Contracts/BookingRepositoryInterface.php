<?php

namespace App\Contracts;

interface BookingRepositoryInterface
{
    public function create(array $data);
    public function find($bookingId);
    public function getBookedSlots($eventId, $date);
}

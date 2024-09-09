<?php

namespace App\Contracts;

interface TimeSlotServiceInterface
{
    public function generateTimeSlots($date, $eventDuration, $eventId, $timezone);
}
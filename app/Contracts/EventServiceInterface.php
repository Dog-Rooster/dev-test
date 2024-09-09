<?php

namespace App\Contracts;

interface EventServiceInterface
{
    public function getEvent($eventId);
    public function getAllEvents();
}
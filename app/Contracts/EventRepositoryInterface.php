<?php

namespace App\Contracts;

interface EventRepositoryInterface
{
    public function find($eventId);
}
<?php

namespace App\Repositories;

use App\Models\Event;
use App\Contracts\EventRepositoryInterface;

class EloquentEventRepository implements EventRepositoryInterface
{
    public function find($eventId)
    {
        return Event::findOrFail($eventId);
    }

    public function getAll()
    {
        return Event::all();
    }
}
<?php
namespace App\Services;

use App\Contracts\EventServiceInterface;
use App\Contracts\EventRepositoryInterface;

class EventService implements EventServiceInterface
{
    protected $eventRepository;

    public function __construct(EventRepositoryInterface $eventRepository)
    {
        $this->eventRepository = $eventRepository;
    }

    public function getEvent($eventId)
    {
        return $this->eventRepository->find($eventId);
    }

    public function getAllEvents()
    {
        return $this->eventRepository->getAll();
    }
}
<?php

namespace App\Repositories\Interfaces;

use App\Models\Event;
use App\Strategies\Interfaces\EventConflictResolutionStrategyInterface;
use Illuminate\Http\Request;

interface BookingRepositoryInterface
{
    public function setConflictResolutionStrategy(EventConflictResolutionStrategyInterface $conflictResolutionStrategy);
    public function all();
    public function bookEvent(Request $request, Event $event);

}

<?php

namespace App\Strategies\Interfaces;

interface EventConflictResolutionStrategyInterface
{
    public function resolveConflict($eventData);
}

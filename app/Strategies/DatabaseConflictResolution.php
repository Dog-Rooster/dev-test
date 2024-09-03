<?php

namespace App\Strategies;

use App\Models\Booking;
use App\Strategies\Interfaces\EventConflictResolutionStrategyInterface;

class DatabaseConflictResolution implements EventConflictResolutionStrategyInterface
{
    public function resolveConflict($eventData)
    {
        $email = $eventData['email'];
        $startTime = $eventData['startTime'];
        $endTime = $eventData['endTime'];
        return Booking::where('attendee_email', $email)
            ->where(function ($query) use ($startTime, $endTime) {
                $query->where(function ($subQuery) use ($startTime, $endTime) {
                    $subQuery->where('start_datetime', '<=', $startTime)
                        ->where('end_datetime', '>=', $endTime);
                })
                    ->orWhere(function ($subQuery) use ($startTime, $endTime) {
                        $subQuery->where('end_datetime', '<=', $endTime)
                            ->where('end_datetime', '>', $startTime);
                    })
                    ->orWhere(function ($subQuery) use ($startTime, $endTime) {
                        $subQuery->where('start_datetime', '>=',$startTime)
                            ->where('start_datetime', '<', $endTime);
                    });
            })
            ->first();
    }
}

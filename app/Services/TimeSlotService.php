<?php

namespace App\Services;

use Carbon\Carbon;
use App\Contracts\TimeSlotServiceInterface;
use App\Contracts\BookingRepositoryInterface;
use Illuminate\Support\Facades\Cache;


class TimeSlotService implements TimeSlotServiceInterface
{
    protected $bookingRepository;

    public function __construct(BookingRepositoryInterface $bookingRepository)
    {
        $this->bookingRepository = $bookingRepository;
    }

    public function generateTimeSlots($date, $event_duration, $eventId, $timezone = 'Asia/Manila')
    {
        $dayOfWeek = Carbon::parse($date, $timezone)->dayOfWeek;
        if ($dayOfWeek == Carbon::SATURDAY || $dayOfWeek == Carbon::SUNDAY) {
            return [];
        }
    
        $startOfDay = Carbon::parse($date, $timezone)->setTime(8, 0);
        $endOfDay = Carbon::parse($date, $timezone)->setTime(17, 0);
        $interval = $event_duration;
        
        $timeSlots = [];
        $currentDateTime = Carbon::now($timezone);
    
        $bookedSlots = $this->getBookedTimeSlots($eventId, $date, $timezone);
    
        while ($startOfDay < $endOfDay) {
            $end = $startOfDay->copy()->addMinutes($interval);
    
            $startTime = $startOfDay->format('H:i');
            $endTime = $end->format('H:i');
    
            if ($startOfDay->greaterThan($currentDateTime) && !in_array($startTime, $bookedSlots)) {
                $timeSlots[] = [
                    'time' => $startTime,
                ];
            }
    
            $startOfDay = $end;
        }
    
        return $timeSlots;
    }

    private function getBookedTimeSlots($eventId, $date, $timezone)
    {
        $bookings = $this->bookingRepository->getBookedSlots($eventId, $date);

        return $bookings->map(function($time) {
            return Carbon::createFromFormat('H:i:s', $time)->format('H:i');
        })->toArray();
    }
}
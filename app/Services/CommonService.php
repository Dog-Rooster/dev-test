<?php

namespace App\Services;

use Carbon\Carbon;
use Carbon\CarbonTimeZone;

class CommonService
{
    public function generateTimeSlots($date, $timezone = 'America/New_York', $startHour = 8, $endHour = 17, $interval = 30)
    {
        $edTimezone = 'America/New_York';
        $localTz = new CarbonTimeZone($timezone);
        $edTz = new CarbonTimeZone($edTimezone);
        $datetimeLocal = Carbon::parse($date, $localTz);

        $startOfDay = $datetimeLocal->startOfDay();
        $endOfDay = $datetimeLocal->copy()->endOfDay();

        $timeSlots = [];
        while ($startOfDay < $endOfDay) {
            // Convert datetime to EDT
            $datetimeInEdt = $startOfDay->copy()->setTimezone($edTz);
            if (!$datetimeInEdt->isWeekend()) {
                // Define working hours in EDT timezone
                $startOfWorkdayEDT = $datetimeInEdt->copy()->startOfDay()->setHour($startHour);
                $endOfWorkdayEDT = $datetimeInEdt->copy()->startOfDay()->setHour($endHour);

                $isInTimeRange = $datetimeInEdt->between($startOfWorkdayEDT, $endOfWorkdayEDT);
                if($isInTimeRange){
                    $timeSlots[] = [
                        'time' => $startOfDay->format('H:i'),
                    ];
                }
            }
            $startOfDay = $startOfDay->addMinutes($interval);
        }

        return $timeSlots;
    }

    public function generateTimeZones(){
        return [
            ['value' => 'Pacific/Kwajalein', 'label' => 'Pacific/Kwajalein (GMT+12)'],
            ['value' => 'Pacific/Fiji', 'label' => 'Pacific/Fiji (GMT+12)'],
            ['value' => 'Pacific/Auckland', 'label' => 'Pacific/Auckland (GMT+13)'],
            ['value' => 'Pacific/Chatham', 'label' => 'Pacific/Chatham (GMT+13:45)'],
            ['value' => 'Pacific/Kiritimati', 'label' => 'Pacific/Kiritimati (GMT+14)'],
            ['value' => 'Asia/Tokyo', 'label' => 'Asia/Tokyo (GMT+9)'],
            ['value' => 'Asia/Seoul', 'label' => 'Asia/Seoul (GMT+9)'],
            ['value' => 'Asia/Kolkata', 'label' => 'Asia/Kolkata (GMT+5:30)'],
            ['value' => 'Asia/Calcutta', 'label' => 'Asia/Calcutta (GMT+5:30)'],
            ['value' => 'Asia/Dhaka', 'label' => 'Asia/Dhaka (GMT+6)'],
            ['value' => 'Asia/Karachi', 'label' => 'Asia/Karachi (GMT+5)'],
            ['value' => 'Asia/Almaty', 'label' => 'Asia/Almaty (GMT+6)'],
            ['value' => 'Asia/Yekaterinburg', 'label' => 'Asia/Yekaterinburg (GMT+5)'],
            ['value' => 'Asia/Tehran', 'label' => 'Asia/Tehran (GMT+3:30)'],
            ['value' => 'Europe/Moscow', 'label' => 'Europe/Moscow (GMT+3)'],
            ['value' => 'Asia/Baghdad', 'label' => 'Asia/Baghdad (GMT+3)'],
            ['value' => 'Europe/Bucharest', 'label' => 'Europe/Bucharest (GMT+2)'],
            ['value' => 'Europe/Helsinki', 'label' => 'Europe/Helsinki (GMT+2)'],
            ['value' => 'Africa/Johannesburg', 'label' => 'Africa/Johannesburg (GMT+2)'],
            ['value' => 'Europe/Paris', 'label' => 'Europe/Paris (GMT+1)'],
            ['value' => 'Europe/Berlin', 'label' => 'Europe/Berlin (GMT+1)'],
            ['value' => 'Europe/Lisbon', 'label' => 'Europe/Lisbon (GMT)'],
            ['value' => 'Europe/London', 'label' => 'Europe/London (GMT)'],
            ['value' => 'Africa/Casablanca', 'label' => 'Africa/Casablanca (GMT)'],
            ['value' => 'Atlantic/Azores', 'label' => 'Atlantic/Azores (GMT-1)'],
            ['value' => 'Europe/Andorra', 'label' => 'Europe/Andorra (GMT+1)'],
            ['value' => 'Africa/Lagos', 'label' => 'Africa/Lagos (GMT+1)'],
            ['value' => 'Africa/Abidjan', 'label' => 'Africa/Abidjan (GMT)'],
            ['value' => 'America/Sao_Paulo', 'label' => 'America/Sao_Paulo (GMT-3)'],
            ['value' => 'America/Argentina/Buenos_Aires', 'label' => 'America/Argentina/Buenos_Aires (GMT-3)'],
            ['value' => 'America/Montevideo', 'label' => 'America/Montevideo (GMT-3)'],
            ['value' => 'America/Chicago', 'label' => 'America/Chicago (GMT-6)'],
            ['value' => 'America/Regina', 'label' => 'America/Regina (GMT-6)'],
            ['value' => 'America/Mexico_City', 'label' => 'America/Mexico_City (GMT-6)'],
            ['value' => 'America/New_York', 'label' => 'America/New_York (GMT-5)'],
            ['value' => 'America/Toronto', 'label' => 'America/Toronto (GMT-5)'],
            ['value' => 'America/Halifax', 'label' => 'America/Halifax (GMT-4)'],
            ['value' => 'America/Santiago', 'label' => 'America/Santiago (GMT-3)'],
            ['value' => 'America/Adak', 'label' => 'America/Adak (GMT-10)'],
            ['value' => 'Pacific/Honolulu', 'label' => 'Pacific/Honolulu (GMT-10)'],
            ['value' => 'Pacific/Pago_Pago', 'label' => 'Pacific/Pago_Pago (GMT-11)'],
            ['value' => 'Pacific/Apia', 'label' => 'Pacific/Apia (GMT-13)']
        ];
    }
}

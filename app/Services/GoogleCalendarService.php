<?php

namespace App\Services;

use DateTime;
use DateTimeZone;
use Google\Service\Exception;
use Google_Client;
use Google_Service_Calendar;
use Google_Service_Calendar_Event;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Session;

class GoogleCalendarService
{
    protected $client;
    protected $service;
    private $calendarId;
    public function __construct()
    {
        $this->client = new Google_Client();
        $base64Config = env('GOOGLE_SERVICE_ACCOUNT_BASE64');
        $jsonConfig = base64_decode($base64Config);
        $this->client->setAuthConfig(json_decode($jsonConfig, true));
        $this->client->setScopes(Google_Service_Calendar::CALENDAR);
        $this->service = new Google_Service_Calendar($this->client);
        $this->calendarId = env('GOOGLE_CALENDAR_ID');
    }

    public function getClient()
    {
        return $this->client;
    }

    public function getService()
    {
        return $this->service;
    }

    public function listEvents()
    {
        $events = $this->service->events->listEvents($this->calendarId);
        return $events->getItems();
    }

    /**
     * @throws Exception
     */
    public function createEvent($title, $description, $duration, $attendeeName, $attendeeEmail, $startDateTimeStr, $endDateTimeStr, $timezone): \Google\Service\Calendar\Event
    {
        $calenderEvent = new Google_Service_Calendar_Event([
            'summary' => $title,
            'description' => $description,
            'start' => [
                'dateTime' => $startDateTimeStr,
                'timeZone' => $timezone,
            ],
            'end' => [
                'dateTime' => $endDateTimeStr,
                'timeZone' => $timezone,
            ],
        ]);
        return $this->service->events->insert($this->calendarId, $calenderEvent);
    }
}

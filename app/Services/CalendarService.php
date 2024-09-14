<?php

namespace App\Services;

use Google\Client as GoogleClient;
use Google\Service\Calendar as GoogleCalendar;
use Google\Service\Calendar\Event as GoogleCalendarEvent;
use Carbon\Carbon;
use Illuminate\Support\Facades\Log;

class CalendarService
{
    protected $client;
    protected $service;

    public function __construct()
    {
        $client = new GoogleClient();
        $credentialsPath = storage_path('app/google-credentials.json');
        $client->setAuthConfig($credentialsPath);
        $client->addScope(GoogleCalendar::CALENDAR);
        $client->setAccessType('offline');
        $client->setPrompt('select_account consent');

        $client->setApplicationName('Calendar');

        $this->service = new GoogleCalendar($client);
    }

    protected function authorize(GoogleClient $client)
    {
        $authUrl = $client->createAuthUrl();
        return redirect()->away($authUrl);
    }

    public function createICS($summary, $startDateTime, $endDateTime, $attendees = [])
    {
        $icsContent = "BEGIN:VCALENDAR\r\n";
        $icsContent .= "VERSION:2.0\r\n";
        $icsContent .= "PRODID:-//Your Company//NONSGML v1.0//EN\r\n";
        $icsContent .= "BEGIN:VEVENT\r\n";
        $icsContent .= "SUMMARY:" . $summary . "\r\n";
        $icsContent .= "DTSTART:" . date('Ymd\THis\Z', strtotime($startDateTime)) . "\r\n";
        $icsContent .= "DTEND:" . date('Ymd\THis\Z', strtotime($endDateTime)) . "\r\n";
        $icsContent .= "DESCRIPTION:Event Description\r\n";
        $icsContent .= "LOCATION:Event Location\r\n";

        foreach ($attendees as $email) {
            $icsContent .= "ATTENDEE;CN=" . $email . ":mailto:" . $email . "\r\n";
        }

        $icsContent .= "END:VEVENT\r\n";
        $icsContent .= "END:VCALENDAR\r\n";

        return $icsContent;
    }

    public function createEvent(array $eventDetails)
    {
        $event = new GoogleCalendarEvent($eventDetails);
        $calendarId = 'dummykolangto20@gmail.com';

        try {
            $response = $this->service->events->insert($calendarId, $event);
            Log::info('Event created successfully:', [
                'id' => $response->getId(),
                'summary' => $response->getSummary(),
                'start' => $response->getStart()->getDateTime(),
                'end' => $response->getEnd()->getDateTime()
            ]);
        } catch (\Google_Service_Exception $e) {
            Log::error('Google Calendar API error: ' . $e->getMessage());
            throw $e;
        }
    }
}

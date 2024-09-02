<?php

namespace App\Services;

use DateInterval;
use DateTimeImmutable;
use Eluceo\iCal\Domain\Entity\Calendar;
use Eluceo\iCal\Domain\Entity\Event;
use Eluceo\iCal\Domain\ValueObject\Alarm;
use Eluceo\iCal\Domain\ValueObject\Attachment;
use Eluceo\iCal\Domain\ValueObject\DateTime;
use Eluceo\iCal\Domain\ValueObject\EmailAddress;
use Eluceo\iCal\Domain\ValueObject\GeographicPosition;
use Eluceo\iCal\Domain\ValueObject\Location;
use Eluceo\iCal\Domain\ValueObject\Organizer;
use Eluceo\iCal\Domain\ValueObject\TimeSpan;
use Eluceo\iCal\Domain\ValueObject\Uri;
use Eluceo\iCal\Presentation\Factory\CalendarFactory;
use Illuminate\Support\Facades\Log;

class IcsGenerator
{
    public function generateIcsFile($title, $description, $startDateTimeStr, $endDateTimeStr, $timezone)
    {
        Log::Info($startDateTimeStr);
        Log::Info($endDateTimeStr);
        $event = new Event();
        $event
            ->setSummary($title)
            ->setDescription($description)
            ->setOrganizer(new Organizer(
                new EmailAddress('raj90.rich@gmail.com'),
                'CalDevAdmin'
            ))
            ->setOccurrence(
                new TimeSpan(
                    new DateTime(DateTimeImmutable::createFromFormat('Y-m-d\TH:i:s', $startDateTimeStr), true),
                    new DateTime(DateTimeImmutable::createFromFormat('Y-m-d\TH:i:s', $endDateTimeStr), true)
                )
            )
            ->addAlarm(
                new Alarm(
                    new Alarm\DisplayAction('Reminder: the event starts in 30 minutes!'),
                    (new Alarm\RelativeTrigger(DateInterval::createFromDateString('-30 minutes')))->withRelationToEnd()
                )
            )
        ;

        $calendar = new Calendar([$event]);

        $componentFactory = new CalendarFactory();
        $calendarComponent = $componentFactory->createCalendar($calendar);

        $icsContent = "".$calendarComponent;
        $fileName = 'event.ics';

        return [$icsContent, $fileName];
    }
}

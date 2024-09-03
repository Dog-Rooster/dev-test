<?php

namespace App\Services;

use DateInterval;
use DateTimeImmutable;
use DateTimeZone;
use Eluceo\iCal\Domain\Entity\Calendar;
use Eluceo\iCal\Domain\Entity\Event;
use Eluceo\iCal\Domain\ValueObject\Alarm;
use Eluceo\iCal\Domain\ValueObject\DateTime;
use Eluceo\iCal\Domain\ValueObject\EmailAddress;
use Eluceo\iCal\Domain\ValueObject\Organizer;
use Eluceo\iCal\Domain\ValueObject\TimeSpan;
use Eluceo\iCal\Presentation\Factory\CalendarFactory;

class IcsGeneratorService
{
    public function generateIcsFile($title, $description, $startDateTimeStr, $endDateTimeStr, $timezone)
    {
        $event = new Event();
        $senderEmail = env('MAIL_FROM_ADDRESS');
        $senderName = env('MAIL_FROM_NAME');
        $event
            ->setSummary($title)
            ->setDescription($description)
            ->setOrganizer(new Organizer(
                new EmailAddress($senderEmail),
                $senderName
            ))
            ->setOccurrence(
                new TimeSpan(
                    new DateTime(DateTimeImmutable::createFromFormat('Y-m-d\TH:i:s', $startDateTimeStr, new DateTimeZone($timezone)), true),
                    new DateTime(DateTimeImmutable::createFromFormat('Y-m-d\TH:i:s', $endDateTimeStr, new DateTimeZone($timezone)), true)
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

<?php

namespace App\Main\EventsCalendar\Services;

use Carbon\Carbon;
use Spatie\GoogleCalendar\Event;

class AddEventInCalendarService
{
    public function __invoke($date, $name, $message, $rangeHour, $idCalendar)
    {
        $dtStart = ($date.' '.$rangeHour->start);
        $dtEnd = ($date.' '.$rangeHour->end);

        $event = new Event();
        $eventResult = $event->create(
            [
                'name' => 'Llamar a '.$name,
                'description' => $message,
                'startDateTime' => new Carbon($dtStart),
                'endDateTime' => new Carbon($dtEnd), ],
                $idCalendar
        );

        return $eventResult;
    }
}

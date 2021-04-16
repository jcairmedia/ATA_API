<?php

namespace App\Main\EventsCalendar\Domain;

use App\CalendarEventMeeting;
use App\Main\CalendarEventMeeting\Domain\AddEventDomain;

class SaveEventInDBDomain
{
    public function __invoke($meetingId, $eventId, $idCalendar)
    {
        try {
            $calendar = new AddEventDomain();
            $calendar(new CalendarEventMeeting([
                'meetings_id' => $meetingId,
                'idevent' => $eventId,
                'idcalendar' => $idCalendar, ]));
        } catch (\Exception $ex) {
            \Log::error('ErrorOfflineAddEvent: '.$ex->getMessage());
        }
    }
}

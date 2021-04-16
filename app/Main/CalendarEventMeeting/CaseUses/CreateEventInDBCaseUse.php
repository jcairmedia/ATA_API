<?php

namespace App\Main\CalendarEventMeeting\CaseUses;

use App\CalendarEventMeeting;
use App\Main\CalendarEventMeeting\Domain\AddEventDomain;

class CreateEventInDBCaseUse
{
    public function __invoke($meetingId, $eventId, $idCalendar)
    {
        try {
            $calendar = new AddEventDomain();

            return $calendar(new CalendarEventMeeting([
            'meetings_id' => $meetingId,
            'idevent' => $eventId,
            'idcalendar' => $idCalendar, ]));
        } catch (\Exception $ex) {
            \Log::error('ErrorTransactionAddEvent: '.$ex->getMessage());
        }
    }
}

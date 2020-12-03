<?php

namespace App\Main\CalendarEventMeeting\Domain;

use App\CalendarEventMeeting;

class GetEventDomain
{
    public function __invoke(int $MeetingId)
    {
        try {
            $event = CalendarEventMeeting::where(['meetings_id' => $MeetingId])->first();

            return $event;
        } catch (\Exception $ex) {
            \Log::error($ex->getMessage().'('.$ex->getCode().')');
            throw new \Exception($ex->getMessage(), (int) $ex->getCode());
        }
    }
}

<?php

namespace App\Main\CalendarEventMeeting\Domain;

use App\CalendarEventMeeting;

class AddEventDomain
{
    public function __invoke(CalendarEventMeeting $event)
    {
        try {
            $event->saveOrFail();

            return $event;
        } catch (\Exception $ex) {
            \Log::error($ex->getMessage().'('.$ex->getCode().')');
            throw new \Exception($ex->getMessage(), (int) $ex->getCode());
        }
    }
}

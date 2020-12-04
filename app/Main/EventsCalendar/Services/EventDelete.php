<?php

namespace App\Main\EventsCalendar\Services;

use Spatie\GoogleCalendar\Event;

class EventDelete
{
    public function __invoke($_idEvent_, $_idCalendar_)
    {
        try {
            $eventObj = Event::find($_idEvent_, $_idCalendar_);
            $eventObj->delete();
        } catch (\Exception $ex) {
            \Log::error('Delete event'.$ex->getMessage());
        }
    }
}

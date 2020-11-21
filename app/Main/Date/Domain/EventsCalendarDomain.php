<?php

namespace App\Main\Date\Domain;

use Carbon\Carbon;
use Spatie\GoogleCalendar\GoogleCalendarFactory;

class EventsCalendarDomain
{
    private $idCalendar = '';

    public function __construct(string $idCalendar)
    {
        $this->idCalendar = $idCalendar;
    }

    public function __invoke(\DateTime $start, \DateTime $end)
    {
        $events = $this->getEvents($start, $end);
        if (count($events) <= 0) {
            return [];
        }
        $meetings = [];
        foreach ($events as $k => $v) {
            $meetings[] = [
                'start' => new \DateTime($v->start->dateTime),
                'end' => new \DateTime($v->end->dateTime),
                'subject' => $v->summary,
            ];
        }

        return $meetings;
    }

    private function getEvents(\DateTime $start, \DateTime $end)
    {
        $startCarbon = new Carbon($start->format('Y-m-d H:i:s'));
        $endCarbon = new Carbon($end->format('Y-m-d H:i:s'));
        $google = GoogleCalendarFactory::createForCalendarId($this->idCalendar);
        $list = $google->listEvents(
            $startCarbon,
            $endCarbon
            );

        return $list->items;
    }
}

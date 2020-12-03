<?php

namespace App\Main\Date\CaseUses;

use App\Main\Scheduler\Domain\SearchSchedulerDomain;
use Carbon\Carbon;
use Spatie\GoogleCalendar\Event;

class IsEnabledHourCaseUse
{
    public function __invoke(
        string $dt,
        string $hour,
        string $typeMeeting,
        string $idCalendar,
        int $numberPlaces)
    {
        // Buscar el rango de horario
        $scheduler = new SearchSchedulerDomain();
        $rangeHour = $scheduler->_searchRangeHour($hour, $typeMeeting);
        if ($rangeHour == null) {
            throw new \Exception('Horario no encontrado');
        }

        $dtStart = ($dt.' '.$rangeHour->start);
        $dtEnd = ($dt.' '.$rangeHour->end);
        $event = new Event();
        $events = $event->get(new Carbon($dtStart), new Carbon($dtEnd), [], $idCalendar);

        $times = count($events);

        return $times < $numberPlaces;
    }
}

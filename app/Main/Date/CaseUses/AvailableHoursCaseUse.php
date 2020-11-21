<?php

namespace App\Main\Date\CaseUses;

use App\Main\Date\Domain\EventsCalendarDomain;
use App\Main\Scheduler\Domain\SearchSchedulerDomain;
use Exception;

class AvailableHoursCaseUse
{
    private $configHourStart = null;
    private $configHourEnd = null;

    public function __construct(string $configHourStart, string $configHourEnd)
    {
        $this->configHourStart = $configHourStart;
        $this->configHourEnd = $configHourEnd;
    }

    public function __invoke(\DateTime $dt, string $typeMeeting = 'FREE', string $idCalendar)
    {
        // Get range hours for search
        $rangeHour = $this->rangeDate($dt);
        // Get range hours of work
        $scheduler = new SearchSchedulerDomain();
        $listObj = [];
        $hours = $scheduler(
                            $rangeHour['start']->format('H:i:s'),
                            $rangeHour['end']->format('H:i:s'),
                            $typeMeeting);
        $hours = $hours->toArray();

        // if (count($hours) <= 0) {
        //     throw new Exception('Favor de registrar los horarios de atención de la citas tipo '.$typeMeeting, 1);
        // }
        // Create Mask
        $templateHours = [];
        foreach ($hours as $key => $value) {
            $templateHours[$value['start']] = [
                'start' => $value['start'],
                'end' => $value['end'],
                'subject' => '',
                'time' => 0,
                'enabled' => 1,
            ];
        }

        // Get events in google Calendar
        $events = new EventsCalendarDomain($idCalendar);
        $listEvents = $events($rangeHour['start'], $rangeHour['end']);

        // Set hours Available and Busy
        foreach ($listEvents as $key => $event) {
            // Search limit up and bottom
            $objStart = $scheduler->__searchStart($event['start']->format('H:i:s'), $typeMeeting);
            $objEnd = $scheduler->__searchEnd($event['end']->format('H:i:s'), $typeMeeting);

            if ($objEnd == null) {
                $objEnd['end'] = $event['end']->format('H:i:s');
                $objEnd['start'] = $event['start']->format('H:i:s');
            }

            // get Hours
            $listHoursDisabled = $scheduler(
                                    $objStart->start,
                                    (string) $objEnd['end'],
                                    $typeMeeting);

            // Count hours no available
            foreach ($listHoursDisabled as $key => $disabled) {
                if (isset($templateHours[$disabled->start])) {
                    $templateHours[$disabled->start]['time'] = $templateHours[$disabled->start]['time'] + 1;
                    $templateHours[$disabled->start]['enabled'] = $disabled->active;
                }
            }
        }

        return $templateHours;
    }

    private function rangeDate(\DateTime $parameter)
    {
        $now = new \DateTime();

        $dtParam = $parameter;
        if ($now->format('Y-m-d') != $dtParam->format('Y-m-d')) {
            return [
                'start' => new \DateTime($parameter->format('Y-m-d').' '.$this->configHourStart),
                'end' => new \DateTime($parameter->format('Y-m-d').' '.$this->configHourEnd),
            ];
        }

        if ((int) $now->format('H') >= 18) {
            throw new Exception('Hora no permitida para agendar', 400);
        }

        if ((int) $now->format('H') < 9) {
            return [
                'start' => new \DateTime($now->format('Y-m-d '.$this->configHourStart)),
                'end' => new \DateTime($parameter->format('Y-m-d '.$this->configHourEnd)),
            ];
        }

        return [
            'start' => $now,
            'end' => new \DateTime($parameter->format('Y-m-d '.$this->configHourEnd)),
        ];
    }
}

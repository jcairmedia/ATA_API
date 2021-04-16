<?php

namespace App\Main\Scheduler\CaseUses;

use App\Main\Scheduler\Domain\SearchSchedulerDomain;

class ExistHourInSchedulerCaseUse
{
    public function __invoke($data)
    {
        $scheduler = new SearchSchedulerDomain();
        $rangeHour = $scheduler->_searchRangeHour($data['time'], 'PAID');
        if ($rangeHour == null) {
            throw new \Exception('Horario no encontrado');
        }

        return $rangeHour;
    }
}

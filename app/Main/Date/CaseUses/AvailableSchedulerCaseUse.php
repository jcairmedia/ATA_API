<?php

namespace App\Main\Date\CaseUses;

class AvailableSchedulerCaseUse
{
    public function __invoke($listMetings, int $numberPlaces)
    {
        if (count($listMetings) <= 0) {
            return ['data' => []];
        }

        $arrayAvailableHours = [];
        foreach ($listMetings as $k => $v) {
            if ((int) $v['time'] < $numberPlaces && ((bool) $v['enabled'])) {
                $arrayAvailableHours[] = $v['start'];
            }
        }

        return ['data' => $arrayAvailableHours];
    }
}

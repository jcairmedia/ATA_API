<?php

namespace App\Main\Date\CaseUses;

use App\Main\Date\Domain\FindHoursService;

class AvailableHours
{
    public function __construct(FindHoursService $fh)
    {
        $this->fh = $fh;
    }

    public function __invoke(\DateTime $date)
    {
        return ['data' => $this->fh->__invoke($date)];
    }
}

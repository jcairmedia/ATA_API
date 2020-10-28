<?php

namespace App\Main\Date\Domain;

class FindHoursService
{
    public function __invoke(\DateTime $date)
    {
        return [
            '09:00',
            '09:40',
        ];
    }
}

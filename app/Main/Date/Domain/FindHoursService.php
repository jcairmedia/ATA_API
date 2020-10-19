<?php

namespace App\Main\Date\Domain;

class FindHoursService
{
    public function __invoke(\DateTime $date)
    {
        return [
            '9:00',
            '9:40',
        ];
    }
}

<?php

namespace App\Main\Scheduler\Domain;

use App\Scheduler;

class SearchSchedulerDomain
{
    public function __invoke(string $hourStart, string $hourEnd, string $typeMeeting)
    {
        return Scheduler::whereBetween('start', [$hourStart, $hourEnd])
        ->whereBetween('end', [$hourStart, $hourEnd])
        ->where('type_scheduler', $typeMeeting)
        ->orderBy('start')
        ->get();
    }

    public function __searchStart(string $hour, string $typeMeeting)
    {
        return Scheduler::whereRaw('? BETWEEN `start` AND `end`', [$hour])
        ->where('type_scheduler', $typeMeeting)
        ->orderBy('start')
        ->first();
    }

    public function __searchEnd(string $hour, string $typeMeeting)
    {
        return Scheduler::whereRaw('? BETWEEN `start` AND `end`', [$hour])
        ->where('type_scheduler', $typeMeeting)
        ->orderBy('end', 'DESC')
        ->first();
    }
}

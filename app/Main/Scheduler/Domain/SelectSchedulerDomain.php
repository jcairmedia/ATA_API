<?php

namespace App\Main\Scheduler\Domain;

use App\Scheduler;

class SelectSchedulerDomain
{
    public function __invoke(string $type)
    {
        return Scheduler::where('type_scheduler', $type)->get();
    }
}

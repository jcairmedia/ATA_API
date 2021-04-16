<?php

namespace App\Main\Scheduler\CaseUses;

class ValidateDateMeetingPaidTransferenceCaseUse
{
    public function __invoke(\DateTime $dt)
    {
        $dayNow = new DateTime();
        $dt->diff($dayNow);
    }
}

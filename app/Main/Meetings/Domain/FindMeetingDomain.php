<?php

namespace App\Main\Meetings\Domain;

use App\Meeting;

class FindMeetingDomain
{
    public function __invoke(array $where)
    {
        return Meeting::where($where)->first();
    }
}

<?php

namespace App\Main\Meetings\Domain;

use App\Meeting;

class MeetingWhereDomain
{
    public function __invoke(array $where)
    {
        return Meeting::where($where)->get();
    }
}

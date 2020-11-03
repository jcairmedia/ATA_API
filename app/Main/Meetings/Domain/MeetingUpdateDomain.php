<?php

namespace App\Main\Meetings\Domain;

use App\Meeting;

class MeetingUpdateDomain
{
    public function __construct()
    {
    }

    public function __invoke($meeting_id, $array)
    {
        try {
            Meeting::where('id', $meeting_id)->update($array);
        } catch (\Exception $ex) {
            \Log::error($ex->getMessage().'('.$ex->getCode().')');
            throw new \Exception($ex->getMessage(), $ex->getCode(), $ex);
        }
    }
}

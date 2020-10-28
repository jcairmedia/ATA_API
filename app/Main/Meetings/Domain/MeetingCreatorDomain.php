<?php

namespace App\Main\Meetings\Domain;

use App\Meeting;

class MeetingCreatorDomain
{
    public function __construct()
    {
    }

    public function __invoke(Meeting $meeting)
    {
        try {
            $meeting->saveOrFail();

            return $meeting;
        } catch (\Exception $ex) {
            \Log::error($ex->getMessage().'('.$ex->getCode().')');
            throw new \Exception($ex->getMessage(), $ex->getCode(), $ex);
        }
    }
}

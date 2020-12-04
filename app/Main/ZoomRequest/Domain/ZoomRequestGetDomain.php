<?php

namespace App\Main\ZoomRequest\Domain;

use App\ZoomRequest;

class ZoomRequestGetDomain
{
    public function __invoke($meetingId)
    {
        try {
            return ZoomRequest::where(['meeting_id' => $meetingId])->orderByRaw('created_at DESC')->first();
        } catch (\Exception $ex) {
            throw new \Exception($ex->getMessage(), $ex->getCode(), $ex);
        }
    }
}

<?php

namespace App\Main\ZoomRequest\Domain;

use App\ZoomRequest;

class ZoomRequestDomain
{
    public function add(ZoomRequest $zoom)
    {
        try {
            $zoom->saveOrFail();
        } catch (\Exception $ex) {
            \Log::error($ex->getMessage().'('.$ex->getCode().')');
            throw new \Exception($ex->getMessage(), $ex->getCode(), $ex);
        }
    }
}

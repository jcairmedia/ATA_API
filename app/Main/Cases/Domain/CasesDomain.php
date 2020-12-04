<?php

namespace App\Main\Cases\Domain;

use App\Cases;

class CasesDomain
{
    public function create(Cases $case)
    {
        try {
            $case->saveOrFail();

            return $case;
        } catch (\Exception $ex) {
            \Log::error($ex->getMessage().'('.$ex->getCode().')');
            throw new \Exception($ex->getMessage(), (int) $ex->getCode(), $ex);
        }
    }
}

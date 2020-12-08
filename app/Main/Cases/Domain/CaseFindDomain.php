<?php

namespace App\Main\Cases\Domain;

class CaseFindDomain
{
    public function __invoke($array)
    {
        try {
            return Cases::where($array)->first();
        } catch (\Exception $ex) {
            \Log::error($ex->getMessage().'('.$ex->getCode().')');
            throw new \Exception($ex->getMessage(), (int) $ex->getCode(), $ex);
        }
    }
}

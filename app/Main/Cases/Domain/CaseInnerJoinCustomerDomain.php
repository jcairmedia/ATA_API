<?php

namespace App\Main\Cases\Domain;

use App\Main\Cases\Queries\CaseInnerJoinCustomerQuery;

class CaseInnerJoinCustomerDomain
{
    public function __invoke($array)
    {
        try {
            return (new CaseInnerJoinCustomerQuery())($array)
            ->first();
        } catch (\Exception $ex) {
            \Log::error($ex->getMessage().'('.$ex->getCode().')');
            throw new \Exception($ex->getMessage(), (int) $ex->getCode(), $ex);
        }
    }
}

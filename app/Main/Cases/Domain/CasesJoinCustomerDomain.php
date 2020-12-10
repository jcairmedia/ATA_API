<?php

namespace App\Main\Cases\Domain;

use App\Main\Cases\Queries\CaseInnerJoinCustomerQuery;

class CasesJoinCustomerDomain
{
    public function __invoke($array)
    {
        try {
            \Log::error('CasesInnerJoinCustomer: '.print_r($array, 1));

            return (new CaseInnerJoinCustomerQuery())($array)
            ->get();
        } catch (\Exception $ex) {
            \Log::error($ex->getMessage().'('.$ex->getCode().')');
            throw new \Exception($ex->getMessage(), (int) $ex->getCode(), $ex);
        }
    }
}

<?php

namespace App\Main\Cases_payments\Domain;

use App\Cases_payments;

class GetPaymentCasesDomain
{
    public function __invoke($where)
    {
        try {
            return Cases_payments::where($where)->get();
        } catch (\Exception $ex) {
            \Log::error($ex->getMessage().'('.$ex->getCode().')');
            throw new \Exception($ex->getMessage(), (int) $ex->getCode(), $ex);
        }
    }
}

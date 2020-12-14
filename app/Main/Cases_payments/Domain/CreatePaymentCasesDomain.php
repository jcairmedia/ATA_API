<?php

namespace App\Main\Cases_payments\Domain;

use App\Cases_payments;

class CreatePaymentCasesDomain
{
    public function save(Cases_payments $payments)
    {
        try {
            $payments->saveOrFail();
        } catch (\Exception $ex) {
            \Log::error($ex->getMessage().'('.$ex->getCode().')');
            throw new \Exception($ex->getMessage(), $ex->getCode(), $ex);
        }
    }
}

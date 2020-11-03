<?php

namespace App\Main\OpenPay_payment_references\Domain;

use App\OpenpayPaymentReference;

class CreaterChargeDomain
{
    public function __invoke(OpenpayPaymentReference $objPayment)
    {
        try {
            return $objPayment->saveOrFail();
        } catch (\Exception $ex) {
            \Log::error($ex->getMessage().'('.$ex->getCode().')');
            throw new \Exception($ex->getMessage(), $ex->getCode(), $ex);
        }
    }
}

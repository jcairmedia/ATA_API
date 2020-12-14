<?php

namespace App\Main\OpenPay_payment_references\Domain;

use App\OpenpayPaymentReference;

class FindOpenPayReferencesDomain
{
    public function __invoke($array)
    {
        try {
            return OpenpayPaymentReference::where($array)->first();
        } catch (\Exception $ex) {
            \Log::error($ex->getMessage().'('.$ex->getCode().')');
            throw new \Exception($ex->getMessage(), $ex->getCode(), $ex);
        }
    }
}

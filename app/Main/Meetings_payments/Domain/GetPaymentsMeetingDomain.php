<?php

namespace App\Main\Meetings_payments\Domain;

use App\Meeting_payments;

class GetPaymentsMeetingDomain
{
    public function __invoke($where)
    {
        try {
            return Meeting_payments::where($where)->get();
        } catch (\Exception $ex) {
            \Log::error($ex->getMessage().'('.$ex->getCode().')');
            throw new \Exception($ex->getMessage(), $ex->getCode(), $ex);
        }
    }
}

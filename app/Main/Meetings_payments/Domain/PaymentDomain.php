<?php

namespace App\Main\Meetings_payments\Domain;

use App\Meeting_payments;

class PaymentDomain
{
    public function save(Meeting_payments $meeting_payments)
    {
        try {
            $meeting_payments->saveOrFail();
        } catch (\Exception $ex) {
            \Log::error($ex->getMessage().'('.$ex->getCode().')');
            throw new \Exception($ex->getMessage(), $ex->getCode(), $ex);
        }
    }
}

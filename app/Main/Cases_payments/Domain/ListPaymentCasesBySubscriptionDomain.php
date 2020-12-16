<?php

namespace App\Main\Cases_payments\Domain;

use App\Cases_payments;

class ListPaymentCasesBySubscriptionDomain
{
    public function __invoke(int $subscriptionId)
    {
        try {
            return Cases_payments::where(['subscription_id' => $subscriptionId])
            ->orderBy('created_at', 'DESC')->get();
        } catch (\Exception $ex) {
            \Log::error($ex->getMessage().'('.$ex->getCode().')');
            throw new \Exception($ex->getMessage(), (int) $ex->getCode(), $ex);
        }
    }
}

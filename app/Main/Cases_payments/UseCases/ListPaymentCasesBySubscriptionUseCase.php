<?php

namespace App\Main\Cases_payments\UseCases;

use App\Main\Cases_payments\Domain\ListPaymentCasesBySubscriptionDomain;

class ListPaymentCasesBySubscriptionUseCase
{
    public function __invoke(int $subscriptionId)
    {
        try {
            $payments = (new ListPaymentCasesBySubscriptionDomain())($subscriptionId);

            return $payments;
        } catch (\Exception $ex) {
            \Log::error($ex->getMessage().'('.$ex->getCode().')');
            throw new \Exception($ex->getMessage(), (int) $ex->getCode(), $ex);
        }
    }
}

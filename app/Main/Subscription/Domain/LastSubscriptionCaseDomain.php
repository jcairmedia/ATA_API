<?php

namespace App\Main\Subscription\Domain;

use App\Cases;

class LastSubscriptionCaseDomain
{
    public function __invoke(array $array)
    {
        try {
            $subscription = Cases::query()
            ->where($array)
            ->join('subscriptions', 'subscriptions.cases_id', '=', 'cases.id')
            ->orderBy('subscriptions.created_at', 'DESC')
            ->select('subscriptions.*')
            ->first();

            return $subscription;
        } catch (\Exception $ex) {
            \Log::error($ex->getMessage().'('.$ex->getCode().')');
            throw new \Exception($ex->getMessage(), $ex->getCode(), $ex);
        }
    }
}

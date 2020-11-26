<?php

namespace App\Main\Subscription\Domain;

use App\Subscription;

class SubscriptionDomain
{
    public function create(Subscription $subscription)
    {
        try {
            $subscription->saveOrFail();

            return $subscription;
        } catch (\Exception $ex) {
            \Log::error($ex->getMessage().'('.$ex->getCode().')');
            throw new \Exception($ex->getMessage(), $ex->getCode(), $ex);
        }
    }
}

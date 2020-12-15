<?php

namespace App\Main\Subscription\Domain;

use App\Subscription;

class FindSubscriptionDomain
{
    public function __invoke(array $array)
    {
        try {
            $subscription = Subscription::where($array)->first();

            return $subscription;
        } catch (\Exception $ex) {
            \Log::error($ex->getMessage().'('.$ex->getCode().')');
            throw new \Exception($ex->getMessage(), $ex->getCode(), $ex);
        }
    }
}

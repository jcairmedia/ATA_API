<?php

namespace App\Main\OpenPayCustomer\Domain;

use App\OpenpayCustomer;

class CreateOpenPayCustomerDomain
{
    public function __invoke(OpenpayCustomer $customer)
    {
        try {
            $customer->saveOrFail();

            return $customer;
        } catch (\Exception $ex) {
            \Log::error($ex->getMessage().'('.$ex->getCode().')');
            throw new \Exception($ex->getMessage(), $ex->getCode(), $ex);
        }
    }
}

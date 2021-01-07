<?php

namespace App\Main\OpenPayCustomer\Domain;

use App\OpenpayCustomer;

class FindCustomerOpenPayDomain
{
    public function __invoke($array)
    {
        try {
            return OpenpayCustomer::where($array)->first();
        } catch (\Exception $ex) {
            \Log::error($ex->getMessage().'('.$ex->getCode().')');
            throw new \Exception($ex->getMessage(), (int) $ex->getCode(), $ex);
        }
    }
}

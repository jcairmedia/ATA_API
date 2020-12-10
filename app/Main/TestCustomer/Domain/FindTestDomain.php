<?php

namespace App\Main\TestCustomer\Domain;

use App\CustomerTest;

class FindTestDomain
{
    public function __construct()
    {
    }

    public function __invoke($array)
    {
        try {
            return CustomerTest::where($array)->first();
        } catch (\Exception $ex) {
            \Log::error($ex->getMessage().'('.$ex->getCode().')');
            throw new \Exception($ex->getMessage(), $ex->getCode(), $ex);
        }
    }
}

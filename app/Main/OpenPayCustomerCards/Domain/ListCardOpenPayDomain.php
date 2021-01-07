<?php

namespace App\Main\OpenPayCustomerCards\Domain;

use App\OpenpayCustomerCards;

class ListCardOpenPayDomain
{
    public function __invoke($array)
    {
        try {
            return OpenpayCustomerCards::where($array)->get();
        } catch (\Exception $ex) {
            \Log::error($ex->getMessage().'('.$ex->getCode().')');
            throw new \Exception($ex->getMessage(), (int) $ex->getCode(), $ex);
        }
    }
}

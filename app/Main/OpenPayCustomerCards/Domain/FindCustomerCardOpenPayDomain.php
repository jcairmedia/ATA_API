<?php

namespace App\Main\OpenPayCustomerCards\Domain;

use App\OpenpayCustomerCards;

class FindCustomerCardOpenPayDomain
{
    public function __invoke($array)
    {
        try {
            return OpenpayCustomerCards::where($array)
            ->join('openpay_customers', 'openpay_customers.id', 'openpay_customer_cards.user_id')
            ->select([
                'openpay_customers.id_open_pay as idcustomeropenpay',
                'openpay_customer_cards.*',
                ])
            ->first();
        } catch (\Exception $ex) {
            \Log::error($ex->getMessage().'('.$ex->getCode().')');
            throw new \Exception($ex->getMessage(), (int) $ex->getCode(), $ex);
        }
    }
}

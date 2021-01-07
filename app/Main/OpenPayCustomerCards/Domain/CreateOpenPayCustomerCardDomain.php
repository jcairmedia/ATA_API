<?php

namespace App\Main\OpenPayCustomerCards\Domain;

use App\OpenpayCustomerCards;

class CreateOpenpayCustomerCardDomain
{
    public function __invoke(OpenpayCustomerCards $card)
    {
        try {
            $card->saveOrFail();

            return $card;
        } catch (\Exception $ex) {
            \Log::error($ex->getMessage().'('.$ex->getCode().')');
            throw new \Exception($ex->getMessage(), (int) $ex->getCode(), $ex);
        }
    }
}

<?php

namespace App\Main\Users\Domain;

use App\UserAddress;

class AddAddressDomain
{
    public function __invoke($array)
    {
        try {
            $u = (new UserAddress($array));

            return $u->saveOrFail();
        } catch (\Exception $ex) {
            \Log::error($ex->getMessage().'('.$ex->getCode().')');
            throw new \Exception($ex->getMessage(), $ex->getCode(), $ex);
        }
    }
}

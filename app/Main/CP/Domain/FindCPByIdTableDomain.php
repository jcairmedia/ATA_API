<?php

namespace App\Main\CP\Domain;

use App\Postalcode;

class FindCPByIdTableDomain
{
    public function __invoke(array $where)
    {
        try {
            return Postalcode::query()
            ->where($where)
            ->firstOrFail();
        } catch (\Exception $ex) {
            \Log::error($ex->getMessage().'('.$ex->getCode().')');
            throw new \Exception($ex->getMessage(), $ex->getCode(), $ex);
        }
    }
}

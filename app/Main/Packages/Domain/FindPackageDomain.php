<?php

namespace App\Main\Packages\Domain;

use App\Packages;

class FindPackageDomain
{
    public function __invoke($arrayWhere)
    {
        try {
            return Packages::where($arrayWhere)->first();
        } catch (\Exception $ex) {
            \Log::error($ex->getMessage().'('.$ex->getCode().')');
            throw new \Exception($ex->getMessage(), $ex->getCode(), $ex);
        }
    }
}

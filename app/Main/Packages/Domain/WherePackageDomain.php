<?php

namespace App\Main\Packages\Domain;

use App\Packages;

class WherePackageDomain
{
    public function __invoke($arrayWhere)
    {
        return Packages::where($arrayWhere)->get();
    }
}

<?php

namespace App\Main\Packages\Domain;

use App\Packages;

class PackageDomain
{
    public function all(array $array_columns = null)
    {
        return is_null($array_columns) || count($array_columns) <= 0 ? Packages::where('state', 1)->get() : Packages::select($array_columns)->where('state', 1)->get();
    }
}

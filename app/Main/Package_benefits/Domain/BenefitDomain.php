<?php

namespace App\Main\Package_benefits\Domain;

use App\PackageBenefit;

class BenefitDomain
{
    public function __construct()
    {
    }

    public function all(array $array_columns = null)
    {
        // return PackageBenefit::all();
        return is_null($array_columns) || count($array_columns) <= 0 ? PackageBenefit::all() : PackageBenefit::all($array_columns);
    }

    public function byPackage(int $packageId, array $array_columns = null)
    {
        return [is_null($array_columns) || count($array_columns) <= 0 ? PackageBenefit::where('package_id', $packageId)->get() : PackageBenefit::select($array_columns)->where('package_id', $packageId)->get()];
    }
}

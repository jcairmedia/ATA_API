<?php

namespace App\Main\Package_benefits\Domain;

use App\PackageBenefit;

class BenefitDomain
{
    public function __construct()
    {
    }

    public function all()
    {
        return PackageBenefit::all();
    }

    public function byPackage(int $packageId)
    {
        return PackageBenefit::where('package_id', $packageId)->get();
    }
}

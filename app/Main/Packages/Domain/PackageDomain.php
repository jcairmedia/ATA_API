<?php

namespace App\Main\Packages\Domain;

use App\Packages;

class PackageDomain
{
    public function all()
    {
        return Packages::all();
    }
}

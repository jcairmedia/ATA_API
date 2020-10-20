<?php

namespace App\Main\Packages\UseCases;

use App\Main\Packages\Domain\PackageDomain;

class ListPackagesUseCases
{
    public function __construct(PackageDomain $packageDomain)
    {
        $this->packageDomain = $packageDomain;
    }

    public function list()
    {
        return $this->packageDomain->all();
    }
}

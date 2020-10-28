<?php

namespace App\Main\Packages\UseCases;

use App\Main\Packages\Domain\PackageDomain;

class ListPackagesUseCases
{
    public function __construct(PackageDomain $packageDomain)
    {
        $this->packageDomain = $packageDomain;
    }

    public function list(array $array_columns = null)
    {
        return ['data' => $this->packageDomain->all($array_columns)];
    }
}

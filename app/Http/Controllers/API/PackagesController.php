<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Main\Packages\Domain\PackageDomain;
use App\Main\Packages\UseCases\ListPackagesUseCases;

class PackagesController extends Controller
{
    public function all()
    {
        $pd = new PackageDomain();
        $lp = new ListPackagesUseCases($pd);

        return $lp->list();
    }
}

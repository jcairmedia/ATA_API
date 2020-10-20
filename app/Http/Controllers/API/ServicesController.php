<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Main\Services\Domain\ServiceDomain;
use App\Main\Services\UseCases\ServiceUseCases;

class ServicesController extends Controller
{
    public function all()
    {
        $sd = new ServiceDomain();
        $scu = new ServiceUseCases($sd);

        return $scu();
    }
}

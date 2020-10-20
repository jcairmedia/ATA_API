<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Main\Package_benefits\Domain\BenefitDomain;
use App\Main\Package_benefits\UseCases\ByPackageUseCase;
use App\Main\Package_benefits\UseCases\ListBenefitsUseCase;

class BenefitsController extends Controller
{
    public function all()
    {
        $bd = new BenefitDomain();
        $bc = new ListBenefitsUseCase($bd);

        return response()->json($bc());
    }

    public function byPackage(int $id)
    {
        $bd = new BenefitDomain();
        $bp = new ByPackageUseCase($bd);

        return response()->json($bp($id));
    }
}

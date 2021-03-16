<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Main\FederalEntities\Domain\FederalEntitiesDomain;

class CRUDFederativeEntitieController extends Controller
{
    public function index()
    {
        $array = (new FederalEntitiesDomain())();

        return response()->json([
            'data' => $array,
        ], 200);
    }
}

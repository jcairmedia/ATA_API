<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Main\Cases\CasesUses\ListCaseCaseUses;
use Illuminate\Http\Request;

class RCasesController extends Controller
{
    public function list(Request $request)
    {
        $index = $request->input('index') ?? 0;
        $filter = $request->input('filter') ?? '';
        $byPage = $request->input('byPage') ?? 10;
        $caseUse = new ListCaseCaseUses();

        return response()->json($caseUse($filter, $index, $byPage, []));
    }
}

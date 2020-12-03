<?php

namespace App\Http\Controllers\API;

use App\Cases;
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

    public function setLawyer(Request $request)
    {
        try {
            $caseId = (int) $request->input('caseId');
            $userId = (int) $request->input('userId');
            $case = Cases::where(['id' => $caseId])->first();
            if ($case == null) {
                throw new \Exception('Caso no encontrado', 500);
            }
            Cases::where(['id' => $caseId])->update(['users_id' => $userId]);

            return response()->json([
                'code' => 200,
                'message' => 'Abogado asociado al caso',
        ], 200);
        } catch (\Exception $ex) {
            \Log::error('Asociar rol al usuario: '.$ex->getMessage().$ex->getCode());

            return response()->json([
                    'code' => (int) $ex->getCode(),
                    'message' => $ex->getMessage(),
            ], (int) $ex->getCode());
        }
    }
}

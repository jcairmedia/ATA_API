<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Main\Cases\CasesUses\CancelSubscriptionCaseUse;
use Illuminate\Http\Request;

class CasesController extends Controller
{
    public function close(Request $request)
    {
        try {
            $caseId = (int) $request->input('caseId');
            (new CancelSubscriptionCaseUse())($caseId);

            return response()->json([
                'code' => 200,
                'message' => 'Caso cerrado y suscripción cerrada',
            ], 200);
        } catch (\Exception $ex) {
            $code = (int) $ex->getCode();
            if (!(($code >= 400 && $code <= 422) || ($code >= 500 && $code <= 503))) {
                $code = 500;
            }

            return response()->json([
                'code' => (int) $ex->getCode(),
                'message' => $ex->getMessage(),
            ], $code);
        }
    }
}

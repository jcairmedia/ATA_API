<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Questionnaire;

class CRUDQuestionnaireController extends Controller
{
    public function list()
    {
        try {
            return response()->json([
                'code' => 200,
                'data' => Questionnaire::get()->toArray(),
            ], 200);
        } catch (\Exception $ex) {
            \Log::error('ex: '.print_r($ex->getMessage(), 1));
            $code = (int) $ex->getCode();
            if (!(($code >= 400 && $code <= 422) || ($code >= 500 && $code <= 503))) {
                $code = 500;
            }

            return response()->json([
                'code' => (int) $code,
                'message' => 'Configuración del calendario incorrecta',
                'data' => $ex->getMessage(),
            ], $code);
        }

        return;
    }
}

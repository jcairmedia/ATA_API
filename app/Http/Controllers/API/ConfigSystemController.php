<?php

namespace App\Http\Controllers\API;

use App\Conf_system;
use App\Http\Controllers\Controller;
use App\Http\Requests\ConfigSystem\ChangePriceMeetingPaidRequest;

class ConfigSystemController extends Controller
{
    public function priceMeetingPaid(ChangePriceMeetingPaidRequest $request)
    {
        try {
            Conf_system::where(['name' => 'MEETING_PAID_AMOUNT'])->update(['value' => $request->input('price')]);

            return response()->json([
                'code' => 200,
                'message' => 'Precio actualizado de la asesoría',
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

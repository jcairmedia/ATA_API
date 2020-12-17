<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Main\Cases_payments\UseCases\ListPaymentCasesBySubscriptionUseCase;
use App\Main\Subscription\Domain\LastSubscriptionCaseDomain;
use Illuminate\Http\Request;

class CasesPaymentsController extends Controller
{
    public function paymentsCase(Request $request)
    {
        try {
            $caseId = $request->input('caseId');
            $subscriptionObj = (new LastSubscriptionCaseDomain())(['cases.id' => $caseId]);
            if (is_null($subscriptionObj)) {
                throw new \Exception('El caso no tiene asociado una suscripción', 404);
            }
            $subscriptionId = $subscriptionObj->id;
            \Log::error('subscription ID: '.$subscriptionId);
            $payments = (new ListPaymentCasesBySubscriptionUseCase())($subscriptionId);

            return response()->json([
                'code' => 200,
                'message' => '',
                'data' => $payments->toArray(),
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

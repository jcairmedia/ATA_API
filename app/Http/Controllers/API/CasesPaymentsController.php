<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Http\Requests\Packages\GetPaymentsCasesRequest;
use App\Main\Cases_payments\UseCases\ListPaymentCasesBySubscriptionUseCase;
use App\Main\Subscription\Domain\LastSubscriptionCaseDomain;

class CasesPaymentsController extends Controller
{
    /**
     * @OA\Get(
     *      path="/api/cases/payments",
     *      summary="Consulta de pagos del caso",
     *      security={{"bearer_token":{}}},
     *      @OA\Response(
     *        response=201,
     *        description="Consulta de los pagos del caso",
     *        @OA\JsonContent(
     *          @OA\Property(
     *            property="code",
     *            type="int",
     *            example="200"
     *          ),
     *          @OA\Property(
     *            property="data",
     *            type="array",
     *            collectionFormat="multi",
     *            description="Lista de pagos ordenados descendente",
     *            @OA\Items(
     *              type="object",
     *              @OA\Property(property="amount", type="number", example="2800"),
     *              @OA\Property(property="bank", type="string", example="Banamex"),
     *              @OA\Property(property="bank_auth_code", type="string", example="801585"),
     *              @OA\Property(property="brand", type="string", example="visa"),
     *              @OA\Property(property="card_type", type="string", example="visa"),
     *              @OA\Property(property="cases_id", type="number", example="1"),
     *              @OA\Property(property="created_at", type="string", example="2021-01-20T21:52:02.000000Z"),
     *              @OA\Property(property="currency", type="string", example="MXN"),
     *              @OA\Property(property="folio", type="string", example="tr6upcmdama4dmyjee3a"),
     *              @OA\Property(property="id", type="number", example="1"),
     *              @OA\Property(property="subscription_id", type="number", example="1"),
     *              @OA\Property(property="type_paid", type="string", example="ONLINE"),
     *              @OA\Property(property="updated_at", type="string", example="2021-02-20T21:52:02.000000Z"),
     *            )
     *          )
     *        )
     *      ),
     *      @OA\Response(
     *        response=422,
     *        description="Unprocessable Entity",
     *        @OA\JsonContent(
     *          @OA\Property(
     *            property="message",
     *            type="string",
     *            example="The given data was invalid."
     *          ),
     *          @OA\Property(
     *            property="errors",
     *            type="object",
     *            @OA\Property(
     *              property="caseId",
     *              type="array",
     *              collectionFormat="multi",
     *              @OA\Items(
     *                  type="string", example="El campo case id es obligatorio"
     *              )
     *           ),
     *          )
     *        )
     *      )
     * )
     */
    public function paymentsCase(GetPaymentsCasesRequest $request)
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

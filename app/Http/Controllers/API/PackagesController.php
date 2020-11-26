<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Http\Requests\Packages\PackagesRequest;
use App\Main\Cases\CasesUses\CasesCasesUse;
use App\Main\Packages\Domain\PackageDomain;
use App\Main\Packages\Domain\WherePackageDomain;
use App\Main\Packages\UseCases\ListPackagesUseCases;
use App\Main\Subscription\CaseUses\SubscriptionCaseUses;
use App\Main\Subscription\CaseUses\SubscriptionOpenPayCaseUses;
use Exception;

class PackagesController extends Controller
{
    /**
     * @OA\Get(
     *  path="/api/packages",
     *  summary="Todos los paquetes",
     *  @OA\Response(
     *    response=200,
     *    description="List",
     *    @OA\JsonContent(
     *      @OA\Property(
     *        property="data",
     *        type="array",
     *        collectionFormat="multi",
     *        @OA\Items(
     *            type="object",
     *            @OA\Property(property="id", type="number", example="1"),
     *            @OA\Property(property="name", type="string", example="Básico"),
     *            @OA\Property(property="description", type="string", example="Seguimiento..."),
     *            @OA\Property(property="periodicity", type="string", example="MONTHLY|YEARLY" ),
     *            @OA\Property(property="amount", type="number", example="2800.00"),
     *          )
     *      )
     *     )
     *  )
     * )
     */
    public function all()
    {
        $pd = new PackageDomain();
        $lp = new ListPackagesUseCases($pd);

        return response()->json($lp->list(['id', 'name', 'periodicity', 'amount']));
    }

    /**
     * @OA\Get(
     *  path="/api/contracts",
     *  summary="Contración de paquetes",
     *  @OA\RequestBody(
     *   required=true ,
     *   description="Registrar una cita gratuita",
     *   @OA\JsonContent(
     *    required={"tokenId", "deviceSessionId", "packageId", "serviceId"},
     *    @OA\Property(property="tokenId", type="string", example="kl8gm1x69epllqw1sqdj"),
     *    @OA\Property(property="deviceSessionId", type="string", example="kl8gm1x69epllqw1sqdj"),
     *    @OA\Property(property="packageId", type="number", example="1"),
     *    @OA\Property(property="serviceId", type="number", example="1"),
     *   )
     *  ),
     *  @OA\Response(
     *    response=400,
     *    description="List",
     *    @OA\JsonContent(
     *      @OA\Property(
     *        property="code",
     *        type="int",
     *        example="201"
     *      ),
     *      @OA\Property(
     *        property="message",
     *        type="string",
     *        example="Token ID does not exist"
     *      ),
     *     )
     *  ),
     *  @OA\Response(
     *    response=201,
     *    description="List",
     *    @OA\JsonContent(
     *      @OA\Property(
     *        property="data",
     *        type="array",
     *        collectionFormat="multi",
     *        @OA\Items(
     *            type="object",
     *            @OA\Property(property="cases_id", type="number", example="kl8gm1x69epllqw1sqdj", description="Id interno de la BD de ATA"),
     *            @OA\Property(property="id_card_openpay", type="string", example="9823987m1x69epllqw1sqdj", description="Id de la tarjeta registrada en openpay"),
     *            @OA\Property(property="id_suscription_openpay", type="string", example="hj3gm1x69epllqw1sqdj", description="Id de la suscripción de en openpay"),
     *            @OA\Property(property="id_customer_openpay", type="string", example="9823bbgm1x69epllqw1sqdj", description="Id del cliente registrado en openpay")
     *          )
     *      )
     *     )
     *  )
     * )
     */
    public function contract(PackagesRequest $request)
    {
        $user = $request->user();

        try {
            $data = $request->all();
            $planId = '';

            // Buscar el paquete para obtener el identificador del plan
            $packageId = $data['packageId'];
            $arrayPackages = (new WherePackageDomain())(['id' => $packageId]);

            if (count($arrayPackages) <= 0) {
                throw new Exception('Paquete no encontrado', 404);
            }
            $packageObj = $arrayPackages[0];
            if ($packageObj->id_plan_openpay == '') {
                throw new Exception('El paquete no cuenta con un plan de open pay', 401);
            }
            $planId = $packageObj->id_plan_openpay;

            // -- PROCESO DE SUSCRIPCIÓN --
            $arrayResponseSubscription = (new SubscriptionOpenPayCaseUses())(
                $user,
                $data['tokenId'],
                $data['deviceSessionId'],
                $planId
            );
            \Log::error(print_r($arrayResponseSubscription, 1));

            // return $packageObj->toArray();
            // Generar pdf
            $urlDoc = 'una url de prueba';

            // Persistir un Caso
            $objCase = (new CasesCasesUse())(
                $packageObj,
                $arrayResponseSubscription['customerId'],
                $user->id,
                $data['serviceId'],
                $urlDoc
            );

            // Persistir la subscripción
            $subs = (new SubscriptionCaseUses())(
                $objCase->id,
                $arrayResponseSubscription['cardId'],
                $arrayResponseSubscription['subscriptionId'],
                $arrayResponseSubscription['customerId']
            );

            // -- ENVIAR DATOS AL USUARIO
            // enviar SMS
            // enviar correo
            // response cliente
            return response()->json(['data' => $subs->toArray()], 201);
        } catch (Exception $ex) {
            \Log::error($ex->getMessage());
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

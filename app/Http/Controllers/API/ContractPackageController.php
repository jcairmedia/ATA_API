<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Http\Requests\Packages\PackagesRequest;
use App\Main\Cases\CasesUses\CasesCasesUse;
use App\Main\Cases\CasesUses\CreatePDFContractCaseUse;
use App\Main\Cases\CasesUses\TemplateContractCasesUse;
use App\Main\Cases\Domain\CaseInnerJoinCustomerDomain;
use App\Main\Packages\Domain\WherePackageDomain;
use App\Main\Subscription\CaseUses\SubscriptionCaseUses;
use App\Main\Subscription\Services\SubscriptionOpenPayService;
use App\Utils\DateUtil;
use App\Utils\SendEmail;
use App\Utils\SMSUtil;

class ContractPackageController extends Controller
{
    public function __construct()
    {
        $this->LAYOUT_CONTRACT_PACKAGES = 'layout_contract_package';
    }

    /**
     * @OA\POST(
     *  path="/api/contracts",
     *  summary="Contración de paquetes: El tipo de autenticacion es bearer",
     *  @OA\SecurityScheme(
     *      securityScheme="bearerAuth",
     *      in="header",
     *      name="bearerAuth",
     *      type="http",
     *      scheme="bearer",
     *      bearerFormat="JWT",
     * ),
     *  @OA\RequestBody(
     *   required=true ,
     *   description="Registrar una cita gratuita",
     *   @OA\JsonContent(
     *    required={"tokenId", "deviceSessionId", "packageId", "serviceId"},
     *    @OA\Property(property="tokenId", type="string", example="kl8gm1x69epllqw1sqdj"),
     *    @OA\Property(property="deviceSessionId", type="string", example="kl8gm1x69epllqw1sqdj"),
     *    @OA\Property(property="packageId", type="number", example="1", description="Id del paquete"),
     *    @OA\Property(property="serviceId", type="number", example="1", description="Id del servicio"),
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
    public function index(PackagesRequest $request)
    {
        $user = $request->user();
        $nowDT = new \DateTime();
        try {
            $data = $request->all();
            $planId = '';

            // Buscar el paquete para obtener el identificador del plan
            $packageId = $data['packageId'];
            $arrayPackages = (new WherePackageDomain())(['id' => $packageId]);

            if (count($arrayPackages) <= 0) {
                throw new \Exception('Paquete no encontrado', 404);
            }
            $packageObj = $arrayPackages[0];
            if ($packageObj->id_plan_openpay == '') {
                throw new \Exception('El paquete no cuenta con un plan de open pay', 401);
            }
            $planId = $packageObj->id_plan_openpay;

            // -- PROCESO DE SUSCRIPCIÓN OPEN PAY--
            $arrayResponseSubscription = (new SubscriptionOpenPayService())(
                $user,
                $data['tokenId'],
                $data['deviceSessionId'],
                $planId
            );
            \Log::error(print_r($arrayResponseSubscription, 1));

            // Guardar caso en BD
            $objCase = (new CasesCasesUse())(
                $packageObj,
                $arrayResponseSubscription['customerId'],
                $user->id,
                $data['serviceId'],
                ''
            );

            // --- Create CONTRACT ---
            // Search Case

            $obj = (new CaseInnerJoinCustomerDomain())(['cases.id' => $objCase->id]);

            // Generate Contract
            $view = (new TemplateContractCasesUse())($obj);
            $namefile = preg_replace('/[^A-Za-z0-9\-]/', '', uniqid($obj->packages_id.$obj->services_id.$obj->customer_id.date('Ymdhis'))).'.pdf';

            // Create and save PDF
            ini_set('max_execution_time', 5000);
            ini_set('memory_limit', '512M');

            (new CreatePDFContractCaseUse())($view['layout'], $namefile, storage_path('contracts/'));

            // Update package with URL_DOC

            $objCase->url_doc = $namefile;
            $objCase->save();

            // Guardar la Subscripción en BD
            $subs = (new SubscriptionCaseUses())(
                $objCase->id,
                $arrayResponseSubscription['cardId'],
                $arrayResponseSubscription['subscriptionId'],
                $arrayResponseSubscription['customerId']
            );

            // -- ENVIAR DATOS AL USUARIO
            // Enviar SMS
            $testSMS = $this->textSMS($packageObj->name);
            if ($user->phone != null) {
                (new SMSUtil())($testSMS, $user->phone);
            }

            // Enviar correo
            $DT_valid = new \DateTime(date('Y-m-d', strtotime(date('Y-m-d').'+1month-1day')));

            $dateUtil = new DateUtil();

            $month = $dateUtil->getNameMonth($nowDT->format('m'));
            $day = $nowDT->format('d');

            $month_valid = $dateUtil->getNameMonth($DT_valid->format('m'));
            $day_valid = $DT_valid->format('d');

            $view = view($this->LAYOUT_CONTRACT_PACKAGES, [
                'package' => $packageObj->name,
                'day' => $day,
                'month' => $month,
                'day_valid' => $day_valid,
                'month_valid' => $month_valid,
            ])->render();
            (new SendEmail())(
                ['email' => env('EMAIL_FROM')],
                [$user->email],
                'Pago exitoso del paquete '.$packageObj->name,
                '',
                $view
            );
            // response cliente
            return response()->json(['data' => $subs->toArray()], 201);
        } catch (\Exception $ex) {
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

    public function textSMS($paquete)
    {
        return
        'Hemos recibido con éxito tu pago para nuestro'.
        ' servicio de asesoría legal en nuestro'.
        ' Paquete '.$paquete.'.'.
        ' Para dudas y aclaraciones comunícate al 55-2625-0649
        ';
    }
}

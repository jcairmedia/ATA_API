<?php

namespace App\Http\Controllers\API;

// use App\Events\UserSendMeetingEvent;
use App\Http\Controllers\Controller;
use App\Http\Requests\Meetings\OnlinePaidMeetingRequest;
use App\Main\Config_System\Domain\SearchConfigDomain;
use App\Main\Config_System\UseCases\SearchConfigurationUseCase;
use App\Main\Meetings\UseCases\MeetingOnlinePayment;
use App\Main\Meetings\UseCases\MeetingRegisterUseCase;
use App\Main\Meetings_payments\Domain\PaymentDomain;
use App\Main\Meetings_payments\UseCases\RegisterPaymentUseCases;
use App\Utils\StorePaymentOpenPay;

class OnlinePaidMeetingController extends Controller
{
    public function __construct()
    {
        $this->CONFIG_PHONE_OFFICE = 'PHONE_OFFICE';
        $this->CONFIG_MEETING_PAID_DURATION = 'MEETING_PAID_DURATION';
        $this->CONFIG_MEETING_PAID_AMOUNT = 'MEETING_PAID_AMOUNT';
    }

    /**
     * @OA\Post(
     *  path="/api/meeting/paid/online",
     *  summary="Registrar una cita de pago en línea",
     *  @OA\RequestBody(
     *   required=true ,
     *   description="Registrar una cita por pago en línea",
     *   @OA\JsonContent(
     *    required={"idfe","date","time","type_meeting", "type_payment", "deviceIdHiddenFieldName", "token_id"},
     *    @OA\Property(property="description", type="string", example="Una descripción"),
     *    @OA\Property(property="idfe", type="string", example="Indetificador unico de la entidad federativa"),
     *    @OA\Property(property="date", type="string", format="date", example="2020-10-26"),
     *    @OA\Property(property="time", type="string", format="string", example="18:00", pattern="/^(09|(1[0-8]))\:[0-5][0-9]$/"),
     *    @OA\Property(property="type_meeting", type="string", format="string", example="CALL", pattern="/^(CALL|VIDEOCALL|PRESENTIAL)$/"),
     *    @OA\Property(property="type_payment", type="string", format="string", example="ONLINE", pattern="/^(ONLINE)$/"),
     *    @OA\Property(property="deviceIdHiddenFieldName", description="Identificador del dispositivo generado con la herramienta antifraudes", type="string", format="string", example="236454545454848484"),
     *    @OA\Property(property="token_id", type="string", format="string", description="Identificador único del cargo. Debe ser único entre todas las transacciones", example="4545454445454"),
     *   )
     *  ),
     *  @OA\Response(
     *    response=201,
     *    description="Created",
     *    @OA\JsonContent(
     *      @OA\Property(
     *        property="code",
     *        type="int",
     *        example="201"
     *      ),     *
     *      @OA\Property(
     *        property="data",
     *        type="object",
     **       @OA\Property(
     *          property="meeting",
     *          type="object",
     *          @OA\Property(property="id", type="number", example="1"),
     *          @OA\Property(property="folio", type="string", example="261020201320595f97219b21381"),
     *          @OA\Property(property="description", type="string", example="una descripción"),
     *          @OA\Property(property="category", type="string", example="PAID"),
     *          @OA\Property(property="type_meeting", type="number", example="CALL"),
     *          @OA\Property(property="url_meeting", type="number", example=""),
     *          @OA\Property(property="dt_start", type="string", format="date-time", example="2020-10-26 18:40:00"),
     *          @OA\Property(property="dt_end", type="string", format="date-time", example="2020-10-26 19:20:00"),
     *          @OA\Property(property="price", type="number", example="1650.50"),
     *          @OA\Property(property="contacts_id", type="number", example="23"),
     *          @OA\Property(property="updated_at", type="number", example="1"),
     *          @OA\Property(property="created_at", type="number", example="1"),
     *         )
     *      )
     *    )),
     *   @OA\Response(
     *    response=422,
     *    description="Unprocessable Entity",
     *    @OA\JsonContent(
     *      @OA\Property(
     *        property="code",
     *        type="int",
     *        example="422"
     *      ),
     *      @OA\Property(
     *        property="message",
     *        type="string",
     *        example="The given data was invalid."
     *      ),
     *      @OA\Property(
     *        property="errors",
     *        type="object",
     *        @OA\Property(
     *            property="email",
     *            type="array",
     *            collectionFormat="multi",
     *             @OA\Items(type="string", example="El campo email es obligatorio.")
     *        ),
     *        @OA\Property(
     *            property="last_name",
     *            type="array",
     *            collectionFormat="multi",
     *             @OA\Items(type="string", example="El campo lastname es obligatorio")
     *        ),
     *        @OA\Property(
     *            property="deviceIdHiddenFieldName",
     *            type="array",
     *            collectionFormat="multi",
     *             @OA\Items(type="string", example="El campo device id hidden field name es obligatorio.")
     *        ),
     *        @OA\Property(
     *            property="token_id",
     *            type="array",
     *            collectionFormat="multi",
     *             @OA\Items(type="string", example="El campo token id es obligatorio.")
     *        )
     *      )
     *    )
     *  )
     * )
     */
    public function index(OnlinePaidMeetingRequest $request)
    {
        try {
            $data = $request->all();
            $searchconfusecase = new SearchConfigurationUseCase(new SearchConfigDomain());
            $response_CONFIG_PHONE_OFFICE = $searchconfusecase($this->CONFIG_PHONE_OFFICE);
            $response_CONFIG_MEETING_PAID_DURATION = $searchconfusecase($this->CONFIG_MEETING_PAID_DURATION);
            $response_CONFIG_MEETING_PAID_AMOUNT = $searchconfusecase($this->CONFIG_MEETING_PAID_AMOUNT);

            $PHONE_OFFICE = $response_CONFIG_PHONE_OFFICE->value;
            $MEETING_PAID_DURATION = $response_CONFIG_MEETING_PAID_DURATION->value;
            $MEETING_PAID_AMOUNT = $response_CONFIG_MEETING_PAID_AMOUNT->value;

            $meeting_online = new MeetingOnlinePayment(
                    new StorePaymentOpenPay(),
                    new RegisterPaymentUseCases(new PaymentDomain()),
                    new MeetingRegisterUseCase());
            $objectMeeting = $meeting_online($data,
                                            $MEETING_PAID_AMOUNT,
                                            $MEETING_PAID_DURATION,
                                            $PHONE_OFFICE,
                                            $request->user()
                                        );
            // event(new UserSendMeetingEvent($objectMeeting['meeting'], $objectMeeting['contact']));

            return response()->json(['code' => 201, 'data' => $objectMeeting['meeting']], 201);
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

/* End of file OnlinePaidMeeting.php */

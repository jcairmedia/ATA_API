<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Http\Requests\Meetings\OfflinePaidMeetingRequest;
use App\Main\Config_System\Domain\SearchConfigDomain;
use App\Main\Config_System\UseCases\SearchConfigurationUseCase;
use App\Main\Contact\Domain\ContactCreatorDomain;
use App\Main\Contact\Domain\ContactSelectDomain;
use App\Main\Contact\UseCases\ContactFindUseCase;
use App\Main\Contact\UseCases\ContactRegisterUseCase;
use App\Main\Meetings\UseCases\MeetingOffilePayment;
use App\Main\Meetings\UseCases\MeetingRegisterUseCase;
use App\Main\OpenPay_payment_references\Domain\CreaterChargeDomain;
use App\Main\OpenPay_payment_references\UseCases\RegisterOpenPayChargeUseCase;
use App\Utils\StorePaymentOpenPay;

class OfflinePaidMeetingController extends Controller
{
    public function __construct()
    {
        $this->CONFIG_PHONE_OFFICE = 'PHONE_OFFICE';
        $this->CONFIG_MEETING_PAID_DURATION = 'MEETING_PAID_DURATION';
        $this->CONFIG_MEETING_PAID_AMOUNT = 'MEETING_PAID_AMOUNT';
    }

    /**
     * @OA\Post(
     *  path="/api/meeting/paid/offline",
     *  summary="Registrar una cita de pago en tienda",
     *  @OA\RequestBody(
     *   required=true ,
     *   description="Registrar una cita por pago en tienda",
     *   @OA\JsonContent(
     *    required={"name","email","phone","date","time","type_meeting", "type_payment", "deviceIdHiddenFieldName", "token_id"},
     *    @OA\Property(property="name", type="string", format="string", example="Nombres"),
     *    @OA\Property(property="email", type="string", format="email", example="user1@mail.com"),
     *    @OA\Property(property="phone", type="string", pattern="[0-9]{10}", format="number", example="1234567890"),
     *    @OA\Property(property="date", type="string", format="date", example="2020-10-26"),
     *    @OA\Property(property="time", type="string", format="string", example="18:00", pattern="/^(09|(1[0-8]))\:[0-5][0-9]$/"),
     *    @OA\Property(property="type_meeting", type="string", format="string", example="CALL", pattern="/^(CALL|VIDEOCALL|PRESENTIAL)$/"),
     *    @OA\Property(property="type_payment", type="string", format="string", example="OFFLINE", pattern="/^(OFFLINE)$/"),
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
     *          @OA\Property(property="category", type="string", example="PAID"),
     *          @OA\Property(property="type_meeting", type="number", example="CALL"),
     *          @OA\Property(property="url_meeting", type="number", example=""),
     *          @OA\Property(property="dt_start", type="string", format="date-time", example="2020-10-26 18:40:00"),
     *          @OA\Property(property="dt_end", type="string", format="date-time", example="2020-10-26 19:20:00"),
     *          @OA\Property(property="price", type="number", example="1650.50"),
     *          @OA\Property(property="contacts_id", type="number", example="23"),
     *          @OA\Property(property="updated_at", type="number", example="1"),
     *          @OA\Property(property="created_at", type="number", example="1"),
     *         ),
     *      @OA\Property(
     *          property="url_file_charge",
     *          type="string",
     *          example="https:\/\/sandbox-dashboard.openpay.mx\/paynet-pdf\/me4rw2430fbizvozxcq1\/9988783077895466",
     *          description="Url de la referencia de pago de Open pay"
     *      )
     *      ))),
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
    public function index(OfflinePaidMeetingRequest $request)
    {
        try {
            $data = $request->all();

            // Search duration meeting
            $searchconfusecase = new SearchConfigurationUseCase(new SearchConfigDomain());
            $response_CONFIG_PHONE_OFFICE = $searchconfusecase->__invoke($this->CONFIG_PHONE_OFFICE);
            $response_CONFIG_MEETING_PAID_DURATION = $searchconfusecase->__invoke($this->CONFIG_MEETING_PAID_DURATION);
            $response_CONFIG_MEETING_PAID_AMOUNT = $searchconfusecase->__invoke($this->CONFIG_MEETING_PAID_AMOUNT);
            $config = $searchconfusecase('CALENDAR_ID_MEETING_PAID');
            $config_places = $searchconfusecase('NUMBER_PLACES_MEETING_PAID');

            $numberPlaces = (int) $config_places->value;
            $idCalendar = $config->value;

            $PHONE_OFFICE = $response_CONFIG_PHONE_OFFICE->value;
            $MEETING_PAID_DURATION = $response_CONFIG_MEETING_PAID_DURATION->value;
            $MEETING_PAID_AMOUNT = $response_CONFIG_MEETING_PAID_AMOUNT->value;

            $meetingOffile = new MeetingOffilePayment(
                                    new StorePaymentOpenPay(),
                                    new MeetingRegisterUseCase(),
                                    new RegisterOpenPayChargeUseCase(new CreaterChargeDomain()),
                                    new ContactRegisterUseCase(new ContactCreatorDomain()),
                                    new ContactFindUseCase(new ContactSelectDomain()));

            $objectMeeting = $meetingOffile($data, $MEETING_PAID_DURATION, $PHONE_OFFICE, $MEETING_PAID_AMOUNT, $numberPlaces, $idCalendar);
            \Log::error(print_r($objectMeeting, 1));

            return response()->json(['code' => 201, 'data' => $objectMeeting], 201);
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
}

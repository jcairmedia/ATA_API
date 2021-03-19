<?php

namespace App\Http\Controllers\API;

use App\Events\UserSendMeetingEvent;
use App\Http\Controllers\Controller;
use App\Http\Requests\Meetings\CustomerMeetingsOnlinePaymentsRequest;
use App\Main\Config_System\Domain\SearchConfigDomain;
use App\Main\Config_System\UseCases\SearchConfigurationUseCase;
use App\Main\Meetings\UseCases\MeetingOnlineCardPayment;
use App\Main\Meetings\UseCases\MeetingRegisterUseCase;
use App\Main\Meetings_payments\Domain\PaymentDomain;
use App\Main\Meetings_payments\UseCases\RegisterPaymentUseCases;
use App\Main\UserAddress\Domain\GetAddressUserDomain;
use App\Utils\ChargeByCardCustomerOpenPay;

class CustomerMeetingsOnlinePaymentController extends Controller
{
    public function __construct()
    {
        $this->CONFIG_PHONE_OFFICE = 'PHONE_OFFICE';
        $this->CONFIG_MEETING_PAID_DURATION = 'MEETING_PAID_DURATION';
        $this->CONFIG_MEETING_PAID_AMOUNT = 'MEETING_PAID_AMOUNT';
    }

    /**
     * @OA\POST(
     *  tags={"App móvil"},
     *  path="/api/v2/meeting/paid/online",
     *  summary="Registro de citas online, pagada con tarjeta seleccionada",
     *  description="",
     *  security={{"bearer_token":{}}},
     *  @OA\RequestBody(
     *   required=true ,
     *   description="Para más información sobre cargo con tarjeta previamente registrada, consultar la siguiente dirección https://www.openpay.mx/docs/api/#con-id-de-tarjeta-o-token",
     *   @OA\JsonContent(
     *    required={"idCard","cvv2","date","time","type_meeting", "type_payment", "deviceIdHiddenFieldName"},
     *    @OA\Property(property="idCard", type="string", format="string", example="1", description="Identificador único interno de la tarjeta registrada por el cliente"),
     *    @OA\Property(property="cvv2", type="string", format="string", example="121", description="cvv de la tarjeta"),
     *    @OA\Property(property="date", type="string",  format="date", example="2021-01-26", description="fecha de la cita"),
     *    @OA\Property(property="time", type="string", example="10:00", description="hora de la cita"),
     *    @OA\Property(property="type_meeting", type="string", example="CALL", pattern="/^(CALL|VIDEOCALL|PRESENTIAL)$/" ),
     *    @OA\Property(property="type_payment", type="string", example="ONLINE", pattern="/^(ONLINE)$/"),
     *    @OA\Property(property="deviceIdHiddenFieldName", type="string", example="236454545454848484", format="string", description="Deberá ser generada desde el API JavaScript")
     *   )
     *  ),
     *
     *  @OA\Response(
     *    response=201,
     *    description="Created",
     *    @OA\JsonContent(
     *       @OA\Property(
     *        property="code",
     *        type="int",
     *        example="201"
     *      ),
     *    @OA\Property(
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
     *          @OA\Property(property="price", type="number", example="300.00"),
     *          @OA\Property(property="contacts_id", type="number", example="23"),
     *          @OA\Property(property="updated_at", type="number", example="1"),
     *          @OA\Property(property="created_at", type="number", example="1"),
     *         )
     *      )
     *    )
     *  ),
     *  @OA\Response(
     *   response=422,
     *   description="Unprocessable Entity",
     *    @OA\JsonContent(
     *      @OA\Property(
     *          property="message",
     *          type="string",
     *          example="The given data was invalid."
     *        ),
     *      @OA\Property(
     *            property="errors",
     *            type="object",
     *            @OA\Property(
     *                property="date",
     *                type="array",
     *                collectionFormat="multi",
     *                @OA\Items(type="string", example="El date no puede ser una fecha anterior a la fecha actual.")
     *            )
     *       )
     *    )
     *  )
     * )
     */
    public function index(CustomerMeetingsOnlinePaymentsRequest $request)
    {
        try {
            $full_name_customer = $request->user()->name.' '.$request->user()->last_name1.' '.$request->user()->last_name2;
            $data = $request->all();

            $data['customerId'] = $request->user()->id;
            $data['name'] = $full_name_customer;
            $data['phone'] = $request->user()->phone;
            $data['email'] = $request->user()->email;
            $searchconfusecase = new SearchConfigurationUseCase(new SearchConfigDomain());
            $response_CONFIG_PHONE_OFFICE = $searchconfusecase($this->CONFIG_PHONE_OFFICE);
            $response_CONFIG_MEETING_PAID_DURATION = $searchconfusecase($this->CONFIG_MEETING_PAID_DURATION);
            $response_CONFIG_MEETING_PAID_AMOUNT = $searchconfusecase($this->CONFIG_MEETING_PAID_AMOUNT);

            $PHONE_OFFICE = $response_CONFIG_PHONE_OFFICE->value;
            $MEETING_PAID_DURATION = $response_CONFIG_MEETING_PAID_DURATION->value;
            $MEETING_PAID_AMOUNT = $response_CONFIG_MEETING_PAID_AMOUNT->value;

            $meeting_online = new MeetingOnlineCardPayment(
                    new ChargeByCardCustomerOpenPay(),
                    new RegisterPaymentUseCases(new PaymentDomain()),
                    new MeetingRegisterUseCase()
                   );
            $objectMeeting = $meeting_online(
                $data,
                $MEETING_PAID_AMOUNT,
                $MEETING_PAID_DURATION,
                $PHONE_OFFICE
            );
            // select address User
            $addressObj = (new GetAddressUserDomain())($request->user()->id);
            event(new UserSendMeetingEvent($objectMeeting['meeting']->toArray(), [
                'name' => $request->user()->name,
                'lastname_1' => $request->user()->last_name1,
                'lastname_2' => $request->user()->last_name2,
                'curp' => $request->user()->curp,
                'email' => $request->user()->email,
                'phone' => $request->user()->phone,
                'idcp' => $addressObj->idcp,
                'street' => $addressObj->street,
                'out_number' => $addressObj->out_number,
                'int_number' => $addressObj->int_number,
                ]));

            return response()->json(['code' => 201, 'data' => $objectMeeting], 201);
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

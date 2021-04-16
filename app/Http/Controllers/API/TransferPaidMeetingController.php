<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Http\Requests\Meetings\TransferPaidMeetingRequest;
use App\Main\Config_System\Domain\SearchConfigDomain;
use App\Main\Config_System\UseCases\SearchConfigurationUseCase;
use App\Main\Meetings\UseCases\MeetingRegisterUseCase;
use App\Main\Meetings\UseCases\MeetingTransferPaidUseCase;

class TransferPaidMeetingController extends Controller
{
    public function __construct()
    {
        $this->CONFIG_PHONE_OFFICE = 'PHONE_OFFICE';
        $this->CONFIG_MEETING_PAID_DURATION = 'MEETING_PAID_DURATION';
        $this->CONFIG_MEETING_PAID_AMOUNT = 'MEETING_PAID_AMOUNT';
        $this->CONFIG_CALENDAR_ID_MEETING_PAID = 'CALENDAR_ID_MEETING_PAID';
        $this->CONFIG_NUMBER_PLACES_MEETING_PAID = 'NUMBER_PLACES_MEETING_PAID';
        $this->CONFIG_BANK_ACCOUNT = 'BANK_ACCOUNT';
    }

    /**
     * @OA\Post(
     *  path="/api/v2/meeting/paid/transfer",
     *  summary="Registrar una cita de pago por transferencia",
     *  security={{"bearer_token":{}}},
     *  description="Solicitud de asesoría de pago pagada por transferencia",
     *  @OA\RequestBody(
     *   required=true ,
     *   description="Registrar una cita por pago por transferencia",
     *   @OA\JsonContent(
     *    required={"idfe", "date","time","type_meeting", "type_payment"},
     *    @OA\Property(property="description", type="string", example="Una descripción"),
     *    @OA\Property(property="idfe", type="number", format="number", example="Identificador único de entidad federativa"),     *
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
     *          @OA\Property(property="meeting_id", type="number", example="1"),
     *          @OA\Property(property="folio", type="string", example="261020201320595f97219b21381"),
     *          @OA\Property(property="status", type="string", example="UPLOAD_PENDING"),
     *          @OA\Property(property="number_times_review", type="number", example="1"),
     *          @OA\Property(property="times_review", type="string", format="date-time", example="2020-10-26 18:40:00")),
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
    public function index(TransferPaidMeetingRequest $request)
    {
        try {
            $data = $request->all();
            $searchconfusecase = new SearchConfigurationUseCase(new SearchConfigDomain());

            $value_CONFIG_PHONE_OFFICE = ($searchconfusecase($this->CONFIG_PHONE_OFFICE))->value;
            $value_CONFIG_MEETING_PAID_DURATION = ($searchconfusecase($this->CONFIG_MEETING_PAID_DURATION))->value;
            $value_CONFIG_MEETING_PAID_AMOUNT = ($searchconfusecase($this->CONFIG_MEETING_PAID_AMOUNT))->value;
            $value_CONFIG_ID_CALENDAR_PAID = ($searchconfusecase($this->CONFIG_CALENDAR_ID_MEETING_PAID))->value;
            $value_CONFIG_NUMBER_PLACES_MEETING_PAID = ($searchconfusecase($this->CONFIG_NUMBER_PLACES_MEETING_PAID))->value;
            $value_BANK_ACCOUNT = ($searchconfusecase($this->CONFIG_BANK_ACCOUNT))->value;

            $transactionPaid = (new MeetingTransferPaidUseCase(new MeetingRegisterUseCase()))(
                $data,
                $request->user(),
                $value_CONFIG_PHONE_OFFICE,
                $value_CONFIG_MEETING_PAID_DURATION,
                $value_CONFIG_MEETING_PAID_AMOUNT,
                $value_CONFIG_ID_CALENDAR_PAID,
                $value_CONFIG_NUMBER_PLACES_MEETING_PAID,
                $value_BANK_ACCOUNT
            );

            return response()->json($transactionPaid, 200);
        } catch (\Exception $ex) {
            $code = (int) $ex->getCode();
            if (!(($code >= 400 && $code <= 422) || ($code >= 500 && $code <= 503))) {
                $code = 500;
            }

            return response()->json([
                'code' => (int) $code,
                'message' => $ex->getMessage(),
            ], $code);
        }
    }
}

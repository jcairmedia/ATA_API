<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Http\Requests\AppointmentDateRequest;
use App\Main\Config_System\Domain\SearchConfigDomain;
use App\Main\Config_System\UseCases\SearchConfigurationUseCase;
use App\Main\Date\CaseUses\AvailableHoursCaseUse;
use App\Main\Date\CaseUses\AvailableSchedulerCaseUse;

class AppointmentDateController extends Controller
{
    /**
     * @OA\Get(
     *  path="/api/hours",
     *  summary="Horarios disponibles referente a la fecha seleccionada y al tipo de cita",
     *  @OA\Parameter(in="query",
     *       required=true,
     *       description="Los valores aceptados son FREE y PAID",
     *       name="type", required=true,
     *       @OA\Schema(
     *          type="string",
     *          format="string",
     *          pattern="^(FREE|PAID)$"
     *       ),
     *       example="FREE"),
     *  @OA\Parameter(in="query", required=true, name="date", @OA\Schema(
     *       type="string",
     *       format="date"
     *    ),example="2020-10-25"),
     *  @OA\Response(
     *    response=422,
     *    description="Unprocessable Entity",
     *    @OA\JsonContent(
     *      required={"message", "errors"},
     *      @OA\Property(property="message", type="string", example="he given data was invalid."),
     *      @OA\Property(property="errors", type="array", collectionFormat="multi",
     *        @OA\Items(
     *            type="object",
     *            @OA\Property(property="date", type="string", example="El campo date ser una fecha posterior o igua a now")
     *        )
     *      )
     *    )
     *  ),
     *  @OA\Response(
     *    response=200,
     *    description="Ok",
     *    @OA\JsonContent(
     *      @OA\Property(
     *        property="data",
     *        type="array",
     *        collectionFormat="multi",
     *        @OA\Items(
     *            type="string",
     *             example="09:00"
     *        )
     *      )
     *    )
     *  )
     * )
     */
    public function hours(AppointmentDateRequest $request)
    {
        try {
            $dt = new \DateTime($request->date);
            $searchconfusecase = new SearchConfigurationUseCase(new SearchConfigDomain());
            $typeMeeting = $request->type;
            $numberPlaces = 1;
            $idCalendar = '';

            $CONFIG_HOUR_START = '09:00:00';
            $CONFIG_HOUR_END = '17:59:59';

            switch ($typeMeeting) {
                case 'FREE':
                    $config = $searchconfusecase('CALENDAR_ID_MEETING_FREE');
                    $config_places = $searchconfusecase('NUMBER_PLACES_MEETING_FREE');

                    $CONFIG_HOUR_START = $searchconfusecase('HOUR_START_MEETING_FREE')->value;
                    $CONFIG_HOUR_END = $searchconfusecase('HOUR_END_MEETING_FREE')->value;

                    $numberPlaces = (int) $config_places->value;

                    $idCalendar = $config->value;

                break;
                case 'PAID':
                    $config = $searchconfusecase('CALENDAR_ID_MEETING_PAID');
                    $config_places = $searchconfusecase('NUMBER_PLACES_MEETING_PAID');

                    $CONFIG_HOUR_START = $searchconfusecase('HOUR_START_MEETING_PAID')->value;
                    $CONFIG_HOUR_END = $searchconfusecase('HOUR_END_MEETING_PAID')->value;

                    $numberPlaces = (int) $config_places->value;
                    $idCalendar = $config->value;
                break;
                default:
                    throw new \Exception('No hay calendario para el tipo de reunión que esta solicitando', 500);
                break;
            }

            $hours = new AvailableHoursCaseUse($CONFIG_HOUR_START, $CONFIG_HOUR_END);

            $obj = $hours($dt, $typeMeeting, $idCalendar);

            $scheduler = new AvailableSchedulerCaseUse();
            $hours = $scheduler($obj, $numberPlaces, $numberPlaces);

            return response()->json($hours);
        } catch (\Exception $ex) {
            \Log::error('ex: '.print_r($ex->getMessage(), 1));
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

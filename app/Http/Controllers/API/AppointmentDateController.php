<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Http\Requests\AppointmentDateRequest;
use App\Main\Date\CaseUses\AvailableHours;
use App\Main\Date\Domain\FindHoursService;

class AppointmentDateController extends Controller
{
    /**
     * @OA\Get(
     *  path="/api/hours",
     *  summary="Horas disponibles referente a la fecha seleccionada y al tipo de cita",
     *  @OA\Parameter(in="query",
     *       required=true,
     *       description="Los valores aceptados son FREE y PAID",
     *       name="type", required=true,
     *       @OA\Schema(
     *          type="string",
     *          format="string",
     *          pattern="(FREE|CALL)$"
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
        $dt = new \DateTime($request->date);
        $fhours = new FindHoursService();
        $ah = new AvailableHours($fhours);
        $array = $ah($dt);

        return response()->json($array);
    }
}

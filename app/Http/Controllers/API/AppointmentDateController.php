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
     *  summary="Horas disponibles referente a la fecha seleccionada",
     *  @OA\RequestBody(
     *    required=true,
     *    description="",
     *    @OA\JsonContent(
     *       required={"type","date"},
     *       @OA\Property(property="type", type="string", example="CALL"),
     *       @OA\Property(property="date", type="string", format="date", example="2020-10-25")
     *    ),
     *  ),
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
     *          )
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

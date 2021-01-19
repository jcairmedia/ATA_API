<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;

class ExpoController extends Controller
{
    /**
     * @OA\POST(
     *  path="/api/exponent/devices/subscribe",
     *  tags={"App móvil"},
     *  summary="Subscripción de token (EXPO)",
     *  security={{"bearer_token":{}}},
     * @OA\RequestBody(
     *   required=true ,
     *   description="Subscripción de token (EXPO)",
     *   @OA\JsonContent(
     *    required={"expo_token"},
     *    @OA\Property(property="expo_token", type="string", format="string", example="1212545bhgghfg.hfbhjg"),
     *   )
     * ),
     *  @OA\Response(
     *    response=500,
     *    description="Internal Server Error",
     *    @OA\JsonContent(
     *       @OA\Property(
     *        property="status",
     *        type="int",
     *        example="failed"
     *      ),
     *      @OA\Property(
     *        property="error",
     *        type="object",
     *        @OA\Property(
     *          property="message",
     *          type="string",
     *          example="The token provided is not a valid expo push notification token."
     *        )
     *      )
     *    )
     *  ),
     * *  @OA\Response(
     *    response=200,
     *    description="Ok",
     *    @OA\JsonContent(
     *       @OA\Property(
     *        property="status",
     *        type="string",
     *        example="succeeded"
     *      ),
     *      @OA\Property(
     *        property="error",
     *        type="object",
     *        @OA\Property(
     *          property="expo_token",
     *          type="string",
     *          example="1212545bhgghfg.hfbhjg"
     *        )
     *      )
     *    )
     *  ),
     * )
     */
    public function index()
    {
        // code...
    }

    /**
     * @OA\POST(
     *  path="/api/exponent/devices/unsubscribe",
     *  tags={"App móvil"},
     *  summary="Eliminar la subcripción del token (EXPO)",
     *  security={{"bearer_token":{}}},
     * @OA\RequestBody(
     *   required=true ,
     *   description="Eliminar la subcripción del token (EXPO)",
     *   @OA\JsonContent(
     *    required={"expo_token"},
     *    @OA\Property(property="expo_token", type="string", format="string", example="1212545bhgghfg.hfbhjg"),
     *   )
     * ),
     *  @OA\Response(
     *    response=500,
     *    description="Internal Server Error",
     *    @OA\JsonContent(
     *       @OA\Property(
     *        property="status",
     *        type="int",
     *        example="failed"
     *      ),
     *      @OA\Property(
     *        property="error",
     *        type="object",
     *        @OA\Property(
     *          property="message",
     *          type="string",
     *          example="The token provided is not a valid expo push notification token."
     *        )
     *      )
     *    )
     *  ),
     * *  @OA\Response(
     *    response=200,
     *    description="Ok",
     *    @OA\JsonContent(
     *       @OA\Property(
     *        property="deleted",
     *        type="boolean",
     *        example="true"
     *      )
     *    )
     *  ),
     * )
     */
    public function FunctionName(Type $var = null)
    {
        // code...
    }
}

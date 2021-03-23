<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Main\NotificationByUser\Domain\PaginateAllNotificationFromUserIdDomain;
use Illuminate\Http\Request;

class AllNotificationUserController extends Controller
{
    /**
     * @OA\Get(
     *      path="/api/v2/notification/user",
     *      tags={"App móvil"},
     *      security={{"bearer_token":{}}},
     *      summary="Consulta de notificaciones del usuarios de los últimos 6 meses",
     *      @OA\Parameter(in="query",
     *           required=false,
     *           name="byPage",
     *           description="Número de registros por página",
     *           @OA\Schema(
     *              type="number",
     *              format="number",
     *              example="30"
     *           )
     *      ),
     *     @OA\Parameter(in="query",
     *           required=false,
     *           name="index",
     *           description="Número de página",
     *           @OA\Schema(
     *              type="number",
     *              format="number",
     *              example="1"
     *           )
     *      ),
     *      @OA\Response(
     *        response=200,
     *        description="Ok",
     *        @OA\JsonContent(
     *          @OA\Property(
     *            property="complete",
     *            type="boolean",
     *            example="false"
     *          ),
     *          @OA\Property(
     *            property="total",
     *            type="number",
     *            example="30"
     *          ),
     *          @OA\Property(
     *            property="index",
     *            type="number",
     *            example="1"
     *          ),
     *          @OA\Property(
     *            property="rows",
     *            type="array",
     *            collectionFormat="multi",
     *            @OA\Items(
     *              type="object",
     *              @OA\Property(property="id", type="number", example="1"),
     *              @OA\Property(property="title", example="Titulo de una notificación de prueba"),
     *              @OA\Property(property="body", example="Cuerpo del notificación de prueba"),
     *              @OA\Property(property="created_at", example="2021-02-02 12:56:87")
     *            )
     *          )
     *        )
     *      ),
     *      @OA\Response(
     *        response=401,
     *        description="Unauthorized",
     *        @OA\JsonContent(
     *          @OA\Property(
     *            property="message",
     *            type="string",
     *            example="The given data was invalid."
     *          )
     *        )
     *      )
     * )
     */
    public function index(Request $request)
    {
        $di = (new \DateTime())->modify('-6 months')->format('Y-m-d');
        $df = (new \DateTime())->format('Y-m-d');
        $byPage = $request->input('byPage') ?? 15;
        $index = $request->input('byPage') ?? 1;
        $user = $request->user();

        $markup = (new PaginateAllNotificationFromUserIdDomain())($user->id, $byPage, $index, $di, $df);

        return response()->json($markup);
    }
}

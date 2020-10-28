<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Main\Services\Domain\ServiceDomain;
use App\Main\Services\UseCases\ServiceUseCases;

class ServicesController extends Controller
{
    /**
     * @OA\Get(
     *  path="/api/services",
     *  summary="Todos los servicios",
     *  @OA\Response(
     *    response=200,
     *    description="List",
     *    @OA\JsonContent(
     *      @OA\Property(
     *        property="data",
     *        type="array",
     *        collectionFormat="multi",
     *        @OA\Items(
     *            type="object",
     *            @OA\Property(property="id", type="number", example="1"),
     *            @OA\Property(property="name", type="string", example="Procedimiento ...")
     *          )
     *      )
     *     )
     *  )
     * )
     */
    public function all()
    {
        $sd = new ServiceDomain();
        $scu = new ServiceUseCases($sd);

        return response()->json($scu(['id', 'name']));
    }
}

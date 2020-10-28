<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Main\Package_benefits\Domain\BenefitDomain;
use App\Main\Package_benefits\UseCases\ByPackageUseCase;
use App\Main\Package_benefits\UseCases\ListBenefitsUseCase;

class BenefitsController extends Controller
{
    /**
     * @OA\Get(
     *  path="/api/benefits",
     *  summary="Todos los beneficios",
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
     *            @OA\Property(property="package_id", type="number", example="2"),
     *            @OA\Property(property="name", type="string", example="Seguimiento..."),
     *          )
     *      )
     *     )
     *  )
     * )
     */
    public function all()
    {
        $bd = new BenefitDomain();
        $bc = new ListBenefitsUseCase($bd);

        return response()->json($bc(['id', 'package_id', 'name']));
    }

    /**
     * @OA\Get(
     *  path="/api/benefits/{idPackage}",
     *  summary="Beneficios por paquete",
     *   @OA\Parameter(
     *    description="Id del paquete",
     *    in="path",
     *    name="idPackage",
     *    required=true,
     *    example="1"),
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
     *            @OA\Property(property="package_id", type="number", example="2"),
     *            @OA\Property(property="name", type="string", example="Seguimiento..."),
     *          )
     *      )
     *     )
     *  )
     * )
     */
    public function byPackage(int $idPackage)
    {
        $bd = new BenefitDomain();
        $bp = new ByPackageUseCase($bd);

        return response()->json($bp($idPackage, ['id', 'package_id', 'name']));
    }
}

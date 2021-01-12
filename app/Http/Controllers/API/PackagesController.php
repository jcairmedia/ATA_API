<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Main\Packages\Domain\PackageDomain;
use App\Main\Packages\UseCases\ListPackagesUseCases;

class PackagesController extends Controller
{
    public function __construct()
    {
        $this->LAYOUT_CONTRACT_PACKAGES = 'layout_contract_package';
    }

    /**
     * @OA\Get(
     *  path="/api/packages",
     *  summary="Todos los paquetes",
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
     *            @OA\Property(property="name", type="string", example="Básico"),
     *            @OA\Property(property="description", type="string", example="Seguimiento..."),
     *            @OA\Property(property="periodicity", type="string", example="MONTHLY|YEARLY" ),
     *            @OA\Property(property="amount", type="number", example="2800.00"),
     *          )
     *      )
     *     )
     *  )
     * )
     */
    public function all()
    {
        $pd = new PackageDomain();
        $lp = new ListPackagesUseCases($pd);

        return response()->json($lp->list(['id', 'name', 'periodicity', 'amount']));
    }
}

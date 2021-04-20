<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Main\ExternalCategory\Domain\SelectCategoriesDomain;
use Illuminate\Http\Request;

class ExternalCategoryController extends Controller
{
    /**
     * @OA\Get(
     *  path="/api/categories",
     *  summary="Consulta de todas las categorias para el blog",
     *  security={{"bearer_token":{}}},
     *  @OA\Response(
     *    response=200,
     *    description="Ok",
     *    @OA\JsonContent(
     *      @OA\Property(
     *        property="data",
     *        type="array",
     *        collectionFormat="multi",
     *        @OA\Items(
     *            type="object",
     *            @OA\Property(property="id", type="number", example="1"),
     *            @OA\Property(property="name", type="string", example="Mis derechos como paciente en un"),
     *            @OA\Property(property="description", type="string", example="Mis derechos como paciente en un")
     *        )
     *      )
     *    )
     *  )
     * )
     */
    public function index(Request $request)
    {
        $ObjsCategories = (new SelectCategoriesDomain())();

        return response()->json(['data' => $ObjsCategories->toArray()], 200);
    }
}

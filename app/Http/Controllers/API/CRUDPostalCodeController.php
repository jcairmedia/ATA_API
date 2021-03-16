<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Main\CP\Domain\SelectAsentasDomain;
use App\Main\CP\Domain\SelectCPDomain;
use Illuminate\Http\Request;

class CRUDPostalCodeController extends Controller
{
    /**
     * @OA\Get(
     *  path="/api/cp",
     *  summary="Consulta de codigos postales",
     *  @OA\Parameter(in="query",
     *       required=false,
     *       description="page",
     *       name="page",
     *       @OA\Schema(
     *          type="string",
     *          format="string"
     *       ),
     *       example="1"),

     *  @OA\Parameter(
     *   in="query",
     *   required=true ,
     *   description="Código postal",
     *   name="cp",
     *   @OA\Schema(
     *     type="string",
     *     format="string"
     *   ), example="72"
     *  ),
     *
     *  @OA\Response(
     *    response=422,
     *    description="Unprocessable Entity",
     *    @OA\JsonContent(
     *      required={"message", "errors"},
     *      @OA\Property(property="message", type="string", example="The given data was invalid."),
     *      @OA\Property(property="errors", type="array", collectionFormat="multi",
     *        @OA\Items(
     *            type="object",
     *            @OA\Property(property="date", type="string", example="El campo cp es obligatorio")
     *        )
     *      )
     *    )
     *  ),
     *  @OA\Response(
     *    response=200,
     *    description="Ok",
     *    @OA\JsonContent(
     *      @OA\Property(
     *        property="current_page",
     *        type="number",
     *        example="1",
     *      ),
     *      @OA\Property(
     *        property="data",
     *        type="array",
     *        collectionFormat="multi",
     *        @OA\Items(
     *            type="object",
     *            @OA\Property(property="cp", type="string", example="72500"),
     *        )
     *      ),
     *      @OA\Property(
     *        property="first_page_url",
     *        type="string",
     *        example="http://localhost:8000/api/cp?page=1",
     *      ),
     *      @OA\Property(
     *        property="from",
     *        type="number",
     *        example="1",
     *      ),
     *      @OA\Property(
     *        property="last_page",
     *        type="number",
     *        example="1",
     *      ),
     *      @OA\Property(
     *        property="last_page_url",
     *        type="string",
     *        example="http://localhost:8000/api/cp?page=1",
     *      ),
     *      @OA\Property(
     *        property="next_page_url",
     *        type="string",
     *        example="http://localhost:8000/api/cp?page=2",
     *      ),
     *      @OA\Property(
     *        property="path",
     *        type="string",
     *        example="http://localhost:8000/api/cp/",
     *      ),
     *      @OA\Property(
     *        property="per_page",
     *        type="number",
     *        example="15",
     *      ),
     *      @OA\Property(
     *        property="prev_page_url",
     *        type="string",
     *        example="null",
     *      ),
     *      @OA\Property(
     *        property="to",
     *        type="number",
     *        example="15",
     *      ),
     *      @OA\Property(
     *        property="total",
     *        type="number",
     *        example="439",
     *      ),
     *    )
     *  )
     * )
     */
    public function index(Request $request)
    {
        $cp = $request->get('cp') ?? '';

        return (new SelectCPDomain())($cp);
    }

    /**
     * @OA\Get(
     *  path="/api/cp/asentas",
     *  summary="Consulta de asentas de acuerdo a un código postal",
     *  @OA\Parameter(in="query",
     *       required=false,
     *       description="page",
     *       name="page",
     *       @OA\Schema(
     *          type="string",
     *          format="string"
     *       ),
     *       example="1"),

     *  @OA\Parameter(
     *   in="query",
     *   required=true ,
     *   description="Código postal completo",
     *   name="cp",
     *   @OA\Schema(
     *     type="string",
     *     format="string"
     *   ), example="72500"
     *  ),
     *
     *  @OA\Response(
     *    response=422,
     *    description="Unprocessable Entity",
     *    @OA\JsonContent(
     *      required={"message", "errors"},
     *      @OA\Property(property="message", type="string", example="The given data was invalid."),
     *      @OA\Property(property="errors", type="array", collectionFormat="multi",
     *        @OA\Items(
     *            type="object",
     *            @OA\Property(property="date", type="string", example="El campo cp es obligatorio")
     *        )
     *      )
     *    )
     *  ),
     *  @OA\Response(
     *    response=200,
     *    description="Ok",
     *    @OA\JsonContent(
     *      @OA\Property(
     *        property="current_page",
     *        type="number",
     *        example="1",
     *      ),
     *      @OA\Property(
     *        property="data",
     *        type="array",
     *        collectionFormat="multi",
     *        @OA\Items(
     *            type="object",
     *            @OA\Property(property="cp", type="string", example="75000"),
     *            @OA\Property(property="id", type="number", example="1000216"),
     *            @OA\Property(property="d_asenta", type="string", example="Ignacio Zaragoza"),
     *        )
     *      ),
     *      @OA\Property(
     *        property="first_page_url",
     *        type="string",
     *        example="http://localhost:8000/api/cp/asentas?page=1",
     *      ),
     *      @OA\Property(
     *        property="from",
     *        type="number",
     *        example="1",
     *      ),
     *      @OA\Property(
     *        property="last_page",
     *        type="number",
     *        example="1",
     *      ),
     *      @OA\Property(
     *        property="last_page_url",
     *        type="number",
     *        example="http://localhost:8000/api/cp/asentas?page=1",
     *      ),
     *      @OA\Property(
     *        property="next_page_url",
     *        type="string",
     *        example="null",
     *      ),
     *      @OA\Property(
     *        property="path",
     *        type="string",
     *        example="http:\/\/localhost:8000\/api\/cp\/asentas",
     *      ),
     *      @OA\Property(
     *        property="per_page",
     *        type="number",
     *        example="15",
     *      ),
     *      @OA\Property(
     *        property="prev_page_url",
     *        type="string",
     *        example="null",
     *      ),
     *      @OA\Property(
     *        property="to",
     *        type="number",
     *        example="4",
     *      ),
     *      @OA\Property(
     *        property="total",
     *        type="number",
     *        example="4",
     *      ),
     *    )
     *  )
     * )
     */
    public function asentas(Request $request)
    {
        $validatedData = $request->validate([
            'cp' => 'required|exists:postalcodes,d_codigo',
        ]);
        if (!$validatedData) {
            return response()->json(['message' => 'Ingresar un código postal válido'], 400);
        }
        $cp = $request->get('cp');

        return (new SelectAsentasDomain())($cp);
    }
}

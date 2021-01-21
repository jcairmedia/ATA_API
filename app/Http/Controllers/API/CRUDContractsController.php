<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Main\Contracts\Domain\PaginateContractsDomain;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\File;

class CRUDContractsController extends Controller
{
    public function paginate(Request $request)
    {
        $index = (int) $request->input('index') ?? 0;
        $filter = $request->input('filter') ?? '';
        $byPage = $request->input('byPage') ?? 100;
        $clientId = $request->input('clientId') ?? 0;
        $array = [];
        if ($clientId > 0) {
            $array['clientId'] = $clientId;
        }
        $r = (new PaginateContractsDomain())($filter, $index, $byPage, $array);

        return response()->json($r);
    }

    /**
     * @OA\POST(
     *  path="/api/v2/contracts/customer",
     *  tags={"App móvil"},
     *  summary="Contratos del cliente",
     *  security={{"bearer_token":{}}},
     *  @OA\RequestBody(
     *   required=true ,
     *   description="Contratos del cliente en session paginados",
     *   @OA\JsonContent(
     *    required={"index", "byPage"},
     *    @OA\Property(property="index", type="string", example="1", description="Número de página"),
     *    @OA\Property(property="byPage", type="string", example="100", description="Número de contratos por página"),
     *   )
     *  ),
     *  @OA\Response(
     *    response=401,
     *    description="Unauthorized",
     *    @OA\JsonContent(
     *      @OA\Property(
     *        property="message",
     *        type="string",
     *        example="Unauthenticated"
     *      ),
     *     )
     *  ),
     *  @OA\Response(
     *    response=200,
     *    description="Ok",
     *    @OA\JsonContent(
     *      @OA\Property(
     *        property="complete",
     *        type="boolean",
     *        example="true"
     *      ),
     *     @OA\Property(
     *        property="total",
     *        type="number",
     *        example="4"
     *      ),
     *      @OA\Property(
     *        property="rows",
     *        type="array",
     *        collectionFormat="multi",
     *        description="Array de contratos por página",
     *        @OA\Items(
     *            type="object",
     *            @OA\Property(property="id", type="number", example="1", description="Id interno del contrato"),
     *            @OA\Property(property="packages_id", type="string", example="2", description="Id interno del paquete"),
     *            @OA\Property(property="url_doc", type="string", example="313202101201234056008779d99b50.pdf", description="nombre del documento"),
     *            @OA\Property(property="price", type="string", example="4000.00", description="Precio del paquete"),
     *            @OA\Property(property="id_customer_openpay", type="string", example="acgebk9tdxre09zitih2", description="Id del cliente registrado en openpay"),
     *            @OA\Property(property="created_at", type="string", example="2021-01-20 12:34:05", description="Fecha de contratación del paquete"),
     *            @OA\Property(property="updated_at", type="string", example="2021-01-20 12:34:05", description="Fecha de actualización del paquete"),
     *            @OA\Property(property="services_id", type="string", example="1", description="Id interno del servicio"),
     *            @OA\Property(property="users_id", type="string", example="1", description="Id interno del abogado encargado"),
     *            @OA\Property(property="customer_id", type="string", example="2", description="Id interno del cliente"),
     *            @OA\Property(property="closed_at", type="string", example="null", description="Fecha del cierre del caso"),
     *            @OA\Property(property="paquete", type="string", example="Básico", description="Nombre del paquete"),
     *            @OA\Property(property="servicio", type="string", example="Procedimiento y/o juicio de divorcio incausado", description="Nombre del servicio"),
     *            @OA\Property(property="cliente", type="string", example="Lourdes Perez Lopez", description="Nombre completo del cliente")
     *          )
     *      )
     *     )
     *  )
     * )
     */
    public function getContractPaginateByCustomer(Request $request)
    {
        try {
            $user = $request->user();

            $array['clientId'] = $user->id;
            $index = (int) $request->input('index') ?? 0;
            $byPage = $request->input('byPage') ?? 100;

            $r = (new PaginateContractsDomain())('', $index, $byPage, $array);

            return response()->json($r);
        } catch (\Exception $ex) {
            $code = (int) $ex->getCode();
            if (!(($code >= 400 && $code <= 422) || ($code >= 500 && $code <= 503))) {
                $code = 500;
            }

            return response()->json([
                'code' => (int) $ex->getCode(),
                'message' => $ex->getMessage(),
            ], $code);
        }
    }

    /**
     * @OA\POST(
     *  path="/api/v2/contracts/files/{id}",
     *  tags={"App móvil"},
     *  summary="Consulta de contrato",
     *  security={{"bearer_token":{}}},
     *   @OA\Parameter(
     *    description="Id del contrato",
     *    in="path",
     *    name="id",
     *    required=true,
     *    example="1"),
     *  @OA\Response(
     *    response=401,
     *    description="Unauthorized",
     *    @OA\JsonContent(
     *      @OA\Property(
     *        property="message",
     *        type="string",
     *        example="Unauthenticated"
     *      ),
     *     )
     *  ),
     *  @OA\Response(
     *    response=200,
     *    description="Ok",
     *    @OA\JsonContent(
     *      @OA\Property(
     *        property="_base64_",
     *        type="string",
     *        example="JVBERi0xLjcKMSAwIG9iago8PCAvVHlwZSAvQ2F0YWxvZwovT3V0bGluZXMgMiAwIFIKL1BhZ2VzIDMgMCBSID4+CmVuZG9iagoyIDAgb2JqCjw8IC9UeXBlIC9PdXRsaW5lcyAvQ291bnQgMCA+PgplbmRvYmoKMyAwIG9i"
     *      ),
     *     )
     *  )
     * )
     */
    public function seeContracts(int $id)
    {
        try {
            $modelCase = \App\Cases::where(['id' => $id])->first();
            if (is_null($modelCase)) {
                throw new \Exception('No existe contracto.', 404);
            }
            $filePath = storage_path('contracts'.DIRECTORY_SEPARATOR.$modelCase->url_doc);

            if (!File::exists($filePath)) {
                throw new \Exception('File not exists', 404);
            }
            $contents = File::get($filePath);
            $string = base64_encode($contents);

            return response()->json(['_base64_' => $string]);
        } catch (\Exception $ex) {
            $code = (int) $ex->getCode();
            if (!(($code >= 400 && $code <= 422) || ($code >= 500 && $code <= 503))) {
                $code = 500;
            }

            return response()->json([
                'code' => (int) $ex->getCode(),
                'message' => $ex->getMessage(),
            ], $code);
        }
    }
}

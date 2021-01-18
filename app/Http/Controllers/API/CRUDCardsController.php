<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Http\Requests\Cards\AddCardRequest;
use App\Main\OpenPayCustomer\Domain\FindCustomerOpenPayDomain;
use App\Main\OpenPayCustomer\UseCases\CreateCustomerUseCase;
use App\Main\OpenPayCustomerCards\Domain\ListCardOpenPayDomain;
use App\Main\OpenPayCustomerCards\UseCases\RegisterCardCustomerUseCase;
use Illuminate\Http\Request;

class CRUDCardsController extends Controller
{
    /**
     * @OA\POST(
     *  path="/api/card",
     *  tags={"App móvil"},
     *  summary="Registro de tarjeta (Nuevo)",
     *  description="Para más información consultar la siguiente página https://www.openpay.mx/docs/api/#crear-una-tarjeta-con-token",
     *  security={{"bearer_token":{}}},
     *  @OA\RequestBody(
     *      required=true ,
     *      description="Registrar una tarjeta",
     *      @OA\JsonContent(
     *       required={"tokenId", "deviceSessionId"},
     *       @OA\Property(property="tokenId", type="string", format="string", example="ksjjwsgdn9mhl3koynw0", description="Identificador del token generado en el navegador o dispositivo del cliente"),
     *       @OA\Property(property="deviceSessionId", type="string", format="string", example="PC3NlVo180uxQvOzugX1L9r7FiZ5uR6O", description="La propiedad device_session_id deberá ser generada desde el API JavaScript"),
     *      )
     *  ),
     *  @OA\Response(
     *    response=200,
     *    description="Created",
     *    @OA\JsonContent(
     *       @OA\Property(
     *        property="code",
     *        type="int",
     *        example="200"
     *      ),
     *    @OA\Property(
     *        property="message",
     *        type="string",
     *        example="Tarjeta creada exitosamente"
     *      )
     *    )
     *  ),
     *  @OA\Response(
     *   response=422,
     *   description="Unprocessable Entity",
     *    @OA\JsonContent(
     *      @OA\Property(
     *          property="message",
     *          type="string",
     *          example="Unprocessable Entity Token ID does not exist"
     *        ),
     *      @OA\Property(
     *            property="errors",
     *            type="object",
     *            @OA\Property(
     *                property="tokenId",
     *                type="array",
     *                collectionFormat="multi",
     *                @OA\Items(type="string", example="El campo token id es obligatorio.")
     *            )
     *       )
     *    )
     *  )
     * )
     */
    public function index(AddCardRequest $request)
    {
        $user = $request->user();
        $deviceSessionId = $request->input('deviceSessionId');
        $tokenId = $request->input('tokenId');
        try {
            $customer = (new FindCustomerOpenPayDomain())(['id' => $user->id]);
            if (is_null($customer)) {
                //1. Register Customer in open pay and bd
                $modelCard = (new CreateCustomerUseCase())(
                    (
                        $user->name.' '.$user->last_name1.' '.$user->last_name2),
                        $user->id,
                        $user->email
                    );
                \Log::error('customerObj: '.print_r($modelCard, 1));
                //2. Register card of customer in open pay and db
                (new RegisterCardCustomerUseCase())(
                    $modelCard->id_open_pay,
                    $user->id,
                    $tokenId,
                    $deviceSessionId);
            } else {
                //1.Register card of customer in open pay and db
                (new RegisterCardCustomerUseCase())(
                    $customer->id_open_pay,
                    $user->id,
                    $tokenId,
                    $deviceSessionId);
            }

            return response()->json([
                'code' => 200,
                'message' => 'Tarjeta creada exitosamente',
            ], 200);
        } catch (\Exception $ex) {
            \Log::error($ex->getMessage());
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
     * @OA\GET(
     *  tags={"App móvil"},
     *  path="/api/cards",
     *  summary="Obtener todas las tarjetas del cliente (Nuevo)",
     *  description="Consulta de todas las tarjetas del cliente.",
     *  security={{"bearer_token":{}}},
     *  @OA\Response(
     *    response=200,
     *    description="Ok",
     *    @OA\JsonContent(
     *       @OA\Property(
     *        property="code",
     *        type="int",
     *        example="200"
     *      ),
     *    @OA\Property(
     *        property="data",
     *        type="array",
     *        @OA\Items( type="object",
     *          @OA\Property(property="id", type="string", example="1"),
     *          @OA\Property(property="user_id", type="string", example="12"),
     *          @OA\Property(property="card_number", type="string", example="411111XXXXXX1111")
     *        )
     *      )
     *    )
     *  ),
     *  @OA\Response(
     *   response=401,
     *   description="Unauthorized",
     *    @OA\JsonContent(
     *      @OA\Property(
     *          property="message",
     *          type="string",
     *          example="Unauthenticated"
     *        )
     *    )
     *  )
     * )
     */
    public function cards(Request $request)
    {
        try {
            \Log::error('Id: '.$request->user()->id);
            $list = (new ListCardOpenPayDomain())(['user_id' => $request->user()->id]);
            $_list = $list->map(function ($item, $key) {
                return [
                    'id' => $item->id,
                    'user_id' => $item->user_id,
                    'card_number' => $item->card_number, ];
            });

            return response()->json([
                'code' => 200,
                'data' => $_list->toArray(),
            ], 200);
        } catch (\Exception $ex) {
            \Log::error($ex->getMessage());
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

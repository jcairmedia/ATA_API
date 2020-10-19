<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Http\Requests\User\UserRequest;
use App\Main\Users\Domain\UserCreatorDomain;
use App\Main\Users\UseCases\RegisterUseCase;

class UsersController extends Controller
{
    /**
     * @OA\Post(
     *  path="/api/users",
     *  summary="Registrar usuario",
     *  @OA\RequestBody(
     *   required=true ,
     *   description="Registrar usuario por el medio tradicional",
     *   @OA\JsonContent(
     *    required={"name","email","password","last_name1","last_name2","phone"},
     *    @OA\Property(property="name", type="string", format="string", example="Nombres"),
     *    @OA\Property(property="email", type="string", format="email", example="user1@mail.com"),
     *    @OA\Property(property="password", type="string", format="password", example="PassWord12345"),
     *    @OA\Property(property="last_name1", type="string", format="string", example="Apellido materno"),
     *    @OA\Property(property="last_name2", type="string", format="string", example="Apellido paterno"),
     *    @OA\Property(property="phone", type="string", pattern="[0-9]{10}", format="number", example="1234567890"),
     *   )
     *  ),
     *  @OA\Response(
     *    response=201,
     *    description="Created",
     *    @OA\JsonContent(
     *      @OA\Property(
     *        property="code",
     *        type="int",
     *        example="201"
     *      ),
     *      @OA\Property(
     *        property="message",
     *        type="string",
     *        example="Usuario creado"
     *      ),
     *      @OA\Property(
     *        property="data",
     *        type="array",
     *        collectionFormat="multi",
     *        @OA\Items(
     *            type="string"
     *          )
     *      )
     *    )
     *  ),
     *  @OA\Response(
     *   response=422,
     *   description="Unprocessable Entity",
     *    @OA\JsonContent(
     *      @OA\Property(
     *        property="message",
     *        type="string",
     *        example="The given data was invalid."
     *      ),
     *      @OA\Property(
     *        property="errors",
     *        type="object",
     *        @OA\Property(
     *          property="email",
     *          type="array",
     *          collectionFormat="multi",
     *          @OA\Items(
     *            type="string",
     *            example="El valor del campo email ya está en uso."
     *          )
     *       )
     *     )
     *    )
     *  )
     * )
     */
    public function register(UserRequest $req)
    {
        try {
            //code...

            $r = new RegisterUseCase(new UserCreatorDomain());
            $r($req->all());

            return response()->json([
                'code' => 201,
                'message' => 'Usuario creado',
                'data' => [],
            ], 201);
        } catch (\Exception $ex) {
            return response()->json([
                    'code' => (int) $ex->getCode(),
                    'message' => $ex->getMessage(),
            ], (int) $ex->getCode());
        }
    }
}

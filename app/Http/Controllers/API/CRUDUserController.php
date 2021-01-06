<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Http\Requests\User\RecoverPasswordRequest;
use App\Http\Requests\User\ResetPassRequest;
use App\Http\Requests\User\UpdateUserRequest;
use App\User;
use App\Utils\GeneratePassword;
use App\Utils\SendEmail;
use Illuminate\Support\Facades\DB;

class CRUDUserController extends Controller
{
    public function index(UpdateUserRequest $request)
    {
        try {
            $data = $request->all();
            $userObj = User::where(['id' => $data['id']])->first();
            if (is_null($userObj)) {
                throw new Exception('Usuario no encontrado', 404);
            }
            $userObj->name = $data['name'];
            $userObj->last_name1 = $data['last_name1'];
            $userObj->last_name2 = $data['last_name2'];
            $userObj->email = $data['email'];
            $userObj->phone = $data['phone'];
            $userObj->save();

            return response()->json([
                'code' => 200,
                'message' => 'Cuenta actualizada',
            ], 200);
        } catch (\Exception $ex) {
            \Log::error('ex: '.print_r($ex->getMessage(), 1));
            $code = (int) $ex->getCode();
            if (!(($code >= 400 && $code <= 422) || ($code >= 500 && $code <= 503))) {
                $code = 500;
            }

            return response()->json([
                'code' => (int) $code,
                'message' => $ex->getMessage(),
            ], $code);
        }
    }

    /**
     * @OA\Post(
     *  path="/api/user/pass",
     *  summary="Cambiar contraseña (nueva)",
     *  security={{"bearer_token":{}}},
     *  @OA\RequestBody(
     *   required=true ,
     *   description="Recuperar cuenta",
     *   @OA\JsonContent(
     *    required={"id", "passwordOld", "passwordNew"},
     *    @OA\Property(property="id", type="number", format="number", example="1"),
     *    @OA\Property(property="passwordOld", type="string", format="email", example="1234567"),
     *    @OA\Property(property="passwordNew", type="string", format="email", example="Nueva_123"),
     *   )
     *  ),
     *  @OA\Response(
     *      response=422,
     *      description="Unprocessable Entity",
     *      @OA\JsonContent(
     *          @OA\Property(
     *              property="message",
     *              type="string",
     *              example="The given data was invalid."
     *          ),
     *          @OA\Property(
     *            property="errors",
     *            type="object",
     *            @OA\Property(
     *                property="id",
     *                type="array",
     *                collectionFormat="multi",
     *                @OA\Items(type="string", example="El campo id seleccionado no existe."),
     *            ),
     *            @OA\Property(
     *                property="passwordOld",
     *                type="array",
     *                collectionFormat="multi",
     *                @OA\Items(type="string", example="El campo password old debe ser una cadena de caracteres."),
     *            ),
     *          @OA\Property(
     *                property="passwordNew",
     *                type="array",
     *                collectionFormat="multi",
     *                @OA\Items(type="string", example="El campo password new es obligatorio.")
     *            )
     *          )
     *      )
     *  ),
     *  @OA\Response(
     *      response=404,
     *      description="No Found",
     *      @OA\JsonContent(
     *          @OA\Property(
     *              property="code",
     *              type="number",
     *              example="404"
     *          ),
     *          @OA\Property(
     *            property="message",
     *            type="string",
     *            example="Datos incorrectos"
     *          )
     *      )
     *  ),
     *   @OA\Response(
     *    response=200,
     *    description="OK",
     *    @OA\JsonContent(
     *      @OA\Property(
     *        property="code",
     *        type="number",
     *        example="200"
     *      ),
     *      @OA\Property(
     *        property="messages",
     *        type="string",
     *        example="Contraseña actualizada, inicie sesión"
     *      )
     *    )),
     * *   @OA\Response(
     *    response=401,
     *    description="Unauthorized",
     *    @OA\JsonContent(
     *      @OA\Property(
     *          property="messages",
     *          type="string",
     *          example="Unauthenticated"
     *        )
     *    )),
     * )
     */
    public function resetPassword(ResetPassRequest $request)
    {
        try {
            $data = $request->all();
            $userObj = DB::table('users')->where(['id' => $data['id']])->first();

            \Log::error('id:'.$data['id']);

            if (is_null($userObj)) {
                throw new \Exception('Usuario no encontrado', 404);
            }
            if (!(password_verify($data['passwordOld'], $userObj->password))) {
                throw new \Exception('Datos incorrectos', 404);
            }

            DB::table('users')
            ->where(['id' => $data['id']])
            ->update(['password' => \Hash::make($data['passwordNew'])]);

            return response()->json([
                'code' => 200,
                'message' => 'Contraseña actualizada, inicie sesión',
            ], 200);
        } catch (\Exception $ex) {
            \Log::error('ex: '.print_r($ex->getMessage(), 1));
            $code = (int) $ex->getCode();
            if (!(($code >= 400 && $code <= 422) || ($code >= 500 && $code <= 503))) {
                $code = 500;
            }

            return response()->json([
                'code' => (int) $code,
                'message' => $ex->getMessage(),
            ], $code);
        }
    }

    /**
     * @OA\Post(
     *  path="/api/user/recover",
     *  summary="Recuperar cuenta (nuevo)",
     *  @OA\RequestBody(
     *   required=true ,
     *   description="Recuperar cuenta",
     *   @OA\JsonContent(
     *    required={"email"},
     *    @OA\Property(property="email", type="email", format="email", example="user1@mail.com"),
     *   )
     *  ),
     *  @OA\Response(
     *      response=422,
     *      description="Unprocessable Entity",
     *      @OA\JsonContent(
     *          @OA\Property(
     *              property="message",
     *              type="string",
     *              example="The given data was invalid."
     *          ),
     *          @OA\Property(
     *            property="errors",
     *            type="object",
     *            @OA\Property(
     *                property="error",
     *                type="array",
     *                collectionFormat="multi",
     *                @OA\Items(type="string", example="El campo email seleccionado no existe.")
     *            )
     *          )
     *      )
     *  ),
     *   @OA\Response(
     *    response=200,
     *    description="OK",
     *    @OA\JsonContent(
     *      @OA\Property(
     *        property="code",
     *        type="number",
     *        example="200"
     *      ),
     *      @OA\Property(
     *        property="messages",
     *        type="string",
     *        example="Se ha enviado por correo electrónico su nueva contraseña para iniciar sesión"
     *      )
     *    )),
     * )
     */
    public function recoverPassword(RecoverPasswordRequest $request)
    {
        try {
            $email = $request->input('email');
            $user = User::where(['email' => $email]);
            if (is_null($user)) {
                throw new Exception('Email no encontrado', 404);
            }
            $newPass = (new GeneratePassword())();

            User::where(['email' => $email])->update(['password' => \Hash::make($newPass)]);
            $view = view('layout_reset_password', ['password' => $newPass])->render();
            (new SendEmail())(
                ['email' => env('EMAIL_FROM')],
                [$email],
                'Recuperación de contraseña',
                '',
                $view);

            return response()->json([
                'code' => 200,
                'message' => 'Se ha enviado por correo electrónico su nueva contraseña para iniciar sesión',
            ], 200);
        } catch (\Exception $ex) {
            \Log::error('ex: '.print_r($ex->getMessage(), 1));
            $code = (int) $ex->getCode();
            if (!(($code >= 400 && $code <= 422) || ($code >= 500 && $code <= 503))) {
                $code = 500;
            }

            return response()->json([
                'code' => (int) $code,
                'message' => $ex->getMessage(),
            ], $code);
        }
    }
}

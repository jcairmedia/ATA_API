<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Http\Requests\User\UserRequest;
use App\Main\Users\Domain\FindUserDomain;
use App\Main\Users\Domain\UserCreatorDomain;
use App\Main\Users\UseCases\RegisterUseCase;
use App\User;
use App\Utils\SendEmail;
use Illuminate\Http\Request;
use Spatie\Permission\Models\Role;

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
            $user = $req->all();
            $dt = date('dmYHis');
            $user['confirmation_code'] = uniqid($dt);
            $r = new RegisterUseCase(new UserCreatorDomain());
            $userSaved = $r($user);

            $sendEmail = new SendEmail();
            $data = [
                        'customer_name' => $userSaved->name,
                        'confirmation_code' => $userSaved->confirmation_code,
                        'url' => env('URL_EMAIL_VERIFY'),
                    ];
            $view = view('layout_verify_email', $data)->render();
            // $sendEmail(
            //     ['email' => env('EMAIL_FROM')],
            //     [$user['email']],
            //     'ATA| Confirmación de email',
            //     '',
            //     $view);

            return response()->json([
                'code' => 201,
                'message' => 'Usuario creado',
                'data' => [],
            ], 201);
        } catch (\Exception $ex) {
            \Log::error('Error en registro de usuario'.$ex->getMessage().$ex->getCode());

            return response()->json([
                    'code' => (int) $ex->getCode(),
                    'message' => $ex->getMessage(),
            ], (int) $ex->getCode());
        }
    }

    public function registerUserDash(UserRequest $req)
    {
        try {
            $user = $req->all();

            $data = [
                'name' => $user['name'],
                'last_name1' => $user['last_name1'],
                'last_name2' => $user['last_name2'],
                'email' => $user['email'],
                'password' => $user['password'],
                'phone' => $user['phone'],
                'email_verified_at' => (new \DateTime())->format('Y-m-d H:i:s'),
            ];

            $dt = date('dmYHis');
            $r = new RegisterUseCase(new UserCreatorDomain());
            $userSaved = $r($data);

            return response()->json([
                'code' => 201,
                'message' => 'Usuario creado',
                'data' => $userSaved->toArray(),
            ], 201);
        } catch (\Exception $ex) {
            \Log::error('Error en registro de usuario'.$ex->getMessage().$ex->getCode());

            return response()->json([
                    'code' => (int) $ex->getCode(),
                    'message' => $ex->getMessage(),
            ], (int) $ex->getCode());
        }
    }

    /**
     * @OA\GET(
     *  path="/api/register/verify/{code}",
     *  summary="Verificación de email",
     *   @OA\Parameter(
     *    description="Código de verificación para activación de cuenta",
     *    in="path",
     *    name="code",
     *    required=true,
     *    example="1231213451152662"
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
     *      @OA\Property(
     *        property="message",
     *        type="string",
     *        example="Usuario verificado"
     *      ),
     *    )
     *  ),
     *  @OA\Response(
     *   response=404,
     *   description="Not Found",
     *    @OA\JsonContent(
     *       @OA\Property(
     *        property="code",
     *        type="int",
     *        example="404"
     *      ),
     *      @OA\Property(
     *        property="message",
     *        type="string",
     *        example="Not Found"
     *      )
     *    )
     *  )
     * )
     */
    public function verify(string $code)
    {
        try {
            $findUser = new FindUserDomain();
            $user = $findUser($code);
            \Log::error('code'.$code);
            \Log::error('User'.print_r($user, 1));
            if (!$user) {
                return response()->json([
                    'code' => 404,
                    'message' => 'Not Found',
                ], 404);
            }
            $user->email_verified_at = (new \DateTime())->format('Y-m-d H:i:s');
            $user->confirmation_code = '';
            $creatorUser = new UserCreatorDomain();
            $creatorUser($user);

            return response()->json([
                'code' => 200,
                'message' => 'Usuario verificado',
            ], 200);
        } catch (\Exception $ex) {
            \Log::error('Asociar rol al usuario: '.$ex->getMessage().$ex->getCode());

            return response()->json([
                    'code' => (int) $ex->getCode(),
                    'message' => $ex->getMessage(),
            ], (int) $ex->getCode());
        }
    }

    /**
     * @OA\GET(
     *  path="/api/user",
     *  summary="Obtener información del usuario usando el JWT",
     *  description="El header debe enviarse así -> Authorization: Bearer eyJ0eXAiOiJKV1QiLCJhbGciOiJSUzI1NiJ9.eyJhdWQiOiI0IiwianRpIjoiMDMxYTEyOWJkODU2ODI3NzJlYzUwODM1OTAwNDgxN2Y3MjRmYzAyODhhZWNiNTMwODc1MTQxMDUwZjU4MWUxYTgwNDUyZDc5ZmRkMDRkZjMiLCJpYXQiOjE2MDQ2MDE4ODgsIm5iZiI6MTYwNDYwMTg4OCwiZXhwIjoxNjA1ODk3ODg3LCJzdWIiOiIxMiIsInNjb3BlcyI6WyIqIl19.WqONJJREL1Ai8GSd2pNXFY2NEmNqWKWmt0wdJFIM-MIvs_lcbmHvM0ZFqeteDh5f5bkKul7u74qCIV1x_LQafgjrKAp7LIyYhyxzXQitYgDuV4Um_dJnHbXIiNS3S3_BfvC6iWRuA1fvqOgmNH4b-3Xxfnhh362Ahf9vBWazwnHAqhydN7gNFM2jETUPs87ugT4fK_xYUJOH1k9nMNr1UkjXx-fy0BZSeUc7qJ3fGURTdiRJKpjrFUklQFeWTbT2CwrR2Wi8UacKKuob8yFTbP4zF5qw0qGeoRzr734YT2-D9TeNyfsVO2tpLuI9k-rXi3S1e_Snemc1JaDgZ5ucDqw1ZutmrcN4aSLThium-bYy3mAUa-bmvcxxW9bbB_KOPawUhe4Zm1J7a5m2ycOxLOXYWLvxf_yfnX7JYW6biL32QHh74xnOOoLD4dX_N2Irrxlg3iSzBhmnUaXFxQ2Zt6nT-XArCs_Bo7sWohg3UtNkphhapWRtMT7tMekSK3ezoeYBgI38-TQ7dV9Mzki495-eBqeadSuD_zjbtzXkh2dlH0jfxD7dpwhc9Pfc6m0xlTotgioK2t_-xhulZLJMaADM2RQGz7jP_xWjVkxVtK59AlTVwy4Wsq7KIDfI2EW5PlsGgcwssl3o0mdmBpdFnDZgbyeSPMXeDOOPLO3wit4",
     *  security={{"bearer_token":{}}},
     *  @OA\Response(
     *    response=200,
     *    description="Created",
     *    @OA\JsonContent(
     *       @OA\Property(
     *        property="code",
     *        type="int",
     *        example="200"
     *      ),
     *      @OA\Property(
     *        property="data",
     *        type="array",
     *        collectionFormat="multi",
     *        @OA\Items(
     *            type="object",
     *            @OA\Property(property="id", type="number", example="1"),
     *            @OA\Property(property="name", type="string", example="usuario"),
     *            @OA\Property(property="last_name1", type="number", example="Apellido paterno"),
     *            @OA\Property(property="last_name2", type="number", example="Apellido materno"),
     *            @OA\Property(property="email", type="string", example="use1@gmail.com"),
     *            @OA\Property(property="email_verified_at", type="string", format="date-time", example="2020-10-26 19:20:00"),
     *            @OA\Property(property="confirmation_code", type="string", example=""),
     *            @OA\Property(property="url_image", type="string", example=""),
     *            @OA\Property(property="phone", type="string", example="1234567890"),
     *            @OA\Property(property="state", type="number", example="1"),
     *            @OA\Property(property="created_at", type="string", example="2020-10-26 19:20:00", format="date-time"),
     *            @OA\Property(property="updated_at", type="string", example="2020-10-26 19:20:00", format="date-time"),
     *          )
     *      )
     *    )
     *  ),
     *  @OA\Response(
     *   response=403,
     *   description="Forbidden",
     *    @OA\JsonContent(
     *      @OA\Property(
     *        property="message",
     *        type="string",
     *        example="Your email address is not verified."
     *      ),
     *      @OA\Property(
     *        property="exception",
     *        type="string",
     *        example="Symfony\\Component\\HttpKernel\\Exception\\HttpException"
     *      ),
     *      @OA\Property(
     *        property="file",
     *        type="string",
     *        example="..Application.php"
     *      ),
     *      @OA\Property(
     *        property="line",
     *        type="number",
     *        example="1067"
     *      )
     *    )
     *  )
     * )
     */
    public function getuser(Request $request)
    {
        $user = $request->user();

        $r = $user->roles->pluck('name')->toArray();

        $p = $user->getAllPermissions()->pluck('name')->toArray();

        $user = $user->toArray();
        $user['roles'] = $r;
        $user['permisos'] = $p;

        return response()->json(
            ['code' => 200,
            'data' => [$user],
            ]);
    }

    public function associate_rol(Request $request)
    {
        try {
            $id = $request->input('id_user');
            $rol = $request->input('rol_id');

            $user = User::where(['id' => $id])->first();
            if ($user == null) {
                throw new \Exception('Usuario no encontrado', 400);
            }
            $rolObj = Role::where(['id' => $rol])->first();
            if ($rolObj == null) {
                throw new Exception('Rol no existe', 500);
            }
            \Log::error('rol: '.$user->hasRole($rol));
            if ($user->hasRole($rol)) {
                throw new \Exception('El usuario ya tiene asociado el rol', 500);
            }

            $user->syncRoles([$rol]);

            return response()->json([
                'code' => 201,
                'message' => 'Rol asignado al usuario',
                'data' => [],
            ], 201);
        } catch (\Exception $ex) {
            \Log::error('Asociar rol al usuario: '.$ex->getMessage().$ex->getCode());

            return response()->json([
                    'code' => (int) $ex->getCode(),
                    'message' => $ex->getMessage(),
            ], (int) $ex->getCode());
        }
    }

    public function getUserByRol(Request $request)
    {
        try {
            $rol = $request->input('rol');

            $rol = $request->input('rol');
            $rolObj = Role::where(['name' => $rol])->first();
            if ($rolObj == null) {
                throw new \Exception('Rol no existe', 500);
            }
            $users = User::role('abogado')->select(['id', 'name', 'email'])->get();

            return response()->json([
                'code' => 200,
                'message' => '',
                'data' => $users,
            ], 200);
        } catch (\Exception $ex) {
            \Log::error('Error en registro de usuario'.$ex->getMessage().$ex->getCode());

            return response()->json([
                    'code' => (int) $ex->getCode(),
                    'message' => $ex->getMessage(),
            ], (int) $ex->getCode());
        }
    }
}

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

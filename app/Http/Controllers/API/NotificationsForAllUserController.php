<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Http\Requests\NotificationsForAllUsersRequest;
use App\Main\NotificationForAllUsers\Domain\CreateNotificationForAllUsersDomain;
use App\NotificationsForAllUser;
use App\User;

class NotificationsForAllUserController extends Controller
{
    public function index(NotificationsForAllUsersRequest $request)
    {
        try {
            $user = $request->user();

            $title = $request->input('title');
            $body = $request->input('body');
            // Guardar
            (new CreateNotificationForAllUsersDomain())(new NotificationsForAllUser(['title' => $title, 'body' => $body, 'user_session_id' => $user->id]));
            // Get all user clientes y customer
            // TODO:Enviar Notification
            $User = User::all();

            return response()->json([
                'message' => 'Notificacion enviada',
                // 'data' => $User,
            ], 200);
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

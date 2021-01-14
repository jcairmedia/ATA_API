<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Http\Requests\NotificationsByUsersRequest;
use App\NotificationByUser;

class NotificationsByUsersController extends Controller
{
    public function index(NotificationsByUsersRequest $request)
    {
        try {
            $user = $request->user();

            $title = $request->input('title');
            $body = $request->input('body');
            $userId = $request->input('userId');
            // Save notification by user in BD.
            (new NotificationByUser(['title' => $title, 'body' => $body, 'user_id' => $userId, 'user_session_id' => $user->id]))->save();

            return response()->json([
                'message' => 'Notificacion dirigida creada exitosamente',
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

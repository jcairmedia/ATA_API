<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Http\Requests\NotificationsByGroupRequest;
use App\NotificationByGroup;

class NotificationsByGroupController extends Controller
{
    public function index(NotificationsByGroupRequest $request)
    {
        try {
            $user = $request->user();
            $title = $request->input('title');
            $body = $request->input('body');
            $groupId = $request->input('groupId');
            // Save notification by user in BD.
            (new NotificationByGroup(['title' => $title, 'body' => $body, 'group_id' => $groupId, 'user_session_id' => $user->id]))->save();

            return response()->json([
                'message' => 'Notificacion de grupo creada exitosamente',
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

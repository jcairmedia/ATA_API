<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Http\Requests\NotificationsByUsersRequest;
use App\Main\NotificationByUser\Domain\PaginateNotificationByUserDomain;
use App\NotificationByUser;
use ExponentPhpSDK\Expo;
use ExponentPhpSDK\ExpoRegistrar;
use Illuminate\Http\Request;
use NotificationChannels\ExpoPushNotifications\Repositories\ExpoDatabaseDriver;

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

            $channel = 'App.User.'.$userId;
            $expo = new Expo(new ExpoRegistrar(new ExpoDatabaseDriver()));
            $notification = ['body' => $body, 'title' => $title];
            $expo->notify([$channel], $notification, false);

            return response()->json([
                'message' => 'Notificacion enviada exitosamente',
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

    public function paginate(Request $request)
    {
        $index = (int) $request->input('index') ?? 0;
        $filter = $request->input('filter') ?? '';
        $byPage = $request->input('byPage') ?? 100;
        $array = [];
        $r = (new PaginateNotificationByUserDomain())($filter, $index, $byPage, $array);

        return response()->json($r);
    }
}

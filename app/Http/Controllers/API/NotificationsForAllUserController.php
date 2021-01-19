<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Http\Requests\NotificationsForAllUsersRequest;
use App\Main\NotificationForAllUsers\Domain\CreateNotificationForAllUsersDomain;
use App\Main\NotificationForAllusers\Domain\GetTokenExpoUsersDomain;
use App\Main\NotificationForAllUsers\Domain\PaginateNotificationGeneralsDomain;
use App\NotificationsForAllUser;
use App\User;
use Illuminate\Http\Request;

class NotificationsForAllUserController extends Controller
{
    public function index(NotificationsForAllUsersRequest $request)
    {
        try {
            $user = $request->user();

            $title = $request->input('title');
            $body = $request->input('body');
            // Guardar
            // (new CreateNotificationForAllUsersDomain())(new NotificationsForAllUser(['title' => $title, 'body' => $body, 'user_session_id' => $user->id]));
            // Get all user clientes y customer
            // TODO:Enviar Notification
            $listModelPushNotification = (new GetTokenExpoUsersDomain())();
            if ($listModelPushNotification->count() <= 0) {
                throw new \Exception('No hay usuario para enviar notificaciones');
            }
            $arrayTokens = $listModelPushNotification->pluck('key')->toArray();

            $expo = new \ExponentPhpSDK\Expo(new \ExponentPhpSDK\ExpoRegistrar(new \NotificationChannels\ExpoPushNotifications\Repositories\ExpoDatabaseDriver()));
            $notification = ['body' => $body];
            $expo->notify($arrayTokens, $notification, false);

            return response()->json([
                'message' => 'Notificacion enviada',
                'data' => $list,
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
        $r = (new PaginateNotificationGeneralsDomain())($filter, $index, $byPage, $array);

        return response()->json($r);
    }
}

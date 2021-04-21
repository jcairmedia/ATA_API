<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Http\Requests\NotificationsByGroupRequest;
use App\Main\NotificationByGroup\Domain\PaginateNotificationByGrupoDomain;
use App\Main\NotificationByGroup\UseCase\GetTokenAndChannelByGroupUseCase;
use App\NotificationByGroup;
use ExponentPhpSDK\Expo;
use ExponentPhpSDK\ExpoRegistrar;
use Illuminate\Http\Request;
use NotificationChannels\ExpoPushNotifications\Repositories\ExpoDatabaseDriver;

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
            $notificationModel = new NotificationByGroup(
                ['title' => $title,
                'body' => $body,
                'group_id' => $groupId,
                'user_session_id' => $user->id, ]
                );

            // Get user´s Tokens
            $idGrupo = $groupId;
            $listModelPushNotifications = (new GetTokenAndChannelByGroupUseCase())($idGrupo);
            if (($listModelPushNotifications->count()) <= 0) {
                \Log::error('Notificación por Grupo: No se encontro usuarios asignados al grupo o no cuentas con token: '.$idGrupo);
                throw new \Exception('Notificación por Grupo: No se encontro usuarios asignados al grupo o no cuentas con token: '.$idGrupo, 1);
            }
            $arrayTokens = $listModelPushNotifications->pluck('key')->toArray();

            $notificationModel->save();

            // Send Notification
            // Para más info consultar https://github.com/Alymosul/exponent-server-sdk-php
            $expo = new Expo(new ExpoRegistrar(new ExpoDatabaseDriver()));
            $notification = ['body' => $body, 'title' => $title];
            $expo->notify($arrayTokens, $notification, false);

            return response()->json([
                'message' => 'Notificacion creada exitosamente',
            ], 200);
        } catch (\ExponentPhpSDK\Exceptions\ExpoException $ex) {
            $code = (int) $ex->getCode();
            if (!(($code >= 400 && $code <= 422) || ($code >= 500 && $code <= 503))) {
                $code = 500;
            }

            return response()->json([
                'code' => (int) $code,
                'message' => $ex->getMessage(),
            ], $code);
        } catch (\Exception $ex) {
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

    public function paginate(Request $request)
    {
        $index = (int) $request->input('index') ?? 0;
        $filter = $request->input('filter') ?? '';
        $byPage = $request->input('byPage') ?? 100;
        $array = [];
        $r = (new PaginateNotificationByGrupoDomain())($filter, $index, $byPage, $array);

        return response()->json($r);
    }
}

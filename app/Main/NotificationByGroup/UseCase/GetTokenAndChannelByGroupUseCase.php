<?php

namespace App\Main\NotificationByGroup\UseCase;

use App\Main\Groups\Domain\JoinUserByGroup;
use App\Main\NotificationByUser\Domain\WhereInPushNotificationDomain;

class GetTokenAndChannelByGroupUseCase
{
    public function __invoke(int $idGroup)
    {
        // Get all users by group
        $listUsers = (new JoinUserByGroup())($idGroup);
        if ($listUsers->count() <= 0) {
            throw new \Exception('El grupo no tiene asociados usuarios. '.$idGroup);
        }
        // Create all usuarios
        $newList = $listUsers->map(function ($item, $key) {
            return ['App.User.'.$item->id];
        });

        // Search the tokens
        $push_notifications = (new WhereInPushNotificationDomain())($newList->pluck(0)->toArray());

        return $push_notifications;
    }
}

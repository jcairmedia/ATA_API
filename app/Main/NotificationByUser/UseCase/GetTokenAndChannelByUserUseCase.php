<?php

namespace App\Main\NotificationByUser\UseCase;

use App\Main\NotificationByUser\Domain\WhereInPushNotificationDomain;

class GetTokenAndChannelByUserUseCase
{
    public function oneUser(User $user)
    {
        $id = 'App.User.'.$user->id;
        $tokens = (new WhereInPushNotificationDomain())([$id]);

        return $tokens->toArray();
    }
}

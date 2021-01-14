<?php

namespace App\Main\NotificationForAllUsers\Domain;

use App\NotificationsForAllUser;

class CreateNotificationForAllUsersDomain
{
    public function __invoke(NotificationsForAllUser $notifications)
    {
        try {
            $notifications->saveOrFail();

            return $notifications;
        } catch (\Exception $ex) {
            \Log::error($ex->getMessage().'('.$ex->getCode().')');
            throw new \Exception($ex->getMessage(), (int) $ex->getCode(), $ex);
        }
    }
}

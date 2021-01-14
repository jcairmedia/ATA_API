<?php

namespace App\Main\Group\Domain;

use App\NotificationByUser;

class CreateNotificationByGroupDomain
{
    public function __invoke(NotificationByUser $notification)
    {
        try {
            $notification->saveOrFail();

            return $notification;
        } catch (\Exception $ex) {
            throw new Exception($ex->getMessage(), $ex->getCode());
        }
    }
}

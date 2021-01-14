<?php

namespace App\Main\Group\Domain;

use App\NotificationByGroup;

class CreateNotificationByGroupDomain
{
    public function __invoke(NotificationByGroup $notification)
    {
        try {
            $notification->saveOrFail();

            return $notification;
        } catch (\Exception $ex) {
            throw new Exception($ex->getMessage(), $ex->getCode());
        }
    }
}

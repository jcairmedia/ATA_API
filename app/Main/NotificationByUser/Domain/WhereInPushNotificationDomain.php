<?php

namespace App\Main\NotificationByUser\Domain;

use Illuminate\Support\Facades\DB;

class WhereInPushNotificationDomain
{
    public function __invoke($arraykeys)
    {
        return DB::table('push_notification')->whereIn('key', $arraykeys)->get();
    }
}

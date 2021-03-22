<?php

namespace App\Main\NotificationByUser\Querys;

use Illuminate\Support\Facades\DB;

class UserNotificationAllQuery
{
    public function __invoke($di, $df)
    {
        $sql = DB::table('notifications_for_all_users');
        $sql->whereBetween(DB::raw('date(created_at)'), [$di, $df])
        ->select(
            ['id',
             'title',
             'body',
             'created_at', ]);

        return $sql;
    }
}

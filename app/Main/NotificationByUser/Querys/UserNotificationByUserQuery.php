<?php

namespace App\Main\NotificationByUser\Querys;

use Illuminate\Support\Facades\DB;

class UserNotificationByUserQuery
{
    public function __invoke($user_id, $di, $df)
    {
        $sql = DB::table('notification_by_users');
        $sql->join('users', 'users.id', '=', 'notification_by_users.user_id')
        ->where(['users.id' => $user_id])
        ->whereBetween(DB::raw('date(notification_by_users.created_at)'), [$di, $df])
        ->select(
            ['notification_by_users.id',
             'notification_by_users.title',
             'notification_by_users.body',
             'notification_by_users.created_at', ]);

        return $sql;
    }
}

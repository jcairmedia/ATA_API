<?php

namespace App\Main\NotificationByUser\Querys;

use Illuminate\Support\Facades\DB;

class UserNotificationByGroupQuery
{
    public function __invoke($user_id, $di, $df)
    {
        $groupTable = DB::table('notification_by_groups');
        $groupTable->join('group_users', 'group_users.id', '=', 'notification_by_groups.group_id')
        ->join('users', 'users.id', '=', 'group_users.user_id')
        ->where(['users.id' => $user_id])
        ->whereBetween(DB::raw('date(notification_by_groups.created_at)'), [$di, $df])
        ->select(
            ['notification_by_groups.id',
             'notification_by_groups.title',
             'notification_by_groups.body',
             'notification_by_groups.created_at', ]);

        return $groupTable;
    }
}

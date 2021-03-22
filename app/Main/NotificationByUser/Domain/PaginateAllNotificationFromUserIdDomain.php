<?php

namespace App\Main\NotificationByUser\Domain;

use App\Main\NotificationByUser\Querys\UserNotificationAllQuery;
use App\Main\NotificationByUser\Querys\UserNotificationByGroupQuery;
use App\Main\NotificationByUser\Querys\UserNotificationByUserQuery;

class PaginateAllNotificationFromUserIdDomain
{
    public function __invoke($userId, $byPage, $index, $di, $df)
    {
        try {
            $markup = [
                'complete' => true,
                'total' => 0,
                'index' => $index,
                'rows' => [],
            ];
            $query = (new UserNotificationByGroupQuery())($userId, $di, $df);
            $query2 = (new UserNotificationByUserQuery())($userId, $di, $df);
            $query3 = (new UserNotificationAllQuery())($di, $df);
            $query3 = $query->union($query2)->union($query3);

            $size = $query3->count();
            $query3->skip($index)
            ->limit($byPage);
            $markup['rows'] = $query3->get()->toArray();
            $markup['total'] = $size;
            $markup['complete'] = ($index + $byPage) > $size;

            return $markup;
        } catch (\Exception $ex) {
            throw new \Exception($ex->getMessage(), $ex->getCode());
        }
    }
}

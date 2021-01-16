<?php

namespace App\Main\NotificationForAllUsers\Domain;

use Illuminate\Support\Facades\DB;

class PaginateNotificationGeneralsDomain
{
    public function __invoke(string $filter, int $index, int $byPage, array $config = [])
    {
        $filter = trim($filter);

        $index = $index >= 0 ? $index : 0;
        $byPage = $byPage > 0 ? $byPage : 100;

        $markup = [
            'complete' => true,
            'total' => 0,
            'index' => $index,
            'rows' => [],
        ];

        $groupTable = DB::table('notifications_for_all_users');

        // $groupTable->when($filter != '', function ($query) use ($filter) {
        //     return $query->where('notification_by_users.title', 'like', $filter.'%');
        // });
        $groupTable->when($filter != '', function ($query) use ($filter) {
            return $query->where(function ($query2) use ($filter) {
                $query2->where('notifications_for_all_users.title', 'like', $filter.'%');
                $query2->orWhere('u.name', 'like', '%'.$filter.'%');
                $query2->orWhere('u.email', 'like', '%'.$filter.'%');
            });
        });
        $groupTable->join('users as u', 'u.id', '=', 'notifications_for_all_users.user_session_id');

        $contador = $groupTable->count();
        $groupTable->select([
           'notifications_for_all_users.*',
           DB::raw("CONCAT(u.name,' ', u.last_name1, ' ',(IFNULL(u.last_name2,''))) as usuario"),
        ])
        ->skip($index)
        ->limit($byPage)
        ->orderByRaw('notifications_for_all_users.created_at DESC');

        \Log::error('notifications_for_all_users query: '.$groupTable->toSql());
        \Log::error('notifications_for_all_users query: '.print_r($groupTable->getBindings(), 1));

        $respuesta = $groupTable->get();

        $markup['rows'] = $respuesta;
        $markup['total'] = $contador;
        $markup['complete'] = ($index + $byPage) > $markup['total'];

        // \Log::error('Datos de la busqueda: '.print_r($markup, 1));

        return $markup;
    }
}

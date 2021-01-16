<?php

namespace App\Main\NotificationByGroup\Domain;

use Illuminate\Support\Facades\DB;

class PaginateNotificationByGrupoDomain
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

        $groupTable = DB::table('notification_by_groups');

        // $groupTable->when($filter != '', function ($query) use ($filter) {
        //     return $query->where('notification_by_users.title', 'like', $filter.'%');
        // });
        $groupTable->when($filter != '', function ($query) use ($filter) {
            return $query->where(function ($query2) use ($filter) {
                $query2->where('notification_by_groups.title', 'like', $filter.'%');
                $query2->orWhere('u.name', 'like', '%'.$filter.'%');
                $query2->orWhere('u.email', 'like', '%'.$filter.'%');
                $query2->orWhere('groups.name', 'like', '%'.$filter.'%');
            });
        });
        $groupTable->join('groups', 'groups.id', '=', 'notification_by_groups.group_id');
        $groupTable->join('users as u', 'u.id', '=', 'notification_by_groups.user_session_id');

        $contador = $groupTable->count();
        $groupTable->select([
           'notification_by_groups.*',
           DB::raw("CONCAT(u.name,' ', u.last_name1, ' ',(IFNULL(u.last_name2,''))) as usuario"),
            'groups.name as grupo',
        ])
        ->skip($index)
        ->limit($byPage)
        ->orderByRaw('notification_by_groups.created_at DESC');

        \Log::error('notification_by_groups query: '.$groupTable->toSql());
        \Log::error('notification_by_groups query: '.print_r($groupTable->getBindings(), 1));

        $respuesta = $groupTable->get();

        $markup['rows'] = $respuesta;
        $markup['total'] = $contador;
        $markup['complete'] = ($index + $byPage) > $markup['total'];

        // \Log::error('Datos de la busqueda: '.print_r($markup, 1));

        return $markup;
    }
}

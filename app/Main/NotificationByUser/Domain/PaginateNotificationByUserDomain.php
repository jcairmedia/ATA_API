<?php

namespace App\Main\NotificationByUser\Domain;

use Illuminate\Support\Facades\DB;

class PaginateNotificationByUserDomain
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

        $groupTable = DB::table('notification_by_users');

        // $groupTable->when($filter != '', function ($query) use ($filter) {
        //     return $query->where('notification_by_users.title', 'like', $filter.'%');
        // });
        $groupTable->when($filter != '', function ($query) use ($filter) {
            return $query->where(function ($query2) use ($filter) {
                $query2->where('notification_by_users.title', 'like', $filter.'%');
                $query2->orWhere('u.name', 'like', '%'.$filter.'%');
                $query2->orWhere('u.email', 'like', '%'.$filter.'%');
                $query2->orWhere('users.email', 'like', '%'.$filter.'%');
                $query2->orWhere('users.name', 'like', '%'.$filter.'%');
            });
        });
        $groupTable->join('users', 'users.id', '=', 'notification_by_users.user_id');
        $groupTable->join('users as u', 'u.id', '=', 'notification_by_users.user_session_id');

        $contador = $groupTable->count();
        $groupTable->select([
           'notification_by_users.*',
            DB::raw("CONCAT(users.name,' ', users.last_name1, ' ',(IFNULL(users.last_name2,''))) as cliente"),
            'users.email as cliente_email',
            DB::raw("CONCAT(u.name,' ', u.last_name1, ' ',(IFNULL(u.last_name2,''))) as usuario"),
            'u.email as usuario_email',
        ])
        ->skip($index)
        ->limit($byPage)
        ->orderByRaw('notification_by_users.created_at DESC');

        \Log::error('group query: '.$groupTable->toSql());
        \Log::error('group query: '.print_r($groupTable->getBindings(), 1));

        $respuesta = $groupTable->get();

        $markup['rows'] = $respuesta;
        $markup['total'] = $contador;
        $markup['complete'] = ($index + $byPage) > $markup['total'];

        // \Log::error('Datos de la busqueda: '.print_r($markup, 1));

        return $markup;
    }
}

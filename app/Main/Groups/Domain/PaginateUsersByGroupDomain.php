<?php

namespace App\Main\Groups\Domain;

use Illuminate\Support\Facades\DB;

class PaginateUsersByGroupDomain
{
    public function __invoke(int $groupId, int $index, int $byPage, array $config = [])
    {
        $index = $index >= 0 ? $index : 0;
        $byPage = $byPage > 0 ? $byPage : 100;

        $markup = [
            'complete' => true,
            'total' => 0,
            'index' => $index,
            'rows' => [],
        ];

        $groupTable = DB::table('group_users');
        $groupTable->where('group_users.group_id', $groupId);
        $groupTable->join('users', 'users.id', '=', 'group_users.user_id');

        $contador = $groupTable->count();
        $groupTable->select([
           'group_users.id',
           'group_users.user_id',
           'group_users.group_id',
           DB::raw(
            "CONCAT(users.name,' ', users.last_name1, ' ',(IFNULL(users.last_name2,''))) as cliente"),
           'users.email',
           ])
        ->skip($index)
        ->limit($byPage)
        ->orderByRaw('group_users.created_at DESC');

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

<?php

namespace App\Main\Users\Domain;

use Illuminate\Support\Facades\DB;

class GetUserPaginateDomain
{
    public function __invoke($filter, $index, $byPage, array $config)
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

        $userTable = DB::table('users');
        $userTable->when($filter != '', function ($query) use ($filter) {
            return $query->where(function ($query2) use ($filter) {
                $query2->where('email', 'like', $filter.'%');
                $query2->orWhere('name', 'like', '%'.$filter.'%');
            });
        });
        $contador = $userTable->count();
        $userTable->select([
           DB::raw(
            "CONCAT(name,' ', last_name1, ' ',(IFNULL(last_name2,''))) as user"),
           'users.email',
           'users.id',
           ])
        ->skip($index)
        ->limit($byPage)
        ->orderByRaw('users.name DESC');

        \Log::error('group query: '.$userTable->toSql());
        \Log::error('group query: '.print_r($userTable->getBindings(), 1));

        $respuesta = $userTable->get();

        $markup['rows'] = $respuesta;
        $markup['total'] = $contador;
        $markup['complete'] = ($index + $byPage) > $markup['total'];

        // \Log::error('Datos de la busqueda: '.print_r($markup, 1));

        return $markup;
    }
}

<?php

namespace App\Main\Groups\Domain;

use Illuminate\Support\Facades\DB;

class PaginateGroupsDomain
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

        $groupTable = DB::table('groups');

        $groupTable->when($filter != '', function ($query) use ($filter) {
            return $query->where('name', 'like', $filter.'%');
        });

        $contador = $groupTable->count();
        $groupTable->select([
           'groups.*', ])
        ->skip($index)
        ->limit($byPage)
        ->orderByRaw('created_at DESC');

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

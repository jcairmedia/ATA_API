<?php

namespace App\Main\Contracts\Domain;

use Illuminate\Support\Facades\DB;

class PaginateContractsDomain
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

        $sqlTable = DB::table('cases as c');

        $sqlTable->when($filter != '', function ($query) use ($filter) {
            return $query->where(function ($query2) use ($filter) {
                $query2->where('u.name', 'like', $filter.'%');
                $query2->orWhere('s.name', 'like', '%'.$filter.'%');
                $query2->orWhere('p.name', 'like', '%'.$filter.'%');
            });
        });

        $clientId = isset($config['clientId']) ? $config['clientId'] : null;

        $sqlTable->when(!is_null($clientId), function ($query) use ($clientId) {
            \Log::error('clientId: '.$clientId);

            return $query->whereRaw('c.customer_id = ?', [$clientId]);
        });

        $sqlTable->join('users as u', 'u.id', '=', 'c.customer_id');
        $sqlTable->join('services as s', 's.id', '=', 'c.services_id');
        $sqlTable->join('packages as p', 'p.id', '=', 'c.packages_id');

        $contador = $sqlTable->count();
        $sqlTable->select([
           'c.*',
           'p.name as paquete',
           's.name as servicio',
           DB::raw(
            "CONCAT(u.name,' ', u.last_name1, ' ',u.last_name2) as cliente"),
            ])
        ->skip($index)
        ->limit($byPage)
        ->orderByRaw('c.created_at DESC');

        \Log::error('contract query: '.$sqlTable->toSql());
        \Log::error('contract query: '.print_r($sqlTable->getBindings(), 1));

        $respuesta = $sqlTable->get();

        $markup['rows'] = $respuesta;
        $markup['total'] = $contador;
        $markup['complete'] = ($index + $byPage) > $markup['total'];

        // \Log::error('Datos de la busqueda: '.print_r($markup, 1));

        return $markup;
    }
}

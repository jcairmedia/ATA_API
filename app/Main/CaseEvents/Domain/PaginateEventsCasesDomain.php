<?php

namespace App\Main\CaseEvents\Domain;

use Illuminate\Support\Facades\DB;

class PaginateEventsCasesDomain
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

        $groupTable = DB::table('case_events as e');
        $caseId = isset($config['caseId']) ? $config['caseId'] : 0;
        \Log::error('caseId:'.$caseId);

        $groupTable->when($filter != '', function ($query) use ($filter) {
            return $query->where(function ($query2) use ($filter) {
                $query2->orWhere('e.subject', 'like', '%'.$filter.'%');
                $query2->orWhere('e.description', 'like', '%'.$filter.'%');
                $query2->orWhere('p.name', 'like', '%'.$filter.'%');
                $query2->orWhere('s.name', 'like', '%'.$filter.'%');
                $query2->orWhere('u.email', 'like', '%'.$filter.'%');
                $query2->orWhere('u.name', 'like', '%'.$filter.'%');
            });
        });
        $groupTable->leftJoin('cases as c', 'c.id', '=', 'e.case_id');
        $groupTable->leftJoin('packages as p', 'p.id', '=', 'c.packages_id');
        $groupTable->leftJoin('services as s', 'c.services_id', '=', 's.id');
        $groupTable->join('users as u', 'u.id', '=', 'c.customer_id');
        $groupTable->leftJoin('users as l', 'l.id', '=', 'c.users_id');

        $groupTable->when($caseId != 0, function ($query) use ($caseId) {
            return $query->where(['c.id' => $caseId]);
        });

        $contador = $groupTable->count();
        $groupTable->select([
           'p.name as paquete',
           'u.name as cliente',
            DB::raw("CONCAT(u.name,' ', u.last_name1, ' ',(IFNULL(u.last_name2,''))) as cliente"),
            'u.email as cliente_email',
            DB::raw("CONCAT(l.name,' ', l.last_name1, ' ',(IFNULL(l.last_name2,''))) as abogado"),
            'l.email as abogado_email',
            'e.*',
        ])
        ->skip($index)
        ->limit($byPage)
        ->orderByRaw('e.created_at DESC');

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

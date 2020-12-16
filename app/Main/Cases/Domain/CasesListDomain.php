<?php

namespace App\Main\Cases\Domain;

use Illuminate\Support\Facades\DB;

class CasesListDomain
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
        $casesTable = DB::table('cases');
        $respuesta = null;
        if ($filter != '') {
            $casesTable = $casesTable->where('name', 'like', '%'.$filter.'%');
        }
        if (count($config) > 0) {
            $casesTable = $casesTable->where($config);
        }
        $casesTable->join('users', 'cases.customer_id', '=', 'users.id');
        $casesTable->join('services', 'cases.services_id', '=', 'services.id');
        $casesTable->join('packages', 'packages.id', '=', 'cases.packages_id');
        //services_id
        $contador = $casesTable->count();
        $respuesta = $casesTable->select(
            [
                'packages.name as package',
                'cases.*',
                DB::raw(
                    "CONCAT(users.name,' ', users.last_name1) as customer"),
                // 'users.name as customer',
                'users.email',
                'services.name as service',
            ])->skip($index)->limit($byPage)->orderByRaw('created_at DESC')->get();

        $markup['rows'] = $respuesta;
        $markup['total'] = $contador;
        $markup['complete'] = ($index + $byPage) > $markup['total'];

        // \Log::error('Datos de la busqueda: '.print_r($markup, 1));

        return $markup;
    }
}

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
            $casesTable->whereRaw("CONCAT(users.name,' ', users.last_name1) like '%{$filter}%'");
            $casesTable->orWhere('users.email', 'like', '%'.$filter.'%');
            $casesTable->orWhere('services.name', 'like', '%'.$filter.'%');
            $casesTable->orWhere('packages.name', 'like', '%'.$filter.'%');
        }
        if (count($config) > 0) {
            $casesTable = $casesTable->where($config);
        }
        $casesTable->join('users', 'cases.customer_id', '=', 'users.id');
        $casesTable->join('services', 'cases.services_id', '=', 'services.id');
        $casesTable->join('packages', 'packages.id', '=', 'cases.packages_id');
        $casesTable->leftJoin('users as abogados', 'abogados.id', '=', 'cases.users_id');
        //services_id
        $contador = $casesTable->count();
        $casesTable->select(
            [
                'packages.name as package',
                DB::raw(
                    "CONCAT(abogados.name,' ', abogados.last_name1) as abogado"),
                'cases.*',
                DB::raw(
                    "CONCAT(users.name,' ', users.last_name1) as customer"),
                // 'users.name as customer',
                'users.email',
                'services.name as service',
            ])->skip($index)->limit($byPage)->orderByRaw('created_at DESC');

        \Log::error('query: '.$casesTable->toSql());
        \Log::error('query: '.print_r($casesTable->getBindings(), 1));
        $respuesta = $casesTable->get();

        $markup['rows'] = $respuesta;
        $markup['total'] = $contador;
        $markup['complete'] = ($index + $byPage) > $markup['total'];

        // \Log::error('Datos de la busqueda: '.print_r($markup, 1));

        return $markup;
    }
}

<?php

namespace App\Main\Cases\Domain;

use App\Cases;
use Illuminate\Support\Facades\DB;

class CaseInnerJoinCustomerDomain
{
    public function __invoke($array)
    {
        try {
            \Log::error('CasesInnerJoinCustomer: '.print_r($array, 1));

            return Cases::where($array)
            ->join('users', 'cases.customer_id', '=', 'users.id')
            ->join('packages', 'packages.id', '=', 'cases.packages_id')
            ->join('services', 'services.id', '=', 'cases.services_id')
            ->leftJoin('contract_templates', 'contract_templates.id', '=', 'services.contract_id')
            ->select([
                'cases.*',
                DB::raw("CONCAT(users.name,' ', users.last_name1) as customer_name"),
                'users.id as customerId_',
                'users.email as customer_email',
                'users.phone as customer_phone',
                'services.name as service_name',
                'packages.amount',
                'packages.name as package_name',
                'contract_templates.name as contract_name',
                ])
            ->orderByRaw('cases.created_at DESC')
            ->first();
        } catch (\Exception $ex) {
            \Log::error($ex->getMessage().'('.$ex->getCode().')');
            throw new \Exception($ex->getMessage(), (int) $ex->getCode(), $ex);
        }
    }
}

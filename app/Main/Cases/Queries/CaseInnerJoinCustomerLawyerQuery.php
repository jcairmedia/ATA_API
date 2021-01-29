<?php

namespace App\Main\Cases\Queries;

use App\Cases;
use Illuminate\Support\Facades\DB;

class CaseInnerJoinCustomerLawyerQuery
{
    public function __invoke($array)
    {
        try {
            return Cases::where($array)
            ->join('users', 'cases.customer_id', '=', 'users.id')
            ->join('packages', 'packages.id', '=', 'cases.packages_id')
            ->join('services', 'services.id', '=', 'cases.services_id')
            ->leftJoin('contract_templates', 'contract_templates.id', '=', 'services.contract_id')
            ->leftJoin('users as l', 'l.id', '=', 'cases.users_id')
            ->select([
                'cases.*',
                DB::raw("CONCAT(users.name,' ', users.last_name1) as customer_name"),
                DB::raw("CONCAT(l.name,' ', l.last_name1) as lawyer_name"),
                'l.email as lawyer_email',
                'l.id as lawyerId',
                'users.id as customerId_',
                'users.email as customer_email',
                'users.phone as customer_phone',
                'services.name as service_name',
                'packages.amount',
                'packages.name as package_name',
                'contract_templates.name as contract_name',
                ])
            ->orderByRaw('cases.created_at DESC');
        } catch (\Exception $ex) {
            \Log::error($ex->getMessage().'('.$ex->getCode().')');
            throw new \Exception($ex->getMessage(), (int) $ex->getCode(), $ex);
        }
    }
}

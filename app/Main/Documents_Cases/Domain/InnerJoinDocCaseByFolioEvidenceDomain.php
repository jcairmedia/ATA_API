<?php

namespace App\Main\Documents_Cases\Domain;
use Illuminate\Support\Facades\DB;
use \App\Cases;
use App\Documents_case;
class InnerJoinDocCaseByFolioEvidenceDomain
{
    public function __invoke(string $folio)
    {
        try {
            $doc = Cases::
            join('documents_cases as dc', 'dc.case_id', '=', 'cases.id')
            ->join('packages as p', 'p.id', '=', 'cases.packages_id')
            ->join('services as s', 's.id', '=', 'cases.services_id')
            ->join('users as u', 'u.id', '=', 'cases.customer_id');


            if($folio != 0){
                $doc->where('dc.folio', '=', $folio);
            }

            $doc->select(
                'dc.*',
                'p.name as package',
                's.name as service',
                DB::raw("concat(u.name,' ', u.last_name1, ' ', u.last_name2) as customer")
            );

            return $doc->first();


        } catch (\Exception $ex) {
            \Log::error($ex->getMessage().'('.$ex->getCode().')');
            throw new \Exception($ex->getMessage(), (int)$ex->getCode(), $ex);
        }

    }


}

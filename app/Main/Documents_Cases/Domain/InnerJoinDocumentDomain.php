<?php

namespace App\Main\Documents_Cases\Domain;
use Illuminate\Support\Facades\DB;
use \App\Cases;
use App\Documents_case;
class InnerJoinDocumentDomain
{
    public function __invoke(string $status ="", int $customerId = 0,  int $index=0, int $byPage=100)
    {

        $index = $index >= 0 ? $index : 0;
        $byPage = $byPage > 0 ? $byPage : 100;

        $markup = [
            'complete' => true,
            'total' => 0,
            'index' => $index,
            'rows' => [],
        ];
        try {
            $doc = Cases::
            join('documents_cases as dc', 'dc.case_id', '=', 'cases.id')
            ->join('packages as p', 'p.id', '=', 'cases.packages_id')
            ->join('services as s', 's.id', '=', 'cases.services_id')
            ->join('users as u', 'u.id', '=', 'cases.customer_id');

            if(!empty($status)){
                $doc->where('dc.status', '=', $status);
            }
            if($customerId != 0){
                $doc->where('cases.customer_id', '=', $customerId);
            }

            $doc->select(
                'dc.*',
                'p.name as package',
                's.name as service',
                DB::raw("concat(u.name,' ', u.last_name1, ' ', u.last_name2) as customer")
            );
            $contador = $doc->count();

            $response = $doc->skip($index)
                            ->limit($byPage)
                            ->orderByRaw('dc.created_at DESC')
                            ->get();
            $markup['rows'] = $response;
            $markup['total'] = $contador;
            $markup['complete'] = ($index + $byPage) > $markup['total'];

            return $markup;


        } catch (\Exception $ex) {
            \Log::error($ex->getMessage().'('.$ex->getCode().')');
            throw new \Exception($ex->getMessage(), (int)$ex->getCode(), $ex);
        }

    }


}

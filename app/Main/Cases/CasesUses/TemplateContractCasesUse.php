<?php

namespace App\Main\Cases\CasesUses;

use App\Main\Cases\Domain\CaseInnerJoinCustomerDomain;

class TemplateContractCasesUse
{
    public function __invoke($caseObj)
    {
        // $caseObj = (new CaseInnerJoinCustomerDomain())(['cases.id' => $caseId]);
        try {
            //code...
            $data = [
                'name' => $caseObj->customer_name,
                'service' => $caseObj->service_name,
                'amountPackage' => $caseObj->price,
                'email' => $caseObj->customer_email,
            ];
            $contract_name = $caseObj->contract_name;
            $view = view($contract_name, $data)->render();

            return ['layout' => $view];
        } catch (\Exception $ex) {
            \Log::error($ex->getMessage().'('.$ex->getCode().')');

            throw new Exception($ex->getMessage(), 1);
        }
    }
}

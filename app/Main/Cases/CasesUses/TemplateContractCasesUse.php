<?php

namespace App\Main\Cases\CasesUses;

use App\Main\Cases\Domain\CaseInnerJoinCustomerDomain;

class TemplateContractCasesUse
{
    public function __invoke($caseObj)
    {
        // $caseObj = (new CaseInnerJoinCustomerDomain())(['cases.id' => $caseId]);

        $data = [
            'name' => $caseObj->customer_name,
            'service' => $caseObj->service_name,
            'amountPackage' => $caseObj->price,
            'email' => $caseObj->customer_email,
        ];
        $contract_name = $caseObj->contract_name;
        $view = view($contract_name, $data)->render();

        return ['layout' => $view];
    }
}

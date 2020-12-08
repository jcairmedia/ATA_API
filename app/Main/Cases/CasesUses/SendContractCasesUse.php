<?php

namespace App\Main\Cases\CasesUses;

use App\Main\Cases\Domain\CaseInnerJoinCustomerDomain;

class SendContractCasesUse
{
    public function __invoke($caseId)
    {
        $caseObj = (new CaseInnerJoinCustomerDomain())(['cases.id' => $caseId]);
        $customer_email = $caseObj->customer_email;
    }
}

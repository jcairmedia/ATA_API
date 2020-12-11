<?php

namespace App\Main\Cases\CasesUses;

class TemplateContractCasesUse
{
    public function __invoke($caseObj)
    {
        try {
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

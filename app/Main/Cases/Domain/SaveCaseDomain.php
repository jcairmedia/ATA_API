<?php

namespace App\Main\Cases\Domain;

use App\Cases;

class SaveCaseDomain
{
    public function __invoke(
        $data
    ) {
        try {
            \Log::error('Save CaseUseCase: '.print_r($data, 1));
            // Register Cases
            $caseObj = new Cases($data);
            $caseObj->saveOrFail();

            return $caseObj;
        } catch (\Exception $ex) {
            \Log::error($ex->getMessage().'('.$ex->getCode().')');
            throw new \Exception($ex->getMessage(), $ex->getCode(), $ex);
        }
    }
}

<?php

namespace App\Main\TestCustomer\Domain;

use App\CustomerTest;

class CreateTestDomain
{
    public function __construct()
    {
    }

    public function __invoke(CustomerTest $questionnaire)
    {
        try {
            $questionnaire->save();

            return $questionnaire;
        } catch (\Exception $ex) {
            \Log::error($ex->getMessage().'('.$ex->getCode().')');
            throw new \Exception($ex->getMessage(), $ex->getCode(), $ex);
        }
    }
}

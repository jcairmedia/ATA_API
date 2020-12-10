<?php

namespace App\Main\TestCustomer\Domain;
use App\CustomerTestQuestionarieAnswer;

class CreateCustomerTestQuestionnaireAnswersDomain
{
    public function __construct()
    {
    }

    public function __invoke(CustomerTestQuestionarieAnswer $test)
    {
        try {
            $test->save();

            return $test;
        } catch (\Exception $ex) {
            \Log::error($ex->getMessage().'('.$ex->getCode().')');
            throw new \Exception($ex->getMessage(), $ex->getCode(), $ex);
        }
    }
}

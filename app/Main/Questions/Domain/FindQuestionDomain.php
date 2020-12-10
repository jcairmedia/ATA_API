<?php

namespace App\Main\Questions\Domain;

use App\Question;

class FindQuestionDomain
{
    public function __invoke($arrayWhere)
    {
        try {
            return Question::where($arrayWhere)->first();
        } catch (\Exception $ex) {
            \Log::error($ex->getMessage().'('.$ex->getCode().')');
            throw new \Exception($ex->getMessage(), $ex->getCode(), $ex);
        }
    }
}

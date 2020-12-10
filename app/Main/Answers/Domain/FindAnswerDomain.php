<?php

namespace App\Main\Answers\Domain;

use App\Answer;

class FindAnswerDomain
{
    public function __invoke($arrayWhere)
    {
        try {
            return Answer::where($arrayWhere)->first();
        } catch (\Exception $ex) {
            \Log::error($ex->getMessage().'('.$ex->getCode().')');
            throw new \Exception($ex->getMessage(), $ex->getCode(), $ex);
        }
    }
}

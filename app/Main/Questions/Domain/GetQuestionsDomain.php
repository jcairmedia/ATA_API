<?php

namespace App\Main\Questions\Domain;

use App\Question;

class GetQuestionsDomain
{
    public function __invoke()
    {
        try {
            return Question::all();
        } catch (\Exception $ex) {
            \Log::error($ex->getMessage().'('.$ex->getCode().')');
            throw new \Exception($ex->getMessage(), $ex->getCode(), $ex);
        }
    }
}

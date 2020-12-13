<?php

namespace App\Main\Questions\Domain;

use App\Question;

class UpdateQuestionDomain
{
    public function __invoke(Question $question)
    {
        try {
            return $question->saveOrFail();
        } catch (\Exception $ex) {
            \Log::error($ex->getMessage().'('.$ex->getCode().')');
            throw new \Exception($ex->getMessage(), $ex->getCode(), $ex);
        }
    }
}

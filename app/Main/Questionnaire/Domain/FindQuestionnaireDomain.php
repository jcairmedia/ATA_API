<?php

namespace App\Main\Questionnaire\Domain;

use App\Questionnaire;

class FindQuestionnaireDomain
{
    public function __construct()
    {
    }

    public function __invoke($array)
    {
        try {
            return Questionnaire::where($array)->first();
        } catch (\Exception $ex) {
            \Log::error($ex->getMessage().'('.$ex->getCode().')');
            throw new \Exception($ex->getMessage(), $ex->getCode(), $ex);
        }
    }
}

<?php

namespace App\Main\Questionnaire\Domain;

use App\Questionnaire;

class FindStructQuestionnaireDomain
{
    public function __construct()
    {
    }

    public function __invoke($questionnaireId)
    {
        try {
            return Questionnaire::where(['questionnaires.id' => $questionnaireId])
            ->join('questions', 'questions.questionnaire_id', '=', 'questionnaires.id')
            ->join('answers', 'answers.question_id', '=', 'questions.id')
            ->select([
                'questionnaires.id as questionnaireId',
                'questionnaires.name as questionnaire',
                'questions.id as questionId',
                'questions.name as question',
                'answers.id as answerId',
                'answers.name as answer',
            ])
            ->get();
        } catch (\Exception $ex) {
            \Log::error($ex->getMessage().'('.$ex->getCode().')');
            throw new \Exception($ex->getMessage(), $ex->getCode(), $ex);
        }
    }
}

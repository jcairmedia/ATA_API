<?php

namespace App\Main\Questionnaire\Domain;

use App\CustomerTest;

class FindQuestionnaireByMeetingIdDomain
{
    public function __construct()
    {
    }

    public function __invoke($meetingId)
    {
        try {
            return CustomerTest::where(['customer_tests.meeting_id' => $meetingId, 'customer_tests.answered'=>1])
            ->join('customer_test_questionarie_answers', 'customer_test_questionarie_answers.customer_tests_id', '=', 'customer_tests.id')
            ->select(['customer_test_questionarie_answers.*'])
            ->get();
        } catch (\Exception $ex) {
            \Log::error($ex->getMessage().'('.$ex->getCode().')');
            throw new \Exception($ex->getMessage(), $ex->getCode(), $ex);
        }
    }
}

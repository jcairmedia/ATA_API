<?php

namespace App\Http\Controllers\API;

use App\CustomerTestQuestionarieAnswer;
use App\Http\Controllers\Controller;
use App\Http\Requests\CustomerQuestionnaire\CustomerQuestionnaireRequest;
use App\Http\Requests\CustomerQuestionnaire\QuestionnaireStructCustomerRequest;
use App\Main\Answers\Domain\FindAnswerDomain;
use App\Main\Config_System\Domain\SearchConfigDomain;
use App\Main\Config_System\UseCases\SearchConfigurationUseCase;
use App\Main\Meetings\Domain\MeetingWithContactDomain;
use App\Main\Questionnaire\Domain\FindStructQuestionnaireDomain;
use App\Main\Questions\Domain\FindQuestionDomain;
use App\Main\TestCustomer\Domain\CreateCustomerTestQuestionnaireAnswersDomain;
use App\Main\TestCustomer\Domain\CreateTestDomain;
use App\Main\TestCustomer\Domain\FindTestDomain;
use App\Utils\SendEmail;

class TestCustomerController extends Controller
{
    public function getQuestions(QuestionnaireStructCustomerRequest $request)
    {
        try {
            $uuidTest = $request->input('uuid');
            $testObj = (new FindTestDomain())(['uuid' => $uuidTest]);
            $_questionnaireId_ = $testObj->questionnaire_id;
            $struct = (new FindStructQuestionnaireDomain())($_questionnaireId_);
            $testArray = [];

            foreach ($struct as $key => $question) {
                if (!isset($testArray[$question->questionId])) {
                    $testArray[$question->questionId] = [
                        'questionId' => $question->questionId,
                        'questionLabel' => $question->question,
                        'answers' => [],
                    ];
                }
                $testArray[$question->questionId]['answers'][] =
                    [
                        'answerId' => $question->answerId,
                        'answerLabel' => $question->answer,
                    ];
            }

            return response()->json([
                'code' => 200,
                'message' => '',
                'data' => array_values($testArray),
            ], 200);
        } catch (\Exception $ex) {
            $code = (int) $ex->getCode();
            if (!(($code >= 400 && $code <= 422) || ($code >= 500 && $code <= 503))) {
                $code = 500;
            }

            return response()->json([
                'code' => (int) $code,
                'message' => $ex->getMessage(),
            ], $code);
        }
    }

    public function saveTest(CustomerQuestionnaireRequest $request)
    {
        try {
            $uuid = $request->input('uuid');
            $array = $request->input('data');

            $testObj = (new FindTestDomain())(['uuid' => $uuid]);
            if ($testObj->answered == 1) {
                throw new \Exception('Cuestionario ya fue respondido', 409);
            }
            $_testId_ = $testObj->id;
            $_meetingId_ = $testObj->meeting_id;

            $createTestQuestionnaireDomain = new CreateCustomerTestQuestionnaireAnswersDomain();
            foreach ($array as $key => $value) {
                $answerObj = (new FindAnswerDomain())(['id' => $value['answer']]);
                $questionObj = (new FindQuestionDomain())(['id' => $value['question']]);
                $createTestQuestionnaireDomain(
                    new CustomerTestQuestionarieAnswer(
                        [
                            'customer_tests_id' => $_testId_,
                            'question_id' => $value['question'],
                            'answer_id' => $value['answer'],
                            'question' => $questionObj->name,
                            'answer' => $answerObj->name,
                            ])
                        );
            }
            // Update state customer cuestionnaire in DB
            $testObj->answered = true;
            (new CreateTestDomain())($testObj);
            // Search contact and meeting
            $meetingObj = (new MeetingWithContactDomain())($_meetingId_);
            // Search price meeting paid in the configuration
            $objConfig = (new SearchConfigurationUseCase(new SearchConfigDomain()))('MEETING_PAID_AMOUNT');
            $priceMeeting = $objConfig->value;
            // Render view for Email
            $view = view('layout_email_after_test', [
                        'category' => $meetingObj->category,
                        'price' => $priceMeeting,
                        'link' => env('URL_ECOMMERCE'),
                    ])->render();
            // Send Email
            (new SendEmail())(
                ['email' => 'noreply@usercenter.mx'],
                [$meetingObj->email],
                '¡Gracias! Al contestar nuestro cuestionario nos ayudas a tener un mejor servicio',
                '',
                $view
            );
            // Send email
            return response()->json([
                'code' => 200,
                'message' => 'Cuestionario guardado',
            ], 200);
        } catch (\Exception $ex) {
            $code = (int) $ex->getCode();
            if (!(($code >= 400 && $code <= 422) || ($code >= 500 && $code <= 503))) {
                $code = 500;
            }

            return response()->json([
                'code' => (int) $code,
                'message' => $ex->getMessage(),
            ], $code);
        }
    }
}

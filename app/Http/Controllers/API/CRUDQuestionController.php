<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Http\Requests\Questionnaires\FindQuestionsByQuestionnaireRequest;
use App\Main\Questions\Domain\FindQuestionDomain;
use App\Main\Questions\Domain\GetQuestionsDomain;
use App\Main\Questions\Domain\UpdateQuestionDomain;
use App\Question;
use Illuminate\Http\Request;

class CRUDQuestionController extends Controller
{
    public function list()
    {
        $questions = (new GetQuestionsDomain())();

        return response()->json($questions);
    }

    public function updateQuestion(Request $request)
    {
        try {
            $questionId = $request->input('questionId');
            $description = $request->input('description');
            $questionObj = (new FindQuestionDomain())(['id' => $questionId]);
            $questionObj->name = $description;
            (new UpdateQuestionDomain())($questionObj);

            return response()->json([
                'message' => 'Pregunta actualizada',
            ]);
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

    public function getQuestionsByQuestionnaireId(FindQuestionsByQuestionnaireRequest $request)
    {
        try {
            $questionnaireId = $request->input('questionnaireId');
            $questionnaires = Question::where(['questionnaire_id' => $questionnaireId])->get()->toArray();

            return response()->json([
                'code' => 200,
                'data' => $questionnaires,
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

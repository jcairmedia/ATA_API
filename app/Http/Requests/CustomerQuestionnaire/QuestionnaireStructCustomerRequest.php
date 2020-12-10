<?php

namespace App\Http\Requests\CustomerQuestionnaire;

use Illuminate\Foundation\Http\FormRequest;

class QuestionnaireStructCustomerRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        return [
            'uuid' => 'required|exists:customer_tests,uuid',
        ];
    }
}

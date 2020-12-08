<?php

namespace App\Http\Requests\Packages;

use Illuminate\Foundation\Http\FormRequest;

class SetLawyerRequest extends FormRequest
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
            'userId' => 'required|exists:users,id', // id card token open pay
            'caseId' => 'required|exists:cases,id',
        ];
    }
}

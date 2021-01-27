<?php

namespace App\Http\Requests\EventsCases;

use Illuminate\Foundation\Http\FormRequest;

class AddEventCaseRequest extends FormRequest
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
            'subject' => 'required|string|min:3|max:255',
            'description' => 'required',
            'date' => ['date'],
            'caseId' => 'required|exists:cases,id',
        ];
    }
}

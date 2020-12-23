<?php

namespace App\Http\Requests\User;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class ResetPassRequest extends FormRequest
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
        'id' => 'required|exists:users,id',
        'passwordOld' => [
            'string',
            'min:3',
            'max:255',
            Rule::requiredIf(function () {
                $request = request();

                return is_null($request->facebook_user_id);
            }),
        ],
        'passwordNew' => [
            'string',
            'min:3',
            'max:255',
            Rule::requiredIf(function () {
                $request = request();

                return is_null($request->facebook_user_id);
            }),
        ],
        ];
    }
}

<?php

namespace App\Http\Requests\User;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UserRequest extends FormRequest
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
        'name' => 'required|string|min:3|max:255',
        'email' => 'required|email|unique:users,email',
        'password' => [
            'string',
            'min:3',
            'max:255',
            Rule::requiredIf(function () {
                $request = request();

                return is_null($request->facebook_user_id);
            }),
        ],
        'last_name1' => 'required|string|min:3|max:255',
        'last_name2' => 'required|string|min:3|max:255',
        'url_image' => 'nullable|string|max:255',
        'phone' => 'required|string|size:10|regex:/[0-9]{10}/',
        'facebook_user_id' => 'nullable|string|unique:users,facebook_user_id|regex:/[0-9]{15,}/',
        ];
    }
}

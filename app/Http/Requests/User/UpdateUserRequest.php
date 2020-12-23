<?php

namespace App\Http\Requests\User;

use Illuminate\Foundation\Http\FormRequest;

class UpdateUserRequest extends FormRequest
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
            'name' => 'required|string|min:3|max:255',
            'email' => 'required|email',
            'last_name1' => 'required|string|min:3|max:255',
            'last_name2' => 'required|string|min:3|max:255',
            'phone' => 'required|string|size:10|regex:/[0-9]{10}/',
            ];
    }
}

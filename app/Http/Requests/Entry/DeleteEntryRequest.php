<?php

namespace App\Http\Requests\Entry;

use Illuminate\Validation\Rule;
use Illuminate\Foundation\Http\FormRequest;

class DeleteEntryRequest extends FormRequest
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
            "id" => ["required", "numeric", "exists:blog_entries,id"],
        ];
    }
}

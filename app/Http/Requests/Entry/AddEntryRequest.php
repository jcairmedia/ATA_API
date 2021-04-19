<?php

namespace App\Http\Requests\Entry;

use Illuminate\Validation\Rule;
use Illuminate\Foundation\Http\FormRequest;

class AddEntryRequest extends FormRequest
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
            "title" => ["required", "string"],
            "description" => ["required", "string"],
            "body" => ["required"],
            "status" => ["required", Rule::in(["PUBLISHED", "DRAFT"])],
            "categoryId" => ["required", "numeric", "exists:external_categories,id"],
            "file" => ["required", "file"]
        ];
    }
}

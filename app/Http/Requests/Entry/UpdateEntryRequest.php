<?php

namespace App\Http\Requests\Entry;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;


class UpdateEntryRequest extends FormRequest
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
            "title" => ["string"],
            "description" => ["string"],
            "body" => ["string"],
            "status" => [Rule::in(["PUBLISHED", "DRAFT"])],
            "categoryId" => ["numeric", "exists:external_categories,id"],
        ];
    }
}

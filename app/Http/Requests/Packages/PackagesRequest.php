<?php

namespace App\Http\Requests\Packages;

use Illuminate\Foundation\Http\FormRequest;

class PackagesRequest extends FormRequest
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
            'tokenId' => 'required|string', // id card token open pay
            'deviceSessionId' => 'required|string',
            'packageId' => 'required|exists:packages,id',
            'serviceId' => 'required|exists:services,id',
        ];
    }
}

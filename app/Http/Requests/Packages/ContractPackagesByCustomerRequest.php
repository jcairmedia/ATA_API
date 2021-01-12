<?php

namespace App\Http\Requests\Packages;

use Illuminate\Foundation\Http\FormRequest;

class ContractPackagesByCustomerRequest extends FormRequest
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
            'packageId' => 'required|exists:packages,id',
            'serviceId' => 'required|exists:services,id',
            'cardId' => 'required|exists:openpay_customer_cards,id',
        ];
    }
}

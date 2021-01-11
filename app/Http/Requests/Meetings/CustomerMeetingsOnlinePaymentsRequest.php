<?php

namespace App\Http\Requests\Meetings;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class CustomerMeetingsOnlinePaymentsRequest extends FormRequest
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
            'idCard' => 'required|exists:openpay_customer_cards,id',
            'cvv2' => 'required|string|min:3|max:255',
            'date' => ['required', 'date', new \App\Rules\CustomDateMeeting()],
            'time' => ['required', 'string', 'regex:/^(09|(1[0-8]))\:[0-5][0-9]$/'],
            'type_meeting' => [
                'required',
                Rule::in(['CALL', 'VIDEOCALL', 'PRESENTIAL']), ],
            'type_payment' => [
                'required',
                Rule::in(['ONLINE']), ],

            'deviceIdHiddenFieldName' => [
                'string',
               'required',
            ],
        ];
    }
}

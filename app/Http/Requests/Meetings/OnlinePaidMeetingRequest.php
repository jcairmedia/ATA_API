<?php

namespace App\Http\Requests\Meetings;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class OnlinePaidMeetingRequest extends FormRequest
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
            'lastname_1' => 'required|string|min:3|max:255',
            'lastname_2' => 'required|string|min:3|max:255',
            'curp' => 'required|string|min:3|max:255',
            'email' => 'required|email',
            'phone' => 'required|string|size:10|regex:/[0-9]{10}/',

            'street' => 'required|string|min:2|max:255',
            'out_number' => 'required|string|min:1|max:255',
            'int_number' => 'string|min:0|max:255',
            'idcp' => 'required|numeric|exists:postalcodes,id',

            'description' => 'string|min:2|max:500',
            'idfe' => 'required|numeric|exists:federalentities,id', // federative entities

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
            'token_id' => [
                'string',
               'required',
            ],
        ];
    }
}

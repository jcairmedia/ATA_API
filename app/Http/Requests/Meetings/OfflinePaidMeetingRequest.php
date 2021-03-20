<?php

namespace App\Http\Requests\Meetings;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\Rule;

class OfflinePaidMeetingRequest extends FormRequest
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
            'name' => [Rule::requiredIf(function () {
                if (Auth::check()) {// The user is logged in...
                    return false;
                }

                return true;
            }), 'string', 'min:3', 'max:255'],
            'lastname_1' => [Rule::requiredIf(function () {
                if (Auth::check()) {// The user is logged in...
                    return false;
                }

                return true;
            }), 'string', 'min:3', 'max:255'],
            'lastname_2' => [Rule::requiredIf(function () {
                if (Auth::check()) {// The user is logged in...
                    return false;
                }

                return true;
            }), 'string', 'min:3', 'max:255'],
            'curp' => [Rule::requiredIf(function () {
                if (Auth::check()) {// The user is logged in...
                    return false;
                }

                return true;
            }), 'string', 'min:3', 'max:255'],
            'email' => [Rule::requiredIf(function () {
                if (Auth::check()) {// The user is logged in...
                    return false;
                }

                return true;
            }), 'email'],
            'phone' => [Rule::requiredIf(function () {
                if (Auth::check()) {// The user is logged in...
                    return false;
                }

                return true;
            }), 'string', 'size:10', 'regex:/[0-9]{10}/'],

            'street' => [Rule::requiredIf(function () {
                if (Auth::check()) {// The user is logged in...
                    return false;
                }

                return true;
            }), 'string', 'min:2', 'max:255'],
            'out_number' => [Rule::requiredIf(function () {
                if (Auth::check()) {// The user is logged in...
                    return false;
                }

                return true;
            }), 'string', 'min:1', 'max:255'],
            'int_number' => ['string', 'min:0', 'max:255'],

            'idcp' => [Rule::requiredIf(function () {
                if (Auth::check()) {// The user is logged in...
                    return false;
                }

                return true;
            }), 'numeric', 'exists:postalcodes,id'],

            'idfe' => ['required', 'numeric', 'exists:federalentities,id'], // federative entities

            'date' => ['required', 'date', new \App\Rules\CustomDateMeeting()],
            'time' => ['required', 'string', 'regex:/^(09|(1[0-8]))\:[0-5][0-9]$/'],
            'type_meeting' => [
                'required',
                Rule::in(['CALL', 'VIDEOCALL', 'PRESENTIAL']), ],
            'type_payment' => [
                'required',
                Rule::in(['OFFLINE']), ],
        ];
    }
}

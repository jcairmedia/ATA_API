<?php

namespace App\Http\Requests\Meetings;

use App\Main\Meetings\Domain\FindMeetingDomain;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class StateMeetingRequest extends FormRequest
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
            'id' => 'required|exists:meetings,id',
            'option' => 'required',
            'reason' => [
                Rule::requiredIf(function () {
                    $request = request();
                    $meetingObj = (new FindMeetingDomain())(['id' => $request->id]);
                    if (is_null($meetingObj)) {
                        return false;
                    }

                    return !$meetingObj->category == 'FREE';
                }),
            ],
        ];
    }
}

<?php

namespace App\Http\Requests\Meetings;

use Illuminate\Foundation\Http\FormRequest;

class ReSchedulerMeetingPaidRequest extends FormRequest
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
            'date' => ['required', 'date', new \App\Rules\CustomDateMeeting()],
            'time' => ['required', 'string', 'regex:/^(09|(1[0-8]))\:[0-5][0-9]$/'],
            'meetingId' => ['required'],
        ];
    }
}

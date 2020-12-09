<?php

namespace App\Main\Meetings\Utils;

class TextForEmailReSchedulerMeetingPaidUtils
{
    public function __construct()
    {
        $this->LAYOUT_EMAIL_ONLINE_MEETING = 'layout_email_rescheduler_meeting';
    }

    public function __invoke($typemeeting, $day, $month, $time, $objZoom, $dayRe, $monthRe)
    {
        $data = [
            'type_meeting' => $typemeeting,
            'dayRe' => $dayRe,
            'monthRe' => $monthRe,
            'zoomObj' => $objZoom,
            'hours' => $time,
            'month' => $month,
            'day' => $day, ];

        return view($this->LAYOUT_EMAIL_ONLINE_MEETING, $data)->render();
    }
}

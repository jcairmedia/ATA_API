<?php

namespace App\Main\Meetings\Utils;

class TextForEmailMeetingPaidUtils
{

    public function __construct()
    {
        $this->LAYOUT_EMAIL_ONLINE_MEETING = "layout_email_online_meeting";
    }

    public function __invoke($type_meeting, $day, $month, $time, $objZoom)
    {
        $data = [
            'type_meeting'=> $type_meeting,
            'zoomObj' => $objZoom,
            'hours' => $time,
            'month'=> $month,
            'day' => $day ];

        return view( $this->LAYOUT_EMAIL_ONLINE_MEETING, $data );
    }
}

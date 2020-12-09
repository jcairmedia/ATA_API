<?php

namespace App\Main\Meetings\Utils;

class TextForSMSReSchedulerMeetingPaidUtils
{
    public function __invoke($day, $month, $time, $dayRe, $monthRe)
    {
        return '¡Hola! Tu asesoría legal programada para el día '
                . $day. ' de '. $month.
                ' ha sido reprogramada para el día '.
                $dayRe. " de ". $monthRe . '. Si tienes'.
                ' dudas, comunícate al 55-2625-0649';
    }
}

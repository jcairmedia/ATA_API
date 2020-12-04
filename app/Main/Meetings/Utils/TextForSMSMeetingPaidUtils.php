<?php

namespace App\Main\Meetings\Utils;

class TextForSMSMeetingPaidUtils
{
    public function __invoke($type_meeting, $day, $month, $time)
    {
        $textMsg = '';
        switch ($type_meeting) {
            case 'CALL':
                $textMsg .= 'Recuerda estar atento al teléfono que nos proporcionaste para tomar tu llamada.';
            break;
            case 'VIDEOCALL':
                $textMsg .= 'Ingresa al correo electrónico que nos proporcionaste y obtén el enlace de tu videollamada';
                break;
            case 'PRESENTIAL':
                $textMsg .= 'Ingresa al correo electrónico que nos proporcionaste y te daremos detalles de tu cita.';
            break;
        }

        return $textMsg;
    }
}

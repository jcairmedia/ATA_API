<?php

namespace App\Main\Meetings\Utils;

class TextForSMSMeetingPaidUtils
{
    public function __invoke($type_meeting, $day, $month, $time)
    {
        return 'Hemos recibido y confirmado tu pago para tu'.
        ' asesoría legal en línea.'.
        ' Entra al correo electrónico'.
        ' que nos proporcionaste para más detalles. ';
    }
}

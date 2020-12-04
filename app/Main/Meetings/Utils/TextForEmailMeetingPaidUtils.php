<?php

namespace App\Main\Meetings\Utils;

class TextForEmailMeetingPaidUtils
{
    public function __invoke($type_meeting, $day, $month, $time, $objZoom)
    {
        $textMsg = '';
        switch ($type_meeting) {
            case 'CALL':
                $textMsg = 'El día '.$day.' de '.$month.' a las '.$time.', tiene programada su primer  guía legal con ATA.';
            break;
            case 'VIDEOCALL':
                $messageZoom = $objZoom['code'] == 200 ?
                ' Recuerda seguir el enlace indicado debajo y presentarte en tiempo y forma'.
                '</br>'.
                '<a href="'.$objZoom['data']['join_url'].'">'.'Enlace'.'</a>' : $objZoom['message'];

                $textMsg = 'El día '.$day.' de '.$month.' a las '.$time.', tiene programada su primer  guía legal con ATA.\n'.
                        $messageZoom.'</br>'.
                        '1. En caso de no poder'.
                        '2. La tolerancia'.
                        '3. En caso de alguna llamada ';
                break;
            case 'PRESENTIAL':
                $textMsg = '
                        El día '.$day.' de '.$month.' a las '.$time.', tiene programada su primer  guía legal con ATA.
                        Recuerda llegar a la dirección indicada debajo y presentarte en tiempo y forma.
                        Av. Cuauhtemoc 145, Roma Norte,
                        06700,CDMX.

                        1. En caso de no poder recibir asesoria
                        2. La tolerancia de espera por parte de nuestros abogados, será de 15 minutos
                        ';
            break;
        }

        return $textMsg;
    }
}

<?php

namespace App\Main\Meetings\Queries;

use App\Meeting;
use Illuminate\Support\Facades\DB;

class MeetingByContactsQuery
{
    public function __invoke()
    {
        return Meeting::query()
        ->join('contacts as c', 'meetings.contacts_id', '=', 'c.id')
        ->join('postalcodes as p', 'p.id', '=', 'c.idcp')
        ->where('meetings.paid_state', '=', '1')
        ->select([
            'meetings.id AS MEETING_ID',
            'c.name as NOMBRES',
            'c.lastname_1 as APELLIDO_PATERNO',
            'c.lastname_2 as APELLIDO_MATERNO',
            'c.curp as CURP',
            'meetings.idfe as ENTIDAD_FEDERATIVA',
            DB::raw("CONCAT(
                c.street,' ',
                c.out_number, ', ',
                IFNULL(c.int_number, ''), IF(c.int_number is not null, ', ', ''),
                p.d_asenta, ', ',
                p.D_mnpio, ', ',
                p.d_estado, ', ',
                p.d_ciudad
                ) as DOMICILIO"),
            'c.email as CORREO',
            'c.phone as TELEFONO_FIJO',
            'c.phone as TELEFONO_MOVIL',
                ]);
    }
}

<?php

namespace App\Main\Meetings\Queries;

use App\Meeting;
use Illuminate\Support\Facades\DB;

class MeetingsByUsersQuery
{
    public function __invoke()
    {
        return Meeting::query()
            ->join('users as u', 'meetings.customer_id', '=', 'u.id')
            ->join('user_addresses as ua', 'ua.users_id', '=', 'u.id')
            ->join('postalcodes as p', 'p.id', '=', 'ua.idcp')
            ->where('meetings.paid_state', '=', '1')
            ->select([
                'meetings.id AS MEETING_ID',
                'u.name as NOMBRES',
                'u.last_name1 AS APELLIDO_PATERNO',
                'u.last_name2 AS APELLIDO_MATERNO',
                'u.curp AS CURP',
                'meetings.idfe as ENTIDAD_FEDERAL',
                DB::raw("CONCAT(
                    ua.street,' ',
                    ua.out_number, ', ',
                    IFNULL(ua.int_number, ''), IF(ua.int_number is not null, ', ', ''),
                    p.d_asenta, ', ',
                    p.D_mnpio, ', ',
                    p.d_estado, ', ',
                    p.d_ciudad
                    ) as DOMICILIO"),
                'u.email AS CORREO',
                'u.phone AS TELEFONO_FIJO',
                'u.phone AS TELEFONO_MOVIL',
                    ]);
    }
}

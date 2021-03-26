<?php

namespace App\Main\Meetings\Queries;

use Illuminate\Support\Facades\DB;

class MeetingsByUsersQuery
{
    public function __invoke()
    {
        $sql1 = "
        SELECT
            max(m.id) AS MEETING_ID,
            max(c.name) AS NOMBRES,
            max(c.lastname_1) AS APELLIDO_PATERNO,
            max(c.lastname_2) AS APELLIDO_MATERNO,
            c.curp AS CURP,
            max(m.idfe) AS ENTIDAD_FEDERAL,
            CONCAT(
                max(c.street),
                ',',
                max(IFNULL(c.int_number, 'NA')),
                ', ',
                max(c.out_number),
                ', ',
                max(p.D_mnpio),
                ', ',
                max(p.d_asenta),
                ', ',
                max(IFNULL(c.colonia, '')),
                ', ',
                max(p.d_estado)
            ) AS DOMICILIO,
            c.email AS CORREO,
            max(c.phone) AS TELEFONO_FIJO,
            max(c.phone) AS TELEFONO_MOVIL
        FROM
            meetings AS m
                INNER JOIN
            contacts AS c ON m.contacts_id = c.id
                INNER JOIN
            postalcodes AS p ON p.id = c.idcp
        WHERE
            m.paid_state = 1
        GROUP BY c.email, c.curp
        ";

        $sql2 = "UNION (SELECT
        MAX(m.id) AS MEETING_ID,
        MAX(u.name) AS NOMBRES,
        MAX(u.last_name1) AS APELLIDOS_PATERNO,
        MAX(u.last_name2) AS APELLIDOS_MATERNO,
        u.curp AS CURP,
        MAX(m.idfe) AS ENTIDAD_FEDERATIVA,
        CONCAT(MAX(ua.street),
                ', ',
                MAX(IFNULL(ua.int_number, 'NA, ')),
                ', ',
                MAX(ua.out_number),
                ', ',
                MAX(p.D_mnpio),
                ', ',
                MAX(p.d_asenta),
                ', ',
                MAX(IFNULL(ua.colonia, '')),
                ', ',
                MAX(p.d_estado)) AS DOMICILIO,
        u.email AS CORREO,
        MAX(u.phone) AS TELEFONO_FIJO,
        MAX(u.phone) AS TELEFONO_MOVIL
    FROM
        meetings AS m
            INNER JOIN
        users AS u ON m.customer_id = u.id
            INNER JOIN
        user_addresses AS ua ON ua.users_id = u.id
            INNER JOIN
        postalcodes AS p ON p.id = ua.idcp
    WHERE
        m.paid_state = 1
    GROUP BY u.email , u.curp);";
        //        where m.paid_state = 1

        return DB::select(DB::raw($sql1.$sql2));
    }
}

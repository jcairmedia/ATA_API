<?php

namespace App\Main\Meetings\Domain;

use Illuminate\Support\Facades\DB;

class MeetingListDomain
{
    public function __invoke(string $filter, int $index, int $byPage)
    // public function __invoke()
    {
        $filter = trim($filter);

        $index = $index >= 0 ? $index : 0;
        $byPage = $byPage > 0 ? $byPage : 100;

        $markup = [
            'complete' => true,
            'total' => 0,
            'index' => $index,
            'rows' => [],
        ];
        $meetingTable = DB::table('meetings');
        $respuesta = null;
        if ($filter != '') {
            $meetingTable = $meetingTable->where('type_meeting', 'like', '%'.$filter.'%');
        }
        $contador = $meetingTable->count();
        $respuesta = $meetingTable->skip($index)->limit($byPage)->orderByRaw('dt_start DESC')->get();

        $markup['rows'] = $respuesta;
        $markup['total'] = $contador;
        $markup['complete'] = ($index + $byPage) > $markup['total'];

        \Log::error('Datos de la busqueda: '.print_r($markup, 1));

        return $markup;
    }
}

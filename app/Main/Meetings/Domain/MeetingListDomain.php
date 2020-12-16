<?php

namespace App\Main\Meetings\Domain;

use Illuminate\Support\Facades\DB;

class MeetingListDomain
{
    public function __invoke(string $filter, int $index, int $byPage, array $config = [])
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
        if (count($config) > 0) {
            $meetingTable = $meetingTable->where($config);
        }
        $meetingTable->join('contacts', 'meetings.contacts_id', '=', 'contacts.id');
        $contador = $meetingTable->count();
        $respuesta = $meetingTable->select(['meetings.*', 'contacts.name'])
        ->skip($index)
        ->limit($byPage)
        ->orderByRaw('created_at DESC')
        ->get();

        $markup['rows'] = $respuesta;
        $markup['total'] = $contador;
        $markup['complete'] = ($index + $byPage) > $markup['total'];

        // \Log::error('Datos de la busqueda: '.print_r($markup, 1));

        return $markup;
    }
}

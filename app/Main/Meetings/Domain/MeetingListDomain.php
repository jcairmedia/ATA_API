<?php

namespace App\Main\Meetings\Domain;

use Illuminate\Support\Facades\DB;

class MeetingListDomain
{
    public function __invoke(string $filter, int $index, int $byPage, array $config = [])
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

        if ($filter != '') {
            $meetingTable->where('type_meeting', 'like', '%'.$filter.'%');
            $meetingTable->orWhere('name', 'like', '%'.$filter.'%');
        }

        $dateEnd = isset($config['dateEnd']) ? $config['dateEnd'] : null;
        $dateStart = isset($config['dateStart']) ? $config['dateStart'] : null;
        $category = isset($config['category']) ? $config['category'] : null;

        $meetingTable->when((!is_null($dateStart) && !is_null($dateEnd)), function ($query) use ($dateStart, $dateEnd) {
            return $query
            ->whereBetween('dt_start', [$dateStart, $dateEnd])
            ->orWhereBetween('dt_start_rescheduler', [$dateStart, $dateEnd]);
        });

        $meetingTable->when(!is_null($category), function ($query, $category) {
            return $query->where('category', $category);
        });

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

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

        $meetingTable->when($filter != '', function ($query) use ($filter) {
            return $query->where(function ($query2) use ($filter) {
                $query2->where('type_meeting', 'like', '%'.$filter.'%');
                $query2->orWhere('name', 'like', '%'.$filter.'%');
            });
        });

        $dateEnd = isset($config['dateEnd']) ? $config['dateEnd'] : null;
        $dateStart = isset($config['dateStart']) ? $config['dateStart'] : null;
        $category = isset($config['category']) ? $config['category'] : null;

        $meetingTable->when(!is_null($category), function ($query) use ($category) {
            \Log::error('catgory: '.$category);

            return $query->whereRaw('category = ?', [$category]);
        });

        $meetingTable->when((!is_null($dateStart) && !is_null($dateEnd)), function ($query) use ($dateStart, $dateEnd) {
            return $query->where(function ($query1) use ($dateStart, $dateEnd) {
                $query1->whereRaw('date(dt_start) BETWEEN ? AND ?', [$dateStart, $dateEnd])
                ->orWhereRaw('date(dt_start_rescheduler) BETWEEN ? AND ?', [$dateStart, $dateEnd]);
            });
        });

        $meetingTable->join('contacts', 'meetings.contacts_id', '=', 'contacts.id');
        $contador = $meetingTable->count();
        $meetingTable->select(['meetings.*', 'contacts.name'])
        ->skip($index)
        ->limit($byPage)
        ->orderByRaw('created_at DESC');

        \Log::error('query: '.$meetingTable->toSql());
        \Log::error('query: '.print_r($meetingTable->getBindings(), 1));

        $respuesta = $meetingTable->get();

        $markup['rows'] = $respuesta;
        $markup['total'] = $contador;
        $markup['complete'] = ($index + $byPage) > $markup['total'];

        // \Log::error('Datos de la busqueda: '.print_r($markup, 1));

        return $markup;
    }
}

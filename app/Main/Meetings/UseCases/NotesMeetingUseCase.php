<?php

namespace App\Main\Meetings\UseCases;

use App\Main\Meetings\Domain\FindMeetingDomain;

class NotesMeetingUseCase
{
    public function __invoke($data)
    {
        $meeting = (new FindMeetingDomain())(['id' => (int) $data['meetingId']]);
        if (is_null($meeting)) {
            throw new \Exception('Reunión no encontrada', 404);
        }
        $meeting->notes = $data['note'];
        $meeting->save();
    }
}

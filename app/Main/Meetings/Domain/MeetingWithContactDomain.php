<?php

namespace App\Main\Meetings\Domain;

use App\Meeting;

class MeetingWithContactDomain
{
    public function __invoke($meetingId)
    {
        return Meeting::where(['meetings.id' => $meetingId])
        ->join('contacts', 'contacts.id', '=', 'meetings.contacts_id')
        ->select(['meetings.*', 'contacts.name as contact', 'contacts.phone', 'contacts.email'])
        ->first();
        // code...
    }
}

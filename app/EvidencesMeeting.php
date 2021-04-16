<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class EvidencesMeeting extends Model
{
    protected $fillable = [
        'reviewer_user_id',
        'meeting_id',
        'folio',
        'url',
        'status',
        'comment',
        'time_review',
        'number_times_review', ];
}

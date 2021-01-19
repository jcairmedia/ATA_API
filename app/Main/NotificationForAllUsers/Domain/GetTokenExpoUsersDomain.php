<?php

namespace App\Main\NotificationForAllusers\Domain;

use Illuminate\Support\Facades\DB;

class GetTokenExpoUsersDomain
{
    public function __invoke()
    {
        return DB::table('push_notification')->get();
    }
}

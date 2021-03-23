<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Main\Meetings\Queries\MeetingByContactsQuery;
use App\Main\Meetings\Queries\MeetingsByUsersQuery;
use Illuminate\Http\Request;

class SendUsersController extends Controller
{
    public function index(Request $request)
    {
        $m = (new MeetingByContactsQuery())();
        $u = (new MeetingsByUsersQuery())();
        $r = $m->union($u)->get();

        return response()->json($r->toArray(), 200);
    }
}

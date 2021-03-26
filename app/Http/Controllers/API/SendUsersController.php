<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Http\Requests\SendUserRequest;
use App\Main\Meetings\Queries\MeetingByContactsQuery;
use App\Main\Meetings\Queries\MeetingsByUsersQuery;

class SendUsersController extends Controller
{
    public function index(SendUserRequest $request)
    {
        // $m = (new MeetingByContactsQuery())();
        $u = (new MeetingsByUsersQuery())();
        // $r = $m->union($u)->get();

        return response()->json($u, 200);
    }
}

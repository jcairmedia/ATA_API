<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class WebHookOfflinePaidMeetingController extends Controller
{
    public function index(Request $request)
    {
        \Log::info('respuesta hook Open pay: '.print_r($request,1));
    }
}

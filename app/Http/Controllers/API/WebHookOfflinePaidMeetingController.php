<?php

namespace App\Http\Controllers\API;

use  App\Http\Controllers\Controller;
use App\Main\OpenpayWebhookEvent\UseCases\EventRequestOfflinePaidUseCase;
use Illuminate\Http\Request;

class WebHookOfflinePaidMeetingController extends Controller
{
    public function index(Request $request)
    {
        \Log::info('respuesta hook Open pay: '.print_r($request->all(), 1));
        $cu = new EventRequestOfflinePaidUseCase();
        $cu($request->all());

        return response('Ok', 200);
    }
}

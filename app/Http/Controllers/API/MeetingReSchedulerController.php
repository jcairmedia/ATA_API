<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Main\Config_System\Domain\SearchConfigDomain;
use App\Main\Config_System\UseCases\SearchConfigurationUseCase;
use App\Main\Meetings\UseCases\MeetingReSchedulerUseCase;
use Illuminate\Http\Request;

class MeetingReSchedulerController extends Controller
{
    public function index(Request $request)
    {
        // \Log::error(print_r($request->all(), 1));

        try {
            $searchconfusecase = new SearchConfigurationUseCase(new SearchConfigDomain());

            $config = $searchconfusecase('CALENDAR_ID_MEETING_PAID');
            $config_places = $searchconfusecase('NUMBER_PLACES_MEETING_PAID');

            $numberPlaces = (int) $config_places->value;
            $idCalendar = $config->value;

            // $idCalendar = 'io7n2prsu83uc8isfcke2eqnrg@group.calendar.google.com';
            // $numberPlaces = 2;
            $data = $request->all();
            $meeting = new MeetingReSchedulerUseCase();
            $m = $meeting($data, $idCalendar, $numberPlaces);

            return response()->json(['code' => 200, 'message' => 'Reunión re agendada', 'data' => $m]);
            // echo $m;
        } catch (\Exception $ex) {
            $code = (int) $ex->getCode();
            if (!(($code >= 400 && $code <= 422) || ($code >= 500 && $code <= 503))) {
                $code = 500;
            }

            return response()->json([
                'code' => (int) $code,
                'message' => $ex->getMessage(),
            ], $code);
        }
    }
}

<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Http\Requests\AppointmentDateRequest;
use App\Main\Date\CaseUses\AvailableHours;
use App\Main\Date\Domain\FindHoursService;

class AppointmentDateController extends Controller
{
    public function hours(AppointmentDateRequest $request)
    {
        $dt = new \DateTime($request->date);
        $fhours = new FindHoursService();
        $ah = new AvailableHours($fhours);
        $array = $ah($dt);

        return response()->json($array);
    }
}

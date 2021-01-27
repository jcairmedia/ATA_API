<?php

namespace App\Http\Controllers\API;

use App\CaseEvent;
use App\Http\Controllers\Controller;
use App\Http\Requests\EventsCases\AddEventCaseRequest;
use App\Main\CaseEvents\Domain\PaginateEventsCasesDomain;
use App\Main\Cases\Domain\CaseInnerJoinCustomerDomain;
use ExponentPhpSDK\Expo;
use ExponentPhpSDK\ExpoRegistrar;
use Illuminate\Http\Request;
use NotificationChannels\ExpoPushNotifications\Repositories\ExpoDatabaseDriver;

class EventsCaseController extends Controller
{
    /**
     * All event.
     *
     * @return void
     */
    public function index(Request $request)
    {
        $index = (int) $request->input('index') ?? 0;
        $filter = $request->input('filter') ?? '';
        $byPage = $request->input('byPage') ?? 100;
        $clientId = $request->input('clientId') ?? 0;
        $array = [];
        if ($clientId > 0) {
            $array['clientId'] = $clientId;
        }
        $r = (new PaginateEventsCasesDomain())($filter, $index, $byPage, $array);

        return response()->json($r);
    }

    /**
     * Get events by Case.
     *
     * @return void
     */
    public function eventsByCase(Request $request)
    {
        try {
            $index = (int) $request->input('index') ?? 0;
            $filter = $request->input('filter') ?? '';
            $byPage = $request->input('byPage') ?? 100;
            $caseId = $request->input('caseId') ?? 0;
            $array = [];
            if ($caseId <= 0) {
                throw new \Exception('El campo case Id es requerido', 422);
            }
            if ($caseId > 0) {
                $array['caseId'] = $caseId;
            }
            $r = (new PaginateEventsCasesDomain())($filter, $index, $byPage, $array);

            return response()->json($r);
        } catch (\Exception $ex) {
            \Log::error($ex->getMessage());
            $code = (int) $ex->getCode();
            if (!(($code >= 400 && $code <= 422) || ($code >= 500 && $code <= 503))) {
                $code = 500;
            }

            return response()->json([
                'code' => (int) $ex->getCode(),
                'message' => $ex->getMessage(),
            ], $code);
        }
    }

    public function addEvent(AddEventCaseRequest $request)
    {
        try {
            $zoom = $request->input('zoom') ?? 0;
            $caseId = $request->input('caseId');
            $subject = $request->input('subject');
            $description = $request->input('description');
            $date = $request->input('date');
            $data = [
                'case_id' => $caseId,
                'subject' => $subject,
                'description' => $description,
                'url_zoom' => '',
                'date_start' => $date,
            ];
            $caseModel = new CaseEvent($data);

            // $caseModel->save();
            $caseModel = (new CaseInnerJoinCustomerDomain())(['cases.id' => $caseId]);
            if (is_null($caseModel)) {
                throw new \Exception('Caso no encontrado', 404);
            }
            $channel = 'App.User.'.$caseModel->customerId_; // TODO: Buscar el id del cliente
            $expo = new Expo(new ExpoRegistrar(new ExpoDatabaseDriver()));
            $notification = ['body' => $description, 'title' => $subject];
            $expo->notify([$channel], $notification, false);

            return response()->json([
                'code' => 200,
                'message' => 'Evento notificado',
                'data' => [$customerId],
            ], 200);
        } catch (\Exception $ex) {
            \Log::error($ex->getMessage());
            $code = (int) $ex->getCode();
            if (!(($code >= 400 && $code <= 422) || ($code >= 500 && $code <= 503))) {
                $code = 500;
            }

            return response()->json([
                'code' => (int) $ex->getCode(),
                'message' => $ex->getMessage(),
            ], $code);
        }
    }
}

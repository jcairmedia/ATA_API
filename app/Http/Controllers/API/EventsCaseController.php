<?php

namespace App\Http\Controllers\API;

use App\CaseEvent;
use App\Http\Controllers\Controller;
use App\Http\Requests\EventsCases\AddEventCaseRequest;
use App\Main\CaseEvents\Domain\PaginateEventsCasesDomain;
use App\Main\Cases\Domain\CaseInnerJoinCustomerDomain;
use App\Main\Cases\Domain\CaseInnerJoinCustomerLawyerDomain;
use App\Main\Config_System\Domain\SearchConfigDomain;
use App\Main\Config_System\UseCases\SearchConfigurationUseCase;
use App\Utils\ZoomMeetings;
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

    /**
     * @OA\Get(
     *  path="/api/v2/events",
     *  summary="Lista de eventos de un cliente",
     *  @OA\Response(
     *    response=200,
     *    description="Ok",)
     * )
     */
    public function index($clientId, Request $request)
    {
        $index = (int) $request->input('index') ?? 0;
        $filter = $request->input('filter') ?? '';
        $byPage = $request->input('byPage') ?? 100;
        // $clientId = $request->input('clientId') ?? 0;
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
            $zoom = $request->input('zoom');
            $caseId = $request->input('caseId');
            $subject = $request->input('subject');
            $description = $request->input('description');
            $date = $request->input('date');
            $guests = \collect($request->input('guests') ?? []);
            $guests = $guests->unique()->values()->toArray();

            // URL de zoom
            $url_zoom = '';
            $zoom_pass = null;
            $zoom_id = null;

            if ($zoom == '1') {
                //Crear url de zoom
                try {
                    $search = new SearchConfigurationUseCase(new SearchConfigDomain());
                    $config = $search->__invoke('ZOOM_ACCESS_TOKEN');
                    $responseZoom = (new ZoomMeetings(env('ZOOM_USER_ID'), $config->value))->build($date.':00', $subject);
                    $url_zoom = $responseZoom['join_url'];
                    $zoom_pass = $responseZoom['password'];
                    $zoom_id = (string) $responseZoom['id'];
                } catch (\Exception $ex) {
                    \Log::error('zoom-citas_casos: '.$ex->getMessage());
                }
            }

            $channels = [];
            $emails = [];
            // Search case's lawyer
            $modelCase = (new CaseInnerJoinCustomerLawyerDomain())(['cases.id' => $caseId]);
            if (!is_null($modelCase->lawyer_email)) {
                $channels[] = $modelCase->lawyerId;
                $emails[] = $modelCase->lawyer_email;
            }
            \Log::error('Lawyer: '.print_r($modelCase->toArray(), 1));
            $emails = array_merge($emails, $guests);
            // Save Case Event
            $data = [
                'case_id' => $caseId,
                'subject' => $subject,
                'description' => $description,
                'url_zoom' => $url_zoom,
                'date_start' => $date,
                'zoom_pass' => $zoom_pass,
                'zoom_id' => $zoom_id,
                'guests' => json_encode($guests),
            ];

            $caseModel = new CaseEvent($data);
            $caseModel->save();

            // Search data case
            $caseModel = (new CaseInnerJoinCustomerDomain())(['cases.id' => $caseId]);
            if (is_null($caseModel)) {
                throw new \Exception('Caso no encontrado', 404);
            }
            // Send Notification
            $channels[] = 'App.User.'.$caseModel->customerId_;
            $emails[] = $caseModel->customer_email;
            $expo = new Expo(new ExpoRegistrar(new ExpoDatabaseDriver()));

            $notification = ['body' => $description, 'title' => $subject];
            $expo->notify($channels, $notification, false);

            // Send Email
            $this->sendemail($date, $subject, $description, $url_zoom, $emails);

            return response()->json([
                'code' => 200,
                'message' => 'Evento notificado',
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

    private function sendemail(string $date, string $subject, string $message, string $url, array $arrayEmails)
    {
        $classDate = new \App\Utils\DateUtil();
        $m = $classDate->getNameMonthByDate($date);
        $d = $classDate->getDayByDate($date);
        $t = $classDate->getTimeWithMeridian($date);
        $view = view('layout_events_case', [
            'asunto' => 'Asunto de prueba',
            'mes' => $m,
            'dia' => $d,
            'hora' => $t,
            'url' => $url,
            'mensaje' => $message, ])->render();
        (new \App\Utils\SendEmail())(
                ['email' => env('EMAIL_FROM')],
                $arrayEmails,
                $subject,
                '',
                $view
        );
    }
}

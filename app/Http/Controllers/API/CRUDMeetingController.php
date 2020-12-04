<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Main\CalendarEventMeeting\Domain\GetEventDomain;
use App\Main\Config_System\Domain\SearchConfigDomain;
use App\Main\Config_System\UseCases\SearchConfigurationUseCase;
use App\Main\EventsCalendar\Services\EventDelete;
use App\Main\Meetings\Domain\MeetingUpdateDomain;
use App\Main\Meetings\Domain\MeetingWhereDomain;
use App\Main\Meetings\UseCases\MeetingListUseCase;
use App\Main\ZoomRequest\Domain\ZoomRequestGetDomain;
use App\Utils\ZoomDelete;
use Illuminate\Http\Request;

class CRUDMeetingController extends Controller
{
    public function list(Request $request)
    {
        $index = $request->input('index') ?? 0;
        $filter = $request->input('filter') ?? '';
        $byPage = $request->input('byPage') ?? 10;
        $category = $request->input('category') ?? '';

        $meetings = new MeetingListUseCase();
        $array = [];
        if ($category != '') {
            $array = ['category' => $category];
        }

        return response()->json($meetings($filter, $index, $byPage, $array));
    }

    /**
     * Cancell or finish meeting.
     */
    public function updateStateMeeting(Request $request)
    {
        try {
            $id = $request->input('id');
            $option = $request->input('option');
            $reason = $request->input('reason');

            $meeting = new MeetingWhereDomain();
            $meetingObj = $meeting(['id' => $id]);
            if (count($meetingObj) <= 0) {
                throw new \Exception('Cita no encontrada', 404);
            }
            $updateDomain = new MeetingUpdateDomain();
            $meetingObj = $meetingObj[0];
            if ($option == 'COMPLETE') {
                // record_state : -1 cancelado, 0 completado, 1 pendiente
                $stateNewMeeting = 0; //$meetingObj->record_state == 0 ? 1 : 0;
                $meetingNew = $updateDomain($meetingObj->id, [
                    'msg_cancellation' => $reason,
                    'record_state' => $stateNewMeeting,
                    'dt_close' => (new \DateTime())->format('Y-m-d H:i:s'),
                ]);
            } else {
                // eliminar evento de calendar
                $event = (new GetEventDomain())($meetingObj->id);
                if ($event != null) {
                    $_idEvent_ = $event->idevent;
                    $_idCalendar_ = $event->idcalendar;
                    (new EventDelete())($_idEvent_, $_idCalendar_);
                }
                $meetingNew = $updateDomain($meetingObj->id, [
                    'msg_cancellation' => $reason,
                    'record_state' => -1,
                    'dt_cancellation' => (new \DateTime())->format('Y-m-d H:i:s'),
                ]);

                // Get id de la reunión de zoom

                $config = (new SearchConfigurationUseCase(new SearchConfigDomain()))('ZOOM_ACCESS_TOKEN');
                $zoomToken = $config->value;
                $zoomUserId = env('ZOOM_USER_ID');
                $zoom = (new ZoomRequestGetDomain())($meetingObj->id);
                (new ZoomDelete($zoomUserId, $zoomToken))($zoom->idmeetingzoom);
            }

            return response()->json([
                'code' => 200,
                'message' => 'Cita actualizada',
                'data' => $meetingNew,
            ], 200);
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

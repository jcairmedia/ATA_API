<?php

namespace App\Http\Controllers\API;

use App\CustomerTest;
use App\Http\Controllers\Controller;
use App\Http\Requests\Meetings\StateMeetingRequest;
use App\Http\Requests\Meetings\UpdateNoteRequest;
use App\Main\CalendarEventMeeting\Domain\GetEventDomain;
use App\Main\Config_System\Domain\SearchConfigDomain;
use App\Main\Config_System\UseCases\SearchConfigurationUseCase;
use App\Main\Contact\Domain\FindContactDomain;
use App\Main\EventsCalendar\Services\EventDelete;
use App\Main\Meetings\Domain\MeetingUpdateDomain;
use App\Main\Meetings\Domain\MeetingWhereDomain;
use App\Main\Meetings\UseCases\MeetingListUseCase;
use App\Main\Meetings\UseCases\NotesMeetingUseCase;
use App\Main\Questionnaire\Domain\FindQuestionnaireByMeetingIdDomain;
use App\Main\Questionnaire\Domain\FindQuestionnaireDomain;
use App\Main\TestCustomer\Domain\CreateTestDomain;
use App\Main\TestCustomer\Domain\FindTestDomain;
use App\Main\ZoomRequest\Domain\ZoomRequestGetDomain;
use App\Utils\SendEmail;
use App\Utils\ZoomDelete;
use Illuminate\Http\Request;

class CRUDMeetingController extends Controller
{
    public function list(Request $request)
    {
        $index = $request->input('index') ?? 0;
        $filter = $request->input('filter') ?? '';
        $byPage = $request->input('byPage') ?? 100;
        $category = $request->input('category');
        $dateStart = $request->input('dateStart');
        $dateEnd = $request->input('dateEnd');

        $meetings = new MeetingListUseCase();

        $array = ['category' => $category, 'dateStart' => $dateStart, 'dateEnd' => $dateEnd];

        return response()->json($meetings($filter, $index, $byPage, $array));
    }

    /**
     * Cancell or finish meeting.
     */
    public function updateStateMeeting(StateMeetingRequest $request)
    {
        try {
            $id = $request->input('id'); // meeting Id
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
                // UPDATE STATE MEETING: record_state : -1 cancelado, 0 completado, 1 pendiente
                $stateNewMeeting = 0; //$meetingObj->record_state == 0 ? 1 : 0;
                $meetingNew = $updateDomain($meetingObj->id, [
                    'msg_cancellation' => $reason,
                    'record_state' => $stateNewMeeting,
                    'dt_close' => (new \DateTime())->format('Y-m-d H:i:s'),
                    'lawyer' => $reason,
                ]);
                $questionnaireObj = null;
                if ($meetingObj->category == 'FREE') {
                    $questionnaireObj = (new FindQuestionnaireDomain())(['category_meeting' => 'FREE']);
                } else {
                    $questionnaireObj = (new FindQuestionnaireDomain())(['category_meeting' => 'PAID']);
                }
                // CREATE TEST
                $_meeting_id_ = $meetingNew->id;
                $_contactId_ = $meetingNew->contacts_id;

                $_questionnaire_id_ = $questionnaireObj->id;
                $uuid = preg_replace('/[^A-Za-z0-9\-\_]/', '', uniqid('', true));
                $test = (new CreateTestDomain())(new CustomerTest([
                    'uuid' => $uuid,
                    'questionnaire_id' => $_questionnaire_id_,
                    'meeting_id' => $_meeting_id_,
                ]));
                // Find Contact
                $contactObj = (new FindContactDomain())(['id' => $_contactId_]);
                $_email_contact_ = $contactObj->email;
                $url = env('URL_TEST').'/'.$uuid;
                // render view
                $view = view('layout_email_send_url_test', [
                    'url' => $url,
                ])->render();
                // Send Email
                (new SendEmail())(
                    ['email' => env('EMAIL_FROM')],
                    [$_email_contact_],
                    'Encuesta de satisfacción',
                    '',
                    $view
                );
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
                if ($zoom) {
                    (new ZoomDelete($zoomUserId, $zoomToken))($zoom->idmeetingzoom);
                }
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

    public function getQuestionnaireByMeetingId(Request $request)
    {
        try {
            $meetingId = $request->input('meetingId');

            $test = (new FindTestDomain())(['meeting_id' => $meetingId]);
            if ($test == null) {
                throw new \Exception('Encuesta no encontrada', 404);
            }

            $questionnaire = (new FindQuestionnaireByMeetingIdDomain())($meetingId);

            return response()->json([
                'code' => 200,
                'data' => $questionnaire->toArray(),
            ]);
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

    public function setNote(UpdateNoteRequest $request)
    {
        try {
            (new NotesMeetingUseCase())($request->all());

            return response()->json([
                'code' => 200,
                'message' => 'Nota actualizada exitosamente',
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

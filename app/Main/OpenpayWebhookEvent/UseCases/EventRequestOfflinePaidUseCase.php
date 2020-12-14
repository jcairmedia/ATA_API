<?php

namespace App\Main\OpenpayWebhookEvent\UseCases;

use App\Main\CalendarEventMeeting\Domain\GetEventDomain;
use App\Main\Config_System\Domain\SearchConfigDomain;
use App\Main\Config_System\UseCases\SearchConfigurationUseCase;
use App\Main\Contact\Domain\FindContactDomain;
use App\Main\EventsCalendar\Services\EventDelete;
use App\Main\Meetings\Domain\MeetingUpdateDomain;
use App\Main\Meetings\Domain\MeetingWhereDomain;
use App\Main\Meetings\Utils\DoURLZoomMeetingPaidUtils;
use App\Main\Meetings\Utils\TextForSMSMeetingPaidUtils;
use App\Main\OpenPay_payment_references\Domain\FindOpenPayReferencesDomain;
use App\Main\OpenpayWebhookEvent\Domain\OpenpayHookEventDomain;
use App\Main\ZoomRequest\Domain\ZoomRequestGetDomain;
use App\OpenpayWebhookEvent;
use App\Utils\DateUtil;
use App\Utils\SendEmail;
use App\Utils\SMSUtil;
use App\Utils\ZoomDelete;
use Illuminate\Support\Arr;

class EventRequestOfflinePaidUseCase
{
    public function __invoke(array $data)
    {
        // code...
        $objEvent = new OpenpayHookEventDomain();
        $objEvent->save(new OpenpayWebhookEvent([
            'type' => Arr::get($data, 'type', null),
            'status' => Arr::get($data, 'transaction.status', null),
            'hook_id' => Arr::get($data, 'transaction.id', null),
            'order_id' => Arr::get($data, 'transaction.order_id', null),
            'json' => json_encode($data),
        ]));
        // Heuristic payment method store
        if ($data['transaction']['method'] == 'store') {
            $statusTansaction = $data['transaction']['status'];
            $referenceHook = $data['transaction']['payment_method']['reference'];

            switch ($statusTansaction) {
                case 'cancelled':
                    // cancelar
                    $referenceObj = (new FindOpenPayReferencesDomain())(['payment_reference' => $referenceHook]);
                    $meetingId = $referenceObj->meeting_id;
                    (new MeetingUpdateDomain())($meetingId,
                        [
                            'dt_cancellation' => (new \DateTime())->format('Y-m-d H:i:s'),
                            'msg_cancellation' => 'Fecha de pago expirada',
                        ]);

                    // Eliminacion del evento en calendar
                    $event = (new GetEventDomain())($meetingId);
                    if ($event != null) {
                        $_idEvent_ = $event->idevent;
                        $_idCalendar_ = $event->idcalendar;
                        (new EventDelete())($_idEvent_, $_idCalendar_);
                    }

                    // Eliminación de la url en ZOOM
                    $config = (new SearchConfigurationUseCase(new SearchConfigDomain()))('ZOOM_ACCESS_TOKEN');
                    $zoomToken = $config->value;
                    $zoomUserId = env('ZOOM_USER_ID');
                    $zoom = (new ZoomRequestGetDomain())($meetingId);
                    if ($zoom) {
                        (new ZoomDelete($zoomUserId, $zoomToken))($zoom->idmeetingzoom);
                    }
                    break;

                case 'completed':
                    // Pago exitoso
                    $referenceObj = (new FindOpenPayReferencesDomain())(['payment_reference' => $referenceHook]);
                    $meetingId = $referenceObj->meeting_id;
                    (new MeetingUpdateDomain())($meetingId, ['paid_state' => 1]);
                    $meetingObj = (new MeetingWhereDomain())(['id' => $meetingId]);
                    $meetingObj = $meetingObj[0];

                    $contactId = $meetingNew->contacts_id;
                    // Find Contact
                    $contactObj = (new FindContactDomain())(['id' => $contactId]);
                    $email_contact = $contactObj->email;
                    $phone_contact = $contactObj->phone;

                    // Send SMS
                    $type_meeting = $meetingObj->type_meeting;
                    $dateUtil = new DateUtil();
                    $date = is_null($contactObj->dt_start_rescheduler) ? $contactObj->dt_start : $contactObj->dt_start_rescheduler;
                    $day = $dateUtil->getDayByDate($date);
                    $month = $dateUtil->getNameMonthByDate($date);
                    $time = $dateUtil->getTime($date);
                    if (env('APP_ENV') != 'local') {
                        $textSMS = (new TextForSMSMeetingPaidUtils())(
                            $type_meeting,
                            $day,
                            $month,
                            $time
                        );
                        (new SMSUtil())($textSMS, $phone);
                    }
                    // Generar url de zoom
                    $zoomresponse = (new DoURLZoomMeetingPaidUtils())(
                        $meetingObj->id,
                        $date,
                        $type_meeting,
                        'ATA | Cita'
                    );
                    // Send Email
                    $textEmail = (new TextForEmailMeetingPaidUtils())(
                        $type_meeting,
                        $day,
                        $month,
                        $time,
                        $zoomresponse
                    );
                    (new SendEmail())(
                        ['email' => env('EMAIL_FROM')],
                        [$email_contact],
                        'Tu asesoria legal ha sido confirmada ',
                        '',
                        $textEmail
                    );
                break;
                default:break;
            }
        }
    }
}

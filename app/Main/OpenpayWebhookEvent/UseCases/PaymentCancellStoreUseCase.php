<?php

namespace App\Main\OpenpayWebhookEvent\UseCases;

use App\Main\CalendarEventMeeting\Domain\GetEventDomain;
use App\Main\Config_System\Domain\SearchConfigDomain;
use App\Main\Config_System\UseCases\SearchConfigurationUseCase;
use App\Main\Contact\Domain\FindContactDomain;
use App\Main\EventsCalendar\Services\EventDelete;
use App\Main\Meetings\Domain\MeetingUpdateDomain;
use App\Main\Meetings\Domain\MeetingWhereDomain;
use App\Main\Meetings\Utils\TextForSMSMeetingPaidUtils;
use App\Main\OpenPay_payment_references\Domain\FindOpenPayReferencesDomain;
use App\Main\ZoomRequest\Domain\ZoomRequestGetDomain;
use App\Utils\DateUtil;
use App\Utils\SendEmail;
use App\Utils\SMSUtil;
use App\Utils\ZoomDelete;

class PaymentCancellStoreUseCase
{
    public function __invoke(string $referenceHook)
    {
        // cancelar
        $referenceObj = (new FindOpenPayReferencesDomain())(['payment_reference' => $referenceHook]);
        $meetingId = $referenceObj->meeting_id;
        (new MeetingUpdateDomain())($meetingId,
             [
                 'dt_cancellation' => (new \DateTime())->format('Y-m-d H:i:s'),
                 'msg_cancellation' => 'Fecha de pago expirada',
             ]);
        $meetingObj = (new MeetingWhereDomain())(['id' => $meetingId]);
        $meetingObj = $meetingObj[0];

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

        // Find Contact
        $contactId = $meetingObj->contacts_id;
        $contactObj = (new FindContactDomain())(['id' => $contactId]);
        $email_contact = $contactObj->email;
        $phone_contact = $contactObj->phone;

        // SMS
        $contactId = $meetingObj->contacts_id;
        $type_meeting = $meetingObj->type_meeting;
        $dateUtil = new DateUtil();
        $date = is_null($contactObj->dt_start_rescheduler) ?
                        $contactObj->dt_start :
                        $contactObj->dt_start_rescheduler;
        $day = $dateUtil->getDayByDate($date);
        $month = $dateUtil->getNameMonthByDate($date);
        $time = $dateUtil->getTime($date);
        $textSMS = $this->getTextSMS();
        if (env('APP_ENV') != 'local') {
            $textSMS = (new TextForSMSMeetingPaidUtils())(
                 $type_meeting,
                 $day,
                 $month,
                 $time
             );
            (new SMSUtil())($textSMS, $phone_contact);
        }

        // Send EMAIL
        $textEmail = view('layout_email_fail_payment_meeting', [
            'day' => $day,
            'month' => $month,
            'link' => env('URL_ECOMMERCE'),
        ])->render();

        (new SendEmail())(
             ['email' => env('EMAIL_FROM')],
             [$email_contact],
             'Tu asesoria legal ha sido cancelada',
             '',
             $textEmail
         );
    }
}

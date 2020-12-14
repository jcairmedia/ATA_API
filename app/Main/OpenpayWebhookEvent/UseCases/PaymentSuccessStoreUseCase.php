<?php

namespace App\Main\OpenpayWebhookEvent\UseCases;

use App\Main\Contact\Domain\FindContactDomain;
use App\Main\Meetings\Domain\MeetingUpdateDomain;
use App\Main\Meetings\Domain\MeetingWhereDomain;
use App\Main\Meetings\Utils\DoURLZoomMeetingPaidUtils;
use App\Main\Meetings\Utils\TextForEmailMeetingPaidUtils;
use App\Main\Meetings\Utils\TextForSMSMeetingPaidUtils;
use App\Main\OpenPay_payment_references\Domain\FindOpenPayReferencesDomain;
use App\Utils\DateUtil;
use App\Utils\SendEmail;
use App\Utils\SMSUtil;

class PaymentSuccessStoreUseCase
{
    public function __invoke(string $referenceHook)
    {
        $referenceObj = (new FindOpenPayReferencesDomain())(['payment_reference' => $referenceHook]);
        $meetingId = $referenceObj->meeting_id;
        (new MeetingUpdateDomain())($meetingId, ['paid_state' => 1]);
        $meetingObj = (new MeetingWhereDomain())(['id' => $meetingId]);
        $meetingObj = $meetingObj[0];

        $contactId = $meetingObj->contacts_id;
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
            (new SMSUtil())($textSMS, $phone_contact);
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
    }
}

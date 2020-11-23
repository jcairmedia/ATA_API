<?php

namespace App\Main\Meetings\UseCases;

use App\CalendarEventMeeting;
use App\Main\CalendarEventMeeting\Domain\AddEventDomain;
use App\Main\Config_System\Domain\SearchConfigDomain;
use App\Main\Config_System\UseCases\SearchConfigurationUseCase;
use App\Main\Contact\UseCases\ContactFindUseCase;
use App\Main\Contact\UseCases\ContactRegisterUseCase;
use App\Main\OpenPay_payment_references\UseCases\RegisterOpenPayChargeUseCase;
use App\Main\Scheduler\Domain\SearchSchedulerDomain;
use App\Utils\CustomMailer\EmailData;
use App\Utils\CustomMailer\MailLib;
use App\Utils\DateUtil;
use App\Utils\SMSUtil;
use App\Utils\StorePaymentOpenPay;
use App\Utils\ZoomMeetings;
use Spatie\GoogleCalendar\Event;

class MeetingOffilePayment
{
    public function __construct(
        StorePaymentOpenPay $storepayment,
        MeetingRegisterUseCase $meetingUseCase,
        RegisterOpenPayChargeUseCase $registeropenpaychargeusecase,
        ContactRegisterUseCase $contactRegisterUseCase,
        ContactFindUseCase $contactfindusecase)
    {
        $this->storepaymentopenpay = $storepayment;
        $this->meetingUseCase = $meetingUseCase;
        $this->registeropenpaychargeusecase = $registeropenpaychargeusecase;
        $this->contactregisterusecase = $contactRegisterUseCase;
        $this->contactfindusecase = $contactfindusecase;
    }

    public function __invoke(array $data, $duration, $phone_office, $amount_paid)
    {
        try {
            /**
             * si la fecha es igual a hoy no puede agendar cita pago
             * en tienda.
             *
             * {
             * "message": "The given data was invalid.",
             * "errors": {
             *  "date": [
             *    "El date no puede ser una fecha anterior a la fecha actual"
             *  ]
             * }
             * }
             */
            $date = new \DateTime($data['date']);
            $now = new \DateTime();
            $dt_interval = $now->diff($date);
            if ((int) $dt_interval->invert == 1) {
                throw new \Exception('El date no puede ser una fecha anterior o igual a la fecha actual', 422);
            }
            $config = $searchconfusecase('CALENDAR_ID_MEETING_PAID');
            $config_places = $searchconfusecase('NUMBER_PLACES_MEETING_PAID');

            $numberPlaces = (int) $config_places->value;
            $idCalendar = $config->value;

            // 1. is enabled hour in Calendar
            $n = new IsEnabledHourCaseUse();
            $isEnableHour = $n(
                $data['date'],
                $data['time'],
                'PAID',
                $idCalendar,
                $numberPlaces
            );
            if (!$isEnableHour) {
                throw new \Exception('Hora no disponible', 400);
            }
            // Exist hour in work's scheduler
            $scheduler = new SearchSchedulerDomain();
            $rangeHour = $scheduler->_searchRangeHour($data['time'], 'PAID');
            if ($rangeHour == null) {
                throw new Exception('Horario no encontrado');
            }

            // 3. Crear un cargo
            $customer = [
                'name' => $data['name'],
                'phone_number' => $data['phone'],
                'email' => $data['email'],
            ];
            $chargeData = [
                'method' => 'store',
                'amount' => (float) $amount_paid,
                'description' => 'ATA| Cargo para cita pagada por tienda',
                'due_date' => $this->getDateValid(new \DateTime()),
                'customer' => $customer,
            ];
            $response_OPEN_PAY_JSON_charge =
            // file_get_contents(storage_path('responseOpenPay/examples/').'cargoexitososinexpiracion.json');
            $this->storepaymentopenpay->__invoke($chargeData);
            $array_charge = json_decode($response_OPEN_PAY_JSON_charge, true);
            \Log::error(__FILE__.PHP_EOL.$response_OPEN_PAY_JSON_charge);

            $charge = [
                'description' => $array_charge['description'],
                'error_message' => $array_charge['error_message'],
                'authorization' => $array_charge['authorization'],
                'amount' => $array_charge['amount'],
                'operation_type' => $array_charge['operation_date'],
                'payment_type' => $array_charge['payment_method']['type'],
                'payment_reference' => $array_charge['payment_method']['reference'],
                'payment_barcode_url' => $array_charge['payment_method']['barcode_url'],
                'order_id' => $array_charge['order_id'],
                'transaction_type' => $array_charge['transaction_type'],
                'creation_date' => $array_charge['creation_date'],
                'currency' => $array_charge['currency'],
                'status' => $array_charge['status'],
                'method' => $array_charge['method'],
                'json_create_reference' => json_encode($array_charge),
            ];
            // 2. Registar un evento al calendar
            $dtStart = ($data['date'].' '.$rangeHour->start);
            $dtEnd = ($data['date'].' '.$rangeHour->end);

            $event = new Event();
            $eventResult = $event->create(
                [
                    'name' => 'Llamar a '.$data['name'],
                    'description' => $this->setTextSubjectEventInCalendar($data['type_meeting']),
                    'startDateTime' => new Carbon($dtStart),
                    'endDateTime' => new Carbon($dtEnd), ],
                    $idCalendar
            );

            // 4. Registar el contacto
            try {
                // Register contact
                $contact = $this->contactregisterusecase->__invoke([
                'name' => $data['name'],
                'email' => $data['email'],
                'phone' => $data['phone'],
                ]);
                $contact_id = $contact->id;
            } catch (\Exception $ex) {
                \Log::error(__FILE__.PHP_EOL.$ex->getMessage());
                $contact = $this->contactfindusecase->__invoke($data['email']);
                $contact_id = $contact->id;
            }
            // 5. Registrar una reunión en BD
            $data['amount'] = $amount_paid;
            $data['category'] = 'PAID';
            $data['paid'] = 0;
            $meetingObj = $this->meetingUseCase->__invoke($data, $contact_id, $duration);

            // 5.1 Add event in DB
            try {
                $calendar = new AddEventDomain();
                $calendar(new CalendarEventMeeting([
                'meetings_id' => $meetingObj->id,
                'idevent' => $eventResult->id,
                'idcalendar' => $idCalendar, ]));
            } catch (\Exception $ex) {
                \Log::error('ErrorOfflineAddEvent: '.$ex->getMessage());
            }
            // 6. Persistir el cargo en BD
            $charge['meeting_id'] = $meetingObj->id;
            $chargeObj = $this->registeropenpaychargeusecase->__invoke($charge);

            // 7. Enviar SMS
            // TODO: Espera de confirmación
            // $dateUtil = new DateUtil();
            // $date = $data['date'];
            // $day = $dateUtil->getDayByDate($date);
            // $month = $dateUtil->getNameMonthByDate($date);
            // $textSMS = $this->createTxt($day, $month, $data['time'], $data['type_meeting']);
            // $smsUtil = new SMSUtil();
            // $smsUtil->__invoke($textSMS, $data['phone']);

            // 7. Enviar la url del recibo del pago
            // {DASHBOARD_PATH}/paynet-pdf/{MERCHANT_ID}/{REFERENCE}
            $url_file_charge = $this->getURLFileCharge($array_charge['payment_method']['reference']);

            // 9. Enviar correo
            // TODO: Formateo de correos
            $textHtml = $this->getTextInHTML($url_file_charge);
            $this->sendEmail($data['email'], 'ATA | Cita', '', $textHtml);

            // Enviar la url de la reunión
            // $responseUrl_meeting = $this->getUrlZoom($data['date'], $data['type_meeting'], 'ATA | Cita');
            return [
                'meeting' => $meetingObj->toArray(),
                'url_file_charge' => $url_file_charge,
            ];
        } catch (\Exception $ex) {
            throw new \Exception($ex->getMessage(), $ex->getCode());
        }
    }

    private function getDateValid(\DateTime $date)
    {
        // $date = new \DateTime($date);
        $date->setTimezone(new \DateTimeZone('UTC'));
        $date->modify('+24 hours');

        return $date->format('Y-m-d\TH:i:s\Z');
    }

    private function getURLFileCharge($payment_method_reference)
    {
        return env('OPEN_PAY_DASHBOARD_PATH').
        '/paynet-pdf'.
        '/'.env('OPENPAY_ID').
        '/'.$payment_method_reference;
    }

    private function setTextSubjectEventInCalendar($type_meeting)
    {
        $text = '';
        switch ($type_meeting) {
            case 'CALL':
                $text .= 'Tipo de cita: llamada';
            break;
            case 'VIDEOCALL':
                $text .= 'Tipo de cita: videollada';
                break;
            case 'PRESENTIAL':
                $text .= 'Tipo de cita: presencial';
            break;
        }

        return $text;
    }

    private function createTxt($day, $month, $time, $type_meeting)
    {
        $textMsg = 'Gracias por confiar en Abogados a Tu Alcance Hemos confirmado la fecha y hora para tu asesoria el día '.$day.' de '.$month.' a las '.$time.' hrs. ';

        switch ($type_meeting) {
            case 'CALL':
                $textMsg .= 'Recuerda estar atento al teléfono que nos proporcionaste para tomar tu llamada.';
            break;
            case 'VIDEOCALL':
                $textMsg .= 'Ingresa al correo electrónico que nos proporcionaste y obtén el enlace de tu videollamada';
                break;
            case 'PRESENTIAL':
                $textMsg .= 'Ingresa al correo electrónico que nos proporcionaste y te daremos detalles de tu cita.';
            break;
        }

        return $textMsg;
    }

    private function sendEmail($email_customer, $subject, $bodyText, $bodyHtml)
    {
        $emailData = new EmailData(
            (object) ['email' => 'noreply@usercenter.mx'],
            [$email_customer],
            $subject,
            $bodyText,
            $bodyHtml
        );

        try {
            $maillib = new MailLib([
                'username' => env('MAIL_USERNAME'),
                'password' => env('MAIL_PASSWORD'),
                'host' => env('MAIL_HOST'),
                'port' => env('MAIL_PORT'),
            ]);
            $maillib->Send($emailData);
        } catch (\Exception $ex) {
            \Log::error($ex->getMessage());
        }
    }

    private function getTextInHTML($url_charge)
    {
        return '<h1> Lorem ipsum dolor sit amet consectetur adipisicing elit. Fugiat illo dicta veniam vitae minima eius laborum tenetur reprehenderit pariatur nisi voluptates optio rem magnam, iste officiis dignissimos quaerat dolore praesentium!</h1>'.
        'Este es su <a href="'.$url_charge.'">recibo de pago</a>, favor de pagarlo antes de 24 hrs.';
    }

    private function getUrlZoom($date, $type_meeting, $subject)
    {
        $zoomresponse = [
            'code' => 500,
            'message' => '',
            'data' => [],
        ];
        if ($type_meeting != 'VIDEOCALL') {
            return $zoomresponse;
        }
        try {
            $search = new SearchConfigurationUseCase(new SearchConfigDomain());
            $config = $search->__invoke('ZOOM_ACCESS_TOKEN');
            $zoomMeeting = new ZoomMeetings(env('ZOOM_USER_ID'), $config->value);
            $response = $zoomMeeting->build($date, $subject);
            $zoomRequestArray = [
                'join_url' => $response['join_url'],
                'password' => $response['password'],
                'start_time' => $response['start_time'],
                'json' => json_encode($response),
            ];
            $this->saveZoomRequest($zoomRequestArray);
        } catch (\Exception $ex) {
            $zoomRequestArray = [
                'join_url' => '',
                'password' => '',
                'start_time' => $date,
                'state_request' => false,
                'json' => json_encode($ex->getMessage()),
            ];
            $this->saveZoomRequest($zoomRequestArray);

            return [
                'code' => 500,
                'message' => 'Error al obtener la url de zoom.('.$ex->getMessage().').'.
                            ' Contacte a su administrador
                            para que le proporcione una url de reunión.',
            ];
        }

        return
            [
                'code' => 200,
                'data' => $response, ];
    }
}

<?php

namespace App\Main\Meetings\UseCases;

use App\CalendarEventMeeting;
use App\Main\CalendarEventMeeting\Domain\AddEventDomain;
use App\Main\Config_System\Domain\SearchConfigDomain;
use App\Main\Config_System\UseCases\SearchConfigurationUseCase;
use App\Main\Contact\UseCases\ContactFindUseCase;
use App\Main\Contact\UseCases\ContactRegisterUseCase;
use App\Main\Date\CaseUses\IsEnabledHourCaseUse;
use App\Main\Meetings\Domain\MeetingUpdateDomain;
use App\Main\Meetings_payments\UseCases\RegisterPaymentUseCases;
use App\Main\Scheduler\Domain\SearchSchedulerDomain;
use App\Main\ZoomRequest\Domain\ZoomRequestDomain;
use App\Main\ZoomRequest\UseCases\RegisterZoomUseCase;
use App\Utils\CustomMailer\EmailData;
use App\Utils\CustomMailer\MailLib;
use App\Utils\DateUtil;
use App\Utils\SMSUtil;
use App\Utils\StorePaymentOpenPay;
use App\Utils\ZoomMeetings;
use App\ZoomRequest;
use Carbon\Carbon;
use Spatie\GoogleCalendar\Event;

class MeetingOnlinePayment
{
    public function __construct(
        StorePaymentOpenPay $storepayment,
        RegisterPaymentUseCases $registerPayment,
        MeetingRegisterUseCase $meetingUseCase,
        ContactRegisterUseCase $contactRegisterUseCase,
        ContactFindUseCase $contactfindusecase
    ) {
        $this->storepaymentopenpay = $storepayment;
        $this->meetingUseCase = $meetingUseCase;
        $this->contactregisterusecase = $contactRegisterUseCase;
        $this->contactfindusecase = $contactfindusecase;
        $this->registerPayment = $registerPayment;
    }

    public function __invoke(array $data, $amount_paid, $durationMeeting, $phone_office)
    {
        // 1. Verificar en el motor de calendar que la fecha este disponible
        $searchconfusecase = new SearchConfigurationUseCase(new SearchConfigDomain());

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
        $scheduler = new SearchSchedulerDomain();
        $rangeHour = $scheduler->_searchRangeHour($data['time'], 'PAID');
        if ($rangeHour == null) {
            throw new Exception('Horario no encontrado');
        }

        $dtStart = ($data['date'].' '.$rangeHour->start);
        $dtEnd = ($data['date'].' '.$rangeHour->end);

        // 2. Create charge OPEN PAY
        $customer = [
            'name' => $data['name'],
            'phone_number' => $data['phone'],
            'email' => $data['email'],
        ];
        $chargeData = [
            'method' => 'card',
            'source_id' => $data['token_id'],
            'amount' => (float) $amount_paid,
            'description' => 'ATA| Cargo para cita de pago en línea',
            'device_session_id' => $data['deviceIdHiddenFieldName'],
            'customer' => $customer,
        ];
        $response_OPEN_PAY_JSON_charge = //file_get_contents('C:\\Users\\Leo\\Desktop\\ataapi\\storage\\responseOpenPay\\examples\\cargoexitososinexpiracion.json');
                                        $this->storepaymentopenpay->__invoke($chargeData);
        $array_charge = json_decode($response_OPEN_PAY_JSON_charge, true);
        \Log::error(__FILE__.' PAGO online: '.PHP_EOL.$response_OPEN_PAY_JSON_charge);

        // $array_charge = $this->mockupPaymentOpenpay();
        // 3. Add event in google Calendar
        $event = new Event();
        $eventResult = $event->create(
            [
                'name' => 'Llamar a '.$data['name'],
                'description' => $this->setTextSubjectEventInCalendar($data['type_meeting']),
                'startDateTime' => new Carbon($dtStart),
                'endDateTime' => new Carbon($dtEnd), ],
                $idCalendar
        );
        // 4. Add contact
        $contact_id = $this->registerContact($data);

        // 5. Add meeting in BD
        $data['amount'] = $amount_paid;
        $data['category'] = 'PAID';
        $data['paid'] = 1;
        $meetingObj = $this->meetingUseCase->__invoke($data, $contact_id, $durationMeeting);

        // 6. Add payment in DB
        $payment = [
            'price' => $amount_paid,
            'folio' => $array_charge['id'],
            'bank_auth_code' => $array_charge['authorization'],
            'type_payment' => 'ONLINE',
            'card_type' => $array_charge['card']['type'],
            'bank' => $array_charge['card']['bank_name'],
            'currency' => $array_charge['currency'],
            'brand' => $array_charge['card']['brand'],
            'meeting_id' => $meetingObj->id,
            'json' => json_encode($array_charge),
        ];
        $this->registerPayment->__invoke($payment);

        // 7. Add Event and meeting in DB
        try {
            $calendar = new AddEventDomain();
            $calendar(new CalendarEventMeeting([
                'meetings_id' => $meetingObj->id,
                'idevent' => $eventResult->id,
                'idcalendar' => $idCalendar, ]));
        } catch (\Exception $ex) {
            \Log::error('Error add Event in DB: '.$ex->getMessage());
        }
        // 8. Send email: type payment email or a sms
        $dateUtil = new DateUtil();
        $date = $data['date'];
        $day = $dateUtil->getDayByDate($date);
        $month = $dateUtil->getNameMonthByDate($date);
        $textSMS = $this->createTxtForSMS($data['type_meeting'], $day, $month, $data['time']);
        $smsUtil = new SMSUtil();
        if (env('APP_ENV') != 'local') {
            $smsUtil->__invoke($textSMS, $data['phone']);
        }

        // 9. Generate de url de zoom
        $zoomresponse = $this->getUrlZoom($meetingObj->id, $data['date'].' '.$data['time'], $data['type_meeting'], 'ATA | Cita');

        // 10. Send EMail
        //TODO: Falta el formateo de correos, estoy en espera
        $textHtml = $this->createTextForEmail(
            $data['type_meeting'],
            $day,
            $month,
            $data['time'],
            $zoomresponse);
        $this->sendEmail(
            $data['email'],
            'ATA | Cita',
            '',
            $textHtml);

        return ['meeting' => $meetingObj];
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

    // Only test: method Mockup response Open pay
    private function mockupPaymentOpenpay()
    {
        return ['id' => 'trzjaozcik8msyqshka4',
                'amount' => 100.00,
                'authorization' => '801585',
                'method' => 'card',
                'operation_type' => 'in',
                'transaction_type' => 'charge',
                'card' => [
                    'id' => 'kqgykn96i7bcs1wwhvgw',
                    'type' => 'debit',
                    'brand' => 'visa',
                    'address' => null,
                    'card_number' => '411111XXXXXX1111',
                    'holder_name' => 'Juan Perez Ramirez',
                    'expiration_year' => '20',
                    'expiration_month' => '12',
                    'allows_charges' => true,
                    'allows_payouts' => true,
                    'creation_date' => '2014-05-26T11:02:16-05:00',
                    'bank_name' => 'Banamex',
                    'bank_code' => '002',
                ],
                'status' => 'completed',
                'currency' => 'USD',
                'exchange_rate' => [
                    'from' => 'USD',
                    'date' => '2014-11-21',
                    'value' => 13.61,
                    'to' => 'MXN',
                ],
                'creation_date' => '2014-05-26T11:02:45-05:00',
                'operation_date' => '2014-05-26T11:02:45-05:00',
                'description' => 'Cargo inicial a mi cuenta',
                'error_message' => null,
                'order_id' => 'oid-00051', ];
    }

    private function registerContact($data)
    {
        $contact_id = 0;
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

        return $contact_id;
    }

    private function createTxtForSMS($type_meeting, $day, $month, $time)
    {
        $textMsg = '';
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

    private function createTextForEmail($type_meeting, $day, $month, $time, $objZoom)
    {
        $textMsg = '';
        switch ($type_meeting) {
            case 'CALL':
                $textMsg = 'El día '.$day.' de '.$month.' a las '.$time.', tiene programada su primer  guía legal con ATA.';
            break;
            case 'VIDEOCALL':
                $messageZoom = $objZoom['code'] == 200 ?
                ' Recuerda seguir el enlace indicado debajo y presentarte en tiempo y forma'.
                '</br>'.
                '<a href="'.$objZoom['data']['join_url'].'">'.'Enlace'.'</a>' : $objZoom['message'];

                $textMsg = 'El día '.$day.' de '.$month.' a las '.$time.', tiene programada su primer  guía legal con ATA.\n'.
                        $messageZoom.'</br>'.
                        '1. En caso de no poder'.
                        '2. La tolerancia'.
                        '3. En caso de alguna llamada ';
                break;
            case 'PRESENTIAL':
                $textMsg = '
                        El día '.$day.' de '.$month.' a las '.$time.', tiene programada su primer  guía legal con ATA.
                        Recuerda llegar a la dirección indicada debajo y presentarte en tiempo y forma.
                        Av. Cuauhtemoc 145, Roma Norte,
                        06700,CDMX.

                        1. En caso de no poder recibir asesoria
                        2. La tolerancia de espera por parte de nuestros abogados, será de 15 minutos
                        ';
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
                'port' => env('MAIL_PORT'), ]);
            $maillib->Send($emailData);
        } catch (\Exception $ex) {
            \Log::error($ex->getMessage());
        }
    }

    private function getUrlZoom($meeting_id, $date, $type_meeting, $subject)
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
            $response = $zoomMeeting->build($date.':00', $subject);
            // Save request Zoom
            $dt_meeting_zoom = new \DateTime($response['start_time'], new \DateTimeZone($response['timezone']));

            $zoomRequestArray = [
                'join_url' => $response['join_url'],
                'password' => $response['password'],
                'start_time' => $dt_meeting_zoom->format('Y-m-d H:i:s'),
                'timezone' => $response['timezone'], //$response['start_time'],
                'json' => json_encode($response),
            ];
            $this->saveZoomRequest($zoomRequestArray);
            // update url meeting
            $meetingUpdate = new MeetingUpdateDomain();
            $meetingUpdate->__invoke($meeting_id, ['url_meeting' => $response['join_url']]);
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

    private function saveZoomRequest($array)
    {
        $registerzoom = new RegisterZoomUseCase(new ZoomRequestDomain());
        $registerzoom->__invoke(new ZoomRequest($array));
    }
}

<?php

namespace App\Main\Meetings\UseCases;

use App\CalendarEventMeeting;
use App\Main\CalendarEventMeeting\Domain\AddEventDomain;
use App\Main\Config_System\Domain\SearchConfigDomain;
use App\Main\Config_System\UseCases\SearchConfigurationUseCase;
use App\Main\Date\CaseUses\IsEnabledHourCaseUse;
use App\Main\Meetings\Utils\DoURLZoomMeetingPaidUtils;
use App\Main\Meetings\Utils\TextForEmailMeetingPaidUtils;
use App\Main\Meetings\Utils\TextForSMSMeetingPaidUtils;
use App\Main\Meetings_payments\UseCases\RegisterPaymentUseCases;
use App\Main\OpenPayCustomerCards\Domain\FindCustomerCardOpenPayDomain;
use App\Main\Scheduler\Domain\SearchSchedulerDomain;
use App\Utils\ChargeByCardCustomerOpenPay;
use App\Utils\DateUtil;
use App\Utils\SendEmail;
use App\Utils\SMSUtil;
use Carbon\Carbon;
use Spatie\GoogleCalendar\Event;

class MeetingOnlineCardPayment
{
    public function __construct(
        ChargeByCardCustomerOpenPay $storepayment,
        RegisterPaymentUseCases $registerPayment,
        MeetingRegisterUseCase $meetingUseCase
    ) {
        $this->storepaymentopenpay = $storepayment;
        $this->meetingUseCase = $meetingUseCase;
        $this->registerPayment = $registerPayment;
    }

    /**
     * array = [
     * idCard,
     * cvv2,
     * date,
     * time,
     * type_meeting,
     * deviceIdHiddenFieldName,
     * customerId, name, phone ].
     *
     * @param [type] $amount_paid
     * @param [type] $durationMeeting
     * @param [type] $phone_office
     *
     * @return void
     */
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
            throw new \Exception('Horario no encontrado');
        }

        $dtStart = ($data['date'].' '.$rangeHour->start);
        $dtEnd = ($data['date'].' '.$rangeHour->end);

        // //--------------------------------------------------------------------------------

        // // Buscar la card
        $responseCard = (new FindCustomerCardOpenPayDomain() )(['openpay_customer_cards.id' => $data['idCard']]);
        if (is_null($responseCard)) {
            throw new \Exception('Tarjeta inv??lida', 404);
        }
        $idcustomerOpenpay = $responseCard['idcustomeropenpay'];
        $uuidCard = $responseCard['id_card_open_pay'];

        $chargeData = [
            'method' => 'card',
            'source_id' => $uuidCard,
            'amount' => (float) $amount_paid,
            'description' => 'ATA | Cargo para cita de pago en l??nea',
            'device_session_id' => $data['deviceIdHiddenFieldName'],
        ];
        \Log::error('array:'.print_r($chargeData, 1));
        $chargeData['cvv2'] = $data['cvv2'];

        $response_OPEN_PAY_JSON_charge = $this->storepaymentopenpay->__invoke($chargeData, $idcustomerOpenpay);
        $array_charge = json_decode($response_OPEN_PAY_JSON_charge, true);
        \Log::error(__FILE__.' PAGO online: '.PHP_EOL.$response_OPEN_PAY_JSON_charge);
        //-----------------------------------------------------------------------------------

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

        // 4. Add meeting in BD
        $data['amount'] = $amount_paid;
        $data['category'] = 'PAID';
        $data['paid'] = 1;
        $meetingObj = $this->meetingUseCase->__invoke($data, 0, $durationMeeting, $data['customerId']);

        // 5. Add payment in DB
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

        // 6. Add Event and meeting in DB
        try {
            $calendar = new AddEventDomain();
            $calendar(new CalendarEventMeeting([
                'meetings_id' => $meetingObj->id,
                'idevent' => $eventResult->id,
                'idcalendar' => $idCalendar, ]));
        } catch (\Exception $ex) {
            \Log::error('Error add Event in DB: '.$ex->getMessage());
        }
        // 7. Send sms
        $dateUtil = new DateUtil();
        $date = $data['date'];
        $day = $dateUtil->getDayByDate($date);
        $month = $dateUtil->getNameMonthByDate($date);
        if (env('APP_ENV') != 'local') {
            $textSMS = (new TextForSMSMeetingPaidUtils())($data['type_meeting'], $day, $month, $data['time']);
            (new SMSUtil())($textSMS, $data['phone']);
        }

        // 8. Generate de url de zoom
        $zoomresponse = (new DoURLZoomMeetingPaidUtils())(
            $meetingObj->id,
            $data['date'].' '.$data['time'],
            $data['type_meeting'],
            'ATA | Cita'
        );

        // 9. Send EMail
        $textEmail = (new TextForEmailMeetingPaidUtils())(
            $data['type_meeting'],
            $day,
            $month,
            $data['time'],
            $zoomresponse
        );
        (new SendEmail())(
            ['email' => env('EMAIL_FROM')],
            [$data['email']],
            'Tu asesoria legal ha sido confirmada ',
            '',
            $textEmail
        );

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
}

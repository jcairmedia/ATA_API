<?php

namespace App\Main\Meetings\UseCases;

use App\CalendarEventMeeting;
use App\Main\CalendarEventMeeting\Domain\AddEventDomain;
use App\Main\Contact\UseCases\ContactFindUseCase;
use App\Main\Contact\UseCases\ContactRegisterUseCase;
use App\Main\Date\CaseUses\IsEnabledHourCaseUse;
use App\Main\OpenPay_payment_references\UseCases\RegisterOpenPayChargeUseCase;
use App\Main\Scheduler\Domain\SearchSchedulerDomain;
use App\Utils\SendEmail;
use App\Utils\SMSUtil;
use App\Utils\StorePaymentOpenPay;
use Carbon\Carbon;
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
        $this->LAYOUT_EMAIL_OFFLINE_MEETING = 'layout_email_offline_meeting';
    }

    public function __invoke(array $data, $duration, $phone_office, $amount_paid, $numberPlaces, $idCalendar, $user)
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
            \Log::error('Antes enable: '.print_r($data, 1));

            // 1. is enabled hour in Calendar
            $this->isEnableHour($data, $idCalendar, $numberPlaces);

            // Exist hour in work's scheduler
            $rangeHour = $this->existHourInScheduler($data);

            // 3. Create charge in open pay (web Service)
            $customer = $this->prepareCustomer($data, $user);
            $nameCustomer = $customer['name'].' '.$customer['last_name'];
            $emailCustomer = $customer['email'];
            $phoneCustomer = $customer['phone_number'];

            $chargeData = $this->prepareJSONForCallServiceOpenpay($customer, $amount_paid);
            $response_OPEN_PAY_JSON_charge =
            // file_get_contents(storage_path('responseOpenPay/examples/').'cargoexitososinexpiracion.json');
            $this->storepaymentopenpay->__invoke($chargeData);
            $array_charge = json_decode($response_OPEN_PAY_JSON_charge, true);
            \Log::error(__FILE__.PHP_EOL.$response_OPEN_PAY_JSON_charge);

            // Prepare charge
            $charge = $this->prepareJSONChargeforSaveDB($array_charge);

            // Save event to calendar
            $eventResult = $this->registerEventInCalendar($data['date'], $nameCustomer, $data['type_meeting'], $rangeHour, $idCalendar);

            // 4. Save contacts
            $contact_id = 0;
            $user_id = 0;
            if (is_null($user)) {
                $contact = $this->saveContactInDB($data);
                $contact_id = $contact->id;
            } else {
                $user_id = $user->id;
            }

            // 5. Register meeting in DB
            $data['amount'] = $amount_paid;
            $data['category'] = 'PAID';
            $data['paid'] = 0;
            $meetingObj = $this->meetingUseCase->__invoke($data, $contact_id, $duration, $user_id);

            // 5.1 Add event in DB
            $this->saveEventInDB($meetingObj->id, $eventResult->id, $idCalendar);

            // 6. Register charge in db
            $charge['meeting_id'] = $meetingObj->id;
            $chargeObj = $this->registeropenpaychargeusecase->__invoke($charge);

            // 7. get url recibo de pago
            // {DASHBOARD_PATH}/paynet-pdf/{MERCHANT_ID}/{REFERENCE}
            $url_file_charge = $this->getURLFileCharge($array_charge['payment_method']['reference']);

            // 9. Send email
            $this->sendEmail($emailCustomer, $url_file_charge, $meetingObj->type_meeting);

            // 10. Envio de SMS

            (new SMSUtil())($this->getTextForSMS(), $phoneCustomer);

            // 11. Send other software

            return [
                'meeting' => $meetingObj->toArray(),
                'url_file_charge' => $url_file_charge,
            ];
        } catch (\Exception $ex) {
            \Log::error($ex->getMessage());
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

    /**
     * Render layout for email.
     */
    private function getTextInHTML($url_charge, $type_meeting)
    {
        return view($this->LAYOUT_EMAIL_OFFLINE_MEETING, [
            'url' => $url_charge,
        ])->render();
    }

    private function getTextForSMS()
    {
        return 'Gracias por confirar en ATA.'.
        ' Continua con tu asesoria al realizar'.
        ' tu pago en las siguientes 24 hrs.'.
        ' De lo contrario tu fecha y día agendado se perderá.';
    }

    private function isEnableHour($data, $idCalendar, $numberPlaces)
    {
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
    }

    private function existHourInScheduler($data)
    {
        $scheduler = new SearchSchedulerDomain();
        $rangeHour = $scheduler->_searchRangeHour($data['time'], 'PAID');
        if ($rangeHour == null) {
            throw new \Exception('Horario no encontrado');
        }

        return $rangeHour;
    }

    private function prepareJSONChargeforSaveDB($array_charge)
    {
        return [
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
    }

    private function prepareJSONForCallServiceOpenpay($customer, $amount_paid)
    {
        $chargeData = [
            'method' => 'store',
            'amount' => (float) $amount_paid,
            'description' => 'ATA| Cargo para cita pagada por tienda',
            'due_date' => $this->getDateValid(new \DateTime()),
            'customer' => $customer,
        ];

        return $chargeData;
    }

    private function registerEventInCalendar($date, $name, $type_meeting, $rangeHour, $idCalendar)
    {
        $dtStart = ($date.' '.$rangeHour->start);
        $dtEnd = ($date.' '.$rangeHour->end);

        $event = new Event();
        $eventResult = $event->create(
            [
                'name' => 'Llamar a '.$name,
                'description' => $this->setTextSubjectEventInCalendar($type_meeting),
                'startDateTime' => new Carbon($dtStart),
                'endDateTime' => new Carbon($dtEnd), ],
                $idCalendar
        );

        return $eventResult;
    }

    private function sendEmail($email, $url_file_charge, $type_meeting)
    {
        $textHtml = $this->getTextInHTML($url_file_charge, $type_meeting);
        (new SendEmail())(
            ['email' => env('EMAIL_FROM')],
            [$email],
            'Estás a un paso de agendar tu asesoría legal',
            '',
            $textHtml
        );
    }

    private function saveEventInDB($meetingId, $eventId, $idCalendar)
    {
        try {
            $calendar = new AddEventDomain();
            $calendar(new CalendarEventMeeting([
            'meetings_id' => $meetingId,
            'idevent' => $eventId,
            'idcalendar' => $idCalendar, ]));
        } catch (\Exception $ex) {
            \Log::error('ErrorOfflineAddEvent: '.$ex->getMessage());
        }
    }

    private function saveContactInDB($data)
    {
        try {
            // Register contact
            if (array_key_exists('int_number', $data)) {
                $arrayContact['int_number'] = $contact['int_number'];
            }
            $contact = $this->contactregisterusecase->__invoke($data);
        } catch (\Exception $ex) {
            \Log::error(__FILE__.PHP_EOL.$ex->getMessage());
            $contact = $this->contactfindusecase->__invoke($data['email']);
        }

        return $contact;
    }

    public function prepareCustomer($data, $user)
    {
        if (is_null($user)) {
            $customer = [
                'name' => $data['name'],
                'last_name' => $data['lastname_1'].' '.$data['lastname_2'],
                'phone_number' => $data['phone'],
                'email' => $data['email'],
            ];
        } else {
            $customer = [
                'name' => $user->name,
                'last_name' => $user->last_name1.' '.$user->last_name2,
                'phone_number' => $user->phone,
                'email' => $user->email,
            ];
        }

        return $customer;
    }
}

<?php

namespace App\Main\OpenpayWebhookEvent\UseCases;

use App\Cases_payments;
use App\Main\Cases_payments\Domain\CreatePaymentCasesDomain;
use App\Main\Cases_payments\Domain\GetPaymentCasesDomain;
use App\Utils\DateUtil;
use App\Utils\SendEmail;
use App\Utils\SMSUtil;
use Illuminate\Support\Arr;
use App\Main\Cases\Domain\CaseFindDomain;


class CompletedSubscriptionUseCase
{
    public function __invoke($data, $subscriptionObj, $caseObj, $casesId)
    {
        $paymentsCasesObj = (new GetPaymentCasesDomain())(['folio' => $data['transaction']['id']]);
        if (($paymentsCasesObj->count()) > 0) {
            \Log::error('Duplicidad de la transacción: '.print_r($data, 1));

            return;
        }
        $data_payment = [
            'folio' => Arr::get($data['transaction'], 'id', null),
            'type_paid' => 'ONLINE',
            'card_type' => $data['transaction']['card']['brand'],
            'bank' => $data['transaction']['card']['bank_name'],
            'currency' => Arr::get($data['transaction'], 'currency', null),
            'brand' => $data['transaction']['card']['brand'],
            'bank_auth_code' => Arr::get($data['transaction'], 'authorization', null),
            'cases_id' => $casesId,
            'amount' => Arr::get($data['transaction'], 'amount', null),
            'subscription_id' => $subscriptionObj->id,
        ];
        (new CreatePaymentCasesDomain())->save(new Cases_payments($data_payment));

        // Search number times paments if first time, change state case
        $arrayPayments = (new GetPaymentCasesDomain())(['cases_id' => $casesId]);

        if($arrayPayments->count() <=1){
            // update state case
            $case = (new CaseFindDomain())(['id' => $casesId]);
            $case->state_paid_opening = 1;
            $case->save();
        }
        // Send SMS
        if (!is_null($caseObj->customer_phone)) {
            $testSMS = $this->textSMS($caseObj->package_name);
            (new SMSUtil())($testSMS, $caseObj->customer_phone);
        }

        // Send Email
        $subscription_Id = $subscriptionObj->id_suscription_openpay;
        $customer_Id = $subscriptionObj->id_customer_openpay;
        $stringResponseService = (new GetStatusSubscriptionServices())($customer_Id, $subscription_Id);
        \Log::error('Consulta estado suscripción: '.print_r($stringResponseService, 1));
        $jsonOPENPAY_SUSCRIPTION = json_decode($stringResponseService, true);
        $period_end_date = Arr::get($jsonOPENPAY_SUSCRIPTION, 'period_end_date', null);
        $dt_i = date('Y-m-d', strtotime($period_end_date.'-1month+1day'));
        $DT_end = new \DateTime($period_end_date);

        $dateUtil = new DateUtil();
        $nowDT = new \DateTime($dt_i);
        $month = $dateUtil->getNameMonth($nowDT->format('m'));
        $day = $nowDT->format('d');

        $month_valid = $dateUtil->getNameMonth($DT_end->format('m'));
        $day_valid = $DT_end->format('d');

        $view = view('layout_contract_package', [
                    'package' => $caseObj->package_name,
                    'day' => $day,
                    'month' => $month,
                    'day_valid' => $day_valid,
                    'month_valid' => $month_valid,
                ])->render();
        (new SendEmail())(
                    ['email' => env('EMAIL_FROM')],
                    [$caseObj->customer_email],
                    'Pago exitoso del paquete '.$caseObj->package_name,
                    '',
                    $view
        );
    }

    public function textSMS($paquete)
    {
        return
        'Hemos recibido con éxito tu pago para nuestro'.
        ' servicio de asesoría legal en nuestro'.
        ' Paquete '.$paquete.'.'.
        ' Para dudas y aclaraciones comunícate al 55-2625-0649
        ';
    }
}

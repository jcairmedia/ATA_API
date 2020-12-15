<?php

namespace App\Main\OpenpayWebhookEvent\UseCases;

use App\Cases_payments;
use App\Main\Cases\Domain\CaseInnerJoinCustomerDomain;
use App\Main\Cases_payments\Domain\CreatePaymentCasesDomain;
use App\Main\OpenpayWebhookEvent\Domain\OpenpayHookEventDomain;
use App\Main\OpenpayWebhookEvent\Services\GetStatusSubscriptionServices;
use App\Main\Subscription\Domain\FindSubscriptionDomain;
use App\Main\Subscription\Domain\SubscriptionDomain;
use App\OpenpayWebhookEvent;
use App\Utils\DateUtil;
use App\Utils\SendEmail;
use App\Utils\SMSUtil;
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
                    (new PaymentCancellStoreUseCase())($referenceHook);
                    break;
                case 'completed':
                    // Pago exitoso
                    (new PaymentSuccessStoreUseCase())($referenceHook);
                break;
                default:break;
            }
        } else {
            if ($data['transaction']['method'] == 'card') {
                if (isset($data['transaction']['subscription_id'])) {
                    $subscription = $data['transaction']['subscription_id'];
                    $subscriptionObj = (new FindSubscriptionDomain())(['id_suscription_openpay' => $subscription]);
                    if (!$subscriptionObj) {
                        \Log::error('Suscripción no encontrada: '.$subscription);

                        return 0;
                    }
                    if ($data['transaction']['status'] == 'failed') {
                        \Log::error('failed: '.print_r($data, 1));
                        // cancelar subscription
                        // Consultar la suscripción
                        $subscription_Id = $subscriptionObj->id_suscription_openpay;
                        $customer_Id = $subscriptionObj->id_customer_openpay;
                        $stringResponseService = (new GetStatusSubscriptionServices())($customer_Id, $subscription_Id);
                        \Log::error('Consulta estado suscripción: '.$stringResponseService);
                        $jsonOPENPAY_SUSCRIPTION = json_decode($stringResponseService, true);
                        $status = $jsonOPENPAY_SUSCRIPTION['status'];
                        if ($status == 'cancelled') {
                            $subscriptionObj->active = 0;
                            $subscriptionObj->dt_cancelation = ((new \DateTime())->format('Y-m-d H:i:s'));
                            (new SubscriptionDomain())->create($subscriptionObj);
                            // Send SMS
                            $case_id = $subscriptionObj->cases_id;
                            // Search Case and Customer
                            $caseObj = (new CaseInnerJoinCustomerDomain())(['cases.id' => $case_id]);
                            $phone = $caseObj->customer_phone;
                            $email = $caseObj->customer_email;
                            $namePackage = $caseObj->package_name;
                            if (env('APP_ENV') != 'local') {
                                $textSMS = $this->getTextSMSSubscriptionCancelled();
                                (new SMSUtil())($textSMS, $phone);
                            }
                            // Send EMAIL
                            $textEmail = view('layout_email_cancell_subscription')->render();
                            (new SendEmail())(
                                ['email' => env('EMAIL_FROM')],
                                [$email],
                                'No hemos logrado confirmar tu pago del paquete '.$namePackage,
                                '',
                                $textEmail
                            );
                        }

                        return;
                    }

                    // Cobro exitoso
                    $casesId = $subscriptionObj->cases_id;
                    $caseObj = (new CaseInnerJoinCustomerDomain())(['cases.id' => $casesId]);

                    if ($data['transaction']['status'] == 'completed') {
                        \Log::error('Complete '.print_r($data, 1));
                        $data_payment = [
                            'folio' => Arr::get($data['transaction'], 'id', null),
                            'type_paid' => 'ONLINE',
                            'card_type' => $data['transaction']['card']['brand'],
                            'bank' => $data['transaction']['card']['bank_name'],
                            'currency' => Arr::get($data['transaction'], 'currency', null),
                            'brand' => $data['transaction']['card']['brand'],
                            'bank_auth_code' => Arr::get($data['transaction'], 'authorization', null),
                            'cases_id' => $casesId,
                        ];
                        (new CreatePaymentCasesDomain())->save(new Cases_payments($data_payment));

                        $testSMS = $this->textSMS($caseObj->package_name);
                        (new SMSUtil())($testSMS, $caseObj->customer_phone);

                        // Enviar correo
                        $subscription_Id = $subscriptionObj->id_suscription_openpay;
                        $customer_Id = $subscriptionObj->id_customer_openpay;
                        $stringResponseService = (new GetStatusSubscriptionServices())($customer_Id, $subscription_Id);
                        \Log::error('Consulta estado suscripción: '.$stringResponseService);
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
                }
            }
        }
    }

    public function getTextSMSSubscriptionCancelled()
    {
        return 'No hemos confirmado tu pago
        para nuestros servicios y el número de intentos
        ha expirado. Te pedimos ponerte en contacto
        en el siguiente número para solucionar
        el problema: 55-2625-0649';
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

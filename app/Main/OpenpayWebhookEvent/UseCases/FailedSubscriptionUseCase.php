<?php

namespace App\Main\OpenpayWebhookEvent\UseCases;

use App\Main\Cases\Domain\CaseInnerJoinCustomerDomain;
use App\Main\OpenpayWebhookEvent\Services\GetStatusSubscriptionServices;
use App\Main\Subscription\Domain\SubscriptionDomain;
use App\Utils\SendEmail;
use App\Utils\SMSUtil;

class FailedSubscriptionUseCase
{
    public function __invoke($data, $subscriptionObj)
    {
        // Consultar la suscripción
        $subscription_Id = $subscriptionObj->id_suscription_openpay;
        $customer_Id = $subscriptionObj->id_customer_openpay;
        $stringResponseService = (new GetStatusSubscriptionServices())($customer_Id, $subscription_Id);
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
                if (!is_null($phone)) {
                    $textSMS = $this->getTextSMSSubscriptionCancelled();
                    (new SMSUtil())($textSMS, $phone);
                }
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
    }

    public function getTextSMSSubscriptionCancelled()
    {
        return 'No hemos confirmado tu pago
        para nuestros servicios y el número de intentos
        ha expirado. Te pedimos ponerte en contacto
        en el siguiente número para solucionar
        el problema: 55-2625-0649';
    }
}

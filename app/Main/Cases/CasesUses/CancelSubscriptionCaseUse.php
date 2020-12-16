<?php

namespace App\Main\Cases\CasesUses;

use App\Main\Cases\Domain\CaseFindDomain;
use App\Main\Cases\Domain\CaseInnerJoinCustomerDomain;
use  App\Main\Cases\Domain\CasesDomain;
use App\Main\Cases\Services\DeleteSubscriptionService;
use App\Main\Subscription\Domain\FindSubscriptionDomain;
use App\Main\Subscription\Domain\SubscriptionDomain;
use App\Utils\SendEmail;
use App\Utils\SMSUtil;

class CancelSubscriptionCaseUse
{
    public function __invoke($caseId)
    {
        try {
            // Find Case
            $caseObj = (new CaseFindDomain())(['id' => $caseId]);
            if (!$caseObj) {
                throw new \Exception('Caso no encontrado', 404);
            }
            // Find Subscription
            $subscriptionObj = (new FindSubscriptionDomain())(['cases_id' => $caseObj->id]);
            if (!$subscriptionObj) {
                throw new \Exception('Suscripción no encontrada', 500);
            }
            \Log::error('subscription: '.print_r($subscriptionObj->toArray(), true));
            if ($subscriptionObj->dt_cancelation) {
                throw new \Exception('Suscripción ya fue cancelada', 409);
            }
            $id_suscription_openpay = $subscriptionObj->id_suscription_openpay;
            $id_customer_openpay = $subscriptionObj->id_customer_openpay;
            // delete Subscription open pay
            \Log::error('subscripcion: '.$id_suscription_openpay.' customer: '.$id_customer_openpay);
            $stringResponse = (new DeleteSubscriptionService())($id_customer_openpay, $id_suscription_openpay);
            if (!empty($stringResponse)) {
                $jsonResponse = json_decode($stringResponse, true);
                \Log::error('respuesta de cancelación de suscripción open pay: '.print_r($stringResponse, 1));
                throw new \Exception('Respuesta Open pay: '.$jsonResponse['description'], $jsonResponse['http_code']);
            }

            // Desactivación of subscription
            $subscriptionObj->active = 0;
            $subscriptionObj->dt_cancelation = (new \DateTime())->format('Y-m-d H:i:s');
            (new SubscriptionDomain())->create($subscriptionObj);
            // Close Case
            $caseObj->closed_at = (new \DateTime())->format('Y-m-d H:i:s');
            (new CasesDomain())->create($caseObj);

            // Find customer of case
            $caseInnerObj = (new CaseInnerJoinCustomerDomain())(['cases.id' => $caseId]);
            $phone_customer = $caseInnerObj->customer_phone;
            $email_customer = $caseInnerObj->customer_email;
            // Send SMS
            $textSMS = $this->getSMS();
            (new SMSUtil())($textSMS, $phone_customer);
            // Send Email
            $view = view('layout_email_close_case')->render();
            (new SendEmail())(
                ['email' => env('EMAIL_FROM')],
                [$email_customer],
                'Tu caso legal ha sido concluido',
                '',
                $view
            );

            return;
        } catch (\Exception $ex) {
            \Log::error($ex->getMessage().'('.$ex->getCode().')');
            throw new \Exception($ex->getMessage(), $ex->getCode(), $ex);
        }
    }

    public function getSMS()
    {
        return 'Gracias por habernos escogido para tu acompañamiento legal.
                Te notificamos que hemos dado por terminado tu caso,
                si aún tienes dudas por resolver, comunícate al 55-2625-0649';
    }
}

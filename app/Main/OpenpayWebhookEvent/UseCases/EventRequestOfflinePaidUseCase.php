<?php

namespace App\Main\OpenpayWebhookEvent\UseCases;

// use App\Events\SendUserMeetingOfflineEvent;
use App\Main\Cases\Domain\CaseInnerJoinCustomerDomain;
use App\Main\Meetings_payments\Domain\GetPaymentsMeetingDomain;
use App\Main\OpenpayWebhookEvent\Domain\OpenpayHookEventDomain;
use App\Main\Subscription\Domain\FindSubscriptionDomain;
use App\OpenpayWebhookEvent;
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

        sleep(2);
        \Log::error('open pay data: '.print_r($data, 1));

        // Heuristic payment method store
        if ($data['transaction']['method'] == 'store') {
            $this->store($data);

            return;
        }

        if ($data['transaction']['method'] != 'card') {
            return;
        }
        if (!isset($data['transaction']['subscription_id'])) {
            return;
        }

        $subscription = $data['transaction']['subscription_id'];
        // 1. Find Subscription
        $subscriptionObj = (new FindSubscriptionDomain())(['id_suscription_openpay' => $subscription]);
        if (is_null($subscriptionObj)) {
            \Log::error('Suscripción no encontrada: '.$subscription);

            return;
        }

        // Buscar si ya esta registrado el pago
        // en la tabla
        // 2. FAILED HOOK
        if ($data['transaction']['status'] == 'failed') {
            (new FailedSubscriptionUseCase())($data, $subscriptionObj);

            return;
        }

        // Cobro exitoso
        $casesId = $subscriptionObj->cases_id;
        $caseObj = (new CaseInnerJoinCustomerDomain())(['cases.id' => $casesId]);
        if (is_null($caseObj)) {
            \Log::error('no encontro join del caSO: '.$case_id);

            return;
            // throw new Exception('Error Processing Request', 1);
        }
        \Log::error('Obj casespayments: '.print_r($caseObj->toArray(), 1));

        // COMPLETED HOOK
        if ($data['transaction']['status'] == 'completed') {
            (new CompletedSubscriptionUseCase())($data, $subscriptionObj, $caseObj, $casesId);

            return;
        }
    }

    private function store($data)
    {
        $statusTansaction = $data['transaction']['status'];
        $referenceHook = $data['transaction']['payment_method']['reference'];

        switch ($statusTansaction) {
            case 'cancelled':
                (new PaymentCancellStoreUseCase())($referenceHook);
                break;
            case 'completed':
                $_paymntsMeeting = (new GetPaymentsMeetingDomain())(['folio' => $data['transaction']['id']]);
                \Log::error('_paymntsMeeting: '.print_r($_paymntsMeeting, 1));

                if ($_paymntsMeeting->count() > 0) {
                    \Log::error('COMPLETED::existe pago en DB: '.print_r($data, 1));

                    return;
                }

                // Pago exitoso
                $idMeeting = (new PaymentSuccessStoreUseCase())($referenceHook, $data);
                \Log::error('Meetings: '.print_r($idMeeting, 1));
                // event(new SendUserMeetingOfflineEvent($idMeeting));

            break;
            default:break;
        }
    }
}

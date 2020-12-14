<?php

namespace App\Main\OpenpayWebhookEvent\UseCases;

use App\Cases_payments;
use App\Main\Cases_payments\Domain\CreatePaymentCasesDomain;
use App\Main\OpenpayWebhookEvent\Domain\OpenpayHookEventDomain;
use App\Main\Subscription\Domain\FindSubscriptionDomain;
use App\Main\Subscription\Domain\SubscriptionDomain;
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
                        \Log::error('Suscripción no encontrada: ');

                        return 0;
                    }
                    if ($data['transaction']['status'] == 'failed') {
                        // cancelar subscription
                        $subscriptionObj->active = 0;
                        $subscriptionObj->dt_cancelation = ((new \DateTime())->format('Y-m-d Hi:s'));
                        (new SubscriptionDomain())($subscriptionObj);

                        return;
                    }
                    // Cobro exitoso
                    $casesId = $subscriptionObj->cases_id;
                    if ($data['transaction']['status'] == 'completed') {
                        $data_payment = [
                            'folio' => $data['transaction']['id'],
                            'type_paid' => 'ONLINE',
                            'type_target' => $data['transaction']['card']['brand'],
                            'bank' => $data['transaction']['card']['bank_name'],
                            'currency' => $data['transaction']['currency'],
                            'brand' => $data['transaction']['card']['brand'],
                            'authorization' => $data['transaction']['authorization'],
                            'cases_id' => $casesId,
                        ];
                        (new CreatePaymentCasesDomain(new Cases_payments($data_payment)))();
                    }
                }
            }
        }
    }
}

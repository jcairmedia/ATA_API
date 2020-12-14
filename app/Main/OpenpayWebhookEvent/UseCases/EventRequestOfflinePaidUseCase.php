<?php

namespace App\Main\OpenpayWebhookEvent\UseCases;

use App\Main\OpenpayWebhookEvent\Domain\OpenpayHookEventDomain;
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
        }
    }

    public function getTextSMS()
    {
        return '¡Hola! Por falta de pago, lamentablemente hemos cancelado
                tu asesoría legal en línea. Entra al correo electrónico
                que nos proporcionaste para más detalles. ';
    }
}

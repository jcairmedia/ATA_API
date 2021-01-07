<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;

class subopay extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'opay:api';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Command description';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
        $openpay = \Openpay::getInstance(env('OPENPAY_ID'), env('OPENPAY_KEY_PRIVATE'));

        // CREACIóN DE SUSBCRIPCIÓN
        /*$planDataRequest = array(
            'amount' => 150.00,
            'status_after_retry' => 'cancelled',
            'retry_times' => 2,
            'name' => 'Plan Curso Natación',
            'repeat_unit' => 'week',
            'trial_days' => '0',
            'repeat_every' => '1',
            'currency' => 'MXN');

        $plan = $openpay->plans->add($planDataRequest);
        \Log::info("Subscripcion:". print_r($plan,1));
        $this->line(print_r($plan));*/

        /*
         * Crear cliente y asociar tarjeta
         */
        $customerData = [
            'name' => 'Erika',
            'email' => 'erika@airmedia.com.mx',
          ];

        $customer = $openpay->customers->add($customerData);
        \Log::info('creacion de cliente: '.print_r($customer, 1));
        $cardData = [
            'token_id' => 'kmys0lpf7vgpnbpze2ru',
            'device_session_id' => '6ocgrgUhBI5TygXhIxkReRsvSUyHSjtc',
        ];
        $card = $customer->cards->add($cardData);
        \Log::info('creacion de tarjeta del cliente: '.print_r($card, 1));
        \Log::info('creacion de tarjeta del cliente: '.print_r($card->toArray(), 1));

        /*
         * Crear subscripción
         */
    /*$subscriptionDataRequest = array(
        // "trial_end_date" => "2020-11-11",
        'plan_id' => 'p2fw0e8f3sihemvh0mi8',
        'card_id' => 'kosyllhwpfn6uwvfn75i');
    $id_cliente = "avxdawriyk3mjuxnju1r";
    $customer = $openpay->customers->get($id_cliente);
    $subscription = $customer->subscriptions->add($subscriptionDataRequest);
        \Log::info("suscripción". print_r($subscription,1));*/
    }
}

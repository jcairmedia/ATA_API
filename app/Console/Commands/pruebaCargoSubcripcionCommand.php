<?php

namespace App\Console\Commands;

use App\Main\OpenPayCustomer\Services\AddCustomerOpenPayService;
use App\Main\OpenPayCustomerCards\Services\AddCustomerCardOpenPayService;
use App\Utils\ChargeByClientOpenPayService;
use Illuminate\Console\Command;

class pruebaCargoSubcripcionCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'openpay:charge';

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
        $nameCustomer = 'Erika';
        $emailCustomer = 'erika@airmedia.com.mx';
        $tokenId = 'kdkpb6714agnc8xue3dc';
        $deviceSessionId = 'pqoH7gpyt0B5LQ62ghmWA90hEqbLNyII';
        $amount_paid = 1000;
        $planId = 'p7ncub9cseswjphcgsid';

        $customerArray = [
            'name' => $nameCustomer,
            'email' => $emailCustomer,
            'requires_account' => false,
        ];

        $customerOpenPay = (new AddCustomerOpenPayService())($customerArray);
        $_responseCustomerOpenPay = json_decode($customerOpenPay, true);

        $idOpenpayCustomer = $_responseCustomerOpenPay['id'];
        \Log::error('cliente: '.print_r($_responseCustomerOpenPay, 1));
        $cardArray = [
            'token_id' => $tokenId,
            'device_session_id' => $deviceSessionId,
        ];

        $cardOpenPay = (new AddCustomerCardOpenPayService())($cardArray, $idOpenpayCustomer);
        $_responseCardOpenPay = json_decode($cardOpenPay, true);
        $cardIdOpenPay = $_responseCardOpenPay['id'];
        \Log::error('card: '.print_r($_responseCardOpenPay, 1));
        $chargeData = [
            'method' => 'card',
            'source_id' => $cardIdOpenPay,
            'amount' => (float) $amount_paid,
            'description' => 'ATA | Cargo para cita de pago en línea | PRUEBA',
            'device_session_id' => $deviceSessionId,
        ];
        $response_OPEN_PAY_JSON_charge = (new ChargeByClientOpenPayService())($chargeData, $idOpenpayCustomer);
        $array_charge = json_decode($response_OPEN_PAY_JSON_charge, true);
        \Log::error('cargo'.print_r($array_charge, 1));

        $subscriptionObj = (new CreateSubscriptionByCustomerOpenPayService())($idOpenpayCustomer, $cardIdOpenPay, $planId);
        \Log::error('message');
    }
}

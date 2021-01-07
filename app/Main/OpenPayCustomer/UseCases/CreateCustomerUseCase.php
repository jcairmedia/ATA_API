<?php

namespace App\Main\OpenPayCustomer\UseCases;

use App\Main\OpenPayCustomer\Domain\CreateOpenPayCustomerDomain;
use App\Main\OpenPayCustomer\Services\AddCustomerOpenPayService;
use App\OpenpayCustomer;

class CreateCustomerUseCase
{
    public function __invoke($user_name, $user_id, $email)
    {
        try {
            $responseCustomerOpenPay = (new AddCustomerOpenPayService())(
                ['name' => $user_name,
                 'email' => $email,
                 'requires_account' => false, ]);
            $_responseCustomerOpenPay = json_decode($responseCustomerOpenPay, true);
            $idCustomerOpenpay = $_responseCustomerOpenPay['id'];
            \Log::error('idcustomerpay: '.$idCustomerOpenpay);
            // Register Customer in DB
            $obj = (new CreateOpenPayCustomerDomain())(new OpenpayCustomer(
                [
                    'id_open_pay' => $idCustomerOpenpay,
                    'id' => $user_id,
                ]));

            return $obj;
        } catch (\Exception $ex) {
            \Log::error($ex->getMessage().'('.$ex->getCode().')');
            throw new \Exception($ex->getMessage(), (int) $ex->getCode(), $ex);
        }
    }
}

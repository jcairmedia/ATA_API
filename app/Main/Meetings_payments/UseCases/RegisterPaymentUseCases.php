<?php

namespace App\Main\Meetings_payments\UseCases;

use App\Main\Meetings_payments\Domain\PaymentDomain;
use App\Meeting_payments;

class RegisterPaymentUseCases
{
    public function __construct(PaymentDomain $paymentDomain)
    {
        $this->paymentDomain = $paymentDomain;
    }

    public function __invoke($array)
    {
        try {
            $this->paymentDomain->save(new Meeting_payments($array));
        } catch (\Exception $ex) {
            \Log::error('payment: '.$ex->getMessage());
            throw new \Exception('RegisterPayment: '.$ex->getMessage().': '.$ex->getCode(), $ex);
        }
    }
}

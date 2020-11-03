<?php

namespace App\Main\OpenPay_payment_references\UseCases;

use App\Main\OpenPay_payment_references\Domain\CreaterChargeDomain;
use App\OpenpayPaymentReference;
class RegisterOpenPayChargeUseCase
{
    public function __construct(CreaterChargeDomain $ccdomain)
    {
        $this->ccdomain = $ccdomain;
    }

    public function __invoke($array)
    {
        return $this->ccdomain->__invoke(new OpenpayPaymentReference(
            [
                'meeting_id' => $array['meeting_id'],
                'description' => $array['description'],
                'error_message' => $array['error_message'],
                'authorization' => $array['authorization'],
                'amount' => $array['amount'],
                'operation_type' => $array['operation_type'],
                'payment_type' => $array['payment_type'],
                'payment_reference' => $array['payment_reference'],
                'payment_barcode_url' => $array['payment_barcode_url'],
                'order_id' => $array['order_id'],
                'transaction_type' => $array['transaction_type'],
                'creation_date' => $array['creation_date'],
                'currency' => $array['currency'],
                'status' => $array['status'],
                'method' => $array['method'],
                'json_create_reference' => $array['json_create_reference'],
        ]));
    }
}

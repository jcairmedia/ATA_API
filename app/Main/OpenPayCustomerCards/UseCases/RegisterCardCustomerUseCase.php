<?php

namespace App\Main\OpenPayCustomerCards\UseCases;

use App\Main\OpenPayCustomerCards\Domain\CreateOpenpayCustomerCardDomain;
use App\Main\OpenPayCustomerCards\Services\AddCustomerCardOpenPayService;
use App\OpenpayCustomerCards;

class RegisterCardCustomerUseCase
{
    public function __invoke($idCustomerOpenpay, $iduser, $tokenId, $deviceSessionId)
    {
        try {
            // Register card in Open pay
            $responseCardOpenPay = (new AddCustomerCardOpenPayService())([
                'token_id' => $tokenId,
                'device_session_id' => $deviceSessionId, ], $idCustomerOpenpay);
            $_responseCardOpenPay = json_decode($responseCardOpenPay, true);

            // Registrar la card en BD.
            return (new CreateOpenpayCustomerCardDomain())(new OpenpayCustomerCards([
                'user_id' => $iduser,
                'id_card_open_pay' => $_responseCardOpenPay['id'],
                'card_number' => $_responseCardOpenPay['card_number'],
                'response' => $responseCardOpenPay, ]));
        } catch (\Exception $ex) {
            \Log::error($ex->getMessage().'('.$ex->getCode().')');
            throw new \Exception($ex->getMessage(), (int) $ex->getCode(), $ex);
        }
    }
}

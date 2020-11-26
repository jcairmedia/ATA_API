<?php

namespace App\Main\Subscription\CaseUses;

use App\Main\Subscription\Domain\SubscriptionDomain;
use App\Subscription;

class SubscriptionCaseUses
{
    public function __invoke($caseId, $cardId, $subscriptionId, $customerId)
    {
        //1. add susbcription
        $data = [
                'cases_id' => $caseId,
                'id_card_openpay' => $cardId,
                'id_suscription_openpay' => $subscriptionId,
                'id_customer_openpay' => $customerId,
        ];

        return (new SubscriptionDomain())->create(new Subscription($data));
    }
}

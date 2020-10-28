<?php

namespace App\Main\SMS\UseCases;

use App\Main\SMS\Service\SMSService;

class SendSMSUseCase
{
    public function __construct(SMSService $smsservice)
    {
        $this->smsservice = $smsservice;
    }

    public function __invoke(string $textmsg, string $numberphone)
    {
        $this->smsservice->__invoke($textmsg, $numberphone);
    }
}

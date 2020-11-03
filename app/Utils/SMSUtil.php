<?php

namespace App\Utils;

use App\Main\SMS\Service\SMSService;
use App\Main\SMS\UseCases\SendSMSUseCase;

class SMSUtil
{
    public function __invoke($text, $phone)
    {
        try {

            $token = env('MSG_TOKEN');
            $sendmsgusecase = new SendSMSUseCase(new SMSService($token));
            $sendmsgusecase->__invoke($text, $phone);
        } catch (\Exception $ex) {
            \Log::error("(".__FILE__.") ".$ex->getMessage());
        }
    }
}

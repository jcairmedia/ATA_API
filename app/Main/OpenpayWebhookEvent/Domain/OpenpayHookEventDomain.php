<?php

namespace App\Main\OpenpayWebhookEvent\Domain;

use App\OpenpayWebhookEvent;

class OpenpayHookEventDomain
{
    public function save(OpenpayWebhookEvent $hook)
    {
        try {
            $hook->saveOrFail();
        } catch (\Exception $ex) {
            \Log::error($ex->getMessage().'('.$ex->getCode().')');
            throw new \Exception($ex->getMessage(), $ex->getCode(), $ex);
        }
    }
}

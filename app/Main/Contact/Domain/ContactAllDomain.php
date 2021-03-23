<?php

namespace App\Main\Contact\Domain;

use App\Contact;

class ContactAllDomain
{
    public function __construct()
    {
    }

    public function __invoke()
    {
        try {
            return Contact::take(10)->get();
        } catch (Exception $ex) {
            log_message('error', __FILE__);
            log_message('error', '('.$ex->getCode().')'.$ex->getMessage());
            throw new \Exception($ex->getMessage(), $ex->getCode(), $ex);
        }
    }
}

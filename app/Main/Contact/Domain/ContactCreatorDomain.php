<?php

namespace App\Main\Contact\Domain;

use App\Contact;

class ContactCreatorDomain
{
    public function __construct()
    {
    }

    public function __invoke(Contact $contact)
    {
        try {
            $contact->saveOrFail();

            return $contact;
        } catch (Exception $ex) {
            log_message('error', __FILE__);
            log_message('error', '('.$ex->getCode().')'.$ex->getMessage());
            throw new \Exception($ex->getMessage(), $ex->getCode(), $ex);
        }
    }
}

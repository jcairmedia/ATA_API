<?php

namespace App\Main\Contact\Domain;

use App\Contact;

class ContactSelectDomain
{
    public function __construct()
    {
    }

    public function __invoke(string $email)
    {
        try {
            $contact = Contact::where('email', $email)->firstOrFail();

            return $contact;
        } catch (Exception $ex) {
            log_message('error', __FILE__);
            log_message('error', '('.$ex->getCode().')'.$ex->getMessage());
            throw new \Exception($ex->getMessage(), $ex->getCode(), $ex);
        }
    }
}

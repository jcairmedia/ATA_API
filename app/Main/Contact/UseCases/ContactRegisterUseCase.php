<?php

namespace App\Main\Contact\UseCases;

use App\Contact;
use App\Main\Contact\Domain\ContactCreatorDomain;

class ContactRegisterUseCase
{
    public function __construct(ContactCreatorDomain $ccd)
    {
        $this->contactcreatordomain = $ccd;
    }

    public function __invoke(array $contact)
    {
        $contactObj = new Contact([
            'name' => $contact['name'],
            'lastname_1' => '',
            'lastname_2' => '',
            'email' => $contact['email'],
            'phone' => $contact['phone'],
        ]);

        return $this->contactcreatordomain->__invoke($contactObj);
    }
}

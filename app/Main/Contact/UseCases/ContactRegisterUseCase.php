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
        \Log::error('contact: '. print_r($contact, 1));
        $arrayContact =[
            'name' => $contact['name'],
            'lastname_1' => $contact['lastname_1'],
            'lastname_2' => $contact['lastname_2'],
            'curp' => $contact['curp'],

            'idcp' => $contact['idcp'],
            'street' => $contact['street'],
            'out_number' => $contact['out_number'],

            'email' => $contact['email'],
            'phone' => $contact['phone'],
        ];

        if(array_key_exists('int_number', $contact)){
            $arrayContact['int_number'] = $contact['int_number'];
        };

        $contactObj = new Contact($arrayContact);

        return $this->contactcreatordomain->__invoke($contactObj);
    }
}

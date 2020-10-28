<?php

namespace App\Main\Contact\UseCases;

use App\Main\Contact\Domain\ContactSelectDomain;

class ContactFindUseCase
{
    public function __construct(ContactSelectDomain $csd)
    {
        $this->contactselectdomain = $csd;
    }

    public function __invoke(string $email)
    {
        return $this->contactselectdomain->__invoke($email);
    }
}

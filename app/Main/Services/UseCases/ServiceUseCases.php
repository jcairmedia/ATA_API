<?php

namespace App\Main\Services\UseCases;

use App\Main\Services\Domain\ServiceDomain;

class ServiceUseCases
{
    public function __construct(ServiceDomain $serviceDomain)
    {
        $this->serviceDomain = $serviceDomain;
    }

    public function __invoke()
    {
        return $this->serviceDomain->all();
    }
}

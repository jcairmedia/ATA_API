<?php

namespace App\Main\Services\UseCases;

use App\Main\Services\Domain\ServiceDomain;

class ServiceUseCases
{
    public function __construct(ServiceDomain $serviceDomain)
    {
        $this->serviceDomain = $serviceDomain;
    }

    public function __invoke(array $array_columns = null)
    {
        return $this->serviceDomain->all($array_columns);
    }
}

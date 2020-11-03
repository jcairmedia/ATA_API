<?php

namespace App\Main\Config_System\UseCases;

use App\Main\Config_System\Domain\SearchConfigDomain;

class SearchConfigurationUseCase
{
    public function __construct(SearchConfigDomain $searchconfigdomain)
    {
        $this->searchconfigdomain = $searchconfigdomain;
    }

    public function __invoke(string $value)
    {
        try {
            return $this->searchconfigdomain->__invoke($value);
        } catch (\Exception $ex) {
            throw new Exception($ex->getMessage(), $ex->getCode());
        }
    }
}

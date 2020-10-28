<?php

namespace App\Main\Package_benefits\UseCases;

use App\Main\Package_benefits\Domain\BenefitDomain;

class ListBenefitsUseCase
{
    private $benefitDomain;

    public function __construct(BenefitDomain $benefitDomain)
    {
        $this->benefitDomain = $benefitDomain;
    }

    public function __invoke(array $array_columns)
    {
        return ['data' => $this->benefitDomain->all($array_columns)];
    }
}

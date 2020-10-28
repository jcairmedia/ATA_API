<?php

namespace App\Main\Package_benefits\UseCases;

use App\Main\Package_benefits\Domain\BenefitDomain;

class ByPackageUseCase
{
    private $benefitDomain;

    public function __construct(BenefitDomain $benefitDomain)
    {
        $this->benefitDomain = $benefitDomain;
    }

    public function __invoke(int $id, array $array_columns = null)
    {
        return ['data' => $this->benefitDomain->byPackage($id, $array_columns)];
    }
}

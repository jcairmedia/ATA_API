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

    public function __invoke(int $id)
    {
        return $this->benefitDomain->byPackage($id);
    }
}

<?php

namespace App\Main\Config_System\UseCases;

use App\Main\Config_System\Domain\UpdateConfigDomain;

class UpdateConfigSystemUseCase
{
    public function __construct(UpdateConfigDomain $searchconfigdomain)
    {
        $this->updateconfigdomain = $searchconfigdomain;
    }

    public function __invoke(int $id, array $data)
    {
        return $this->updateconfigdomain->__invoke($id, $data);
    }
}

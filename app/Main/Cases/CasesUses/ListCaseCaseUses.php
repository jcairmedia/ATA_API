<?php

namespace App\Main\Cases\CasesUses;

use App\Main\Cases\Domain\CasesListDomain;

class ListCaseCaseUses
{
    public function __invoke(string $filter, int $index, int $byPage = 10, array $config = [])
    {
        $list = new CasesListDomain();

        return $list($filter, $index, $byPage, $config);
    }
}

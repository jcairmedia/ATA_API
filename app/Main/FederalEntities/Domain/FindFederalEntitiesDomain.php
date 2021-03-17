<?php

namespace App\Main\FederalEntities\Domain;

use App\Federalentitie;

class FindFederalEntitiesDomain
{
    public function __invoke(array $where)
    {
        try {
            return Federalentitie::query()
            ->where($where)
            ->firstOrFail();
        } catch (\Exception $ex) {
            \Log::error($ex->getMessage().'('.$ex->getCode().')');
            throw new \Exception($ex->getMessage(), $ex->getCode(), $ex);
        }
    }
}

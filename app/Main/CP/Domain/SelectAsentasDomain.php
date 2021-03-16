<?php

namespace App\Main\CP\Domain;

use App\Postalcode;

class SelectAsentasDomain
{
    public function __invoke(string $cp)
    {
        try {
            return Postalcode::query()
            ->where(['d_codigo' => $cp])
            ->select(['d_codigo as cp', 'id', 'd_asenta'])
            ->paginate();
        } catch (\Exception $ex) {
            \Log::error($ex->getMessage().'('.$ex->getCode().')');
            throw new \Exception($ex->getMessage(), $ex->getCode(), $ex);
        }
    }
}

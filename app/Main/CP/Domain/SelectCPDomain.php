<?php

namespace App\Main\CP\Domain;

use App\Postalcode;

class SelectCPDomain
{
    public function __invoke(string $cp)
    {
        try {
            return Postalcode::query()
            ->where('d_codigo', 'like', $cp.'%')
            ->groupBy('d_codigo')
            ->select(['d_codigo as cp'])
            ->paginate();
        } catch (\Exception $ex) {
            \Log::error($ex->getMessage().'('.$ex->getCode().')');
            throw new \Exception($ex->getMessage(), $ex->getCode(), $ex);
        }
    }
}

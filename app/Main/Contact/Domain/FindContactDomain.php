<?php

namespace App\Main\Contact\Domain;

use App\Contact;

class FindContactDomain
{
    public function __invoke($arrayWhere)
    {
        try {
            return Contact::where($arrayWhere)->first();
        } catch (\Exception $ex) {
            \Log::error($ex->getMessage().'('.$ex->getCode().')');
            throw new \Exception($ex->getMessage(), $ex->getCode(), $ex);
        }
    }
}

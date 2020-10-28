<?php

namespace App\Main\Config_System\Domain;

use App\Conf_system;

class SearchConfigDomain
{
    public function __construct()
    {
    }

    public function __invoke(string $value)
    {
        try {
            $config = Conf_system::where('name', $value)->firstOrFail();

            return $config;
        } catch (\Exception $ex) {
            \Log::error($ex->getMessage().'('.$ex->getCode().')');
            throw new \Exception($ex->getMessage(), $ex->getCode(), $ex);
        }
    }
}

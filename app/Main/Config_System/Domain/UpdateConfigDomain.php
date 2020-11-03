<?php

namespace App\Main\Config_System\Domain;

use App\Conf_system;

class UpdateConfigDomain
{
    public function __construct()
    {
    }

    public function __invoke(int $id, array $data)
    {
        try {
            $config = Conf_system::where('id', $id)
            ->update($data);

            return $config;
        } catch (\Exception $ex) {
            \Log::error($ex->getMessage().'('.$ex->getCode().')');
            throw new \Exception($ex->getMessage(), $ex->getCode());
        }
    }
}

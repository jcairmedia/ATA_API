<?php

namespace App\Main\Cases\CasesUses;

use App\Cases;
use App\Main\Cases\Domain\CasesDomain;
use App\Packages;

class CasesCasesUse
{
    public function __invoke(
        Packages $package,
        string $idCustomerOpenpay,
        $customerId,
        $serviceId,
        string $urlDoc,
        int $idfe
    ) {
        try {
            $data = [
            'packages_id' => $package->id,
            'url_doc' => $urlDoc,
            'price' => $package->amount,
            'idfe' => $idfe,
            'id_customer_openpay' => $idCustomerOpenpay,
            'services_id' => $serviceId,
            'customer_id' => $customerId,
        ];
            \Log::error('CaseUseCase: '.print_r($data, 1));
            // Register Cases
            $casesDomain = new CasesDomain();

            return $casesDomain->create(new Cases($data));
        } catch (\Exception $ex) {
            \Log::error($ex->getMessage().'('.$ex->getCode().')');
            throw new \Exception($ex->getMessage(), $ex->getCode(), $ex);
        }
    }
}

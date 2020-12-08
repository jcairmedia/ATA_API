<?php

namespace App\Main\Cases\CasesUses;

class CreatePDFContractCaseUse
{
    public function __invoke($view, $uri, $path)
    {
        try {
            $mpdf = new \Mpdf\Mpdf();
            $mpdf->WriteHTML($view);
            $mpdf->Output($path.$uri, 'F');
        } catch (\Exception $ex) {
            \Log::error('Ocurrio un error al tratar de generar el contrato: '.print_r($ex->getMessage(), 1));
            throw new \Exception($ex->getMessage(), (int) $ex->getCode());
        }
    }
}

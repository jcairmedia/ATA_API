<?php

namespace App\Main\Cases\CasesUses;

class CreatePDFContractCaseUse
{
    public function __invoke($view, $uri, $path)
    {
        try {
            $pdf = App::make('dompdf.wrapper');
            $pdf->loadHTML($view)->save($path.$uri);
        } catch (\Exception $ex) {
            \Log::error('Ocurrio un error al tratar de generar el contrato: '.print_r($ex->getMessage(), 1));
            throw new \Exception($ex->getMessage(), (int) $ex->getCode());
        }
    }
}

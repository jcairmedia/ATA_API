<?php
namespace App\Main\Documents_Cases\Domain;
use \App\Documents_case;
class SearchDocumentCaseDomain
{
    public function __invoke($array)
    {
        try{
            return Documents_case::where($array)->get();
        } catch (\Exception $ex) {
            \Log::error($ex->getMessage().'('.$ex->getCode().')');
            throw new \Exception($ex->getMessage(), (int) $ex->getCode(), $ex);
        }
    }
}

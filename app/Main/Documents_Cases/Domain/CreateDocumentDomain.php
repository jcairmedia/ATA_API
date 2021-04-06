<?php
namespace App\Main\Documents_Cases\Domain;
use App\Documents_case;
class CreateDocumentDomain
{
    public function __invoke($data)
    {
        try {
            $doc = new Documents_case($data);
            $doc->saveOrFail();
            return $doc;
        } catch (\Exception $ex) {
            \Log::error($ex->getMessage().'('.$ex->getCode().')');
            throw new \Exception($ex->getMessage(), $ex->getCode(), $ex);
        }

    }
}

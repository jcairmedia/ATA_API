<?php

namespace App\Main\BlogEntry\Domain;

use App\BlogEntry;

class UpdateEntryDomain
{
    public function __invoke($entryId, $dataupdate)
    {
        try {
            $obj = BlogEntry::findOrFail($entryId);

            \Log::error('update-origin'.print_r($obj->toArray(), 1));
            \Log::error('update'.print_r($dataupdate, 1));
            $obj->fill($dataupdate);

            return $obj->saveOrFail();
        } catch (\Exception $th) {
            throw new \Exception($th->getMessage(), $th->getCode());
        }
    }
}

<?php

namespace App\Main\BlogEntry\Domain;

use App\BlogEntry;

class DeleteEntryDomain
{
    public function __invoke($categoryId)
    {
        try {
            return BlogEntry::where('id', '=', $categoryId)->delete();
        } catch (\Exception $ex) {
            throw new \Exception($ex->getMessage(), (int) $ex->getCode());
        }
    }
}

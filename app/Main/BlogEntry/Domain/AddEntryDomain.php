<?php

namespace App\Main\BlogEntry\Domain;

use App\BlogEntry;

class AddEntryDomain
{
    public function __invoke(BlogEntry $entry)
    {
        $entry->save();

        return $entry;
    }
}

<?php

namespace App\Main\ExternalCategory\Domain;

use App\ExternalCategory;

class FindCategoriesDomain
{
    public function __invoke()
    {
        return ExternalCategory::whereNull('deleted_at')->get();
    }
}

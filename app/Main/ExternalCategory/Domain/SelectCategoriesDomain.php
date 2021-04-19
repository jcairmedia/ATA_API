<?php

namespace App\Main\ExternalCategory\Domain;

use Illuminate\Support\Facades\DB;

class SelectCategoriesDomain
{
    public function __invoke($status = 'PUBLISHED')
    {
        return DB::table('external_categories')
            ->whereNull('deleted_at')
            ->orderByDesc('created_at')
            ->get();
    }
}

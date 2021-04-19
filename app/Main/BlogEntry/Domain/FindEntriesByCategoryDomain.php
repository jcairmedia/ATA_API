<?php

namespace App\Main\BlogEntry\Domain;

use Illuminate\Support\Facades\DB;

class FindEntriesByCategoryDomain
{
    public function __invoke($categoryId)
    {
        return DB::table("blog_entries")->join("external_categories as exc", "exc.id", "=", "blog_entries.external_category_id")
            ->where(["exc.id" => $categoryId])
            ->orderByDesc("blog_entries.updated_at")
            ->select(["blog_entries.*", "exc.name as category"])
            ->get();
    }
}

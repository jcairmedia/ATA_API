<?php

namespace App\Main\BlogEntry\Domain;

use Illuminate\Support\Facades\DB;

class PaginateEntriesDomain
{
    /**
     * Undocumented function.
     *
     * @param [type] $status
     * @param [type] $index
     * @param [type] $byPage
     * @param array  $config => [[key => value]]
     *
     * @return void
     */
    public function __invoke($status, $index, $byPage, $config = [])
    {
        $index = $index >= 0 ? $index : 0;
        $byPage = $byPage > 0 ? $byPage : 100;

        $markup = [
            'complete' => true,
            'total' => 0,
            'index' => $index,
            'rows' => [],
        ];
        $responseDB = DB::table('blog_entries')
            ->join('external_categories as exc', 'exc.id', '=', 'blog_entries.external_category_id');
        if ($status != '') {
            $responseDB->where(['blog_entries.status' => $status]);
        }
        $responseDB->where('blog_entries.deleted_at', '=', null);
        $responseDB->orderBy('blog_entries.updated_at', 'desc');
        $responseDB->select(['blog_entries.*', 'exc.name as category']);
        if (count($config) > 0) {
            foreach ($config as $key => $value) {
                $responseDB->where($value);
            }
        }
        $counter = $responseDB->count();
        $response = $responseDB->skip($index)
            ->limit($byPage);
        $markup['rows'] = $response->get();
        $markup['total'] = $counter;
        $markup['complete'] = ($index + $byPage) > $markup['total'];

        return $markup;
    }
}

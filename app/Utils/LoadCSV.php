<?php

namespace App\Utils;

use App\Imports\EmailsImport;
use Maatwebsite\Excel\Facades\Excel;

class LoadCSV
{
    public function __invoke($request)
    {
        $worksheetCollection = Excel::toCollection(new EmailsImport(), $request->file('file'), null, \Maatwebsite\Excel\Excel::CSV);

        return $worksheetCollection;
    }
}

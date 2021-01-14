<?php

namespace App\Imports;

use Illuminate\Support\Collection;
use Maatwebsite\Excel\Concerns\ToCollection;

class EmailsImport implements ToCollection
{
    public $emails = [];

    public function collection(Collection $rows)
    {
        foreach ($rows as $row) {
            $this->emails = [
                'email' => $row[0],
            ];
        }
    }
}

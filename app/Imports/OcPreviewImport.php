<?php

namespace App\Imports;

use Maatwebsite\Excel\Concerns\ToArray;
use Maatwebsite\Excel\Concerns\ToCollection;
use Maatwebsite\Excel\Concerns\WithHeadingRow;

class OcPreviewImport implements ToArray
{
    //public function collection(\Illuminate\Support\Collection $rows) {}

    public function array(array $array)
    {
        return $array;
    }
}

<?php

namespace App\Imports;

use Maatwebsite\Excel\Concerns\WithMultipleSheets;

class BaseSheetHandler implements WithMultipleSheets 
{
    public function sheets(): array
    {
        return [
            new BaseComercialImport() 
        ];
    }
}
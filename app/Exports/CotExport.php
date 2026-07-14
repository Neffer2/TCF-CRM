<?php

namespace App\Exports;

use Maatwebsite\Excel\Concerns\WithMultipleSheets;

class CotExport implements WithMultipleSheets
{

    protected $info = [];

    function __construct($info) {
        $this->info = $info;
    }

    public function sheets(): array
    {
        return [
            'Presupuestos'         => new PresupuestosSheetsExports($this->info),
        ];
    }

}

<?php

namespace App\Exports;

use Illuminate\Contracts\View\View;
use Maatwebsite\Excel\Concerns\FromView;
use Maatwebsite\Excel\Concerns\WithColumnWidths;
use PhpOffice\PhpSpreadsheet\Style\NumberFormat;
use Maatwebsite\Excel\Concerns\WithColumnFormatting;
use Maatwebsite\Excel\Events\AfterSheet;
use Maatwebsite\Excel\Concerns\WithEvents;
use App\Models\OrdenCompra;
use Carbon\Carbon;

class OrdenesTesoreria implements FromView
{
    protected $ordenes = [];

    function __construct($yearInfo = null) {
        ini_set('max_execution_time', 10000);
        $this->ordenes = $ordenes = OrdenCompra::where([
            ['estado_id', '5'],
            ['cod_causal', '<>', 'NULL'],
            ['created_at', '>=', $yearInfo->meses->first()->f_inicio],
            ['created_at', '<=', $yearInfo->meses->last()->f_fin]
        ])
        ->orderBy('created_at', 'desc')
        ->get();
    }

    /**
    * @return \Illuminate\Support\Collection
    */
    public function view(): View
    {
        return view('exports.ordenes-tesoreria', [
            'ordenes' => $this->ordenes
        ]);
    }
}

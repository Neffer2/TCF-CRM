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

class ConsumidosExport implements FromView, WithColumnFormatting, WithColumnWidths, WithEvents
{
    protected $ordenes = [];

    function __construct() {
        ini_set('max_execution_time', 10000); // or this way

        $this->ordenes = OrdenCompra::where([
            ['estado_id', '5'],
            ['cod_causal', NULL]
        ])
        ->orWhere([
            ['estado_id', '2'],
            ['cod_causal', '!=', NULL]
        ])
        ->whereBetween('created_at', ['2025-01-01', Carbon::now()->toDateTimeString()])
        // ->whereBetween('created_at', [Carbon::now()->startOfMonth()->toDateTimeString(), Carbon::now()->toDateTimeString()])
        ->orderBy('created_at', 'desc')
        ->get();
    }

    /**
    * @return \Illuminate\Support\Collection
    */
    public function view(): View
    {
        return view('exports.reporte-consumidos', [
            'ordenes' => $this->ordenes
        ]);
    }

    public function columnWidths(): array
    {
        return [
            'A' => 16,
            'B' => 16,
            'C' => 16,
            'D' => 16,
            'E' => 16,
            'F' => 16,
            'G' => 16,
            'H' => 16,
            'I' => 16,
            'J' => 16
        ];
    }

    public function columnFormats(): array
    {
        return [
            'H' => NumberFormat::FORMAT_NUMBER_COMMA_SEPARATED1,
            'I' => NumberFormat::FORMAT_NUMBER_COMMA_SEPARATED1,
        ];
    }

    public function registerEvents(): array
    {
        return [
            AfterSheet::class => function(AfterSheet $event) {
                $event->sheet->getDelegate()->setAutoFilter('A1:Q1');
            },
        ];
    }
}

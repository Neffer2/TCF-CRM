<?php

namespace App\Exports;

use Maatwebsite\Excel\Concerns\FromCollection;
use Illuminate\Contracts\View\View;
use Maatwebsite\Excel\Concerns\FromView;
use Maatwebsite\Excel\Concerns\WithColumnWidths;
use PhpOffice\PhpSpreadsheet\Style\NumberFormat;
use Maatwebsite\Excel\Concerns\WithColumnFormatting;
use Maatwebsite\Excel\Events\AfterSheet;
use Maatwebsite\Excel\Concerns\WithEvents;
use App\Models\OrdenCompra;
use Carbon\Carbon;

class PlanoExport implements FromView, WithColumnFormatting, WithColumnWidths, WithEvents
{
    protected $ordenes = [];

    function __construct($mes = null) {
        ini_set('max_execution_time', 10000); // or this way

        $año = Carbon::now()->year;
        if ($mes) {
            $startDate = Carbon::createFromDate($año, $mes, 1)->startOfMonth()->toDateTimeString();
            $endDate = Carbon::createFromDate($año, $mes, 1)->endOfMonth()->toDateTimeString();
        } else {
            $startDate = Carbon::now()->startOfMonth()->toDateTimeString();
            $endDate = Carbon::now()->toDateTimeString();
        }

        $this->ordenes = OrdenCompra::where('cod_causal', NULL)
            ->where('tipo_oc', 2)
            ->whereBetween('created_at', [$startDate, $endDate])
            ->orderBy('created_at', 'desc')
            ->get();
    }

    /**
    * @return \Illuminate\Support\Collection
    */
    public function view(): View
    {
        return view('exports.reporte-plano-helisa', [
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
            'F' => 16
        ];
    }

    public function columnFormats(): array
    {
        return [
            'D' => NumberFormat::FORMAT_NUMBER_COMMA_SEPARATED1,
        ];
    }

    public function registerEvents(): array
    {
        return [
            AfterSheet::class => function(AfterSheet $event) {
                $event->sheet->getDelegate()->setAutoFilter('A1:F1');
            },
        ];
    }
}

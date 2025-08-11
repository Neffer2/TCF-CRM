<?php

namespace App\Exports;

use Illuminate\Contracts\View\View;
use Maatwebsite\Excel\Concerns\FromView;
use Maatwebsite\Excel\Concerns\WithColumnWidths;
use PhpOffice\PhpSpreadsheet\Style\NumberFormat;
use Maatwebsite\Excel\Concerns\WithColumnFormatting;
use App\Models\OrdenCompra;
use App\Models\Año;
use Carbon\Carbon;

class ConsumidosExport implements FromView, WithColumnFormatting, WithColumnWidths
{
    protected $ordenes = [];

    function __construct() {
        // $currentYear = Carbon::now()->subDays(Carbon::now()->dayOfYear)->toDateTimeString();
        $currentYear = Carbon::now()->subDays(7)->toDateTimeString(); // 7 days ago

        $this->ordenes = OrdenCompra::where([
            ['estado_id', '!=', '6'],
            ['estado_id', '!=', '2'],
            ['estado_id', '!=', '3']
        ])
        ->where('created_at', '>=', $currentYear)
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
}

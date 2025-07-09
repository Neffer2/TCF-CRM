<?php

namespace App\Exports;

use Illuminate\Contracts\View\View;
use Maatwebsite\Excel\Concerns\FromView;
use Maatwebsite\Excel\Concerns\WithColumnWidths;
use PhpOffice\PhpSpreadsheet\Style\NumberFormat;
use Maatwebsite\Excel\Concerns\WithColumnFormatting;
use Carbon\Carbon;
use App\Models\OrdenCompra;

class ConsumidosExport implements FromView, WithColumnFormatting, WithColumnWidths
{
    protected $ordenes = [];

    function __construct() {
        $hourFilter = Carbon::now()->subDay(); // 24 hours ago
        $this->ordenes = OrdenCompra::where([
            ['created_at', '=>', $hourFilter],
            ['estado_id', '!=', '6'],
            ['estado_id', '!=', '2'],
            ['estado_id', '!=', '3']
        ])->orderBy('created_at', 'desc')->limit(10)->get();
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

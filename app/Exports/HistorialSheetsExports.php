<?php

namespace App\Exports;

use Illuminate\Contracts\View\View;
use Maatwebsite\Excel\Concerns\FromView;
use Maatwebsite\Excel\Concerns\WithColumnFormatting;
use Maatwebsite\Excel\Concerns\WithColumnWidths;
use Maatwebsite\Excel\Concerns\WithTitle;
use PhpOffice\PhpSpreadsheet\Style\NumberFormat;

class HistorialSheetsExports implements fromView, WithTitle, WithColumnFormatting, WithColumnWidths
{
    protected $historial;

    public function __construct($historial)
    {
        $this->historial = $historial;
    }

    public function title(): string
    {
        return 'Historial de cambios';
    }

    public function view(): View
    {
        return view('exports.historial_cambios', [
            'items' => $this->historial
        ]);
    }

    public function columnFormats(): array
    {
        return [
          'D' => NumberFormat::FORMAT_NUMBER_COMMA_SEPARATED1,
          'E' => NumberFormat::FORMAT_NUMBER_COMMA_SEPARATED1,
          'F' => NumberFORMAT::FORMAT_NUMBER_COMMA_SEPARATED1,
        ];
    }

    public function columnWidths(): array
    {
        return [
            'D' => 18,
            'E' => 18,
            'F' => 16
        ];
    }
}

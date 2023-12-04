<?php

namespace App\Exports;

use App\Models\Helisa;
use Illuminate\Contracts\View\View;
use Maatwebsite\Excel\Concerns\FromView;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithStyles;
use PhpOffice\PhpSpreadsheet\Style\NumberFormat;
use Maatwebsite\Excel\Concerns\WithColumnFormatting;
use Maatwebsite\Excel\Concerns\WithColumnWidths;
use Maatwebsite\Excel\Concerns\WithMapping;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;

class HelisaExport implements FromView, WithStyles, WithColumnFormatting, WithColumnWidths
{   
    protected $data = [];

    function __construct($data) {
        $this->data = $data;
    }
     
    /**
    * @return \Illuminate\Support\Collection 
    */
    public function view(): View
    {   
        return view('exports.excel-helisa', [
            'registros_helisa' => $this->data['registros_helisa']
        ]);
    }

    public function headings(): array
    {
        
    }

    public function columnFormats(): array 
    {
        return [
            'G' => NumberFormat::FORMAT_NUMBER_COMMA_SEPARATED1,
            'H' => NumberFormat::FORMAT_NUMBER_COMMA_SEPARATED1,
            'L' => NumberFormat::FORMAT_NUMBER_COMMA_SEPARATED1,
            'O' => NumberFormat::FORMAT_NUMBER_COMMA_SEPARATED1,
        ];
    }

    public function styles(Worksheet $sheet)
    {
        return [
            // Style the first row as bold text.
            1 => ['font' => ['bold' => true]]
        ];
    }

    public function columnWidths(): array
    {
        return [
            'A' => 15,
            'B' => 15,
            'C' => 15,
            'D' => 38,
            'E' => 20,
            'F' => 38,
            'G' => 15,
            'H' => 15,
            'I' => 22,
            'J' => 18,
            'K' => 15,
            'L' => 20,
            'M' => 10,
            'N' => 10,
            'O' => 15
        ];
    }
}

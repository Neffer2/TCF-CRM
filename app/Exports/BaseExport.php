<?php

namespace App\Exports; 

use App\Models\Base_comercial;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithStyles;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;
use PhpOffice\PhpSpreadsheet\Style\NumberFormat;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\WithColumnFormatting;
use Maatwebsite\Excel\Concerns\WithColumnWidths;

class BaseExport implements FromCollection, WithHeadings, WithStyles, WithMapping, WithColumnFormatting, WithColumnWidths
{
    protected $filtros = [];

    function __construct($filtros) {
        $this->filtros = $filtros['filtros'];
    }

    /** 
    * @return \Illuminate\Support\Collection
    */ 
    public function collection()
    {   
        $registros_base = Base_comercial::where($this->filtros)->get(); 
        return $registros_base;
    }

    public function headings(): array
    {
        return ["FECHA", "CLIENTE", "PROYECTO", "COD_CC", "VALOR ORIGINAL", "VALOR", "COM_1", "COM_2", "ESTADO", "CUENTA", "INICIO", "FIN", "COMERCIAL"];
    }

    /** 
    * @var Invoice $invoice
    */
    public function map($invoice): array
    {       
        return [
            $invoice->fecha,
            $invoice->nom_cliente,
            $invoice->nom_proyecto,
            $invoice->cod_cc,
            $invoice->valor_original,
            $invoice->valor_proyecto,
            $invoice->com_1,
            $invoice->com_2,
            $invoice->estado_cuenta->description,
            $invoice->cuenta->description, 
            $invoice->fecha_inicio,
            $invoice->dura_mes,
            $invoice->comercial->name
        ];
    }

    public function styles(Worksheet $sheet)
    {
        return [
            // Style the first row as bold text.
            1 => ['font' => ['bold' => true]]
        ];
    }

    
    public function columnFormats(): array 
    {
        return [
            'E' => NumberFormat::FORMAT_NUMBER_COMMA_SEPARATED1,
            'F' => NumberFormat::FORMAT_NUMBER_COMMA_SEPARATED1
        ];
    }

    public function columnWidths(): array
    {
        return [
            'A' => 12,
            'B' => 25,
            'C' => 15,
            'D' => 10,
            'E' => 15,
            'F' => 15,
            'G' => 10,
            'H' => 10,
            'I' => 10,
            'J' => 10,
            'K' => 10,
            'L' => 10,
            'M' => 10
        ];
    }
}

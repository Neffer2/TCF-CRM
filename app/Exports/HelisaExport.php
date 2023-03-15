<?php

namespace App\Exports;

use App\Models\Helisa;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithStyles;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;

class HelisaExport implements FromCollection, WithHeadings, WithStyles
{
    /**
    * @return \Illuminate\Support\Collection
    */
    public function collection()
    {
        return Helisa::select('fecha', 'tipo_doc', 'num_doc', 'concepto', 'identidad', 'nom_tercero', 'centro', 'nom_centro_costo', 'debito', 'credito', 'comercial', 'participacion', 'base_factura', 'mes', 'año', 'comision')->get();
    }

    public function headings(): array
    {
        return ['FECHA', 'TIPO DOC', 'NUM DOC,', 'CONCEPTO', 'IDENTIDAD', 'NOMBRE TERCERO', 'CENTRO', 'NOMBRE CENTRO DE COSTO', 'DEBITO', 'CREDITO', 'COMERCIAL', 'PARTICIPACION', 'BASE FACTURA', 'MES', 'AÑO', 'COMISION'];
    }

    public function styles(Worksheet $sheet)
    {
        return [
            // Style the first row as bold text.
            1 => ['font' => ['bold' => true]]
        ];
    }
}

<?php

namespace App\Exports;

use App\Models\Base_comercial;
use Maatwebsite\Excel\Concerns\FromCollection;
use Illuminate\Support\Facades\Auth;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithStyles;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;

class BaseExport implements FromCollection, WithHeadings, WithStyles
{
    /**
    * @return \Illuminate\Support\Collection
    */
    public function collection()
    {
        return Base_comercial::select('fecha', 'nom_cliente', 'nom_proyecto', 'cod_cc', 'valor_proyecto', 'com_1', 'com_2', 'id_estado', 'fecha_inicio', 'dura_mes')->where('id_user', Auth::user()->id)->get();
    }

    public function headings(): array
    {
        return ["FECHA", "CLIENTE", "PROYECTO", "COD_CC", "VALOR", "COM_1", "COM_2", "ESTADO", "INICIO", "FIN"];
    }

    public function styles(Worksheet $sheet)
    {
        return [
            // Style the first row as bold text.
            1 => ['font' => ['bold' => true]]
        ];
    }
}

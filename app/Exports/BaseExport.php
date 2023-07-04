<?php

namespace App\Exports;

use App\Models\Base_comercial;
use Maatwebsite\Excel\Concerns\FromCollection;
use Illuminate\Support\Facades\Auth;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithStyles;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;
use Maatwebsite\Excel\Concerns\FromQuery;
use Maatwebsite\Excel\Concerns\WithMapping;

class BaseExport implements FromCollection, WithHeadings, WithStyles, WithMapping
{
    /** 
    * @return \Illuminate\Support\Collection
    */
    public function collection()
    {
        return Base_comercial::select('fecha', 'nom_cliente', 'nom_proyecto', 'cod_cc',
        'valor_original', 'valor_proyecto', 'com_1', 'com_2', 'id_estado', 'id_cuenta', 'fecha_inicio', 'dura_mes')
        ->where('id_user', Auth::user()->id)->get();
    }

    public function headings(): array
    {
        return ["FECHA", "CLIENTE", "PROYECTO", "COD_CC", "VALOR ORIGINAL", "VALOR", "COM_1", "COM_2", "ESTADO", "CUENTA", "INICIO", "FIN"];
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
            $invoice->dura_mes
        ];
    }

    public function styles(Worksheet $sheet)
    {
        return [
            // Style the first row as bold text.
            1 => ['font' => ['bold' => true]]
        ];
    }
}

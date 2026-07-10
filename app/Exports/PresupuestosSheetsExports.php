<?php

namespace App\Exports;

use Illuminate\Contracts\View\View;
use Maatwebsite\Excel\Concerns\FromView;
use Maatwebsite\Excel\Concerns\WithColumnWidths;
use Maatwebsite\Excel\Concerns\WithDrawings;
use Maatwebsite\Excel\Concerns\WithTitle;
use PhpOffice\PhpSpreadsheet\Exception;
use PhpOffice\PhpSpreadsheet\Worksheet\Drawing;
use PhpOffice\PhpSpreadsheet\Style\NumberFormat;
use Maatwebsite\Excel\Concerns\WithColumnFormatting;


class PresupuestosSheetsExports implements FromView, WithTitle, WithDrawings, WithColumnFormatting, WithColumnWidths
{
    protected $info;

    public function __construct($info)
    {
        $this->info = $info;
    }

    public function title(): string
    {
        return 'Presupuestos';
    }

    public function view(): View
    {
        return view('exports.excel', [
            'items' => $this->info['items'],
            'presto' => $this->info['presto'],
            'tipo' => $this->info['tipo'],
            'proveedores' => $this->info['proveedores'],

            'margenItems' => $this->info['margenItems'],
            'ventaProyecto' => $this->info['ventaProyecto'],
            'costosProyecto' => $this->info['costosProyecto'],
            'margenProyecto' => $this->info['margenProyecto'],
            'margenBruto' => $this->info['margenBruto'],


        ]);
    }

    /**
     * @throws Exception
     */
    public function drawings(): Drawing
    {
        $drawing = new Drawing();
        $drawing->setName('Logo');
        $drawing->setDescription('Bull marketing logo');
        $drawing->setPath(public_path('assets/img/bull-logo.png'));
        $drawing->setHeight(80);
        $drawing->setCoordinates('A1');

        return $drawing;
    }

    public function columnFormats(): array
    {
        return [
            // 'I' => NumberFormat::FORMAT_CURRENCY_EUR_SIMPLE,
            'K' => NumberFormat::FORMAT_NUMBER_COMMA_SEPARATED1,
            'L' => NumberFormat::FORMAT_NUMBER_COMMA_SEPARATED1,
            'M' => NumberFormat::FORMAT_NUMBER_COMMA_SEPARATED1,
            'N' => NumberFormat::FORMAT_NUMBER_COMMA_SEPARATED1,
            'O' => NumberFormat::FORMAT_NUMBER_COMMA_SEPARATED1,
            'B' => NumberFormat::FORMAT_NUMBER_COMMA_SEPARATED1,
        ];
    }

    public function columnWidths(): array
    {
        return [
            'K' => 18,
            'L' => 18,
            'B' => 24,
            'C' => 24,
        ];
    }

}


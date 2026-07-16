<?php

namespace app\Exports;

use Illuminate\Contracts\View\View;
use Maatwebsite\Excel\Concerns\FromView;
use Maatwebsite\Excel\Concerns\WithDrawings;
use Maatwebsite\Excel\Concerns\WithTitle;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use PhpOffice\PhpSpreadsheet\Exception;
use PhpOffice\PhpSpreadsheet\Worksheet\Drawing;

class PresupuestoSheet implements FromView, WithTitle, ShouldAutoSize, WithDrawings
{
    protected $payload;
    protected $titulo;


    public function __construct(array $payload, string $titulo){
        $this->payload = $payload;
        $this->titulo = $titulo;
    }

    public function view(): View{
        // Reutilizamos el mismo Blade de presupuesto que estructuramos antes
        return view('exports.historial_cambios', $this->payload);
    }

    public function title(): string{
        return $this->titulo;
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
}

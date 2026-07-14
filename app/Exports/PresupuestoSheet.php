<?php

namespace app\Exports;

use Illuminate\Contracts\View\View;
use Maatwebsite\Excel\Concerns\FromView;
use Maatwebsite\Excel\Concerns\WithTitle;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;

class PresupuestoSheet implements FromView, WithTitle, ShouldAutoSize
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
}

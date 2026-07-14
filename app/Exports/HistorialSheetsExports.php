<?php

namespace App\Exports;

use Maatwebsite\Excel\Concerns\WithMultipleSheets;

class HistorialSheetsExports implements WithMultipleSheets
{
    protected $payloadActual;
    protected $payloadHistoricos;

    public function __construct(array $payloadActual, array $payloadHistoricos)
    {
        $this->payloadActual = $payloadActual;
        $this->payloadHistoricos = $payloadHistoricos;
    }

    public function sheets(): array
    {
        $sheets = [];

        // 1. Hoja 1: El estado vivo/actual
        $sheets[] = new PresupuestoSheet($this->payloadActual, 'Presupuesto Actual');

        // 2. Hojas de historial dinámicas (V1, V2, V3...)
        foreach ($this->payloadHistoricos as $historico) {
            $sheets[] = new PresupuestoSheet($historico, $historico['titulo_pestana']);
        }

        return $sheets;
    }
}

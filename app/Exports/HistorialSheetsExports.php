<?php

namespace App\Exports;

use Illuminate\Contracts\View\View;
use Maatwebsite\Excel\Concerns\FromView;
use Maatwebsite\Excel\Concerns\WithTitle;

class HistorialSheetsExports implements fromView, WithTitle
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
}

<?php

namespace App\Services;

use App\Models\OrdenCompra;
use Dompdf\Dompdf;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\View;

class PdfService
{
    public function generarPdfOC(OrdenCompra $orden, $path) {
        $orden->load([
            'proveedor',
            'presupuesto',
            'presupuesto.presupuestoItems',
            'ordenItems'
        ]);

        $subtotal = 0;

        foreach ($orden->ordenItems as $item) {
            $subtotal += $item->vtotal_oc;
        }

        $dompdf = new Dompdf(['enable_remote' => true]);
        $html = View::make('exports.orden_compra_pdf', [
            'orden' => $orden,
            'subtotal' => $subtotal,
        ])->render();

        $dompdf->loadHtml($html);
        $dompdf->setPaper('A4', 'portrait');
        $dompdf->render();
        $output = $dompdf->output();
        $file_path = $path . "/orden_compra_" . $orden->cod_oc . ".pdf";
        Storage::put($file_path, $output);

        return $file_path;
    }
}

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

        $cod_oc = "OC" . $orden->id;

        $dompdf = new Dompdf(['enable_remote' => true]);
        $html = View::make('exports.orden_compra_pdf', [
            'orden' => $orden,
            'cod_oc' => $cod_oc,
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

    public function generarPdfCCO(OrdenCompra $orden, $path) {
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

        $cod_cuenta_cobro = "CC" . $orden->id;

        $dompdf = new Dompdf(['enable_remote' => true]);
        $html = View::make('exports.cuenta_cobro_pdf', [
            'orden' => $orden,
            'cod_cuenta_cobro' => $cod_cuenta_cobro,
            'subtotal' => $subtotal,
        ])->render();

        $dompdf->loadHtml($html);
        $dompdf->setPaper('A4', 'portrait');
        $dompdf->render();
        $output = $dompdf->output();
        $file_path = $path . "/cuenta_cobro_" . $orden->id . ".pdf";
        Storage::put($file_path, $output);

        return $file_path;
    }
}

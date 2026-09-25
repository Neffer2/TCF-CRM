<?php

namespace App\Services;

use App\Models\Anticipo;
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

    /**
     * Orden de anticipo de un anticipo jurídico aprobado: a quién se le anticipa, cuánto, sobre qué
     * base y quién lo aprobó (del recorrido en anticipo_pasos). Devuelve la ruta en storage.
     */
    public function generarPdfOrdenAnticipo(Anticipo $anticipo): string
    {
        $dompdf = new Dompdf();
        $dompdf->loadHtml($this->htmlOrdenAnticipo($anticipo));
        $dompdf->setPaper('A4', 'portrait');
        $dompdf->render();

        // Nombre no adivinable (la carpeta pública se sirve sin sesión); la versión anterior se reemplaza.
        $ruta = 'public/anticipos_juridicos/ordenes/orden_anticipo_'.$anticipo->cod_orden_anticipo.'_'.\Illuminate\Support\Str::random(20).'.pdf';
        Storage::put($ruta, $dompdf->output());
        if ($anticipo->archivo_orden_anticipo && $anticipo->archivo_orden_anticipo !== $ruta) {
            $anterior = $anticipo->archivo_orden_anticipo;
            // Solo cuando la aprobación queda guardada (si la transacción se deshace, el archivo viejo sigue siendo el vigente)
            \Illuminate\Support\Facades\DB::afterCommit(function () use ($anterior) { Storage::delete($anterior); });
        }
        return $ruta;
    }

    public function htmlOrdenAnticipo(Anticipo $anticipo): string
    {
        $anticipo->load(['proveedor', 'ordenCompra', 'presupuesto.gestion.contacto', 'productor_info', 'pasos.usuario']);

        // Solo cuenta la última ronda: si lo devolvieron y se reenvió, las aprobaciones anteriores ya no aplican
        $pasos = $anticipo->pasos->values();
        $inicio = null;
        foreach ($pasos as $i => $p) {
            if (in_array($p->accion, ['solicitar', 'reenviar'], true)) { $inicio = $i; }
        }
        $aprobaciones = $pasos->slice($inicio ?? 0)
            ->filter(fn ($p) => in_array($p->accion, ['aprobar_lider', 'aprobar_controller', 'aprobar_gerencia'], true))->values();

        $logo = public_path('assets/img/bull-logo.png');
        return View::make('exports.orden_anticipo_pdf', [
            'a' => $anticipo,
            'aprobaciones' => $aprobaciones,
            'solicitud' => $inicio === null ? null : $pasos[$inicio],
            'logo' => is_file($logo) ? 'data:image/png;base64,'.base64_encode(file_get_contents($logo)) : null,
        ])->render();
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
            'borrador' => false,
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

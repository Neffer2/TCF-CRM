<?php

namespace App\Exports;

use App\Models\Anticipo;
use Dompdf\Dompdf;
use Dompdf\Options;
use Illuminate\Support\Carbon;
use Symfony\Component\HttpFoundation\StreamedResponse;


class AnticipoOrdenes 
{
    /**
     * Genera y descarga el PDF de la orden de anticipo utilizando Dompdf nativo.
     *
     * @param int $anticipoId
     * @return StreamedResponse
     */
    public static function downloadPdf(int $anticipoId): StreamedResponse
    {
        // Cargar las relaciones según la estructura real de tu proyecto
        $anticipo = Anticipo::with([
            'ordenCompra.presupuesto.gestion.contacto',
            'ordenCompra.proveedor',
            'ordenCompra.ordenItems',
            'aprobaciones.usuario',
            'estado',
        ])->findOrFail($anticipoId);

        // Formateadores auxiliares
        $pesos = fn($valor) => '$ ' . number_format((float) ($valor ?? 0), 2, ',', '.');

        $fecha = function ($valor) {
            if (!$valor) {
                return '—';
            }
            return Carbon::parse($valor)->format('d/m/Y H:i');
        };

        // Extracción de entidades relacionadas a través del presupuesto
        $orden = $anticipo->ordenCompra;
        $proveedor = $orden?->proveedor;
        $presupuesto = $orden?->presupuesto;
        $gestion = $presupuesto?->gestion;

        // Mapeo de acciones/estados en las aprobaciones
        $puesto = [
            'APROBADO'   => 'Aprobado por',
            'RECHAZADO'  => 'Rechazado por',
            'REVISADO'   => 'Revisado por',
            'SOLICITADO' => 'Solicitado por'
        ];

        // Cálculo o lectura del valor base
        $valorBase = $orden && $orden->ordenItems 
            ? $orden->ordenItems->sum('vtotal_oc') 
            : ($anticipo->valor_base ?? $anticipo->total_anticipo);

        // Logo codificado en Base64 para Dompdf
        $logoPath = public_path('images/logo.png'); // Ajusta la ruta a tu logo real
        $logo = file_exists($logoPath) 
            ? 'data:image/png;base64,' . base64_encode(file_get_contents($logoPath)) 
            : null;

        // Primer paso de aprobación (solicitud)
        $solicitud = $anticipo->aprobaciones->where('orden', 1)->first();

        // Renderizar la vista a string
        $html = view('exports.orden_anticipo_pdf', [
            'a'           => $anticipo,
            'orden'       => $orden,
            'p'           => $proveedor,
            'gestion'     => $gestion,
            'presupuesto' => $presupuesto,
            'valorBase'   => $valorBase,
            'aprobaciones'=> $anticipo->aprobaciones,
            'solicitud'   => $solicitud,
            'puesto'      => $puesto,
            'pesos'       => $pesos,
            'fecha'       => $fecha,
            'logo'        => $logo,
        ])->render();

        // Configuración de Dompdf
        $options = new Options();
        $options->set('isRemoteEnabled', true);
        $options->set('isHtml5ParserEnabled', true);
        $options->set('defaultFont', 'DejaVu Sans');

        $dompdf = new Dompdf($options);
        $dompdf->loadHtml($html);
        $dompdf->setPaper('letter', 'portrait');
        $dompdf->render();

        $fileName = 'Orden_Anticipo_' . ($anticipo->cod_orden_anticipo ?? $anticipo->id) . '.pdf';

        return response()->streamDownload(
            fn() => print($dompdf->output()),
            $fileName,
            ['Content-Type' => 'application/pdf']
        );
    }
}
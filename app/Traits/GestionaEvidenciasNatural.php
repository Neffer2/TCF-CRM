<?php

namespace App\Traits;

use App\Models\OrdenCompra;
use Dompdf\Dompdf;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\View;
use Illuminate\Support\Str;

trait GestionaEvidenciasNatural
{
    public function validateEvidencia($estado)
    {
        if ($estado == 5) {
            $archivo = null;

            try {
                DB::transaction(function () use (&$archivo) {
                    $orden = OrdenCompra::lockForUpdate()->findOrFail($this->queriedOrden->id);

                    // Si ya tenía GR (ciclo de rechazo y reenvío), se conserva
                    if (empty($orden->gr)) {
                        $orden->gr = $orden->codigoGR();
                    }

                    // El PDF necesita el GR ya asignado
                    $archivo = $this->generarPdfCuentaCobro($orden);

                    $orden->archivo_cuenta_cobro = $archivo;
                    $orden->fecha_aprobacion     = now();
                    $orden->estado_id            = 5;
                    $orden->save();

                    $this->queriedOrden = $orden;
                    $this->gr       = $orden->gr;
                });
            } catch (\RuntimeException $e) {
                if ($archivo) Storage::disk('public')->delete($archivo);
                $this->addError('gr', $e->getMessage());
                return;
            } catch (\Throwable $e) {
                if ($archivo) Storage::disk('public')->delete($archivo);
                report($e);
                $this->addError('gr', 'No se pudo generar la cuenta de cobro. Intenta de nuevo.');
                //dd($e);
                return;
            }

            $this->ocNaturalRevisionContabilidad($this->queriedOrden);

            return redirect()->route('ordenes-compra')->with('success', 'Validación exitosa con código ' . $this->gr . '.');

        } elseif ($estado == 7) {
            $this->validate([
                'justificacion_rechazo' => 'required|string|max:255'
            ]);

            $this->queriedOrden->justificacion_rechazo = $this->justificacion_rechazo;
            $this->queriedOrden->estado_id = 7;
            $this->queriedOrden->update();

            $this->oc_evidencias_rechazadas($this->queriedOrden);
            if (Auth()->user()->rol == 1){
                $this->ocNaturalEvidenciasRechazadas($this->queriedOrden);
            }
        }

        if (Auth()->user()->rol == 1) {
            return redirect()->route('ordenes-compra')->with('success', 'Validación exitosa con codigo ' . $this->cod_oc . '.');
        } elseif (Auth()->user()->rol == 7) {
            return redirect()->route('ordenes-prod')->with('success', 'Validación exitosa');
        }
    }

    public function toggleRechazo()
    {
        $this->toggleRechazo = !$this->toggleRechazo;
    }

    protected function generarPdfCuentaCobro(OrdenCompra $orden): string
    {
        $orden->load([
            'naturalInfo.tercero',
            'ordenItems.itemPresupuesto.presto',
        ]);

        $items   = $orden->ordenItems;
        $tercero = $orden->naturalInfo->tercero;
        $total   = $items->sum('vtotal_oc');
        $fecha   = now()->locale('es')->translatedFormat('d \d\e F \d\e Y');

        $html = View::make('exports.cuenta-cobro', [
            'oc'       => $orden,
            'orden'    => $orden,
            'items'    => $items,
            'tercero'  => $tercero,
            'total'    => $total,
            'fecha'    => $fecha,
            'grNumero' => $orden->gr,
            'grFecha'  => now(),
            'preview'  => false,
        ])->render();

        $dompdf = new Dompdf(['isRemoteEnabled' => true]);
        $dompdf->loadHtml($html);
        $dompdf->setPaper('A4', 'portrait');
        $dompdf->render();

        $ruta = 'ordenes_naturales/cuenta_cobro_' . Str::slug($orden->gr) . '.pdf';
        Storage::disk('public')->put($ruta, $dompdf->output());

        return $ruta;
    }
}

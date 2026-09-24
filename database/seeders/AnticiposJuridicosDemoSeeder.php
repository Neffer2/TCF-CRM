<?php

namespace Database\Seeders;

use App\Models\Anticipo;
use App\Models\PresupuestoProyecto;
use App\Models\Proveedor;
use App\Models\User;
use App\Services\AnticipoJuridicoFlujo;
use App\Services\PdfService;
use Dompdf\Dompdf;
use Illuminate\Database\Seeder;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;

/**
 * Dos anticipos jurídicos de demostración para probar el flujo nuevo en LOCAL.
 *
 * Uso:  php artisan db:seed --class=AnticiposJuridicosDemoSeeder
 *
 * - SOLO corre en entorno local (APP_ENV=local) y necesita los usuarios prueba.* (UsuariosPruebaSeeder).
 * - Recorre el flujo real (App\Services\AnticipoJuridicoFlujo), con correos simulados si MAIL_MAILER=log:
 *     A) sin orden de compra, $30.000.000 × 40 % = $12.000.000: aprobado por líder, Controller y
 *        Gerencia; queda en Contabilidad con su orden de anticipo en PDF.
 *     B) sin orden de compra, $2.000.000 × 50 % = $1.000.000: queda esperando al líder.
 * - Se puede correr varias veces: si los anticipos ya existen no los repite, solo vuelve a crear los
 *   PDF que falten (por ejemplo, si la base se importó de un volcado sin la carpeta storage).
 */
class AnticiposJuridicosDemoSeeder extends Seeder
{
    const MARCA = 'DEMO:';

    public function run()
    {
        if (!app()->environment('local')) {
            $this->command->error('Este seeder es SOLO para entorno local (APP_ENV=local). Abortado.');
            return;
        }

        $u = fn ($email) => User::where('email', $email)->first();
        $prod = $u('prueba.productor@local.test');
        $lider = $u('prueba.lider@local.test');
        $ctrl = $u('prueba.controller@local.test');
        $ger = $u('prueba.gerencia@local.test');
        if (!$prod || !$lider || !$ctrl || !$ger) {
            $this->command->error('Faltan usuarios de prueba: corre primero php artisan db:seed --class=UsuariosPruebaSeeder');
            return;
        }

        $existentes = Anticipo::where('tipo', Anticipo::TIPO_JURIDICO)->where('productor_id', $prod->id)
            ->whereHas('pasos', fn ($q) => $q->where('accion', 'solicitar')->where('observacion', 'like', self::MARCA.'%'))
            ->orderBy('id')->get();
        if ($existentes->count() >= 2) {
            $this->reponerArchivos($existentes);
            $this->resumen($existentes);
            return;
        }

        $proveedor = Proveedor::where('tipo', 'juridica')->where('documento', '901043165')->first()
            ?? Proveedor::where('tipo', 'juridica')->orderBy('id')->first();
        $grande = PresupuestoProyecto::where('productor', $prod->id)->where('estado_id', 1)->whereNotNull('cod_cc')
            ->where('costos_proy', '>=', 30000000)->orderBy('id')->first();
        $pequeno = PresupuestoProyecto::where('productor', $prod->id)->where('estado_id', 1)->whereNotNull('cod_cc')
            ->where('costos_proy', '>=', 2000000)->where('id', '!=', optional($grande)->id)->orderBy('id')->first() ?? $grande;
        if (!$proveedor || !$grande) {
            $this->command->error('prueba.productor necesita un proyecto aprobado con costo de al menos $30.000.000 (UsuariosPruebaSeeder le asigna centros de costo).');
            return;
        }

        $f = new AnticipoJuridicoFlujo;
        $a = $f->solicitar($prod, ['modo' => 'borrador', 'presupuesto_id' => $grande->id, 'proveedor_id' => $proveedor->id,
            'valor_base' => 30000000, 'porcentaje' => 40,
            'observacion' => self::MARCA.' el proveedor pide el anticipo para reservar la fecha del evento.'], [$this->pdf('cotizacion-demo-1.pdf')]);
        $a = $f->aprobarLider($a, $lider, 'Cotización revisada; el proveedor es el de la tarifa del evento.');
        $a = $f->aprobarController($a, $ctrl);
        $a = $f->aprobarGerencia($a, $ger, 'Aprobado: el evento lo exige y el cliente ya pagó el primer hito.');

        $b = $f->solicitar($prod, ['modo' => 'borrador', 'presupuesto_id' => $pequeno->id, 'proveedor_id' => $proveedor->id,
            'valor_base' => 2000000, 'porcentaje' => 50,
            'observacion' => self::MARCA.' anticipo pequeño para probar el camino sin Gerencia.'], [$this->pdf('cotizacion-demo-2.pdf')]);

        $this->resumen(collect([$a->fresh(), $b->fresh()]));
    }

    /** Un PDF de cotización de mentiras, como archivo subido. */
    private function pdf(string $nombre): UploadedFile
    {
        $tmp = tempnam(sys_get_temp_dir(), 'cot').'.pdf';
        file_put_contents($tmp, $this->contenidoPdf($nombre));
        return new UploadedFile($tmp, $nombre, 'application/pdf', null, true);
    }

    private function contenidoPdf(string $nombre): string
    {
        $d = new Dompdf();
        $d->loadHtml('<h2>'.e($nombre).'</h2><p>Documento de demostración para las pruebas del CRM. No es una cotización real.</p>');
        $d->render();
        return $d->output();
    }

    /** Si la base viene de un volcado, los archivos de storage pueden no existir: se vuelven a generar. */
    private function reponerArchivos($anticipos): void
    {
        foreach ($anticipos as $a) {
            foreach ($a->evidencias as $ev) {
                if ($ev->foto_evidencia && !Storage::exists($ev->foto_evidencia)) {
                    Storage::put($ev->foto_evidencia, $this->contenidoPdf($ev->observacion_evidencia ?: 'soporte.pdf'));
                }
            }
            if ($a->archivo_orden_anticipo && !Storage::exists($a->archivo_orden_anticipo)) {
                app(PdfService::class)->generarPdfOrdenAnticipo($a);
            }
        }
    }

    private function resumen($anticipos): void
    {
        foreach ($anticipos as $a) {
            $this->command->info(sprintf('Anticipo jurídico #%d · $%s · %s%s', $a->id, number_format((float) $a->total_anticipo, 0, ',', '.'),
                optional($a->estado)->description, $a->cod_orden_anticipo ? ' · '.$a->cod_orden_anticipo : ''));
        }
    }
}

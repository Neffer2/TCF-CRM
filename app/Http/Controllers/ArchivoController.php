<?php

namespace App\Http\Controllers;

use App\Models\OrdenCompra;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;

/**
 * Sirve los archivos subidos (storage/app/public) exigiendo sesión. Antes se servían directamente en
 * /storage sin iniciar sesión: cédulas, RUT, certificaciones bancarias, contratos, firmas y comprobantes.
 * El tercero del portal (sin cuenta) solo ve los documentos de su propia orden.
 */
class ArchivoController extends Controller
{
    /** Carpetas con datos personales de terceros: no las ven los roles comerciales (2 y 5). */
    const CARPETAS_TERCEROS = ['ruts', 'copia_cedula', 'cert_bancarias', 'planillas_aportes', 'contratos', 'art383', 'firmas_terceros'];

    public function ver(string $ruta)
    {
        // Sin salir de la carpeta pública del almacenamiento
        $ruta = ltrim(str_replace(['\\', "\0"], '/', $ruta), '/');
        abort_if($ruta === '' || str_contains($ruta, '..'), 404);
        $completa = 'public/'.$ruta;
        abort_unless(Storage::exists($completa), 404);

        $user = Auth::user();
        if ($user) {
            abort_if((int) $user->rol === 4, 403);
            $carpeta = explode('/', $ruta)[0];
            abort_if(in_array((int) $user->rol, [2, 5], true) && in_array($carpeta, self::CARPETAS_TERCEROS, true), 403);
        } else {
            abort_unless($this->permitidoParaPortal($ruta), 403);
        }

        return response()->file(Storage::path($completa), ['Cache-Control' => 'private, max-age=0, no-store']);
    }

    /** El tercero del portal (enlace firmado en sesión) solo abre los archivos de su orden. */
    private function permitidoParaPortal(string $ruta): bool
    {
        $ordenId = session('portal_terceros_orden');
        if (!$ordenId) { return false; }
        $orden = OrdenCompra::with(['naturalInfo.tercero', 'evidencias'])->find($ordenId);
        if (!$orden || !$orden->naturalInfo) { return false; }
        $t = $orden->naturalInfo->tercero;
        $permitidos = [
            $orden->naturalInfo->contrato, $orden->archivo_cuenta_cobro, $orden->archivo_orden_helisa,
            optional($t)->rut, optional($t)->copia_cedula, optional($t)->cert_bancaria, optional($t)->planilla_aportes,
            optional($t)->art383, optional($t)->firma,
        ];
        foreach ($orden->evidencias as $e) { $permitidos[] = $e->foto ?? $e->foto_evidencia ?? null; }
        $normal = fn ($p) => ltrim(preg_replace('#^(public/|storage/)#', '', (string) $p), '/');
        return in_array($ruta, array_map($normal, array_filter($permitidos)), true);
    }
}

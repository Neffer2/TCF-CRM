<?php

namespace App\Http\Middleware;

use App\Models\ActividadLog;
use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

/**
 * Registra en actividad_log cada petición de un usuario autenticado:
 *  - páginas (GET) con su ruta;
 *  - acciones de Livewire (POST /livewire/message/...) con el componente,
 *    el método llamado, sus parámetros y los campos escritos;
 *  - otros POST (formularios) con sus datos.
 * Se escribe DESPUÉS de responder (terminate) para no frenar la página.
 */
class RegistrarActividad
{
    public function handle(Request $request, Closure $next)
    {
        $request->attributes->set('crm_inicio', microtime(true));
        return $next($request);
    }

    public function terminate(Request $request, $response)
    {
        if (!Auth::check()) { return; }
        $ruta = '/'.ltrim($request->path(), '/');
        if (preg_match('#^/(livewire/livewire\.js|assets|storage|_debugbar|favicon)#', $ruta)) { return; }

        $ms = (int) round((microtime(true) - ($request->attributes->get('crm_inicio') ?: microtime(true))) * 1000);
        $base = ['metodo' => $request->method(), 'ruta' => mb_substr($ruta, 0, 255), 'estado_http' => method_exists($response, 'getStatusCode') ? $response->getStatusCode() : null, 'duracion_ms' => $ms];

        // Acciones de Livewire: componente + métodos llamados + campos sincronizados
        if (str_starts_with($ruta, '/livewire/message/')) {
            $componente = substr($ruta, strlen('/livewire/message/'));
            $updates = (array) $request->input('updates', []);
            $llamadas = []; $campos = [];
            foreach ($updates as $u) {
                $tipo = $u['type'] ?? '';
                if ($tipo === 'callMethod') {
                    $m = $u['payload']['method'] ?? '?'; $params = $u['payload']['params'] ?? [];
                    if (in_array($m, ['$set', '$toggle'], true)) { $campos[$params[0] ?? '?'] = $params[1] ?? null; }
                    else { $llamadas[] = ['metodo' => $m, 'params' => ActividadLog::limpiar($params)]; }
                } elseif ($tipo === 'syncInput') {
                    $campos[$u['payload']['name'] ?? '?'] = ActividadLog::limpiar($u['payload']['value'] ?? null);
                } elseif ($tipo === 'fireEvent') {
                    $llamadas[] = ['evento' => $u['payload']['event'] ?? '?', 'params' => ActividadLog::limpiar($u['payload']['params'] ?? [])];
                }
            }
            if (!$llamadas && !$campos) { return; }   // p. ej. solo un re-render
            $accion = $componente.($llamadas ? '.'.implode(',', array_map(fn($l) => $l['metodo'] ?? $l['evento'], $llamadas)) : ' (edita campos)');
            $detalle = array_filter(['llamadas' => $llamadas, 'campos' => ActividadLog::limpiar($campos)]);
            ActividadLog::registrar('accion', $accion, $base + ['detalle' => $detalle ?: null, 'objeto' => $this->objetoDe($request)]);
            return;
        }

        if ($request->isMethod('GET')) {
            if ($request->ajax() || $request->wantsJson()) { return; }
            ActividadLog::registrar('pagina', $ruta, $base + ['detalle' => $request->query() ? ActividadLog::limpiar($request->query()) : null]);
            return;
        }

        // Formularios clásicos (logout, cambio de rol, subida de base, etc.)
        if ($ruta === '/logout') { return; } // lo registra el listener de sesión
        ActividadLog::registrar('accion', $ruta, $base + ['detalle' => ActividadLog::limpiar($request->except(['_token', '_method', 'password', 'password_confirmation'])) ?: null]);
    }

    /** Referencia al registro de la pantalla (p. ej. la orden/presupuesto en la URL de donde viene la acción). */
    private function objetoDe(Request $request): ?string
    {
        $ref = (string) $request->headers->get('referer');
        if (preg_match('#/(presupuesto|orden-juridica|orden-natural|orden-compra-natural|ordenes-nomina-prod|anticipo-admin|anticipo-lid|anticipo-prod|detalle-anticipo-contabilidad|detalle-anticipo-tesoreria|consumido|update-gestion-comercial|firmar-remision)/(\d+)#', $ref, $m)) {
            return $m[1].':'.$m[2];
        }
        return null;
    }
}

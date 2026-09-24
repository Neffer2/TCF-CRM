<?php

namespace App\Services;

use App\Models\Anticipo;
use App\Models\AnticipoPaso;
use App\Models\EvidenciaAnticipo;
use App\Models\OrdenCompra;
use App\Models\Permiso;
use App\Models\PresupuestoProyecto;
use App\Models\Proveedor;
use App\Models\User;
use App\Traits\Email;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\ValidationException;

/**
 * Flujo del anticipo jurídico (A.J.) según la matriz de flujos de Gerencia (sep-2026):
 *
 *   Productor ─► Líder de producción ─► Controller ─┬─ total ≤ umbral ─► Aprobado
 *                                                   └─ total > umbral ─► Gerencia ─► Aprobado
 *   Aprobado (se genera la orden de anticipo) ─► Contabilidad causa ─► Tesorería paga
 *
 * - Líder, Controller y Gerencia pueden devolverlo al productor con una observación; el productor
 *   lo corrige y vuelve a empezar por el líder.
 * - Contabilidad puede devolverlo al Controller, que lo vuelve a aprobar o lo devuelve al productor.
 * - Del diagrama anterior se conserva el borrador: un A.J. sin orden de compra previa, sobre un
 *   valor base que digita el productor.
 *
 * Aquí viven TODAS las reglas (quién puede hacer qué y desde qué estado); las pantallas solo
 * llaman a este servicio. Cada decisión queda en anticipo_pasos y los correos salen después de
 * guardar.
 */
class AnticipoJuridicoFlujo
{
    use Email;

    const APROBADO = 1;
    const CAUSADO = 5;
    const REV_LIDER = 8;
    const REV_GERENCIA = 9;
    const RECH_LIDER = 11;
    const RECH_GERENCIA = 12;
    const RECH_CONTABILIDAD = 13;
    const PAGADO = 14;
    const REV_CONTROLLER = 15;
    const RECH_CONTROLLER = 16;

    /** Devueltos al productor: los puede corregir y reenviar. */
    const CORREGIBLES = [self::RECH_LIDER, self::RECH_GERENCIA, self::RECH_CONTROLLER];

    /** acción => [quién la hace, estados desde los que se permite] */
    const REGLAS = [
        'reenviar'              => ['productor',    self::CORREGIBLES],
        'aprobar_lider'         => ['lider',        [self::REV_LIDER]],
        'rechazar_lider'        => ['lider',        [self::REV_LIDER]],
        'aprobar_controller'    => ['controller',   [self::REV_CONTROLLER, self::RECH_CONTABILIDAD]],
        'rechazar_controller'   => ['controller',   [self::REV_CONTROLLER, self::RECH_CONTABILIDAD]],
        'aprobar_gerencia'      => ['gerencia',     [self::REV_GERENCIA]],
        'rechazar_gerencia'     => ['gerencia',     [self::REV_GERENCIA]],
        'causar'                => ['contabilidad', [self::APROBADO]],
        'devolver_contabilidad' => ['contabilidad', [self::APROBADO]],
        'pagar'                 => ['tesoreria',    [self::CAUSADO]],
    ];

    /** Texto de cada acción en el recorrido. */
    const TEXTOS = [
        'solicitar'             => 'Solicitó el anticipo',
        'reenviar'              => 'Corrigió y volvió a enviar',
        'aprobar_lider'         => 'Aprobó',
        'rechazar_lider'        => 'Lo devolvió al productor',
        'aprobar_controller'    => 'Aprobó',
        'rechazar_controller'   => 'Lo devolvió al productor',
        'aprobar_gerencia'      => 'Aprobó',
        'rechazar_gerencia'     => 'Lo devolvió al productor',
        'causar'                => 'Lo causó',
        'devolver_contabilidad' => 'Lo devolvió al Controller',
        'pagar'                 => 'Registró el pago',
    ];

    /** Quién hace cada paso, en palabras. */
    const ACTORES = [
        'productor' => 'Productor', 'lider' => 'Líder de producción', 'controller' => 'Controller',
        'gerencia' => 'Gerencia', 'contabilidad' => 'Contabilidad', 'tesoreria' => 'Tesorería',
    ];

    const TIPOS_SOPORTE = 'pdf,jpg,jpeg,png';
    const MAX_SOPORTE_KB = 10240;

    public static function umbral(): float
    {
        return (float) config('crm.anticipos.juridico_umbral_gerencia', 10000000);
    }

    public static function requiereGerencia($total): bool
    {
        return (float) $total > self::umbral();
    }

    /**
     * Valor en pesos digitado a mano: "12.500.000", "12,500,000", "$ 12.500.000,50" o 12500000.
     * Un separador seguido de 1 o 2 dígitos al final son centavos; los demás separan miles.
     */
    public static function pesos($v): float
    {
        if (is_int($v) || is_float($v)) { return (float) $v; }
        $v = preg_replace('/[^\d.,]/', '', (string) $v);
        if ($v === '') { return 0.0; }
        if (preg_match('/^(.*)[.,](\d{1,2})$/', $v, $m)) {
            return (float) (preg_replace('/\D/', '', $m[1]).'.'.$m[2]);
        }
        return (float) preg_replace('/\D/', '', $v);
    }

    /* ==================================================================== */
    /*  Permisos                                                            */
    /* ==================================================================== */

    /** ¿$u ocupa el puesto $actor con su rol activo? (Gerencia es un permiso, no un rol activo). */
    public static function es(User $u, string $actor): bool
    {
        switch ($actor) {
            case 'productor':    return (int) $u->rol === 7;
            case 'lider':        return (int) $u->rol === 6;
            case 'controller':   return (int) $u->rol === 11;
            case 'gerencia':     return Permiso::tiene($u, 'revisar-anticipos-gerencia');
            case 'contabilidad': return (int) $u->rol === 9;
            case 'tesoreria':    return (int) $u->rol === 8;
        }
        return false;
    }

    /** Por qué $u no puede hacer $accion ahora, o null si puede. */
    public static function impedimento(Anticipo $a, User $u, string $accion): ?string
    {
        if (!$a->esJuridico()) { return 'Este anticipo no es jurídico.'; }
        if (!isset(self::REGLAS[$accion])) { return 'Acción no permitida.'; }
        [$actor, $desde] = self::REGLAS[$accion];

        if (!self::es($u, $actor)) {
            return 'Este paso le corresponde a '.self::ACTORES[$actor].'.';
        }
        if ($actor === 'productor' && (int) $a->productor_id !== (int) $u->id) {
            return 'Solo el productor que lo solicitó puede corregirlo.';
        }
        // Nadie revisa su propia solicitud (una cuenta con varios roles podría hacerlo)
        if (in_array($actor, ['lider', 'controller', 'gerencia'], true) && (int) $a->productor_id === (int) $u->id) {
            return 'No puedes revisar un anticipo que tú mismo solicitaste.';
        }
        if (!in_array((int) $a->estado_id, $desde, true)) {
            return 'El anticipo ya no está en un estado que permita esta acción ('.optional($a->estado)->description.').';
        }
        if ($accion === 'pagar' && $a->comprobante_pago) {
            return 'Este anticipo ya tiene comprobante de pago.';
        }
        return null;
    }

    /** Acciones que $u puede hacer ahora sobre $a (para mostrar solo los botones que aplican). */
    public static function acciones(Anticipo $a, User $u): array
    {
        return array_values(array_filter(array_keys(self::REGLAS), fn ($acc) => self::impedimento($a, $u, $acc) === null));
    }

    /** Quién tiene el anticipo en este momento (para decirlo en pantalla y en las listas). */
    public static function enManosDe(Anticipo $a): ?string
    {
        switch ((int) $a->estado_id) {
            case self::REV_LIDER:         return 'lider';
            case self::REV_CONTROLLER:
            case self::RECH_CONTABILIDAD: return 'controller';
            case self::REV_GERENCIA:      return 'gerencia';
            case self::APROBADO:          return 'contabilidad';
            case self::CAUSADO:           return 'tesoreria';
            case self::RECH_LIDER:
            case self::RECH_GERENCIA:
            case self::RECH_CONTROLLER:   return 'productor';
        }
        return null;
    }

    /* ==================================================================== */
    /*  Solicitud y corrección (productor)                                  */
    /* ==================================================================== */

    /** Órdenes jurídicas aprobadas del productor que todavía no tienen anticipo. */
    public static function ordenesDisponibles(User $u)
    {
        $desde = optional(\App\Models\Año::orderBy('description', 'desc')->first())->description;
        return OrdenCompra::with(['proveedor:id,tercero,documento', 'presupuesto:id,cod_cc,id_gestion,productor', 'presupuesto.gestion:id,nom_proyecto_cot'])
            ->withSum('ordenItems as valor_orden', 'vtotal_oc')
            ->whereHas('presupuesto', fn ($p) => $p->where('productor', $u->id))
            ->where('tipo_oc', 1)
            ->where('estado_id', 1)
            ->when($desde, fn ($q) => $q->where('created_at', '>=', $desde.'-01-01'))
            ->whereDoesntHave('anticipos', fn ($q) => $q->where('estado_id', '!=', 6))
            ->orderByDesc('created_at')
            ->get();
    }

    /** Presupuestos aprobados (con centro de costos) del productor, para el borrador sin orden. */
    public static function presupuestosDisponibles(User $u)
    {
        return PresupuestoProyecto::with('gestion:id,nom_proyecto_cot')
            ->where('productor', $u->id)
            ->where('estado_id', 1)
            ->whereNotNull('cod_cc')
            ->orderByDesc('id')
            ->get(['id', 'cod_cc', 'id_gestion', 'costos_proy']);
    }

    /**
     * Datos que se guardan a partir de lo que digitó el productor, ya validados.
     * $d: modo ('oc'|'borrador'), oc_id, presupuesto_id, proveedor_id, valor_base, porcentaje.
     */
    private function datosValidados(User $u, array $d, ?Anticipo $actual = null): array
    {
        $errores = [];
        $modo = $d['modo'] ?? 'oc';
        $pct = $d['porcentaje'] ?? null;
        if (!is_numeric($pct) || $pct < 1 || $pct > 100) {
            $errores['porcentaje'] = 'El porcentaje debe estar entre 1 y 100.';
        }

        if ($modo === 'oc') {
            $orden = OrdenCompra::withSum('ordenItems as valor_orden', 'vtotal_oc')->find($d['oc_id'] ?? null);
            if (!$orden) {
                $errores['oc_id'] = 'Elige la orden de compra.';
            } elseif ($actual && (int) $actual->oc_id === (int) $orden->id) {
                // Corrección de un anticipo que ya tenía esta orden: basta con que no esté anulada
                if ((int) $orden->estado_id === 6) {
                    $errores['oc_id'] = 'La orden de compra fue anulada: este anticipo ya no se puede reenviar.';
                }
            } elseif ((int) $orden->tipo_oc !== 1 || (int) $orden->estado_id !== 1
                || (int) optional($orden->presupuesto)->productor !== (int) $u->id) {
                $errores['oc_id'] = 'Esa orden no está disponible: debe ser jurídica, aprobada y de un proyecto tuyo.';
            } elseif (Anticipo::where('oc_id', $orden->id)->where('estado_id', '!=', 6)->exists()) {
                $errores['oc_id'] = 'Esa orden ya tiene un anticipo.';
            }
            if ($errores) { throw ValidationException::withMessages($errores); }

            $base = round((float) $orden->valor_orden, 2);
            if ($base <= 0) { throw ValidationException::withMessages(['oc_id' => 'La orden no tiene valor.']); }
            return [
                'oc_id' => $orden->id,
                'presupuesto_id' => $orden->presupuesto_id,
                'proveedor_id' => $orden->proveedor_id,
                'valor_base' => $base,
                'porcentaje_anticipo' => round((float) $pct, 2),
                'total_anticipo' => round($base * $pct / 100, 2),
            ];
        }

        // Borrador: sin orden de compra previa
        $presupuesto = PresupuestoProyecto::find($d['presupuesto_id'] ?? null);
        if (!$presupuesto || (int) $presupuesto->productor !== (int) $u->id || (int) $presupuesto->estado_id !== 1 || !$presupuesto->cod_cc) {
            $errores['presupuesto_id'] = 'Elige uno de tus proyectos aprobados (con centro de costos).';
        }
        $proveedor = Proveedor::find($d['proveedor_id'] ?? null);
        if (!$proveedor) {
            $errores['proveedor_id'] = 'Elige el proveedor.';
        } elseif ($proveedor->tipo !== 'juridica') {
            $errores['proveedor_id'] = 'El anticipo jurídico es para proveedores con NIT (persona jurídica).';
        }
        $base = self::pesos($d['valor_base'] ?? null);
        if ($base <= 0) {
            $errores['valor_base'] = 'Escribe el valor sobre el que se calcula el anticipo (por ejemplo, el de la cotización).';
        } elseif ($presupuesto && $base > (float) $presupuesto->costos_proy) {
            $errores['valor_base'] = 'El valor base no puede superar el costo total del proyecto ($'.number_format((float) $presupuesto->costos_proy, 0, ',', '.').').';
        }
        if ($errores) { throw ValidationException::withMessages($errores); }

        return [
            'oc_id' => null,
            'presupuesto_id' => $presupuesto->id,
            'proveedor_id' => $proveedor->id,
            'valor_base' => round($base, 2),
            'porcentaje_anticipo' => round((float) $pct, 2),
            'total_anticipo' => round($base * $pct / 100, 2),
        ];
    }

    private function validarArchivos(array $archivos): void
    {
        foreach ($archivos as $i => $f) {
            $ext = strtolower($f->getClientOriginalExtension() ?: $f->extension());
            if (!in_array($ext, explode(',', self::TIPOS_SOPORTE), true)) {
                throw ValidationException::withMessages(['soportes' => 'Los soportes deben ser PDF o imagen (JPG, PNG).']);
            }
            if ($f->getSize() > self::MAX_SOPORTE_KB * 1024) {
                throw ValidationException::withMessages(['soportes' => 'Cada soporte puede pesar máximo 10 MB.']);
            }
        }
    }

    private function guardarSoportes(Anticipo $a, array $archivos): void
    {
        foreach ($archivos as $f) {
            EvidenciaAnticipo::create([
                'anticipo_id' => $a->id,
                'item_id' => null,
                'fecha_evidencia' => now()->toDateString(),
                'foto_evidencia' => $f->store('public/anticipos_juridicos/soportes'),
                'observacion_evidencia' => mb_substr($f->getClientOriginalName(), 0, 255),
            ]);
        }
    }

    /** El productor solicita un A.J. nuevo: queda en revisión del líder de producción. */
    public function solicitar(User $u, array $d, array $soportes = []): Anticipo
    {
        if (!self::es($u, 'productor')) {
            throw ValidationException::withMessages(['general' => 'Solo un productor puede solicitar anticipos.']);
        }
        $datos = $this->datosValidados($u, $d);
        if (!$datos['oc_id'] && !$soportes) {
            throw ValidationException::withMessages(['soportes' => 'Sin orden de compra hay que adjuntar al menos un soporte (la cotización del proveedor).']);
        }
        $this->validarArchivos($soportes);

        $a = DB::transaction(function () use ($u, $d, $datos, $soportes) {
            $a = Anticipo::create($datos + [
                'tipo' => Anticipo::TIPO_JURIDICO,
                'estado_id' => self::REV_LIDER,
                'fecha_solicitud' => now(),
                'productor_id' => $u->id,
            ]);
            $this->guardarSoportes($a, $soportes);
            $this->registrarPaso($a, $u, 'solicitar', null, self::REV_LIDER, $d['observacion'] ?? null);
            return $a;
        });

        $this->ajRevisionLider($a->fresh());
        return $a;
    }

    /** El productor corrige un A.J. devuelto y lo reenvía: vuelve a empezar por el líder. */
    public function reenviar(Anticipo $a, User $u, array $d, array $soportes = []): Anticipo
    {
        $this->exigir($a, $u, 'reenviar');
        $d['modo'] = $a->oc_id ? 'oc' : 'borrador';
        if ($a->oc_id) { $d['oc_id'] = $a->oc_id; }
        else { $d['presupuesto_id'] = $a->presupuesto_id; }
        // Todos los errores de una vez: los de los datos y los de la corrección
        $errores = [];
        try { $datos = $this->datosValidados($u, $d, $a); }
        catch (ValidationException $e) { $errores = array_map(fn ($m) => $m[0], $e->errors()); }
        if (!$a->oc_id && !$soportes && !$a->evidencias()->exists()) {
            $errores['soportes'] = 'Sin orden de compra hay que adjuntar al menos un soporte (la cotización del proveedor).';
        }
        if (!trim((string) ($d['observacion'] ?? ''))) {
            $errores['observacion'] = 'Cuéntale a los revisores qué corregiste.';
        }
        if ($errores) { throw ValidationException::withMessages($errores); }
        $this->validarArchivos($soportes);

        $a = DB::transaction(function () use ($a, $u, $d, $datos, $soportes) {
            $a = Anticipo::lockForUpdate()->findOrFail($a->id);
            $this->exigir($a, $u, 'reenviar');
            $desde = (int) $a->estado_id;
            $a->fill($datos);
            $a->estado_id = self::REV_LIDER;
            $a->fecha_solicitud = now();
            $a->save();
            $this->guardarSoportes($a, $soportes);
            $this->registrarPaso($a, $u, 'reenviar', $desde, self::REV_LIDER, $d['observacion']);
            return $a;
        });

        $this->ajRevisionLider($a->fresh());
        return $a;
    }

    /** El productor quita un soporte mientras puede corregir. */
    public function quitarSoporte(Anticipo $a, User $u, int $evidenciaId): void
    {
        $this->exigir($a, $u, 'reenviar');
        $e = EvidenciaAnticipo::where('anticipo_id', $a->id)->find($evidenciaId);
        if (!$e) { throw ValidationException::withMessages(['soportes' => 'Ese soporte no existe.']); }
        Storage::delete($e->foto_evidencia);
        $e->delete();
    }

    /* ==================================================================== */
    /*  Revisiones                                                          */
    /* ==================================================================== */

    public function aprobarLider(Anticipo $a, User $u, ?string $obs = null): Anticipo
    {
        $a = $this->mover($a, $u, 'aprobar_lider', self::REV_CONTROLLER, $obs, false);
        $this->ajRevisionController($a);
        return $a;
    }

    public function rechazarLider(Anticipo $a, User $u, ?string $obs): Anticipo
    {
        $a = $this->mover($a, $u, 'rechazar_lider', self::RECH_LIDER, $obs, true);
        $this->ajRechazoLider($a);
        return $a;
    }

    /**
     * El Controller aprueba. Desde su revisión: por encima del umbral pasa a Gerencia; si no,
     * queda aprobado. Si Contabilidad se lo había devuelto, vuelve aprobado a Contabilidad
     * (el valor no cambió y Gerencia, si aplicaba, ya lo aprobó).
     */
    public function aprobarController(Anticipo $a, User $u, ?string $obs = null): Anticipo
    {
        $devueltoPorContabilidad = (int) $a->estado_id === self::RECH_CONTABILIDAD;
        $hasta = (!$devueltoPorContabilidad && self::requiereGerencia($a->total_anticipo)) ? self::REV_GERENCIA : self::APROBADO;
        $a = $this->mover($a, $u, 'aprobar_controller', $hasta, $obs, $devueltoPorContabilidad,
            fn (Anticipo $a) => $hasta === self::APROBADO && !$devueltoPorContabilidad ? $this->aprobar($a) : null,
            $devueltoPorContabilidad ? [self::RECH_CONTABILIDAD] : [self::REV_CONTROLLER]);

        if ($hasta === self::REV_GERENCIA) { $this->ajRevisionGerencia($a); }
        else { $this->ajAprobado($a); }
        return $a;
    }

    public function rechazarController(Anticipo $a, User $u, ?string $obs): Anticipo
    {
        $a = $this->mover($a, $u, 'rechazar_controller', self::RECH_CONTROLLER, $obs, true);
        $this->ajRechazoController($a);
        return $a;
    }

    public function aprobarGerencia(Anticipo $a, User $u, ?string $obs = null): Anticipo
    {
        $a = $this->mover($a, $u, 'aprobar_gerencia', self::APROBADO, $obs, false, fn (Anticipo $a) => $this->aprobar($a));
        $this->ajAprobado($a);
        return $a;
    }

    public function rechazarGerencia(Anticipo $a, User $u, ?string $obs): Anticipo
    {
        $a = $this->mover($a, $u, 'rechazar_gerencia', self::RECH_GERENCIA, $obs, true);
        $this->ajRechazoGerencia($a);
        return $a;
    }

    /* ==================================================================== */
    /*  Contabilidad y tesorería                                            */
    /* ==================================================================== */

    public function causar(Anticipo $a, User $u, $codigo, ?string $obs = null): Anticipo
    {
        $codigo = trim((string) $codigo);
        if ($codigo === '' || !ctype_digit($codigo)) {
            throw ValidationException::withMessages(['cod_causal' => 'Escribe el número de la causación (solo dígitos).']);
        }
        $a = $this->mover($a, $u, 'causar', self::CAUSADO, $obs, false, function (Anticipo $a) use ($codigo, $obs) {
            $a->cod_causal = $codigo;
            $a->observacion_causal = $obs;
            $a->fecha_causal = now();
        });
        $this->anticipoCausado($a);
        return $a;
    }

    public function devolverContabilidad(Anticipo $a, User $u, ?string $obs): Anticipo
    {
        $a = $this->mover($a, $u, 'devolver_contabilidad', self::RECH_CONTABILIDAD, $obs, true, function (Anticipo $a) use ($obs) {
            $a->observaciones_contabilidad = $obs;
            $a->cod_causal = null;
        });
        $this->ajRechazoContabilidad($a);
        return $a;
    }

    /** Tesorería sube el comprobante: el anticipo queda pagado. */
    public function pagar(Anticipo $a, User $u, $comprobante, ?string $obs = null): Anticipo
    {
        if (!$comprobante) {
            throw ValidationException::withMessages(['comprobante' => 'Adjunta el comprobante de pago en PDF.']);
        }
        $ext = strtolower($comprobante->getClientOriginalExtension() ?: $comprobante->extension());
        if ($ext !== 'pdf' || $comprobante->getSize() > self::MAX_SOPORTE_KB * 1024) {
            throw ValidationException::withMessages(['comprobante' => 'El comprobante debe ser un PDF de máximo 10 MB.']);
        }
        $this->exigir($a, $u, 'pagar');
        $ruta = $comprobante->store('public/anticipos');
        try {
            $a = $this->mover($a, $u, 'pagar', self::PAGADO, $obs, false, function (Anticipo $a) use ($ruta) {
                $a->comprobante_pago = $ruta;
                $a->fecha_comprobante_pago = now();
            });
        } catch (\Throwable $e) {
            Storage::delete($ruta);
            throw $e;
        }
        $this->anticipoPagado($a, $obs);
        return $a;
    }

    /* ==================================================================== */
    /*  Núcleo                                                              */
    /* ==================================================================== */

    private function exigir(Anticipo $a, User $u, string $accion): void
    {
        if ($motivo = self::impedimento($a, $u, $accion)) {
            throw ValidationException::withMessages(['general' => $motivo]);
        }
    }

    /**
     * Cambia de estado con bloqueo de fila (dos personas no pueden decidir a la vez), registra el
     * paso y, si se pide, obliga a dejar observación. $desdeSolo restringe los estados de origen
     * cuando una misma acción tiene destinos distintos según de dónde venga.
     */
    private function mover(Anticipo $a, User $u, string $accion, int $hasta, ?string $obs, bool $obsObligatoria, ?callable $cambios = null, ?array $desdeSolo = null): Anticipo
    {
        $obs = trim((string) $obs) ?: null;
        if ($obsObligatoria && !$obs) {
            throw ValidationException::withMessages(['observacion' => 'Escribe el motivo: la persona que lo recibe necesita saber qué corregir.']);
        }
        if ($obs && mb_strlen($obs) > 1000) {
            throw ValidationException::withMessages(['observacion' => 'La observación puede tener máximo 1.000 caracteres.']);
        }
        $this->exigir($a, $u, $accion);

        return DB::transaction(function () use ($a, $u, $accion, $hasta, $obs, $cambios, $desdeSolo) {
            $a = Anticipo::lockForUpdate()->findOrFail($a->id);
            $this->exigir($a, $u, $accion);
            if ($desdeSolo && !in_array((int) $a->estado_id, $desdeSolo, true)) {
                throw ValidationException::withMessages(['general' => 'El anticipo cambió de estado mientras decidías. Recarga la página.']);
            }
            $desde = (int) $a->estado_id;
            $a->estado_id = $hasta;
            $this->registrarPaso($a, $u, $accion, $desde, $hasta, $obs);
            if ($cambios) { $cambios($a); }
            $a->save();
            return $a->fresh();
        });
    }

    private function registrarPaso(Anticipo $a, User $u, string $accion, ?int $desde, int $hasta, ?string $obs): void
    {
        AnticipoPaso::create([
            'anticipo_id' => $a->id,
            'accion' => $accion,
            'estado_desde' => $desde,
            'estado_hasta' => $hasta,
            'user_id' => $u->id,
            'rol' => (int) $u->rol,
            'observacion' => trim((string) $obs) ?: null,
            'created_at' => now(),
        ]);
    }

    /** Queda aprobado: fecha de aprobación y orden de anticipo (PDF con quién aprobó). */
    private function aprobar(Anticipo $a): void
    {
        $a->fecha_aprobacion = now();
        $a->cod_orden_anticipo = $a->cod_orden_anticipo ?: sprintf('OA-%06d', $a->id);
        $a->fecha_orden_anticipo = now();
        $a->archivo_orden_anticipo = app(PdfService::class)->generarPdfOrdenAnticipo($a);
    }
}

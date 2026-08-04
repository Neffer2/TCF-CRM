<?php

namespace App\Http\Livewire\Admin\Produccion\Ordenes_Compra;


use App\Models\ItemPresupuesto;
use Livewire\Component;
use Livewire\WithFileUploads;
use Maatwebsite\Excel\Facades\Excel;
use App\Imports\OcPreviewImport;
use App\Services\ImportOcResolver;
use App\Models\OrdenCompra;
use App\Models\OcItem;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;

class OrdenesCompraVehiculosYBodega extends Component {

    use WithFileUploads;

    public $file;
    public $preview = [];
    public $totalRows = 0;
    public $totalErrors = 0;
    public $cacheKey;
    public $presupuestoDetectado;
    public $processing = false;


    public function updatedFile()
    {
        $this->validate(['file' => 'required|mimes:xlsx,xls,csv|max:10240']);

        $this->processing = true;
        $this->reset(['preview', 'totalRows', 'totalErrors', 'presupuestoDetectado']);

        $resolver = new ImportOcResolver();

        // toArray() con ToArray (sin heading) devuelve un array de hojas -> filas -> columnas
        $sheets = Excel::toArray(new OcPreviewImport(), $this->file);
        //dd($sheets[0][0], $sheets[0][1], $sheets[0][2]);
        $rows = $sheets[0]; // primera hoja

        // Índices de columna según tu excel (A=0, B=1, C=2...)
        // C.C=0 | NOMBRE C.C.=1 | IDENTIFICACIÓN=2 | DOC=3 | NUMERO=4 | ITEM=5
        // CANTIDAD=6 | VALOR UNITARIO=7 | VALOR TOTAL=8 | OBSERVACIÓN=9
        $COL_CC = 0;
        $COL_ITEM = 5;
        $COL_CANTIDAD = 6;
        $COL_VUNIT = 7;
        $COL_VTOTAL = 8;
        $COL_OBSERVACION = 9;

        $mappedRows = [];
        $presupuestoRef = null;
        $ultimoCodCc = null;

        // Saltamos la fila 0 porque es la cabecera (ajusta si tienes más de 1 fila de título)
        foreach ($rows as $index => $row) {
            if ($index === 0) {
                continue; // fila de cabecera, no es dato
            }

            $errores = [];

            $codCc = isset($row[$COL_CC]) ? trim((string) $row[$COL_CC]) : '';

            if ($codCc === '') {
                $codCc = $ultimoCodCc;
            } else {
                $ultimoCodCc = $codCc;
            }

            $itemNumero = isset($row[$COL_ITEM]) ? $row[$COL_ITEM] : null;
            $cantidad = isset($row[$COL_CANTIDAD]) ? (float) $row[$COL_CANTIDAD] : 0;
            $valorUnitario = isset($row[$COL_VUNIT]) ? (float) $row[$COL_VUNIT] : 0;
            $valorTotal = isset($row[$COL_VTOTAL]) ? (float) $row[$COL_VTOTAL] : 0;
            $observacion = isset($row[$COL_OBSERVACION]) ? $row[$COL_OBSERVACION] : null;

            // Si la fila entera está vacía (fila en blanco al final del excel), la saltamos
            if ($codCc === null && $itemNumero === null && $cantidad == 0) {
                continue;
            }

            $presupuesto = $resolver->resolverPresupuesto($codCc);

            if (!$presupuesto) {
                $errores[] = "No se encontró el proyecto con C.C: '" . ($codCc ?? 'VACÍO') . "' (fila " . ($index + 1) . ")";
            } else {
                if ($presupuestoRef === null) {
                    $presupuestoRef = $presupuesto;
                    $this->presupuestoDetectado = $presupuesto->cod_cc;
                } elseif ($presupuesto->id !== $presupuestoRef->id) {
                    $errores[] = "Este archivo mezcla proyectos distintos ({$presupuestoRef->cod_cc} y {$presupuesto->cod_cc}). Solo se permite un proyecto por orden de compra.";
                }
            }

            $item = null;
            if ($presupuesto) {
                $item = $resolver->resolveItem($presupuesto, $itemNumero);
                if (!$item) {
                    $errores[] = "No se encontró el ítem #{$itemNumero} en el proyecto {$presupuesto->cod_cc}.";
                }
            }

            if ($item) {
                $errores = array_merge(
                    $errores,
                    $resolver->validarDisponibilidad($item, $cantidad, $valorTotal)
                );
            }

            $mappedRows[] = [
                'fila_excel'     => $index + 1,
                'cod_cc'         => $codCc,
                'item_numero'    => $itemNumero,
                'item_id'        => $item ? $item->id : null,
                'item_desc'      => $item ? $item->descripcion : null,
                'cantidad'       => $cantidad,
                'valor_unitario' => $valorUnitario,
                'valor_total'    => $valorTotal,
                'observacion'    => $observacion,
                'presupuesto_id' => $presupuesto ? $presupuesto->id : null,
                'errores'        => $errores,
                'valid'          => empty($errores),
            ];
        }

        $this->totalRows = count($mappedRows);
        $this->totalErrors = collect($mappedRows)->where('valid', false)->count();

        $this->cacheKey = 'import_oc_' . Auth::id() . '_' . uniqid();
        Cache::put($this->cacheKey, $mappedRows, now()->addMinutes(30));

        $this->preview = $mappedRows;

        $this->processing = false;
    }

    public function confirmImport()
    {
        $rows = Cache::get($this->cacheKey);

        if (!$rows) {
            session()->flash('error', 'La sesión de importación expiró, vuelve a subir el archivo.');
            return;
        }

        $filasValidas = collect($rows)->where('valid', true);

        if ($filasValidas->isEmpty()) {
            session()->flash('error', 'No hay filas válidas para importar.');
            return;
        }

        // 1. Tomamos el primer ítem válido de la lista
        $primeraFila = $filasValidas->first();
        $presupuestoId = $primeraFila['presupuesto_id'];
        $itemId = $primeraFila['item_id'];

        // 2. Buscamos el ítem en la base de datos para extraer su proveedor
        $itemPresupuesto = ItemPresupuesto::find($itemId);

        if (!$itemPresupuesto) {
            session()->flash('error', "No se encontró el ítem de presupuesto ID: {$itemId} en la base de datos.");
            return;
        }

        // 3. Extraemos el proveedor_id desempaquetando la cadena serializada o el valor directo
        $proveedorId = null;
        $proveedorRaw = $itemPresupuesto->proveedor; // Campo de la tabla items_presupuesto

        if ($proveedoresArray = @unserialize($proveedorRaw)) {
            // Si estaba serializado (a:1:{i:0;s:3:"687";}), tomamos el primer ID del arreglo
            $proveedorId = is_array($proveedoresArray) ? reset($proveedoresArray) : null;
        } else {
            // Si era un ID directo (ej: 687)
            $proveedorId = $proveedorRaw;
        }

        // Validación de seguridad por si el ítem no tenía proveedor asignado en BD
        if (!$proveedorId) {
            session()->flash('error', "El ítem de presupuesto no tiene un proveedor asignado.");
            return;
        }

        // 4. Creación de la Orden de Compra y sus ítems en la BD
        DB::transaction(function () use ($filasValidas, $presupuestoId, $proveedorId) {
            $oc = OrdenCompra::create([
                'tipo_oc'          => 6,
                'estado_id'        => 1,
                'presupuesto_id'   => $presupuestoId,
                'proveedor_id'     => $proveedorId, // Asignado correctamente desde items_presupuesto (ej: 687)
                'fecha_aprobacion' => now(),
            ]);

            foreach ($filasValidas as $row) {
                OcItem::create([
                    'oc_id'        => $oc->id,
                    'item_id'      => $row['item_id'],
                    'display_item' => $row['item_numero'],
                    'desc_oc'      => $row['observacion'],
                    'cant_oc'      => $row['cantidad'],
                    'dias_oc'      => $row['dias'] ?? 1, // <--- AÑADIDO: Toma días del Excel o asigna 1 por defecto
                    'vunit_oc'     => $row['valor_unitario'],
                    'otros_oc'     => $row['otros'] ?? 1,
                    'vtotal_oc'    => $row['valor_total'],
                ]);
            }
        });

        Cache::forget($this->cacheKey);

        session()->flash('message', "Orden de compra creada con {$filasValidas->count()} ítems. Filas omitidas por error: " . (count($rows) - $filasValidas->count()));

        $this->reset(['file', 'preview', 'totalRows', 'totalErrors', 'cacheKey', 'presupuestoDetectado']);
    }



    public function render() {
        return view('livewire.admin.produccion.ordenes-compra.orden-compra-vehiculos');
    }
}

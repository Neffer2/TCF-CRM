<?php

namespace App\Http\Livewire\Admin\Produccion\Ordenes_Compra;

use App\Http\Livewire\Admin\Produccion\OrdenesCompra;
use App\Models\Año;
use App\Models\ItemPresupuesto;
use App\Models\Mes;
use App\Models\OcItem;
use App\Models\OrdenCompra;
use App\Models\PresupuestoProyecto;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use Livewire\Component;

class OrdenesCompraAnticipo extends Component{

    protected $listeners = [
        'cargarOrden' => 'cargarOrdenExistente',
        'nuevaOrden'  => 'resetearFormulario',
    ];

    public $modo = 'nuevo';
    public $productor_id = null;
    public $presupuesto = null;
    public $item_presupuesto = null;


    public $productores = [];
    public $proyectos_productor = [];
    public $items_presupuesto = [];

    public $cantidad = '';
    public $dia = '';
    public $otros = '';
    public $valor_unitario = '';
    public $valor_total = '';
    public $items = [];

    public $comercial_encargado = '';
    public $concepto_oc = '';
    public $observaciones_comercial = '';

    // Variables para filtros
    public $catalogo_anos = []; // Lista de objetos de la DB (Año::all())
    public $catalogo_meses = [];

    public $filtro_productor_id = null;
    public $filtro_anio_id = null; // Guardará el ID del registro seleccionado
    public $filtro_mes = null;
    public $filtro_presupuesto_id = null;


    public $orden_id = null;
    public $queriedOrden = null;

    public $successMessage = null;
    public $errorMessage = null;

    public function mount($orden_id = null)
    {
        $this->orden_id = $orden_id;
        $this->cargarCatalogosBase();
        //$this->filtro_mes = date('m');
        $this->cargarAnos();
        $this->cargarMeses();

        $this->proyectos_productor = collect();
        $this->items_presupuesto = collect();

        if ($orden_id) {
            $this->orden_id = $orden_id;
            $this->cargarOrdenExistente($orden_id);
        }
    }

    /**
     * Carga el catálogo de Años y selecciona por defecto el más reciente
     */
    public function cargarAnos()
    {
        $this->catalogo_anos = Año::orderBy('description', 'desc')->get();

        // Seleccionar el ID del año más reciente por defecto
        $añoMasReciente = $this->catalogo_anos->first();
        if ($añoMasReciente) {
            $this->filtro_anio_id = $añoMasReciente->id;
        }
    }

    public function cargarMeses(){
        $this->catalogo_meses = Mes::all();
        // Seleccionar por defecto el mes actual o el más reciente
        $mesReciente = $this->catalogo_meses->first();
        if ($mesReciente) {
            $this->filtro_mes = $mesReciente->id;
        }
    }

    /**
     * PROPIEDAD COMPUTADA: Presupuestos filtrados correctamente
     */
    public function getProyectosFiltradosProperty()
    {
        if (empty($this->filtro_productor_id) || empty($this->filtro_anio_id) || empty($this->filtro_mes)) {
            return collect();
        }
        // 1. Obtener registro de año
        $registroAno = Año::find($this->filtro_anio_id);
        if (!$registroAno) {
            return collect();
        }

        // 2. Obtener registro de Mes
        $registroMes = Mes::find($this->filtro_mes);
        if (!$registroMes) {
            return collect();
        }

        // Asegurarse de extraer el valor numérico del año y del mes
        $numeroAnio  = (int) $registroAno->description;

        // Si la columna 'description' del mes tiene números ("1", "09", etc.) o si usas 'id' / otra columna:
        $numeroMes   = is_numeric($registroMes->description)
            ? (int) $registroMes->description
            : (int) ($registroMes->numero ?? $registroMes->id);

        $productorId = (int) $this->filtro_productor_id;

        return PresupuestoProyecto::query()
            ->where('estado_id', 1) // Presupuesto Aprobado
            ->where(function ($query) use ($productorId) {
                $query->where('productor', $productorId);
            })
            ->whereYear('created_at', $numeroAnio)
            ->whereMonth('created_at', $numeroMes)
            ->orderBy('id', 'desc')
            ->get();
    }

    /**
     * Carga el registro existente y pobla las propiedades del formulario
     */
    public function cargarOrdenExistente($id)
    {
        $this->modo = 'ver';
        $this->orden_id = $id;

        $this->queriedOrden = OrdenCompra::with(['ordenItems.itemPresupuesto', 'presupuesto'])->find($id);

        if ($this->queriedOrden) {
            $this->productor_id = $this->queriedOrden->proveedor_id;
            $this->presupuesto  = $this->queriedOrden->presupuesto_id;

            // Mantener sincronizados los selects de la vista
            $this->filtro_productor_id   = $this->productor_id;
            $this->filtro_presupuesto_id = $this->presupuesto;

            // Cargar ítems disponibles del presupuesto
            $this->items_presupuesto = ItemPresupuesto::where('presupuesto_id', $this->presupuesto)->get();

            // Mapear la lista de ítems ya guardados en la Orden
            $this->items = [];
            foreach ($this->queriedOrden->ordenItems as $ocItem) {
                $this->items[] = [
                    'presupuesto_id' => $this->presupuesto,
                    'cod_cc'         => $this->queriedOrden->presupuesto->cod_cc ?? '',
                    'nombre_cc'      => $this->queriedOrden->presupuesto->nombre ?? '',
                    'item_presu_id'  => $ocItem->item_id,
                    'item_nombre'    => $ocItem->desc_oc ?? ($ocItem->itemPresupuesto->descripcion ?? $ocItem->itemPresupuesto->nombre ?? ''),
                    'cantidad'       => $ocItem->cant_oc,
                    'valor_unitario' => $ocItem->vunit_oc,
                    'valor_total'    => $ocItem->vtotal_oc,
                ];
            }
        }
    }

    /**
     * Limpia el submódulo para crear un registro nuevo
     */
    public function resetearFormulario()
    {
        $this->modo = 'nuevo';
        $this->orden_id = null;
        $this->queriedOrden = null;
        $this->productor_id = null;
        $this->presupuesto = null;
        $this->item_presupuesto = null;
        $this->items = [];
        $this->proyectos_productor = collect();
        $this->items_presupuesto = collect();
        $this->comercial_encargado = '';
        $this->concepto_oc = '';
        $this->observaciones_comercial = '';
    }

    public function cargarCatalogosBase()
    {
        $this->productores = User::where('rol', 7)->get();
        $this->proyectos_productor = collect();
        $this->items_presupuesto = collect();
    }

    /**
     * PROPIEDAD COMPUTADA: Calcula el total antes de agregar el ítem a la tabla
     */
    public function updatedProductorId($value)
    {
        $this->presupuesto = null;
        $this->item_presupuesto = null;
        $this->items_presupuesto = collect();

        $this->proyectos_productor = $value
            ? PresupuestoProyecto::where('productor', $value)->get()
            : collect();
    }

    /**
     * HOOK: Se dispara SOLO al cambiar de Proyecto / CC
     */
    public function updatedPresupuesto($value)
    {
        $this->presupuesto = $value;
        $this->productor_id = $this->filtro_productor_id;

        // 2. Cargar la información del presupuesto seleccionado
        if ($value) {
            $this->items_presupuesto = ItemPresupuesto::where('presupuesto_id', $value)->get();
        } else {
            $this->items_presupuesto = collect();
            $this->items = [];
        }
    }

    // Hooks de reactividad al cambiar filtros
    public function updatedFiltroProductorId()
    {
        $this->reset(['filtro_presupuesto_id', 'presupuesto', 'items']);
        $this->items_presupuesto = collect();
    }

    public function updatedFiltroAnioId()
    {
        $this->reset(['filtro_presupuesto_id', 'presupuesto', 'items']);
        $this->items_presupuesto = collect();
    }

    public function updatedFiltroMes()
    {
        $this->reset(['filtro_presupuesto_id', 'presupuesto', 'items']);
        $this->items_presupuesto = collect();
    }

    public function newItem()
    {
        $this->validate([
            'presupuesto'      => 'required',
            'item_presupuesto' => 'required',
            'cantidad'         => 'required|numeric|gt:0',
            'dia'             => 'required|numeric|gt:0',
            'valor_unitario'   => 'required|numeric|gte:0'
        ]);

        $yaExiste = collect($this->items)->contains(function ($item) {
            return $item['item_presu_id'] == $this->item_presupuesto
                && $item['presupuesto_id'] == $this->presupuesto;
        });

        if ($yaExiste) {
            $this->addError('item_presupuesto', 'Este ítem ya fue agregado a la orden de compra.');
            return;
        }

        $presuObj = PresupuestoProyecto::find($this->presupuesto);
        $itemObj  = ItemPresupuesto::find($this->item_presupuesto);

        // Validar que el valor unitario ingresado no exceda el de la BD
        if ((float) $this->valor_unitario > (float) $itemObj->v_unitario) {
            $this->addError('valor_unitario', 'El valor unitario no puede ser mayor a $' . number_format($itemObj->v_unitario, 2) . ' (valor máximo del presupuesto).');
            return;
        }

        // Validar que la cantidad ingresada no exceda la de la BD
        if ((float) $this->cantidad > (float) $itemObj->cantidad) {
            $this->addError('cantidad', 'La cantidad no puede ser mayor a ' . $itemObj->cantidad . ' (cantidad máxima del presupuesto).');
            return;
        }

        // Validar días
        if ((float) $this->dia > (float) $itemObj->dia) {
            $this->addError('dia', 'Los días no pueden ser mayores a ' . $itemObj->dia . ' (días máximos del presupuesto).');
            return;
        }


        $this->items[] = [
            'presupuesto_id' => $presuObj->id,
            'cod_cc'         => $presuObj->cod_cc,
            'nombre_cc'      => $presuObj->nombre,
            'item_presu_id'  => $itemObj->id,
            'item_nombre'    => $itemObj->nombre ?? $itemObj->descripcion,
            'cantidad'       => $this->cantidad,
            'dia'           => $this->dia,
            'otros'          => $this->otros,
            'valor_unitario' => $this->valor_unitario,
            'valor_total'    => (float)$this->cantidad * (float)$this->valor_unitario,
        ];

        $this->reset(['item_presupuesto', 'cantidad', 'valor_unitario', 'valor_total']);
    }

    //Actualiza ordenes de anticipo
    public function uploadOC()
    {
        $this->reset(['successMessage', 'errorMessage']);

        $this->validate([
            'productor_id' => 'required',
            'presupuesto'  => 'required',
        ], [
            'productor_id.required' => 'Debes seleccionar un productor.',
            'presupuesto.required'  => 'Debes seleccionar un proyecto / centro de costo.',
        ]);

        if (empty($this->items)) {
            $this->addError('items', 'Debes agregar al menos un ítem al presupuesto.');
            return;
        }

        DB::beginTransaction();

        try {
            // ID correspondiente al tipo de orden (ej. '1' o la ID para Anticipo Colaborador en TipoOrdenCompra)
            $tipoOcId = 4;

            // 2. Crear o actualizar la Orden de Compra usando estrictamente los campos de tu modelo
            $orden = OrdenCompra::updateOrCreate(
                ['id' => $this->orden_id],
                [
                    'tipo_oc'                => $tipoOcId,
                    'estado_id'              => 1, // ID de estado Aprobado
                    'presupuesto_id'         => $this->presupuesto,
                    'proveedor_id'           => $this->productor_id, // Si el productor actúa como proveedor/beneficiario
                    'fecha_aprobacion'       => Carbon::now(),
                    'fecha_envio_produccion' => Carbon::now(),
                    'actualizado'            => 0,
                ]
            );

            // 3. Sincronizar los ítems a través de la relación ordenItems() -> OcItem
            if ($this->orden_id) {
                OcItem::where('oc_id', $orden->id)->delete();
            }

            foreach ($this->items as $item) {
                OcItem::create([
                    'oc_id'           => $orden->id,
                    'item_id'         => $item['item_presu_id'],
                    'desc_oc'         => $item['item_nombre'] ?? '',
                    'cant_oc'         => $item['cantidad'] ?? 1,
                    'dia_oc'         => (int)($item['dia'] ?? 1),
                    'otros_oc'        => (int)($item['otros'] ?? 0),
                    'vunit_oc'        => $item['valor_unitario'],
                    'vtotal_oc'       => $item['valor_total'],
                ]);
            }

            DB::commit();

            $this->orden_id = $orden->id;
            $this->queriedOrden = $orden;

            $this->successMessage = 'Orden de Compra #' . $orden->id . ' generada y aprobada correctamente.';

            $this->emit('ordenProcesada', $orden->id);

            $this->resetearFormulario();

        } catch (\Exception $e) {
            DB::rollBack();
            logger('Error en uploadOC: ' . $e->getMessage());
            $this->errorMessage('general', 'Ocurrió un error al procesar la orden: ' . $e->getMessage());
        }
    }

    /**
     * 2. Obtiene las Órdenes de Compra (Anticipos) pertenecientes al Proyecto seleccionado
     */
    public function getAnticiposProyectoProperty()
    {
        if (!$this->filtro_presupuesto_id) {
            return collect();
        }

        return OrdenCompra::query()
            ->where('presupuesto_id', $this->filtro_presupuesto_id)
            ->where('proveedor_id', $this->filtro_productor_id)
            ->with(['ordenItems', 'presupuesto'])
            ->latest()
            ->get();
    }

    public function deleteItem($index)
    {
        unset($this->items[$index]);
        $this->items = array_values($this->items);
    }

    /**
     * Hooks para resetear la selección de proyecto cuando se cambie cualquier filtro previo
     */
    public function updatedFiltroPresupuestoId($value)
    {
        // Sincronizar variables
        $this->presupuesto = $value;
        $this->productor_id = $this->filtro_productor_id;

        if ($value) {
            // Cargar los ítems del presupuesto seleccionado
            $this->items_presupuesto = ItemPresupuesto::where('presupuesto_id', $value)->get();
        } else {
            $this->items_presupuesto = collect();
            $this->items = [];
        }
    }

    public function getValorTotalPreviewProperty()
    {
        $cant = is_numeric($this->cantidad) ? (float) $this->cantidad : 0;
        $vunit = is_numeric($this->valor_unitario) ? (float) $this->valor_unitario : 0;
        $dia = is_numeric($this->dia) ? (float) $this->dia : 0;
        $valorTotal = $cant * $vunit * $dia;

        return $valorTotal;
    }

    public function render()
    {
        return view('livewire.admin.produccion.ordenes-compra.orden_compra_anticipo');
    }
}

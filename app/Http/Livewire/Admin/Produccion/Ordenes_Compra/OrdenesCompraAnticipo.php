<?php

namespace App\Http\Livewire\Admin\Produccion\Ordenes_Compra;

use App\Http\Livewire\Admin\Produccion\OrdenesCompra;
use App\Models\ItemPresupuesto;
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
    public $dias = '';
    public $otros = '';
    public $valor_unitario = '';
    public $valor_total = '';
    public $items = [];

    public $comercial_encargado = '';
    public $concepto_oc = '';
    public $observaciones_comercial = '';


    public $orden_id = null;
    public $queriedOrden = null;

    public $successMessage = null;
    public $errorMessage = null;

    public function mount($orden_id = null)
    {
        $this->orden_id = $orden_id;
        $this->cargarCatalogosBase();

        $this->proyectos_productor = collect();
        $this->items_presupuesto = collect();

        if ($orden_id) {
            $this->orden_id = $orden_id;
            $this->cargarOrdenExistente($orden_id);
        }
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

            // Cargar proyectos e ítems en cascada
            $this->proyectos_productor = PresupuestoProyecto::where('productor_id', $this->productor_id)->get();
            $this->items_presupuesto   = ItemPresupuesto::where('presupuesto_id', $this->presupuesto)->get();

            // Mapear la lista de ítems
            $this->items = [];
            foreach ($this->queriedOrden->ordenItems as $ocItem) {
                $this->items[] = [
                    'presupuesto_id' => $this->presupuesto,
                    'cod_cc'         => $this->queriedOrden->presupuesto->cod_cc ?? '',
                    'nombre_cc'      => $this->queriedOrden->presupuesto->nombre ?? '',
                    'item_presu_id'  => $ocItem->item_id,
                    'item_nombre'    => $ocItem->desc_oc ?? ($ocItem->itemPresupuesto->descripcion ?? ''),
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
        $this->item_presupuesto = null;

        $this->items_presupuesto = $value
            ? ItemPresupuesto::where('presupuesto_id', $value)->get()
            : collect();
    }

    public function newItem()
    {
        $this->validate([
            'presupuesto'      => 'required',
            'item_presupuesto' => 'required',
            'cantidad'         => 'required|numeric|gt:0',
            'valor_unitario'   => 'required|numeric|gte:0',
        ]);

        $presuObj = PresupuestoProyecto::find($this->presupuesto);
        $itemObj  = ItemPresupuesto::find($this->item_presupuesto);

        $this->items[] = [
            'presupuesto_id' => $presuObj->id,
            'cod_cc'         => $presuObj->cod_cc,
            'nombre_cc'      => $presuObj->nombre,
            'item_presu_id'  => $itemObj->id,
            'item_nombre'    => $itemObj->nombre ?? $itemObj->descripcion,
            'cantidad'       => $this->cantidad,
            'dias'           => $this->dias,
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
                    'dias_oc'         => (int)($item['dias'] ?? 1),
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

    public function deleteItem($index)
    {
        unset($this->items[$index]);
        $this->items = array_values($this->items);
    }

    public function render()
    {
        return view('livewire.admin.produccion.ordenes-compra.orden_compra_anticipo');
    }
}

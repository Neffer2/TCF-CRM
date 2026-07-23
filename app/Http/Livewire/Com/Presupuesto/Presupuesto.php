<?php

namespace App\Http\Livewire\Com\Presupuesto;

use App\Exports\HistorialSheetsExports;
use App\Http\Livewire\Com\GestionComercial\Clientes\Cliente;
use App\Models\clientes;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use Livewire\Component;
use App\Rules\CentroCostos;
use App\Rules\SameCategory;
use App\Rules\PrestoConsumido;
use Illuminate\Support\Facades\Auth;
use App\Models\GestionComercial;
use App\Models\Mes;
use App\Models\Año;
use App\Models\CategoriaProveedor;
use App\Models\Proveedor;
use App\Models\ItemPresupuesto;
use App\Models\Tarifario;
use App\Models\cliente_parametros_cc;
use App\Models\PresupuestoProyecto;
use App\Traits\Email;
use App\Models\HistorialItemPresupuesto;

use Maatwebsite\Excel\Facades\Excel;

class Presupuesto extends Component
{
    // Habilita el trait para envío de emails
    use Email;

    // Variables del modelo y del formulario
    public $cod;
    public $concepto;
    public $cantidad;
    public $dia;
    public $otros;
    public $descripcion;
    public $valor_unitario = 0;
    public $valor_total = 0;
    public $valor_total_cliente = 0;
    public $proveedor = [];
    public $utilidad;
    public $tiempoFactura;
    public $notas;

    public $mes;
    public $dias;
    public $ciudad;
    public $ubicacion;
    public $item_ubicacion;

    public $imprevistos = 0;
    public $administracion = 0;
    public $fee = 0;

    public $centroCostos = '';
    public $justificacion;
    public $justificacion_compras;
    public $justificacion_lider_comercial;
    public $justificacion_gerencia;

    // Variables útiles para lógica y vistas
    public $presupuesto;
    //public $items = [];
    public $presupuesto_id;
    public $ciudades = [];
    public $meses = [];
    public $categorias_proveedor = [];
    public $proveedores = [];
    public $tarifario = [];
    public $selected_item;
    public $rentabilidadView = true;
    public $estadoValidator;

    // Métricas del proyecto
    public $margenItems = 0;
    public $costosProyecto = 0;
    public $ventaProyecto = 0;
    public $margenProyecto = 0;
    public $margenBruto = 0;

    // Datos de contacto
    public $nombre;
    public $cliente;
    public $nomProyecto;
    public $ciudadContacto;

    // Variable global para la gestión
    public $id_gestion;

    public $clienteSeleccionado; // Almacena el ID elegido en el select
    public $mesCC;

    public $modoMoviento = false;


    public $listeners = ['itemsReordered' => 'updateOrden'];

    // Renderiza la vista principal del presupuesto
    public function render()
    {
        $clientesParametros = cliente_parametros_cc::orderBy('codigo_cc', 'ASC')->get();

        $this->items = ItemPresupuesto::where('presupuesto_id', $this->presupuesto_id)
            ->orderBy('orden')
            ->get();

        return view('livewire.com.presupuesto.presupuesto', [
            'clientesParametros' => $clientesParametros
            ]);
    }

    // Inicializa el componente y carga datos principales
    public function mount(){
        $validator = PresupuestoProyecto::where('id_gestion', $this->id_gestion)->first();
        if (is_null($validator)){
            // Si no existe presupuesto, lo crea
            $presupuesto = new PresupuestoProyecto;
            $presupuesto->id_gestion = $this->id_gestion;
            $presupuesto->cod_cot = $this->getLatestCodCot() + 1;
            $presupuesto->save();
            $this->presupuesto = $presupuesto;

            $this->presupuesto_id = $presupuesto->id;
            $this->estadoValidator = $presupuesto->estado_id;
        }else {
            // Si ya existe, carga los datos
            $this->presupuesto_id = $validator->id;
            $this->estadoValidator = $validator->estado_id;
            $this->justificacion = $validator->justificacion;
            $this->justificacion_compras = $validator->justificacion_compras;
            $this->justificacion_lider_comercial = $validator->justificacion_lider;
            $this->presupuesto = $validator;
        }

        // Si es actualización, puedes mostrar justificación (comentado)
        // if ($this->presupuesto->cod_cc){
        //     $this->showJustificacion = true;
        // }

        $this->refresh();
        $this->getCiudades();
        $this->getProveedores();
        $this->getMeses();
        $this->getTarifario();
    }

    // Obtiene el último código de cotización registrado
    public function getLatestCodCot(){
        $results = PresupuestoProyecto::select('cod_cot')->orderBy('id', 'desc')->limit(1)->first();
        if (is_null($results)){
            return 10000;
        }
        return $results->cod_cot;
    }

    // Agrega un nuevo ítem al presupuesto
    public function new_item(){
        $presto = PresupuestoProyecto::where('id_gestion', $this->id_gestion)->first();

        // Valida los campos requeridos
        $this->validate([
            'cod' => ['required'],
            'cantidad' => ['required'],
            'dia' => ['required'],
            'otros' => ['required'],
            'descripcion' => ['required'],
            'valor_unitario' => ['required'],
            'valor_total' => ['required'],
            // 'proveedor' => ['required', new SameCategory],
            'proveedor' => ['required'],
            'valor_total_cliente' => ['numeric', 'required'],
            'utilidad' => ['required', 'numeric'],
            'mes' => ['required'],
            'dias' => ['required'],
            'ciudad' => ['required'],
            'imprevistos' => ['required', 'numeric'],
            'administracion' => ['required', 'numeric'],
            'fee' => ['required', 'numeric'],
        ]);

        DB::transaction(function () use ($presto){

            $maxOrden = ItemPresupuesto::where('presupuesto_id', $this->presupuesto_id)
                ->lockForUpdate()
                ->max('orden') ?? 0;

            // Crea el nuevo ítem
            $item = new ItemPresupuesto;
            $item->cod = $this->cod;
            $item->presupuesto_id = $this->presupuesto_id;
            $item->cantidad = $this->cantidad;
            $item->dia = $this->dia;
            $item->otros = $this->otros;
            $item->descripcion = $this->descripcion;
            $item->v_unitario = $this->valor_unitario;
            $item->v_total = $this->valor_total;
            $item->proveedor = serialize($this->proveedor);
            $item->margen_utilidad = $this->utilidad;
            $item->mes = $this->mes;
            $item->dias = $this->dias;
            $item->ciudad = $this->ciudad;
            $item->v_total_cliente = $this->valor_total_cliente;

            // Si el presupuesto ya tiene centro de costos, marca como actualizado
            if ($this->presupuesto->cod_cc){
                $item->actualizado = 1;
                $this->setEnEdicion($presto);
            }

            // Calcula valores de cotización y rentabilidad
            $item->v_unitario_cot = ($this->utilidad > 0) ? $this->valor_unitario / $this->utilidad : 0;
            $item->v_total_cot = ($this->utilidad > 0) ? $this->cantidad * $this->dia * $this->otros * $item->v_unitario_cot : 0;
            $item->rentabilidad = ($this->utilidad > 0) ? $item->v_total_cot - $item->v_total : 0;

            // Calcula el total de items del presupuesto y asigna el nuevo num_item y orden
            $item->num_item = $maxOrden +1;
            $item->orden = $maxOrden +1;

            $item->save();
        });

        $this->refresh();
        $this->limpiar();
    }

    // Agrega un nuevo evento al presupuesto (ítem especial)
    public function new_event(){
        $this->validate([
            'descripcion' => ['required']
        ]);

        $item = new ItemPresupuesto;
        $item->cod = 0;
        $item->presupuesto_id = $this->presupuesto_id;
        $item->evento = 1;
        $item->cantidad = 0;
        $item->dia = 0;
        $item->otros = 0;
        $item->descripcion = $this->descripcion;
        $item->v_unitario = 0;
        $item->v_total = 0;
        $item->proveedor = 0;
        $item->margen_utilidad = 0;
        $item->mes = 0;
        $item->dias = 0;
        $item->ciudad = 0;
        $item->v_unitario_cot = 0;
        $item->v_total_cot = 0;
        $item->rentabilidad = 0;
        $item->save();

        $this->refresh();
        $this->limpiar();
    }
    /*
    public function getItemsProperty(){
        return ItemPresupuesto::where('presupuesto_id', $this->presupuesto_id)
            ->orderBy('orden')
            ->get();
    }
    */
    // Obtiene los ítems del presupuesto actual
    /*
    public function getItems(){
        $this->items = ItemPresupuesto::query()
            ->where('presupuesto_id', $this->presupuesto_id)
            ->when('orden' != null, function($query) {
                return $query->orderBy('orden');
            })->get();
    }
    */
    // Calcula y actualiza las métricas del presupuesto
    public function getMetricas(){
        $this->getInfoFacturas();

        // Validamos si el presupuesto tiene items o no para calcular el margen de items
        $items = ItemPresupuesto::where('presupuesto_id', $this->presupuesto_id)->get();

        if ($items->count() > 0) {
            $this->margenItems = ( ItemPresupuesto::where('presupuesto_id', $this->presupuesto_id)
                    ->where('evento', 0)
                    ->where('margen_utilidad', '>', 0)
                    ->sum('v_total') ) / ( ItemPresupuesto::where('presupuesto_id', $this->presupuesto_id)
                    ->where('evento', 0)
                    ->sum('v_total_cot') );
        }
        else {
            $this->margenItems = 0;
        }

        $this->ventaProyecto = ItemPresupuesto::where('presupuesto_id', $this->presupuesto_id)->where('evento', 0)->sum('v_total_cot');
        $this->ventaProyecto += ($this->ventaProyecto * ($this->imprevistos/100)) + ($this->ventaProyecto * ($this->administracion/100)) + ($this->ventaProyecto * ($this->fee/100));
        $this->costosProyecto = ItemPresupuesto::where('presupuesto_id', $this->presupuesto_id)->where('evento', 0)->sum('v_total');
        if ($this->ventaProyecto > 0){
            $this->margenProyecto =  (($this->ventaProyecto - $this->costosProyecto)/$this->ventaProyecto)*100;
        }
        $this->margenBruto = $this->ventaProyecto - $this->costosProyecto;

        // Actualiza los valores en el modelo de presupuesto
        $presto = PresupuestoProyecto::where('id_gestion', $this->id_gestion)->first();
        $presto->margen_general = $this->margenItems;
        $presto->venta_proy = $this->ventaProyecto;
        $presto->costos_proy = $this->costosProyecto;
        $presto->margen_proy = $this->margenProyecto;
        $presto->margen_bruto = $this->margenBruto;
        $presto->update();

        $this->centroCostos = $this->presupuesto->cod_cc;
        $this->imprevistos = $presto->imprevistos;
        $this->administracion = $presto->administracion;
        $this->fee = $presto->fee;
        $this->tiempoFactura = $presto->tiempo_factura;
        $this->notas = $presto->notas;
    }

    // Obtiene información de facturación del presupuesto
    public function getInfoFacturas(){
        $presto = PresupuestoProyecto::where('id_gestion', $this->id_gestion)->first();
        $this->imprevistos = $presto->imprevistos;
        $this->administracion = $presto->administracion;
        $this->fee = $presto->fee;
        $this->tiempoFactura = $presto->tiempo_factura;
        $this->notas = $presto->notas;
    }

    // Actualiza la información de facturación y recarga métricas
    public function updateInfoFactura(){
        $presto = PresupuestoProyecto::where('id_gestion', $this->id_gestion)->first();
        $presto->imprevistos = $this->imprevistos;
        $presto->administracion = $this->administracion;
        $presto->fee = $this->fee;
        $presto->tiempo_factura = $this->tiempoFactura;
        $presto->notas = $this->notas;
        $presto->update();

        $this->refresh();
    }

    public function toogleModoMoviento()
    {
        $this->modoMoviento = ! $this->modoMoviento;
    }

    // Obtiene la lista de ciudades disponibles
    public function getCiudades(){
        $this->ciudades = app('ciudades');
    }

    // Obtiene la lista de proveedores y categorías
    public function getProveedores(){
        $this->categorias_proveedor = CategoriaProveedor::select('id', 'description')->orderBy('id','desc')->get();
        $this->proveedores = Proveedor::select('id', 'tercero')->get();
    }

    // Obtiene la lista de meses del año actual
    public function getMeses(){
        $año = Año::select('id', 'description')->where('description', date('Y'))->first();
        $this->meses = Mes::select('id', 'description')->where('ano_id', $año->id)->get();
    }

    // Obtiene el tarifario de conceptos y valores
    public function getTarifario(){
        $this->tarifario = Tarifario::select('id', 'concepto', 'caso', 'v_unidad')->get();
    }

    // Cambia la disponibilidad de un ítem
    public function changeDisponibilidad($id){
        $item = ItemPresupuesto::find($id);
        $item->disponible = !$item->disponible;
        $item->update();
        $this->refresh();
    }

    public function updateOrden($orderedIds) {

        $orderedIds = array_filter($orderedIds, function ($id) {
            return !is_null($id) && $id !== '';
        });

        if (empty($orderedIds)) {
            \Log::warning('updateOrden recibió un array vacío o inválido', ['orderedIds' => $orderedIds]);
            return;
        }

        $orderedIds = array_map('intval', array_values($orderedIds));

        DB::transaction(function () use ($orderedIds) {
            // 1. Trae TODOS los ids del presupuesto (incluye eventos y cualquier item
            //    que no haya llegado en el array arrastrado), en su orden actual.
            $todosLosIds = ItemPresupuesto::where('presupuesto_id', $this->presupuesto_id)
                ->orderBy('orden')
                ->pluck('id')
                ->toArray();

            // 2. Filtra los que NO vinieron en el array arrastrado (ej. eventos fijos,
            //    o cualquier fila que el JS no haya reportado).
            $noArrastrados = array_values(array_diff($todosLosIds, $orderedIds));

            // 3. Reconstruye el orden completo: primero los arrastrados en su nuevo
            //    orden, luego los que quedaron fuera, al final, en su orden relativo previo.
            //    (Ajusta esta lógica si necesitas otra regla de mezcla con eventos)
            $ordenFinal = array_merge($orderedIds, $noArrastrados);

            // FASE 1: mover TODO a valores temporales negativos (evita cualquier colisión)
            $casesTemp = [];
            $bindingsTemp = [];
            foreach ($ordenFinal as $index => $id) {
                $casesTemp[] = "WHEN ? THEN ?";
                $bindingsTemp[] = $id;
                $bindingsTemp[] = -($index + 1);
            }
            $placeholders = implode(',', array_fill(0, count($ordenFinal), '?'));
            $allBindingsTemp = array_merge($bindingsTemp, $ordenFinal);

            DB::update(
                "UPDATE items_presupuesto SET orden = CASE id " . implode(' ', $casesTemp) . " END WHERE id IN ({$placeholders})",
                $allBindingsTemp
            );

            // FASE 2: asignar los valores finales reales (0, 1, 2, 3...n-1)
            $casesFinal = [];
            $bindingsFinal = [];
            foreach ($ordenFinal as $index => $id) {
                $casesFinal[] = "WHEN ? THEN ?";
                $bindingsFinal[] = $id;
                $bindingsFinal[] = $index;
            }
            $allBindingsFinal = array_merge($bindingsFinal, $ordenFinal);

            DB::update(
                "UPDATE items_presupuesto SET orden = CASE id " . implode(' ', $casesFinal) . " END WHERE id IN ({$placeholders})",
                $allBindingsFinal
            );
        });
    }

    // Elimina un ítem del presupuesto
    public function deleteItem($id){
        ItemPresupuesto::destroy($id);

        $this->refresh();
    }

    // Refresca métricas e ítems del presupuesto
    public function refresh(){
        $this->getMetricas();
        //$this->getItems();
    }

    // Carga los datos de un ítem para edición
    public function getDataEdit($id){
        $this->selected_item = [];
        foreach ($this->items as $item) {
            if ($item->id == $id) {
                $this->selected_item = $item;
                break;
            }

        }

        $this->cod = $this->selected_item->cod;
        $this->presupuesto_id = $this->selected_item->presupuesto_id;
        $this->cantidad = $this->selected_item->cantidad;
        $this->dia = $this->selected_item->dia;
        $this->otros = $this->selected_item->otros;
        $this->descripcion = $this->selected_item->descripcion;
        $this->valor_unitario = $this->selected_item->v_unitario;
        $this->valor_total = $this->selected_item->v_total;
        $this->valor_total_cliente = $this->selected_item->v_total_cliente;
        $this->proveedor = (@unserialize($this->selected_item->proveedor)) ? @unserialize($this->selected_item->proveedor) : [$this->selected_item->proveedor];
        $this->utilidad = $this->selected_item->margen_utilidad;
        $this->mes = $this->selected_item->mes;
        $this->dias = $this->selected_item->dias;
        $this->ciudad = $this->selected_item->ciudad;
    }

    public function actualizarItem($id, array $nuevosDatos)
    {
        $item = ItemPresupuesto::findOrFail($id);

        HistorialItemPresupuesto::create([
            'item_presupuesto_id' => $item->id,
            'valores_anteriores'  => $item->toArray(),
            'user_id'             => auth()->id(),
        ]);

        $item->update(array_merge($nuevosDatos, ['actualizado' => 1]));
    }

    // Guarda los cambios al editar un ítem
    public function actionEdit(){
        $presto = PresupuestoProyecto::where('id_gestion', $this->id_gestion)->first();

        if (is_null($this->selected_item)){
            return redirect()->back()->withErrors('Ningún elemento seleccionado')->withInput();
        }

        $itemOriginal = ItemPresupuesto::find($this->selected_item->id);

        if (!$itemOriginal) {
            return redirect()->back()->withErrors('El elemento no existe.');
        }

        if ($this->selected_item->evento){
            $this->validate([
                'descripcion' => ['required'],
            ]);

            HistorialItemPresupuesto::create([
                'item_presupuesto_id' => $itemOriginal->id,
                'valores_anteriores'  => $itemOriginal->toArray(),
                'user_id'             => auth()->id(),
            ]);

            $itemOriginal->cod = 0;
            $itemOriginal->presupuesto_id = $this->presupuesto_id;
            $itemOriginal->evento = 1;
            $itemOriginal->cantidad = 0;
            $itemOriginal->dia = 0;
            $itemOriginal->otros = 0;
            $itemOriginal->descripcion = $this->descripcion;
            $itemOriginal->v_unitario = 0;
            $itemOriginal->v_total = 0;
            $itemOriginal->proveedor = serialize([]);
            $itemOriginal->margen_utilidad = 0;
            $itemOriginal->mes = 1;
            $itemOriginal->dias = 0;
            $itemOriginal->ciudad = 0;
            $itemOriginal->update();
        }
        else{
            // Validaciones para ítem normal
            $this->validate([
                'cod' => ['required'],
                'dia' => ['required'],
                'otros' => ['required'],
                'descripcion' => ['required'],
                'proveedor' => ['required'],
                'utilidad' => ['required'],
                'mes' => ['required'],
                'dias' => ['required'],
                'ciudad' => ['required']
            ]);

            $this->validate([
                'cantidad' => ['required', (new PrestoConsumido($itemOriginal))],
                'valor_total' => ['required', (new PrestoConsumido($itemOriginal))],
                'valor_total_cliente' => ['numeric', 'required']
            ]);

            // Valida que no se cambie un proveedor ya consumido
            $proveedores_consumidos = $itemOriginal->consumidos->map(function ($orden){
                return $orden->OrdenCompra->proveedor_id;
            });

            if ((@unserialize($itemOriginal->proveedor))){
                foreach (@unserialize($itemOriginal->proveedor) as $proveedor) {
                    if ($proveedores_consumidos->contains($proveedor) && in_array($proveedor, $this->proveedor) == false){
                        $this->addError('proveedor', "No puedes cambiar el proveedor {$this->proveedores->find($proveedor)->tercero} porque ya ha sido consumido.");
                        return redirect()->back();
                    }
                }
            }

            HistorialItemPresupuesto::create([
                'item_presupuesto_id' => $itemOriginal->id,
                'valores_anteriores'  => $itemOriginal->toArray(),
                'user_id'             => auth()->id(),
            ]);

            if($itemOriginal->actualizado == 0)
            {
                $itemOriginal->actualizado = 1;
            } elseif($itemOriginal->actualizado == 2)
            {
                $itemOriginal->actualizado = 3;
            }

            $this->setEnEdicion($presto);

            $itemOriginal->cod = $this->cod;
            $itemOriginal->presupuesto_id = $this->presupuesto_id;
            $itemOriginal->cantidad = $this->cantidad;
            $itemOriginal->dia = $this->dia;
            $itemOriginal->otros = $this->otros;
            $itemOriginal->descripcion = $this->descripcion;
            $itemOriginal->v_unitario = $this->valor_unitario;
            $itemOriginal->v_total = $this->valor_total;
            $itemOriginal->v_total_cliente = $this->valor_total_cliente;
            $itemOriginal->proveedor = serialize($this->proveedor);
            $itemOriginal->margen_utilidad = $this->utilidad;
            $itemOriginal->mes = $this->mes;
            $itemOriginal->dias = $this->dias;
            $itemOriginal->ciudad = $this->ciudad;

            $itemOriginal->v_unitario_cot = ($this->utilidad > 0) ? $this->valor_unitario / $this->utilidad : 0;
            $itemOriginal->v_total_cot = ($this->utilidad > 0) ? $this->cantidad * $this->dia * $this->otros * $itemOriginal->v_unitario_cot : 0;
            $itemOriginal->rentabilidad = ($this->utilidad > 0) ? $itemOriginal->v_total_cot - $itemOriginal->v_total : 0;
            $itemOriginal->update();
        }

        $this->refresh();
        $this->limpiar();
    }

    public function exportarFijo(){
        $presto = PresupuestoProyecto::where('id_gestion', $this->id_gestion)->first();

        if (!$presto) {
            return session()->flash('error', 'El presupuesto no existe.');
        }

        // 1. OBTENER ESTADO ACTUAL (Pestaña Principal)
        $itemsActuales = ItemPresupuesto::where('presupuesto_id', $presto->id)->get();
        $proveedores = Proveedor::select('id', 'categoria_id', 'tercero')->get();

        $costosProyecto = $itemsActuales->sum('v_total');
        $ventaProyecto = $itemsActuales->sum('v_total_cliente');
        $margenBruto = $ventaProyecto - $costosProyecto;
        $margenProyecto = $ventaProyecto > 0 ? ($margenBruto / $ventaProyecto) * 100 : 0;
        $margenItems = $itemsActuales->avg('margen_utilidad') ?? 0;

        // NOTA: Ajustamos las llaves para que coincidan EXACTAMENTE con tu 'PresupuestosSheetsExports'
        $payloadActual = [
            'presto'         => $presto, // Cambiado de 'presupuesto' a 'presto'
            'tipo'           => $this->rentabilidadView, // Mapeado a 'tipo'
            'items'          => $itemsActuales,
            'proveedores'    => $proveedores,
            'margenItems'    => $margenItems,
            'ventaProyecto'  => $ventaProyecto,
            'costosProyecto' => $costosProyecto,
            'margenProyecto' => $margenProyecto,
            'margenBruto'    => $margenBruto,
        ];

        // 2. OBTENER LAS VERSIONES HISTÓRICAS DE APROBACIONES
        // Para no perder ningún histórico (incluso si eliminas o creas ítems nuevos),
        // buscaremos TODOS los registros del historial que tengan el JSON con tipo_registro = 'snapshot_aprobacion'
        $aprobacionesHistoricas = HistorialItemPresupuesto::orderBy('created_at', 'asc')
            ->get()
            ->filter(function($historial) use ($presto) {
                $datos = is_array($historial->valores_anteriores)
                    ? $historial->valores_anteriores
                    : json_decode($historial->valores_anteriores, true);

                // 1. Validamos que el JSON sea un array y tenga la llave 'tipo_registro'
                if (is_array($datos) && isset($datos['tipo_registro'])) {
                    if ($datos['tipo_registro'] === 'snapshot_aprobacion') {
                        if (isset($datos['items'][0]['presupuesto_id'])) {
                            return $datos['items'][0]['presupuesto_id'] == $presto->id;
                        }
                    }
                }
                return false;
            })
            // Aseguramos que no queden duplicados basados en el ID único del historial
            ->unique('id');

        $payloadHistoricos = [];
        $version = 1;

        foreach ($aprobacionesHistoricas as $aprobacion) {
            $datosSello = is_array($aprobacion->valores_anteriores)
                ? $aprobacion->valores_anteriores
                : json_decode($aprobacion->valores_anteriores, true);

            // Convertimos los ítems a objetos stdClass para que la vista los lea sin problemas
            $itemsHistoricosDeEstaVersion = collect($datosSello['items'])->map(function($item) {
                return (object) $item;
            });

            $fechaAprobacion = $datosSello['fecha_aprobacion'] ?? $aprobacion->created_at->format('Y-m-d H:i:s');

            // Totales de la versión histórica
            $costosHistorial = $itemsHistoricosDeEstaVersion->sum('v_total');
            $ventaHistorial = $itemsHistoricosDeEstaVersion->sum('v_total_cliente');
            $margenBrutoHistorial = $ventaHistorial - $costosHistorial;
            $margenProyectoHistorial = $ventaHistorial > 0 ? ($margenBrutoHistorial / $ventaHistorial) * 100 : 0;
            $margenItemsHistorial = $itemsHistoricosDeEstaVersion->avg('margen_utilidad') ?? 0;

            $payloadHistoricos[] = [
                'titulo_pestana' => "V{$version} (" . date('d-M-Y', strtotime($fechaAprobacion)) . ")",
                'presto'         => $presto, // Cambiado de 'presupuesto' a 'presto'
                'tipo'           => $this->rentabilidadView, // Mapeado a 'tipo'
                'items'          => $itemsHistoricosDeEstaVersion,
                'proveedores'    => $proveedores,
                'margenItems'    => $margenItemsHistorial,
                'ventaProyecto'  => $ventaHistorial,
                'costosProyecto' => $costosHistorial,
                'margenProyecto' => $margenProyectoHistorial,
                'margenBruto'    => $margenBrutoHistorial,
            ];
            $version++;
        }

        if (!empty($payloadHistoricos)) {
            $ultimoHistorico = end($payloadHistoricos);

            // Comparamos un dato clave (por ejemplo, el costo total o la suma de totales)
            $totalesActuales = $payloadActual['items']->sum('v_total');
            $totalesHistoricos = collect($ultimoHistorico['items'])->sum('v_total');

            // Si los totales y la cantidad de ítems son exactamente iguales,
            // significa que no se ha modificado nada desde la última aprobación.
            // Removemos el último histórico del array para no mostrarlo duplicado.
            if ($totalesActuales === $totalesHistoricos && count($payloadActual['items']) === count($ultimoHistorico['items'])) {
                array_pop($payloadHistoricos);
            }
        }

        $nombreArchivo = ($presto->gestion->nom_proyecto_cot ?? 'presupuesto') . '.xlsx';

        // 3. Enviamos a Maatwebsite con las clases correctas
        return Excel::download(new HistorialSheetsExports($payloadActual, $payloadHistoricos), $nombreArchivo);
    }

    // Redirecciones para exportar cotizaciones y presupuestos
    public function cotizacionPdf(){
        return redirect()->route('cotizacion', ['prespuesto' => $this->id_gestion, 'nom_proyecto' => $this->presupuesto->gestion->nom_proyecto_cot, 'tipo' => 1]);
    }

    public function internoPdf(){
        return redirect()->route('cotizacion', ['prespuesto' => $this->id_gestion, 'nom_proyecto' => $this->presupuesto->gestion->nom_proyecto_cot, 'tipo' => 0]);
    }

    public function cotizacionExcel(){
        return redirect()->route('cotizacionExcel', ['prespuesto' => $this->id_gestion, 'nom_proyecto' => $this->presupuesto->gestion->nom_proyecto_cot, 'tipo' => 1]);
    }

    public function internoExcel(){
        return redirect()->route('cotizacionExcel', ['prespuesto' => $this->id_gestion, 'nom_proyecto' => $this->presupuesto->gestion->nom_proyecto_cot, 'tipo' => 0]);
    }

    // Envía el presupuesto a Validación del lider comercial
    public function aprobacion(){
        // Si es actualización, justificación es obligatoria
        if ($this->presupuesto->cod_cc){
            $this->validate([
                'justificacion' => ['required', 'string', 'max:254']
            ]);
        }

        $presto = PresupuestoProyecto::where('id_gestion', $this->id_gestion)->first();
        $presto->estado_id = 4;
//        $presto->estado_id = 2;
        $presto->justificacion = $this->justificacion;
        $presto->update();
        $this->estadoValidator = $presto->estado_id;

        // Envía email de validación
        $this->presupuestoValidacionLiderComercial($presto, Auth::user());
//        $this->presupuestoAprobacion($presto, Auth::user());
        return redirect()->route('presupuesto', $this->id_gestion);
    }

    // Guarda la gestión de validación del Lider Comercial
    public function validacionLiderComercial() {
        $presto = PresupuestoProyecto::where('id_gestion', $this->id_gestion)->first();

        // Si el margen del proyecto es menor al 30%, se envia a validación de gerencia (estado_id = 5),
        // de lo contrario se envia a revisión por parte de Controller (estado_id = 2)
        if ($presto->margen_proy < 30.00) {
            $presto->estado_id = 5;
        }
        else {
            $presto->estado_id = 2;
            // Envía notificación de revisión
            $this->presupuestoAprobacion($presto, Auth::user());
        }

        $presto->justificacion_lider = null;
        $presto->update();
        return redirect()->route('validaciones')->with('success', 'Presupuesto validado.');
    }

    // Guarda la gestión de validación de Gerencia
    public function validacionGerencia() {
        $presto = PresupuestoProyecto::where('id_gestion', $this->id_gestion)->first();
        $presto->estado_id = 1;
        $presto->justificacion_lider = null;

        $gestion = GestionComercial::find($this->id_gestion);
        $gestion->id_estado = 4;

        $gestion->update();
        $presto->update();

        // Envía notificación de revisión
        $this->presupuestoAprobacion($presto, Auth::user());
        return redirect()->route('validaciones')->with('success', 'Presupuesto validado.');
    }

    // Marca el presupuesto como editable
    public function setEnEdicion($presto){
        if ($presto->estado_id != 3){
            $presto->estado_id = 3;
            $presto->update();
        }
    }

    // Actualiza el centro de costos y recalcula valores
    public function updateCentro(){
        if (!$this->presupuesto->cod_cc) {
            $this->calcularCentroCostos();
        }

        if (!$this->presupuesto->cod_cc){
            $this->validate([
                'centroCostos' => ['required', 'string', new CentroCostos]
            ]);
        }else {
            $this->validate([
                'centroCostos' => ['required', 'string']
            ]);
        }

        $presupuesto = PresupuestoProyecto::where('id_gestion', $this->id_gestion)->first();

        $itemsParaAprobar = ItemPresupuesto::where('presupuesto_id', $presupuesto->id)->get();

        if ($itemsParaAprobar->isNotEmpty()) {
            $firstItem = $itemsParaAprobar->first();

            HistorialItemPresupuesto::create([
                'item_presupuesto_id' => $firstItem->id, // ID real y existente en la BD
                'valores_anteriores'  => json_encode([
                    'tipo_registro'    => 'snapshot_aprobacion', // Flag para identificar que es una aprobación completa
                    'items'            => $itemsParaAprobar->toArray(),
                    'cod_cc'           => $this->centroCostos,
                    'fecha_aprobacion' => date("Y-m-d H:i:s")
                ]),
                'user_id'             => auth()->id(),
            ]);
        }

        // Si el presupuesto esta en validación lider comercial y el margen del proyecto es menor al 30%,
        // se envia a validación de gerencia (estado_id = 5),
        // de lo contrario se envia a revisión por parte de Controller (estado_id = 2)
        if ($presupuesto->margen_proy < 30.00) {
            $presupuesto->estado_id = 5;
            $presupuesto->cod_cc = $this->centroCostos;
            $presupuesto->update();
            return redirect()->route('presupuesto-proyecto')->with('success', 'Presupuesto aprobado y enviado a validación de gerencia');
        }

        // Si es la primera vez que se asigna centro de costos, cambia el estado de la gestión comercial
        if (is_null($presupuesto->cod_cc)){
            $gestion = GestionComercial::find($this->id_gestion);
            $gestion->id_estado = 4;
            $gestion->update();
        }

        // Marca todos los ítems como no actualizados
        ItemPresupuesto::where('presupuesto_id', $presupuesto->id)->get()->map(function ($item){
            $item->actualizado = false;
            $item->update();
        });

        // Actualiza datos del presupuesto
        $presupuesto->cod_cc = $this->centroCostos;
        $presupuesto->fecha_cc = date("Y-m-d");
        $presupuesto->estado_id = 1;
        $presupuesto->justificacion_compras = null;
        $presupuesto->justificacion = null;
        $presupuesto->update();

        // Recalcula valores de la base y gestión comercial
        $this->reCalculate($presupuesto);

        // Envía email de aprobación
        $this->presupuestoAprobado($presupuesto->gestion->comercial, $presupuesto->gestion, null, $presupuesto->cod_cc);

        return redirect()->route('presupuesto-proyecto')->with('success', 'Presupuesto aprobado y Centro de costos asignado');
    }

    // Recalcula los valores de la base comercial y gestión comercial
    public function reCalculate($presupuesto){
        $prestosCom = [];

        // Actualiza el valor de la gestión comercial
        $presupuesto->gestion->presto_cot = $presupuesto->venta_proy;
        $presupuesto->gestion->update();

        // Calcula el presupuesto para el usuario creador de la gestión
        array_push($prestosCom, [
            'comercial_id' => $presupuesto->gestion->id_user,
            'presupuesto' => ($presupuesto->gestion->presto_cot * $presupuesto->gestion->porcentaje)/100
        ]);

        // Calcula el presupuesto para los usuarios participantes en la gestión
        $i = 2;
        while($i < 5){
            array_push($prestosCom, [
                'comercial_id' => $presupuesto->gestion->{'comercial_'.$i},
                'presupuesto' => ($presupuesto->gestion->presto_cot * $presupuesto->gestion->{'porcentaje_'.$i})/100,
            ]);
            $i++;
        }

        // Actualiza los valores en la base comercial
        foreach ($presupuesto->gestion->baseComercial as $key => $base){
            if ($base->id_user == $prestosCom[$key]['comercial_id']){
                $base->valor_original = $presupuesto->venta_proy;
                $base->valor_proyecto = $prestosCom[$key]['presupuesto'];
                $base->update();
            }
        }
    }

    // Rechaza el presupuesto y guarda la justificación
    public function rechazar(){
        $presupuesto = PresupuestoProyecto::where('id_gestion', $this->id_gestion)->first();

        if ($presupuesto->estado_id == 4) {
            $this->validate([
                'justificacion_lider_comercial' => ['required', 'string']
            ]);

            $presupuesto->justificacion_lider = $this->justificacion_lider_comercial;
        }
        elseif ($presupuesto->estado_id == 5) {
            $this->validate([
                'justificacion_gerencia' => ['required', 'string']
            ]);

            $presupuesto->justificacion_gerencia = $this->justificacion_gerencia;
        }
        else {
            $this->validate([
                'justificacion_compras' => ['required', 'string']
            ]);

            $presupuesto->justificacion_compras = $this->justificacion_compras;
        }

        $presupuesto->estado_id = 3;
        $presupuesto->update();

        // Envía email de rechazo
        $this->presupuestoRechazado($presupuesto->gestion->comercial, $presupuesto->gestion, $presupuesto->justificacion_compras, $presupuesto->cod_cc);

        return redirect()->route('presupuesto-proyecto')->with('success', 'Cambios guardados exitosamente');
    }

    // VALIDACIONES EN TIEMPO REAL DE LOS CAMPOS DEL FORMULARIO

    public function updatedCod(){
        $this->cod = trim($this->cod);
        $this->validate([
            'cod' => ['required']
        ]);
        $this->setDataTarifario($this->cod);
        $this->getValorTotal();
    }

    public function updatedCantidad(){
        $this->cantidad = trim($this->cantidad);
        $this->validate([
            'cantidad' => ['required']
        ]);
        $this->getValorTotal();
    }

    public function updatedDia(){
        $this->dia = trim($this->dia);
        $this->validate([
            'dia' => ['required']
        ]);
        $this->getValorTotal();
    }

    public function updatedOtros(){
        $this->otros = trim($this->otros);
        $this->validate([
            'otros' => ['required']
        ]);
        $this->getValorTotal();
    }

    public function updatedDescripcion(){
        $this->descripcion = trim($this->descripcion);
        $this->validate([
            'descripcion' => ['required']
        ]);
    }

    public function updatedValorUnitario(){
        $this->valor_unitario = trim($this->valor_unitario);
        $this->valor_unitario = str_replace(",",'', $this->valor_unitario);
        $this->validate([
            'valor_unitario' => ['required', 'numeric']
        ]);
        $this->getValorTotal();
    }

    public function updatedValorTotal(){
        $this->valor_total = trim($this->valor_total);
        $this->valor_total = str_replace(",",'', $this->valor_total);
        $this->validate([
            'valor_total' => ['required', 'numeric']
        ]);
    }

    public function updatedValorTotalCliente(){
        $this->valor_total_cliente = trim($this->valor_total_cliente);
        $this->valor_total_cliente = str_replace(",",'', $this->valor_total_cliente);
        $this->validate([
            'valor_total_cliente' => ['numeric', 'required']
        ]);
        if ($this->valor_total != 0){
            $this->getUtilidad();
        }
    }

    public function updatedProveedor(){
        $this->validate([
            // 'proveedor' => ['required', new SameCategory]
            'proveedor' => ['required']
        ]);
    }

    public function updatedUtilidad(){
        $this->utilidad = trim($this->utilidad);
        $this->utilidad = str_replace(",",'', $this->utilidad);
        $this->validate([
            'utilidad' => ['required', 'numeric']
        ]);
    }

    public function updatedMes(){
        $this->mes = trim($this->mes);
        $this->validate([
            'mes' => ['required']
        ]);
    }

    public function updatedDias(){
        $this->dias = trim($this->dias);
        $this->validate([
            'dias' => ['required']
        ]);
    }

    public function updatedCiudad(){
        $this->validate([
            'ciudad' => ['required']
        ]);
    }

    public function updatedCentroCostos(){
        $this->validate([
            'centroCostos' => ['required', 'string', new CentroCostos]
        ]);
    }

    public function updatedImprevistos(){
        $this->validate([
            'imprevistos' => ['required', 'numeric']
        ]);
        $this->updateInfoFactura();
    }

    public function updatedAdministracion(){
        $this->validate([
            'administracion' => ['required', 'numeric']
        ]);
        $this->updateInfoFactura();
    }

    public function updatedFee(){
        $this->validate([
            'fee' => ['required', 'numeric']
        ]);
        $this->updateInfoFactura();
    }

    public function updatedTiempoFactura(){
        $this->validate([
            'tiempoFactura' => ['required', 'numeric']
        ]);
        $this->updateInfoFactura();
    }

    public function updatedNotas(){
        $this->validate([
            'notas' => ['required', 'string', 'max:254']
        ]);
        $this->updateInfoFactura();
    }

    // Calcula el valor total del ítem
    public function getValorTotal(){
        if (!is_null($this->cantidad) && !is_null($this->dia) && !is_null($this->otros) && !is_null($this->valor_unitario)){
            $this->valor_total = $this->cantidad * $this->dia * $this->otros * $this->valor_unitario;
        }
        if ($this->valor_total_cliente != 0){
            $this->getUtilidad();
        }
    }

    // Calcula la utilidad del ítem
    public function getUtilidad(){
        if ($this->valor_total_cliente > 0){
            $this->utilidad = $this->valor_total / $this->valor_total_cliente;
        }
        else {
            $this->utilidad = 0;
        }
    }

    // Asigna datos del tarifario al ítem seleccionado
    public function setDataTarifario($cod_tarifario){
        if ($cod_tarifario == 0){
            $this->descripcion = "";
            $this->valor_unitario = 0;
            return redirect()->back();
        }
        $tarifario = $this->tarifario->firstWhere('id', $cod_tarifario);
        $this->descripcion = $tarifario->concepto." ".$tarifario->caso;
        $this->valor_unitario = $tarifario->v_unidad;
    }

    // Limpia los campos del formulario
    public function limpiar(){
        $this->cod = "";
        $this->cantidad = null;
        $this->dia = null;
        $this->otros = null;
        $this->descripcion = "";
        $this->valor_unitario = 0;
        $this->valor_total = 0;
        $this->proveedor = [];
        $this->utilidad = "";
        $this->valor_total_cliente = 0;
        $this->mes = "";
        $this->dias = "";
        $this->ciudad = "";
        $this->selected_item = null;
        $this->ubicacion = "";
        $this->item_ubicacion = "";
    }

    // Alterna la vista de rentabilidad
    public function toggelRentabilidad(){
        $this->rentabilidadView = !$this->rentabilidadView;
    }

    public function updatedMesCC($value){
        $this->calcularCentroCostos();
    }

    public function updatedClienteSeleccionado($value)
    {
        $this->calcularCentroCostos();
    }

    public function calcularCentroCostos(){
        if (!$this->clienteSeleccionado) {
            $this->centroCostos = '';
            return;
        }

        // 1. Buscamos directamente el registro en clientes_parametro_cc
        $parametro = cliente_parametros_cc::find($this->clienteSeleccionado);

        if (!$parametro || !$parametro->codigo_cc) {
            $this->centroCostos = '';
            return;
        }

        // 2. Tomamos el código de 2 dígitos (ej: 'C3', '09', '11')
        $codigoCC = $parametro->codigo_cc;

        // 3. Factores de fecha: Año actual y Mes por defecto actual (ej: '07')
        $anio = date('y');
        $mes = $this->mesCC ?? date('m');
        $mesFormateado = str_pad($mes, 2, '0', STR_PAD_LEFT);

        // Raíz de búsqueda: "C3-202607-"
        $prefijoBusqueda = "{$codigoCC}-{$anio}{$mesFormateado}-";

        // 4. Consultamos el último consecutivo en presupuestos_proyecto
        $ultimoPresupuesto = PresupuestoProyecto::where('cod_cc', 'LIKE', "{$prefijoBusqueda}%")
            ->orderBy('cod_cc', 'desc')
            ->first();

        $nuevoConsecutivo = 1;

        if ($ultimoPresupuesto) {
            $partes = explode('-', $ultimoPresupuesto->cod_cc);
            $ultimoNumero = (int) end($partes);
            $nuevoConsecutivo = $ultimoNumero + 1;
        }

        $consecutivoFormateado = str_pad($nuevoConsecutivo, 2, '0', STR_PAD_LEFT);

        // 5. Asignamos la cadena final a la propiedad de previsualización
        $this->centroCostos = "{$prefijoBusqueda}{$consecutivoFormateado}";
    }
}

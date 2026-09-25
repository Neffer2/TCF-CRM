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
use App\Services\AprobacionPresupuesto;

use Maatwebsite\Excel\Facades\Excel;
use PhpOffice\PhpSpreadsheet\Calculation\MathTrig\Sum;

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
    public $valor_unitario_cliente;
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

    // Utilidad = costo ÷ precio (0,7 → precio con 30 % de margen; 0 = ítem sin precio al cliente).
    // Por encima de 2 es casi siempre un error de digitación (70 en vez de 0,7).
    const REGLA_UTILIDAD = ['required', 'numeric', 'min:0', 'max:2'];

    protected $messages = [
        'utilidad.max' => 'La utilidad (costo ÷ precio) va de 0 a 2: 0,7 es un precio con 30 % de margen. Revisa si escribiste 70 en vez de 0,7 o el precio al cliente.',
        'utilidad.min' => 'La utilidad no puede ser negativa.',
    ];

    // Las fija el servidor: el navegador no puede cambiarlas (en Livewire toda propiedad pública
    // se puede modificar desde la consola).
    protected $propiedadesBloqueadas = [
        'id_gestion', 'presupuesto', 'presupuesto_id', 'estadoValidator', 'selected_item', 'items',
        'ciudades', 'meses', 'categorias_proveedor', 'proveedores', 'tarifario',
        'margenItems', 'costosProyecto', 'ventaProyecto', 'margenProyecto', 'margenBruto',
    ];

    public function updating($name, $value)
    {
        abort_if(in_array(explode('.', $name)[0], $this->propiedadesBloqueadas, true), 403);
    }

    // Las acciones se autorizan en el servidor con las mismas condiciones con que la vista
    // muestra cada botón; el estado se lee de la base, no del componente.
    private function prestoActual(): PresupuestoProyecto
    {
        return PresupuestoProyecto::findOrFail($this->presupuesto_id);
    }

    // Comercial o ejecutivo arman el presupuesto; mientras está en revisión (2) o con el líder (4) no se toca.
    private function autorizarEdicion(): void
    {
        abort_unless(in_array((int) Auth::user()->rol, [2, 5], true), 403);
        abort_if(in_array((int) $this->prestoActual()->estado_id, [2, 4], true), 403, 'El presupuesto está en revisión.');
    }

    private function itemDelPresupuesto($id): ItemPresupuesto
    {
        $item = ItemPresupuesto::where('presupuesto_id', $this->presupuesto_id)->find($id);
        abort_unless($item, 404);
        return $item;
    }

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
        // Solo quien trabaja la gestión (o administración) abre su presupuesto; una gestión inexistente no crea uno.
        $gestion = GestionComercial::find($this->id_gestion);
        abort_unless($gestion, 404);
        abort_unless(Auth::user()->puedeVerGestion($gestion), 403);

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

        $this->refresh((int) $this->presupuesto->estado_id === 3);
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
        $this->autorizarEdicion();
        $presto = PresupuestoProyecto::where('id_gestion', $this->id_gestion)->first();

        $this->validate([
            'cod' => ['required'],
            'cantidad' => ['required'],
            'dia' => ['required'],
            'descripcion' => ['required'],
            'valor_unitario' => ['required'],
            'valor_total' => ['required'],
            'proveedor' => ['required'],
            'valor_total_cliente' => ['numeric', 'required'],
            'utilidad' => self::REGLA_UTILIDAD,
            'imprevistos' => ['required', 'numeric'],
            'administracion' => ['required', 'numeric'],
            'fee' => ['required', 'numeric'],
        ]);

        DB::transaction(function () use ($presto){

            // Excluimos los eventos del cálculo del consecutivo
            $maxNumItem = ItemPresupuesto::where('presupuesto_id', $this->presupuesto_id)
                ->where('evento', 0)
                ->lockForUpdate()
                ->max('num_item') ?? 0;

            $maxOrden = ItemPresupuesto::where('presupuesto_id', $this->presupuesto_id)
                ->lockForUpdate()
                ->max('orden') ?? 0;

            $item = new ItemPresupuesto;
            $item->cod = $this->cod;
            $item->presupuesto_id = $this->presupuesto_id;
            $item->cantidad = $this->cantidad;
            $item->dia = $this->dia;
            $item->otros = (int) $this->factorOtros();
            $item->descripcion = $this->descripcion;
            $item->v_unitario = $this->valor_unitario;
            $item->v_total = $this->valor_total;
            $item->proveedoresPendientes = (array) $this->proveedor;
            $item->margen_utilidad = $this->utilidad;
            $item->mes = !empty($this->mes) ? $this->mes : null;
            $item->dias = !empty($this->dias) ? (int)$this->dias : 0;
            $item->ciudad = !empty($this->ciudad) ? $this->ciudad : null;
            $item->v_total_cliente = $this->valor_total_cliente;
            $item->evento = 0; // Aseguramos que sea ítem normal

            $item->num_item = $maxNumItem + 1;
            $item->orden = $maxOrden + 1;

            if ($this->presupuesto->cod_cc){
                $item->actualizado = 3;
                $item->actualizado_con = 3;
                $this->setEnEdicion($presto);
            }

            $item->v_unitario_cot = ($this->utilidad > 0) ? $this->valor_unitario / $this->utilidad : 0;
            $item->v_total_cot = ($this->utilidad > 0) ? ($this->cantidad * $this->dia * $this->factorOtros() * $item->v_unitario_cot) : 0;
            $item->rentabilidad = ($this->utilidad > 0) ? ($item->v_total_cot - $item->v_total) : 0;

            $item->save();
        });

        $this->getMetricas();
        $this->limpiar();
    }

    // Agrega un nuevo evento al presupuesto (ítem especial)
    public function new_event(){
        $this->autorizarEdicion();
        $this->validate([
            'descripcion' => ['required']
        ]);

        DB::transaction(function () {
            // Obtenemos el último orden para que el evento quede bien posicionado en la lista
            $maxOrden = ItemPresupuesto::where('presupuesto_id', $this->presupuesto_id)
                ->lockForUpdate()
                ->max('orden') ?? 0;

            $item = new ItemPresupuesto;
            $item->cod = 0;
            $item->presupuesto_id = $this->presupuesto_id;
            $item->evento = 1;
            $item->num_item = null; // SOLUCIÓN: Sin número consecutivo
            $item->orden = $maxOrden + 1; // Asignamos orden consecutivo
            $item->cantidad = 0;
            $item->dia = 0;
            $item->otros = 0;
            $item->descripcion = $this->descripcion;
            $item->v_unitario = 0;
            $item->v_total = 0;
            // Un evento no tiene proveedor. La columna 'proveedor' ya no existe:
            // los proveedores de un ítem viven en item_presupuesto_proveedor.
            $item->margen_utilidad = 0;
            $item->mes = '';
            $item->dias = 0;
            $item->ciudad = '';
            $item->v_unitario_cot = 0;
            $item->v_total_cot = 0;
            $item->rentabilidad = 0;
            $item->save();
        });

        $this->refresh();
        $this->limpiar();
    }

    // $guardar: las cifras se guardan al editar (y al abrir uno editable); abrir uno aprobado o en revisión
    // ya no las reescribe con lo que den los ítems en ese momento.
    private function getMetricas(bool $guardar = true) {
        $this->getInfoFacturas();

        $sumTotalConMargen = ItemPresupuesto::where('presupuesto_id', $this->presupuesto_id)
            ->where('evento', 0)
            ->where('margen_utilidad', '>', 0)
            ->sum('v_total');

        $sumTotalCot = ItemPresupuesto::where('presupuesto_id', $this->presupuesto_id)
            ->where('evento', 0)
            ->sum('v_total_cot');

        // Validación segura de división por cero
        if ($sumTotalCot > 0) {
            $this->margenItems = $sumTotalConMargen / $sumTotalCot;
        } else {
            $this->margenItems = 0;
        }

        $this->ventaProyecto = $sumTotalCot;
        $this->ventaProyecto += ($this->ventaProyecto * ($this->imprevistos / 100)) 
                            + ($this->ventaProyecto * ($this->administracion / 100)) 
                            + ($this->ventaProyecto * ($this->fee / 100));

        $this->costosProyecto = ItemPresupuesto::where('presupuesto_id', $this->presupuesto_id)
            ->where('evento', 0)
            ->sum('v_total');

        if ($this->ventaProyecto > 0){
            $this->margenProyecto = (($this->ventaProyecto - $this->costosProyecto) / $this->ventaProyecto) * 100;
        } else {
            $this->margenProyecto = 0;
        }

        $this->margenBruto = $this->ventaProyecto - $this->costosProyecto;

        // Actualización directa en la BD
        $presto = PresupuestoProyecto::where('id_gestion', $this->id_gestion)->first();

        if ($presto) {
            if ($guardar) {
                $presto->margen_general = $this->margenItems;
                $presto->venta_proy     = $this->ventaProyecto;
                $presto->costos_proy    = $this->costosProyecto;
                $presto->margen_proy    = $this->margenProyecto;
                $presto->margen_bruto   = $this->margenBruto;
                $presto->save();
            }

            $this->centroCostos  = optional($this->presupuesto)->cod_cc;
            $this->imprevistos   = $presto->imprevistos;
            $this->administracion = $presto->administracion;
            $this->fee           = $presto->fee;
            $this->tiempoFactura = $presto->tiempo_factura;
            $this->notas         = $presto->notas;
        }
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
        $this->autorizarEdicion();
        $presto = PresupuestoProyecto::where('id_gestion', $this->id_gestion)->first();
        // Cambiar un recargo cambia la venta: en uno ya aprobado vuelve a Editable para re-aprobarlo
        // (antes se reescribía la venta aprobada sin pasar por nadie).
        $cambianValores = (float) $presto->imprevistos != (float) $this->imprevistos
            || (float) $presto->administracion != (float) $this->administracion
            || (float) $presto->fee != (float) $this->fee;
        $presto->imprevistos = $this->imprevistos;
        $presto->administracion = $this->administracion;
        $presto->fee = $this->fee;
        $presto->tiempo_factura = $this->tiempoFactura;
        $presto->notas = $this->notas;
        $presto->update();
        if ($cambianValores && $presto->cod_cc) {
            $this->setEnEdicion($presto);
        }

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
        // Se precargan los proveedores de cada categoría: la vista los recorre para el desplegable
        // y sin esto hacía una consulta por cada proveedor (1.688 por acción).
        $this->categorias_proveedor = CategoriaProveedor::select('id', 'description')
            ->with(['proveedores' => function ($q) { $q->select('id', 'categoria_id', 'tercero')->orderBy('tercero'); }])
            ->orderBy('id','desc')->get();
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
        $this->autorizarEdicion();
        $item = $this->itemDelPresupuesto($id);
        $item->disponible = !$item->disponible;
        $item->update();
        $this->refresh();
    }

    public function updateOrden($orderedIds) {
        $this->autorizarEdicion();

        $orderedIds = array_filter((array) $orderedIds, function ($id) {
            return !is_null($id) && $id !== '';
        });

        if (empty($orderedIds)) {
            \Log::warning('updateOrden recibió un array vacío o inválido', ['orderedIds' => $orderedIds]);
            return;
        }

        $orderedIds = array_map('intval', array_values($orderedIds));

        // Solo se reordenan ítems de este presupuesto (el arreglo llega del navegador).
        $propios = ItemPresupuesto::where('presupuesto_id', $this->presupuesto_id)->pluck('id')->all();
        $orderedIds = array_values(array_unique(array_intersect($orderedIds, $propios)));
        if (empty($orderedIds)) {
            return;
        }

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
        $this->autorizarEdicion();
        $item = $this->itemDelPresupuesto($id);
        // Igual que la vista: un ítem normal solo se borra antes de tener centro de costos, y nunca si ya tiene órdenes.
        if (!$item->evento) {
            abort_if((bool) $this->prestoActual()->cod_cc, 403);
            if ($item->consumidos()->exists()) {
                return redirect()->back()->withErrors('Este ítem ya tiene órdenes de compra: no se puede borrar.');
            }
        }
        $item->delete();
        $this->refresh();
    }

    // Refresca métricas e ítems del presupuesto
    // Sin indicar, las cifras solo se guardan si el presupuesto está Editable (3): en uno aprobado o en revisión
    // (marcar visto, disponibilidad) no se tocan las cifras con las que se aprobó.
    private function refresh(?bool $guardar = null){
        $this->getMetricas($guardar ?? ((int) $this->prestoActual()->estado_id === 3));
        //$this->getItems();
    }

    // Carga los datos de un ítem para edición
    public function getDataEdit($id){
        $this->autorizarEdicion();
        $this->selected_item = $this->itemDelPresupuesto($id);

        $this->cod = $this->selected_item->cod;
        $this->presupuesto_id = $this->selected_item->presupuesto_id;
        $this->cantidad = $this->selected_item->cantidad;
        $this->dia = $this->selected_item->dia;
        $this->otros = $this->selected_item->otros;
        $this->descripcion = $this->selected_item->descripcion;
        $this->valor_unitario = $this->selected_item->v_unitario;
        $this->valor_total = $this->selected_item->v_total;
        $this->valor_unitario_cliente = $this->selected_item->v_unitario_cot;
        $this->valor_total_cliente = $this->selected_item->v_total_cliente;
        $this->proveedor = $this->selected_item->proveedorIds();
        $this->utilidad = $this->selected_item->margen_utilidad;
        $this->mes = $this->selected_item->mes;
        $this->dias = $this->selected_item->dias;
        $this->ciudad = $this->selected_item->ciudad;
    }

    public function items()
    {
        return $this->hasMany(ItemPresupuesto::class, 'presupuesto_id');
    }

    private function actualizarItem($id, array $nuevosDatos)
    {
        $item = $this->itemDelPresupuesto($id);

        HistorialItemPresupuesto::create([
            'item_presupuesto_id' => $item->id,
            'valores_anteriores'  => $item->snapshotHistorial(),
            'user_id'             => auth()->id(),
        ]);

        $item->update(array_merge($nuevosDatos, ['actualizado' => 1])); // Marca como actualizado
    }

    // Guarda los cambios al editar un ítem
    public function actionEdit(){
        $this->autorizarEdicion();
        $presto = PresupuestoProyecto::where('id_gestion', $this->id_gestion)->first();

        if (!($this->selected_item instanceof ItemPresupuesto)){
            return redirect()->back()->withErrors('Ningún elemento seleccionado')->withInput();
        }

        $itemOriginal = ItemPresupuesto::where('presupuesto_id', $this->presupuesto_id)->find($this->selected_item->id);

        if (!$itemOriginal) {
            return redirect()->back()->withErrors('El elemento no existe.');
        }

        if ($this->selected_item->evento){
            $this->validate([
                'descripcion' => ['required'],
            ]);

            HistorialItemPresupuesto::create([
                'item_presupuesto_id' => $itemOriginal->id,
                'valores_anteriores'  => $itemOriginal->snapshotHistorial(),
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
            $itemOriginal->v_total_cliente = 0;
            $itemOriginal->proveedoresPendientes = [];
            $itemOriginal->margen_utilidad = 0;
            $itemOriginal->mes = '';
            $itemOriginal->dias = 0;
            $itemOriginal->ciudad = '';
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
                'utilidad' => self::REGLA_UTILIDAD,
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

            if (true){
                foreach ($itemOriginal->proveedorIds() as $proveedor) {
                    if ($proveedores_consumidos->contains($proveedor) && in_array($proveedor, $this->proveedor) == false){
                        $this->addError('proveedor', "No puedes cambiar el proveedor {$this->proveedores->find($proveedor)->tercero} porque ya ha sido consumido.");
                        return redirect()->back();
                    }
                }
            }

            HistorialItemPresupuesto::create([
                'item_presupuesto_id' => $itemOriginal->id,
                'valores_anteriores'  => $itemOriginal->snapshotHistorial(),
                'user_id'             => auth()->id(),
            ]);

            if($presto->cod_cc)
            {
                if($itemOriginal->actualizado == 0)
                {
                    $itemOriginal->actualizado = 1;
                }
                elseif($itemOriginal->actualizado == 2)
                {
                    $itemOriginal->actualizado = 3;
                }
            }

            if($presto->cod_cc){
                if($itemOriginal->actualizado_con == 0){
                    $itemOriginal->actualizado_con = 1;
                } elseif($itemOriginal->actualizado_con == 2){
                    $itemOriginal->actualizado_con = 3;
                }
            }

            $this->setEnEdicion($presto);

            $itemOriginal->cod = $this->cod;
            $itemOriginal->presupuesto_id = $this->presupuesto_id;
            $itemOriginal->cantidad = $this->cantidad;
            $itemOriginal->dia = $this->dia;
            $itemOriginal->otros = (int) $this->factorOtros();
            $itemOriginal->descripcion = $this->descripcion;
            $itemOriginal->v_unitario = $this->valor_unitario;
            $itemOriginal->v_total = $this->valor_total;
            $itemOriginal->v_total_cliente = $this->valor_total_cliente;
            $itemOriginal->v_unitario_cot = $this->valor_unitario_cliente;
            $itemOriginal->proveedoresPendientes = (array) $this->proveedor;
            $itemOriginal->margen_utilidad = $this->utilidad;
            $itemOriginal->mes = $this->mes;
            $itemOriginal->dias = $this->dias;
            $itemOriginal->ciudad = $this->ciudad;

            $itemOriginal->v_unitario_cot = ($this->utilidad > 0) ? $this->valor_unitario / $this->utilidad : 0;
            $itemOriginal->v_total_cot = ($this->utilidad > 0) ? $this->cantidad * $this->dia * $this->factorOtros() * $itemOriginal->v_unitario_cot : 0;
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

        // Las mismas cifras de la pantalla (venta = ítems a precio cliente + recargos). Antes se sumaba
        // v_total_cliente, que no cuadra con la venta del proyecto (a veces en 0, a veces inflado).
        $costosProyecto = (float) $presto->costos_proy;
        $ventaProyecto = (float) $presto->venta_proy;
        $margenBruto = (float) $presto->margen_bruto;
        $margenProyecto = (float) $presto->margen_proy;
        // Protegido contra división por cero (presupuestos con utilidad 0
        // o solo eventos tienen v_total_cot en 0), igual que getMetricas().
        $sumVTotalCot = ItemPresupuesto::where('presupuesto_id', $this->presupuesto_id)
            ->where('evento', 0)
            ->sum('v_total_cot');
        $sumVTotalMargen = ItemPresupuesto::where('presupuesto_id', $this->presupuesto_id)
            ->where('evento', 0)
            ->where('margen_utilidad', '>', 0)
            ->sum('v_total');

        $margenItems = $sumVTotalCot > 0 ? ($sumVTotalMargen / $sumVTotalCot) : 0;

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
        $aprobacionesHistoricas = HistorialItemPresupuesto::where('valores_anteriores', 'like', '%snapshot_aprobacion%')
            ->where('valores_anteriores', 'like', '%'.$presto->id.'%')
            ->orderBy('created_at', 'asc')
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

            // Totales de la versión histórica, con la fórmula de la pantalla (los recargos no quedan en la foto:
            // se usan los actuales del presupuesto)
            $itemsSinEventos = $itemsHistoricosDeEstaVersion->where('evento', 0);
            $costosHistorial = $itemsSinEventos->sum('v_total');
            $ventaBaseHistorial = $itemsSinEventos->sum('v_total_cot');
            $recargos = ((float) $presto->imprevistos + (float) $presto->administracion + (float) $presto->fee) / 100;
            $ventaHistorial = $ventaBaseHistorial * (1 + $recargos);
            $margenBrutoHistorial = $ventaHistorial - $costosHistorial;
            $margenProyectoHistorial = $ventaHistorial > 0 ? ($margenBrutoHistorial / $ventaHistorial) * 100 : 0;
            $margenItemsHistorial = $ventaBaseHistorial > 0 ? $itemsSinEventos->where('margen_utilidad', '>', 0)->sum('v_total') / $ventaBaseHistorial : 0;

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

    // El nombre del proyecto va en la dirección (solo nombra el archivo): "/", "#", "?" o "%" la rompían (404)
    private function nombreParaRuta(): string
    {
        return trim(str_replace(['/', '\\', '#', '?', '%'], '-', (string) $this->presupuesto->gestion->nom_proyecto_cot)) ?: 'cotizacion';
    }

    // Redirecciones para exportar cotizaciones y presupuestos
    public function cotizacionPdf(){
        return redirect()->route('cotizacion', ['prespuesto' => $this->id_gestion, 'nom_proyecto' => $this->nombreParaRuta(), 'tipo' => 1]);
    }

    public function internoPdf(){
        return redirect()->route('cotizacion', ['prespuesto' => $this->id_gestion, 'nom_proyecto' => $this->nombreParaRuta(), 'tipo' => 0]);
    }

    public function cotizacionExcel(){
        return redirect()->route('cotizacionExcel', ['prespuesto' => $this->id_gestion, 'nom_proyecto' => $this->nombreParaRuta(), 'tipo' => 1]);
    }

    public function internoExcel(){
        return redirect()->route('cotizacionExcel', ['prespuesto' => $this->id_gestion, 'nom_proyecto' => $this->nombreParaRuta(), 'tipo' => 0]);
    }

    // Envía el presupuesto a Validación del lider comercial
    public function aprobacion(){
        $this->autorizarEdicion();
        // Si es actualización, justificación es obligatoria
        if ($this->presupuesto->cod_cc){
            $this->validate([
                'justificacion' => ['required', 'string', 'max:254']
            ]);
        }

        // Las cifras que verán el líder y Gerencia se recalculan justo antes de enviarlo
        $this->getMetricas(true);
        $presto = PresupuestoProyecto::where('id_gestion', $this->id_gestion)->first();
        $presto->estado_id = 4;
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

        if (!$presto) {
            return redirect()->back()->with('error', 'Presupuesto no encontrado.');
        }
        // Mismo permiso que el bloque del líder en la vista: estado 4 y rol Admin o Líder comercial.
        abort_unless((int) $presto->estado_id === 4 && in_array((int) Auth::user()->rol, [1, 12], true), 403);

        // Con margen menor al 30 % (AprobacionPresupuesto::MARGEN_GERENCIA) va a validación de Gerencia (estado_id = 5);
        // de lo contrario se aprueba directamente (estado_id = 1)
        if (AprobacionPresupuesto::requiereGerencia($presto)) {
            $presto->estado_id = 5;
        } else {
            $presto->estado_id = 1;
        }

        $presto->justificacion_lider = null;

        ItemPresupuesto::where('presupuesto_id', $presto->id)->update(['actualizado' => false]);

        $presto->update();
        $this->marcarConCambiosPendientes();

        return redirect()->route('validaciones')->with('success', 'Presupuesto validado.');
    }

    // Guarda la gestión de validación de Gerencia
    public function validacionGerencia() {
        $presto = PresupuestoProyecto::where('id_gestion', $this->id_gestion)->first();

        if (!$presto) {
            return redirect()->back()->with('error', 'Presupuesto no encontrado.');
        }
        // Mismo permiso que el botón de la vista: estado 5 y permiso aprobar-presupuestos.
        abort_unless((int) $presto->estado_id === 5 && Auth::user()->espacioAdmin() && Auth::user()->can('aprobar-presupuestos'), 403);

        $presto->estado_id = 1; // Se aprueba directamente
        $presto->justificacion_lider = null;
        $presto->justificacion_compras = null;
        $presto->justificacion = null;
        if (!$presto->fecha_cc) {
            $presto->fecha_cc = date('Y-m-d');
        }

        AprobacionPresupuesto::avanzarGestion($presto);

        ItemPresupuesto::where('presupuesto_id', $presto->id)->update(['actualizado' => false]);


        $presto->update();

        // Igual que la aprobación del líder: el valor aprobado llega a la gestión y a la base comercial,
        // y el comercial recibe el correo de aprobado (antes salía el de "solicita aprobación").
        $this->reCalculate($presto);
        $this->presupuestoAprobado($presto->gestion->comercial, $presto->gestion, null, $presto->cod_cc);
        $this->marcarConCambiosPendientes();

        return redirect()->route('validaciones')->with('success', 'Presupuesto validado.');
    }

    // Marca el presupuesto como editable
    private function setEnEdicion($presto){
        if ($presto->estado_id != 3){
            $presto->estado_id = 3;
            $presto->update();
        }
    }

    // Actualiza el centro de costos y recalcula valores
    public function updateCentro(){
        // Mismo permiso que la vista: Controller/Admin en revisión (2) o Admin/Líder comercial en su validación (4).
        $estadoActual = (int) $this->prestoActual()->estado_id;
        $rol = (int) Auth::user()->rol;
        abort_unless(($estadoActual === 2 && Auth::user()->revisaPresupuestos())
            || ($estadoActual === 4 && in_array($rol, [1, 12], true)), 403);

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

        // Con margen menor al 30 % (AprobacionPresupuesto::MARGEN_GERENCIA) se envía a validación de Gerencia (estado_id = 5)
        if (AprobacionPresupuesto::requiereGerencia($presupuesto)) {
            $presupuesto->estado_id = 5;
            $presupuesto->cod_cc = $this->centroCostos;
            $presupuesto->update();
            // Gerencia se entera de que tiene un presupuesto por validar.
            $this->presupuestoAprobacion($presupuesto, $presupuesto->gestion->comercial);
            return redirect()->route('presupuesto-proyecto')->with('success', 'Presupuesto aprobado y enviado a validación de gerencia');
        }

        // La gestión pasa a Propuesta (sin devolver una que ya está en Venta o Perdido)
        AprobacionPresupuesto::avanzarGestion($presupuesto);

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

        $this->marcarConCambiosPendientes();

        return redirect()->route('presupuesto-proyecto')->with('success', 'Presupuesto aprobado y Centro de costos asignado');
    }

    // Recalcula los valores de la base comercial y gestión comercial
    private function reCalculate($presupuesto){
        AprobacionPresupuesto::propagarValores($presupuesto);
    }

    // Rechaza el presupuesto y guarda la justificación
    public function rechazar(){
        $presupuesto = PresupuestoProyecto::where('id_gestion', $this->id_gestion)->first();
        // Mismas condiciones que los dos botones Rechazar de la vista (líder comercial y Gerencia).
        $user = Auth::user();
        $estadoActual = (int) $presupuesto->estado_id;
        $puedeLider = in_array((int) $user->rol, [1, 12], true) && ($estadoActual === 4 || !$user->comerciales()->exists());
        $puedeGerencia = $estadoActual === 5 && $user->espacioAdmin() && $user->can('aprobar-presupuestos');
        abort_unless($puedeLider || $puedeGerencia, 403);

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
            // El centro de costos se asignó al pasar a Gerencia; si nunca se aprobó, se libera para otro proyecto
            // (antes quedaba ocupado por un presupuesto rechazado).
            if (!$presupuesto->fecha_cc) {
                $presupuesto->cod_cc = null;
            }
        }
        else {
            $this->validate([
                'justificacion_compras' => ['nullable', 'string']
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
        $this->getValorTotalCliente();
    }

    public function updatedDia(){
        $this->dia = trim($this->dia);
        $this->validate([
            'dia' => ['required']
        ]);
        $this->getValorTotal();
        $this->getValorTotalCliente();
    }

    public function updatedOtros(){
        $this->otros = trim((string) $this->otros);
        $this->validate([
            'otros' => ['nullable', 'numeric', 'min:1']
        ], ['otros.min' => 'Otros (meses) es el número de meses: mínimo 1.']);
        $this->getValorTotal();
        $this->getValorTotalCliente();
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

        if (!empty($this->utilidad)) {
            $this->calcularPorUtilidad();
        }
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

    public function updatedValorUnitarioCliente()
    {
        $this->valor_unitario_cliente = trim($this->valor_unitario_cliente);
        $this->valor_unitario_cliente = str_replace(",", '', $this->valor_unitario_cliente);
        $this->validate([
            'valor_unitario_cliente' => ['required', 'numeric']
        ]);
        $this->getValorTotalCliente();
    }

    // Nuevo método, análogo a getValorTotal()
    public function getValorTotalCliente()
    {
        if (!is_null($this->cantidad) && !is_null($this->dia) && !is_null($this->valor_unitario_cliente)) {
            $this->valor_total_cliente = $this->cantidad * $this->dia * $this->factorOtros() * $this->valor_unitario_cliente;
            $this->getUtilidad();
        }
    }

    private function calcularPorUtilidad()
    {
        if ($this->utilidad <= 0) {
            return;
        }
        // Si no, pero sí hay valor_unitario (interno), calculamos el cliente a partir de él.
        if (!empty($this->valor_unitario)) {
            $this->valor_unitario_cliente = $this->valor_unitario / $this->utilidad;
            $this->getValorTotalCliente();
        }
}

    public function updatedProveedor(){
        $this->validate([
            // 'proveedor' => ['required', new SameCategory]
            'proveedor' => ['required']
        ]);
    }

    public function updatedUtilidad(){
        // La utilidad es un factor decimal: la coma es separador decimal ("0,7" era 7 y el precio salía 10 veces menor).
        $this->utilidad = str_replace(',', '.', trim((string) $this->utilidad));
        $this->validate([
            'utilidad' => self::REGLA_UTILIDAD
        ]);

        $this->calcularPorUtilidad();
    }

    public function updatedMes(){
        $this->mes = trim($this->mes);
        $this->validate([
            'mes' => ['required']
        ]);
    }
    /*
    public function updatedDias(){
        $this->dias = trim($this->dias);
        $this->validate([
            'dias' => ['required']
        ]);
    }
    */
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

    /** "Otros (meses)" multiplica el total; vacío o 0 cuenta como 1 (un solo mes). */
    private function factorOtros(): float
    {
        return is_numeric($this->otros) && (float) $this->otros > 0 ? (float) $this->otros : 1.0;
    }

    // Calcula el valor total del ítem
    public function getValorTotal(){
        if (!is_null($this->cantidad) && !is_null($this->dia) && !is_null($this->valor_unitario)){
            $this->valor_total = $this->cantidad * $this->dia * $this->factorOtros() * $this->valor_unitario;
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
        $this->valor_unitario_cliente = 0;
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

   public function calcularCentroCostos()
   {
        if (!$this->clienteSeleccionado) {
            $this->centroCostos = '';
            return;
        }

        // 1. Buscamos directamente el registro en clientes_parametro_cc
        $parametro = cliente_parametros_cc::find($this->clienteSeleccionado);
        $nomProyecto = gestionComercial::find($this->id_gestion)->nom_proyecto_cot ?? '';

        if (!$parametro || !$parametro->codigo_cc) {
            $this->centroCostos = '';
            return;
        }

        // 2. Tomamos el código de 2 dígitos (ej: 'C3', '09', '11')
        $codigoCC = $parametro->codigo_cc;

        // 3. Factores de fecha: Año actual y Mes por defecto actual (ej: '08')
        $anio = date('y');
        $mes = $this->mesCC ?? date('m');
        $mesFormateado = str_pad($mes, 2, '0', STR_PAD_LEFT);

        // Prefijo de búsqueda sin guiones (ej: "C32608")
        $prefijoBusqueda = "{$codigoCC}{$anio}{$mesFormateado}";

        // 4. Consultamos el último consecutivo en presupuestos_proyecto
        $ultimoPresupuesto = PresupuestoProyecto::where('cod_cc', 'LIKE', "{$prefijoBusqueda}%")
            ->orderBy('cod_cc', 'desc')
            ->first();

        $nuevoConsecutivo = 1;

        if ($ultimoPresupuesto) {
            // Extrae los 2 dígitos del consecutivo que vienen inmediatamente después del prefijo
            $sinPrefijo = substr($ultimoPresupuesto->cod_cc, strlen($prefijoBusqueda));
            if (preg_match('/^(\d{2})/', $sinPrefijo, $coincidencias)) {
                $nuevoConsecutivo = (int) $coincidencias[1] + 1;
            }
        }

        $consecutivoFormateado = str_pad($nuevoConsecutivo, 2, '0', STR_PAD_LEFT);

        // 5. Asignamos la cadena final sin guiones y con el nombre del proyecto al final
        $this->centroCostos = mb_strtoupper("{$prefijoBusqueda}{$consecutivoFormateado} - {$nomProyecto}");
    }

    private function marcarConCambiosPendientes()
    {
        $presupuesto = PresupuestoProyecto::find($this->presupuesto_id);

        if ($presupuesto) {
            // Guardamos el estado anterior antes de actualizar
            $estabaNotificado = (bool) $presupuesto->notificacion_actualizacion;

            // Actualizamos en BD
            $presupuesto->update(['notificacion_actualizacion' => true]);

            // Si estaba en false (no notificado), enviamos el correo a través del Trait
            if (!$estabaNotificado) {
                $this->actualizacionControllerPresupuesto($presupuesto);
            }
        }
    }
    
    public function marcarComoVisto($id_gestion = null)
    {
        abort_unless(Auth::user()->can('gestionar-presupuestos-especiales'), 403);
        // El id que manda la vista siempre es el de este presupuesto: no se acepta otro.
        $id = $this->id_gestion;

        if ($id) {
            $presto = PresupuestoProyecto::where('id_gestion', $id)->first();

            if ($presto) {
                $presto->update(['notificacion_actualizacion' => false]);
                
                // Reiniciamos ambos campos para limpiar estados en ambos roles/sub-roles
                ItemPresupuesto::where('presupuesto_id', $presto->id)->update([
                    'actualizado_con' => false,
                    'actualizado' => false
                ]);
                
                $this->presupuesto = $presto;
                
                // Forzar actualización en el componente
                $this->refresh();
                //$this->getItems();
            }
        }
    }
} 
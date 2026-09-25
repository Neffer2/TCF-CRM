<?php

namespace App\Http\Livewire\Productor\Ordenes;

use App\Models\Anticipo as ModelAnticipo;
use App\Models\Año;
use App\Models\EvidenciaAnticipo;
use App\Models\ItemAnticipo;
use App\Models\PresupuestoProyecto as CentrosCosto;
use App\Traits\Email;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Livewire\Component;
use Livewire\WithFileUploads;

class AnticipoProductor extends Component
{
    // Habilita la subida de archivos y envio de correos
    use WithFileUploads, Email;

    // Models Productor
    public $centro_costo, $item_presupuesto, $selectedItem, $descripcion, $cantidad, $dias, $otros, $valor_unitario, $valor_total,
        $valor_anticipo, $saldo, $total_anticipo, $observaciones_revision_lider, $rechazo_revision_lider, $observaciones_revision_gerencia, $rechazo_revision_gerencia,
        $observaciones_revision_evidencias, $rechazo_revision_evidencias;

    // Variables para el formulario de evidencias
    public $item_evidencia, $fecha_evidencia, $foto_evidencia, $observacion_evidencia;

    // Colección de evidencias
    public $evidencias = [];

    // Useful vars Productor
    public $centros_costo = [], $items_presupuesto = [], $items = [], $selected_item, $limiteCantidad, $limiteDias, $limiteOtros,
        $limiteValorUnitario, $limiteValorTotal, $items_anticipos_general = [], $firma_productor;

    // Filled
    public $anticipo_id, $queriedAnticipo;

    // Listener para ejecutar el metodo nuevoAnticipoProductor cuando se emite 'store-anticipo'
    protected $listeners = ['store-anticipo' => 'nuevoAnticipoProductor'];

    // Las fija el servidor (anticipo, ítems, total, firma, topes): el navegador no puede cambiarlas.
    protected $propiedadesBloqueadas = ['anticipo_id', 'queriedAnticipo', 'items', 'total_anticipo', 'firma_productor', 'centros_costo',
        'items_presupuesto', 'items_anticipos_general', 'evidencias', 'selected_item',
        'limiteCantidad', 'limiteDias', 'limiteOtros', 'limiteValorUnitario', 'limiteValorTotal'];

    public function updating($name, $value)
    {
        abort_if(in_array(explode('.', $name)[0], $this->propiedadesBloqueadas, true), 403);
    }

    /**
     * Saldo del ítem para anticipos: lo presupuestado menos lo pedido en órdenes de compra no anuladas y en otros
     * anticipos no anulados. La misma fórmula al crear y al corregir (antes la corrección ignoraba las órdenes).
     */
    private function saldoItem($item_info): array
    {
        $oc = $item_info->consumidos()->whereHas('OrdenCompra', function ($q) { $q->where('estado_id', '!=', 6); })->get();
        $anticipos = $item_info->consumidos_anticipo()
            ->whereHas('anticipo', function ($q) { $q->where('estado_id', '!=', 6); })
            ->when($this->queriedAnticipo, function ($q) { $q->where('anticipo_id', '!=', $this->queriedAnticipo->id); })
            ->get();
        return [
            ($item_info->cantidad * $item_info->dia * $item_info->otros) - ($oc->sum('cant_oc') + $anticipos->sum('cant')),
            $item_info->v_total - ($oc->sum('vtotal_oc') + $anticipos->sum('vanticipo')),
        ];
    }

    /**
     * Antes de guardar: el proyecto es un presupuesto aprobado del productor, cada ítem es de ese proyecto y cabe en
     * su saldo, y el anticipo no pasa el valor del ítem. Devuelve el total calculado aquí (no el que llegó del navegador).
     */
    private function totalValidado(): ?float
    {
        $presupuesto = CentrosCosto::where('productor', Auth::id())->where('estado_id', 1)->find($this->centro_costo);
        if (!$presupuesto) {
            $this->addError('items-error', 'Elige uno de tus proyectos aprobados.');
            return null;
        }
        $vistos = [];
        foreach ($this->items as $fila) {
            $item = $presupuesto->presupuestoItems->where('id', $fila['item_id'])->first();
            if (!$item || isset($vistos[$item->id])) {
                $this->addError('items-error', 'Uno de los ítems no es de este proyecto o está repetido.');
                return null;
            }
            $vistos[$item->id] = true;
            [$cant, $valor] = $this->saldoItem($item);
            if ((float) $fila['cant'] > $cant || (float) $fila['valor_total'] > $valor + 0.01
                || (float) $fila['valor_anticipo'] <= 0 || (float) $fila['valor_anticipo'] > (float) $fila['valor_total'] + 0.01) {
                $this->addError('items-error', 'El ítem '.$fila['display_item'].' supera lo disponible en el presupuesto.');
                return null;
            }
        }
        return (float) $this->items->sum(function ($fila) { return (float) $fila['valor_anticipo']; });
    }

    /** Guarda la firma dibujada como un archivo propio del anticipo (PNG validado); nunca sobrescribe otra. */
    private function guardarFirma($dataUri): ?string
    {
        if (!is_string($dataUri) || !str_starts_with($dataUri, 'data:image/png;base64,')) {
            return null;
        }
        $png = base64_decode(substr($dataUri, strlen('data:image/png;base64,')), true);
        if ($png === false || strlen($png) > 1024 * 1024 || substr($png, 0, 8) !== "\x89PNG\r\n\x1a\n") {
            return null;
        }
        $ruta = 'public/firmas_productores/'.Auth::id().'_'.Str::random(24).'.png';
        Storage::put($ruta, $png);
        return $ruta;
    }

    public function render()
    {
        return view('livewire.productor.ordenes.anticipo-productor');
    }

    public function mount() {
        $this->items = collect();
        $this->evidencias = collect();
        $this->items_anticipos_general = collect();

        if ($this->anticipo_id) {
            $this->queriedAnticipo = ModelAnticipo::find($this->anticipo_id);
            abort_unless($this->queriedAnticipo, 404);
            // El productor solo abre sus anticipos (líder y admin entran por sus propias rutas).
            if ((int) Auth::user()->rol === 7) {
                abort_unless((int) $this->queriedAnticipo->productor_id === (int) Auth::id(), 403);
            }
        }

        $this->getData();
    }

    public function getData() {
        // Obtenemos el listado de todos los items de los anticipos que aun no se han cerrado
        $this->items_anticipos_general = ItemAnticipo::whereHas('anticipo', function($query) {
            $query->where('estado_id', '!=',  10);
        })->get();

        // Consultamos si el productor ya tiene una firma guardada
        // (su último anticipo con firma: el primero solía ser un jurídico sin firma y pedía firmar otra vez)
        $this->firma_productor = ModelAnticipo::where('productor_id', Auth::user()->id)->whereNotNull('firma_productor')
            ->where('firma_productor', '!=', '')->latest('id')->value('firma_productor') ?: '';

        // Si existe un anticipo, cargamos los datos
        if ($this->queriedAnticipo) {
            $this->centro_costo = $this->queriedAnticipo->presupuesto_id;
            $this->total_anticipo = $this->queriedAnticipo->total_anticipo;

            $this->centros_costo = CentrosCosto::find($this->centro_costo);
            $this->updatedCentroCosto();

            // Items
            $queriedItems = $this->queriedAnticipo->anticipoItems;
            foreach ($queriedItems as $item){
                $newItem = [
                    'item_id' => $item->item_id,
                    'display_item' => $item->display_item,
                    'desc' => $item->desc,
                    'cant' => $item->cant,
                    'dias' => $item->dias,
                    'otros' => $item->otros,
                    'valor_unitario' => $item->vunit,
                    'valor_total' => $item->vtotal,
                    'valor_anticipo' => $item->vanticipo,
                    'saldo' => $item->saldo,
                ];

                $this->items->push($newItem);
            }

            // Evidencias cargadas (las ve el productor al cargarlas y el líder al revisarlas)
            $this->evidencias = EvidenciaAnticipo::with('itemPresupuesto')->where('anticipo_id', $this->queriedAnticipo->id)->get();
        }
        else {
            // Centros de costo del productor autenticado
            $centros = CentrosCosto::where([
                ['productor', Auth::user()->id],
                ['estado_id', 1],
                ['fecha_cc', '>=', Año::orderBy('description', 'desc')->first()->description.'-01-01']
            ])
                ->orderBy('created_at', 'desc')
                ->get();

            $this->centros_costo = $centros;
        }
    }

    public function getItemLimite() {
        // Trae información del item seleccionado
        $item_info = $this->items_presupuesto->where('id', $this->item_presupuesto)->first();

        /* LIMITES: omiten los ítems del anticipo actual si es actualización, y lo anulado */
        [$this->limiteCantidad, $this->limiteValorTotal] = $this->saldoItem($item_info);

        $this->limiteDias = $item_info->dia;
        $this->limiteOtros = $item_info->otros;
        $this->limiteValorUnitario = $item_info->v_unitario;
    }

    // Agrega un nuevo item al anticipo
    public function newItem() {
        // Límites y derivados se recalculan en servidor.
        $this->getItemLimite();
        $this->getValorTotal();

        $this->validate([
            'item_presupuesto' => 'required',
            'cantidad' => 'required|numeric|min: 1|max:'.$this->limiteCantidad,
            'dias' => 'required|numeric|min: 1|max:'.$this->limiteDias,
            'otros' => 'required|numeric|min: 1|max:'.$this->limiteOtros,
            'valor_unitario' => 'required|numeric|min:1|max:'.$this->limiteValorUnitario,
            'valor_total' => 'required|numeric|min: 1|max:'.$this->limiteValorTotal,
            // El anticipo no puede superar el valor total del item
            'valor_anticipo' => 'required|numeric|min: 1|max:'.$this->valor_total
        ]);

        // El saldo siempre se deriva en servidor
        $this->saldo = $this->valor_total - $this->valor_anticipo;

        $item = $this->centros_costo->find($this->centro_costo)->presupuestoItems->where('id', $this->item_presupuesto)->first();

        $newItem = [
            'item_id' => $item->id,
            'display_item' => $item->displayItem(),
            'desc' => $item->descripcion,
            'cant' => $this->cantidad,
            'dias' => $this->dias,
            'otros' => $this->otros,
            'valor_unitario' => $this->valor_unitario,
            'valor_total' => $this->valor_total,
            'valor_anticipo' => $this->valor_anticipo,
            'saldo' => $this->saldo,
        ];

        $this->items->push($newItem);
        $this->total_anticipo += $this->valor_anticipo;

        // Limpia los campos del formulario
        $this->resetFields([
            'item_presupuesto',
            'cantidad',
            'dias',
            'otros',
            'valor_unitario',
            'valor_total',
            'valor_anticipo',
            'saldo'
        ]);
    }

    // Carga los datos de un item seleccionado para editar
    public function getItem($itemId) {
        $this->selected_item = $itemId;
        $item = $this->items[$itemId];

        $this->item_presupuesto = $item['item_id'];
        $this->cantidad = $item['cant'];
        $this->dias = $item['dias'];
        $this->otros = $item['otros'];
        $this->valor_unitario = $item['valor_unitario'];
        $this->valor_total = $item['valor_total'];
        $this->valor_anticipo = $item['valor_anticipo'];
        $this->saldo = $item['saldo'];
    }

    // Edita un item existente en el anticipo
    public function actionEdit() {
        // La edición respeta los mismos topes que la creación:
        // límites y derivados recalculados en servidor.
        $this->getItemLimite();
        $this->getValorTotal();

        $this->validate([
            'item_presupuesto' => 'required',
            'cantidad' => 'required|numeric|min: 1|max:'.$this->limiteCantidad,
            'dias' => 'required|numeric|min: 1|max:'.$this->limiteDias,
            'otros' => 'required|numeric|min: 1|max:'.$this->limiteOtros,
            'valor_unitario' => 'required|numeric|min:1|max:'.$this->limiteValorUnitario,
            'valor_total' => 'required|numeric|min: 1|max:'.$this->limiteValorTotal,
            'valor_anticipo' => 'required|numeric|min: 1|max:'.$this->valor_total
        ]);

        // El saldo siempre se deriva en servidor
        $this->saldo = $this->valor_total - $this->valor_anticipo;

        $item = $this->centros_costo->find($this->centro_costo)->presupuestoItems->where('id', $this->item_presupuesto)->first();
        $this->total_anticipo -= $this->items[$this->selected_item]['valor_anticipo'];

        $this->items[$this->selected_item] = [
            'item_id' => $item->id,
            'display_item' => $item->displayItem(),
            'desc' => $item->descripcion,
            'cant' => $this->cantidad,
            'dias' => $this->dias,
            'otros' => $this->otros,
            'valor_unitario' => $this->valor_unitario,
            'valor_total' => $this->valor_total,
            'valor_anticipo' => $this->valor_anticipo,
            'saldo' => $this->saldo,
        ];

        $this->total_anticipo += $this->valor_anticipo;

        // Limpia los campos del formulario
        $this->resetFields([
            'item_presupuesto',
            'cantidad',
            'dias',
            'otros',
            'valor_unitario',
            'valor_total',
            'valor_anticipo',
            'saldo'
        ]);

        unset($this->selected_item);
    }

    // Elimina un item del anticipo
    public function deleteItem($itemId) {
        $this->total_anticipo -= $this->items[$itemId]['valor_anticipo'];
        unset($this->items[$itemId]);
    }

    /**
     *  UPDATES
     **/
    public function updatedCentroCosto() {
        $this->validate([
            'centro_costo' => 'required'
        ]);

        if ($this->centro_costo) {
            $items_presupuesto = $this->centros_costo->where('id', $this->centro_costo)->first()->presupuestoItems;
            $this->items_presupuesto = $items_presupuesto->filter(function($item) {
                return $item->proveedores->contains('id', 3) && $item->disponible == 1;
            });
        }
        else {
            $this->items_presupuesto = [];
        }

        $this->resetFields(['item_presupuesto', 'cantidad', 'dias', 'otros', 'valor_unitario', 'valor_total']);
    }

    public function updatedItemPresupuesto() {
        $this->validate([
            'item_presupuesto' => 'required'
        ]);

        foreach ($this->items as $item){
            if ($item['item_id'] == $this->item_presupuesto){
                $this->addError('items-error', 'Este item ya fue agregado en esta anticipo.');
                return back();
            }
        }

        if ($this->item_presupuesto){
            $this->getItemLimite();

            $this->cantidad = $this->limiteCantidad;
            $this->dias = $this->limiteDias;
            $this->otros = $this->limiteOtros;
            $this->valor_unitario = $this->limiteValorUnitario;
            $this->valor_total = $this->limiteValorTotal;
        }
        else{
            $this->items_presupuesto = [];
        }
    }

    public function updatedCantidad(){
        $this->cantidad = trim($this->cantidad);
        $this->cantidad = str_replace(",",'', $this->cantidad);

        $this->getItemLimite();

        $this->validate([
            'cantidad' => 'required|numeric|min: 1|max:'.$this->limiteCantidad
        ]);

        $this->getValorTotal();
    }

    public function updatedValorUnitario(){
        $this->valor_unitario = trim($this->valor_unitario);
        $this->valor_unitario = str_replace(",",'', $this->valor_unitario);
        $this->validate([
            'valor_unitario' => 'required|numeric|min: 1|max:'.$this->limiteValorUnitario
        ]);

        $this->getValorTotal();
    }

    public function updatedValorTotal(){
        $this->validate([
            'valor_total' => 'required|numeric|min: 1|max:'.$this->limiteValorTotal
        ]);

        $this->getValorTotal();
    }

    public function updatedValorAnticipo() {
        if ($this->item_presupuesto && $this->valor_anticipo) {
            if ($this->valor_anticipo > $this->valor_total) {
                $this->addError('valor_anticipo', 'El valor del anticipo no puede ser mayor al valor total del item.');
                $this->resetFields(['saldo']);
                return back();
            }

            $this->saldo = $this->valor_total - $this->valor_anticipo;
        }
        else {
            $this->resetFields(['saldo']);
        }
    }

    public function getValorTotal(){
        $this->valor_total = ($this->cantidad  * $this->valor_unitario);
    }

    /*
        * GESTIONES
    */
    public function nuevoAnticipoProductor($data = null) {
        // Solo un productor puede solicitar anticipos
        if (Auth::user()->rol != 7) {
            $this->addError('items-error', 'Solo un productor puede solicitar anticipos.');
            return back();
        }

        if ($this->items->count() <= 0){
            $this->addError('items-error', 'No se han agregado items al anticipo.');
            return back();
        }

        $total = $this->totalValidado();
        if ($total === null) {
            return back();
        }

        // Si ya hay una firma guardada se reutiliza; si no, la dibujada se guarda como archivo propio
        // (antes todos los anticipos compartían un archivo que se sobrescribía, y la carpeta podía no existir).
        $firma = $this->firma_productor ?: $this->guardarFirma($data);
        if (!$firma) {
            $this->addError('items-error', 'Firma no válida: vuelve a firmar.');
            return back();
        }

        // Creamos el registro en la tabla "anticipos"
        $anticipo = ModelAnticipo::create([
            'oc_id' => null,
            'presupuesto_id' => $this->centro_costo,
            'porcentaje_anticipo' => 0,
            'total_anticipo' => $total,
            'estado_id' => 8,
            'fecha_solicitud' => now(),
            'productor_id' => Auth::user()->id,
            'firma_productor' => $firma
        ]);

        // Inicializamos el modelo de items_anticipo y guardamos los items
        $item_anticipo = new ItemAnticipo();
        foreach ( $this->items as $item ) {
            $item_anticipo->create([
                'anticipo_id' => $anticipo->id,
                'item_id' => $item['item_id'],
                'display_item' => $item['display_item'],
                'desc' => $item['desc'],
                'cant' => $item['cant'],
                'dias' => $item['dias'],
                'otros' => $item['otros'],
                'vunit' => $item['valor_unitario'],
                'vtotal' => $item['valor_total'],
                'vanticipo' => $item['valor_anticipo'],
                'saldo' => $item['saldo'],
            ]);
        }

        // Se envia notificación por correo a los Lideres de producción
        $this->anticipoProdRevisionLiderProd($anticipo);

        $this->resetFields([
            'item_presupuesto',
            'cantidad',
            'dias',
            'otros',
            'valor_unitario',
            'valor_total',
            'valor_anticipo',
            'saldo',
            'firma_productor'
        ]);

        return redirect()->route('solicitd-anticipo-prod')->with('success', 'Anticipo creado');
    }

    public function actualizarAnticipoProductor() {
        // Solo el productor dueño puede actualizar, y solo mientras el
        // anticipo esté pendiente (8) o rechazado (11, 12).
        if (!(Auth::user()->rol == 7 && $this->queriedAnticipo->productor_id == Auth::user()->id)) {
            $this->addError('items-error', 'No tienes permisos para actualizar este anticipo.');
            return back();
        }
        if (!in_array($this->queriedAnticipo->estado_id, [8, 11, 12, 13])) {
            $this->addError('items-error', 'El anticipo no está en un estado válido para actualizarse.');
            return back();
        }

        if ($this->items->count() <= 0){
            $this->addError('items-error', 'No se han agregado items al anticipo.');
            return back();
        }
        $total = $this->totalValidado();
        if ($total === null) {
            return back();
        }
        \DB::beginTransaction();

        // Vuelve a Revisión del líder (8) con el total de los ítems corregidos (antes quedaba el total viejo)
        $this->queriedAnticipo->update([
            'estado_id' => 8,
            'total_anticipo' => $total,
        ]);

        // Elimina los items actuales y crea los nuevos
        foreach ($this->queriedAnticipo->anticipoItems as $item){
            $item->delete();
        }

        $item_anticipo = new ItemAnticipo();
        foreach ( $this->items as $item ) {
            $item_anticipo->create([
                'anticipo_id' => $this->queriedAnticipo->id,
                'item_id' => $item['item_id'],
                'display_item' => $item['display_item'],
                'desc' => $item['desc'],
                'cant' => $item['cant'],
                'dias' => $item['dias'],
                'otros' => $item['otros'],
                'vunit' => $item['valor_unitario'],
                'vtotal' => $item['valor_total'],
                'vanticipo' => $item['valor_anticipo'],
                'saldo' => $item['saldo'],
            ]);
        }
        \DB::commit();

        // Se envia notificación por correo a los Lideres de producción
        $this->anticipoProdRevisionLiderProd($this->queriedAnticipo);

        $this->resetFields([
            'item_presupuesto',
            'cantidad',
            'dias',
            'otros',
            'valor_unitario',
            'valor_total',
            'valor_anticipo',
            'saldo'
        ]);

        return redirect()->route('lista-anticipos-prod')->with('success', 'Anticipo actualizado');
    }

    public function revisionAnticipoProductor($estado_id) {
        $redirect_route = 'lista-anticipos-lid';

        // GUARDIA DE TRANSICIONES: qué rol puede pedir cada estado y desde
        // qué estado actual. Todo valor no contemplado se rechaza (antes,
        // cualquier estado enviado desde el navegador se escribía tal cual).
        $reglas = [
            7  => ['roles' => [1], 'desde' => [9]],  // Gerencia aprueba -> evidencias
            9  => ['roles' => [6], 'desde' => [8]],  // Líder aprueba -> gerencia
            11 => ['roles' => [6], 'desde' => [8]],  // Rechazo líder
            12 => ['roles' => [1], 'desde' => [9]],  // Rechazo gerencia
        ];

        if (!isset($reglas[$estado_id])) {
            $this->addError('items-error', 'Transición de estado no permitida.');
            return back();
        }
        if (!in_array(Auth::user()->rol, $reglas[$estado_id]['roles'])
            || (in_array($estado_id, [7, 12]) && !Auth::user()->can('revisar-anticipos-gerencia'))) {
            $this->addError('items-error', 'No tienes permisos para realizar esta acción.');
            return back();
        }
        if (!in_array($this->queriedAnticipo->estado_id, $reglas[$estado_id]['desde'])) {
            $this->addError('items-error', 'El anticipo no está en un estado válido para esta acción.');
            return back();
        }

        // Los correos salen después de guardar, con la observación ya asignada (antes llevaban el motivo viejo).
        $correo = null;

        // REVISIÓN GERENCIA APROBADA
        if ($estado_id == 7) {
            $this->validate([
                'observaciones_revision_gerencia' => 'required|string|max:1000'
            ]);

            $this->queriedAnticipo->observaciones_revision_gerencia = $this->observaciones_revision_gerencia;
            $redirect_route = 'lista-anticipos-admin';

            // Se envia notificación al productor para el cargue de evidencias
            $correo = 'anticipoProdEvidencias';
        }
        // REVISIÓN LIDER PRODUCCIÓN APROBADA
        elseif ($estado_id == 9) {
            $this->validate([
                'observaciones_revision_lider' => 'required|string|max:1000'
            ]);

            // SI EL VALOR TOTAL DEL ANTICIPO ES MENOR A 500.000, SE CAMBIA A ESTADO EVIDENCIAS ($estado_id = 7),
            // DE LO CONTRARIO, SE ENVIA A REVISIÓN DE GERENCIA (estado_id = 9)
            if ($this->queriedAnticipo->total_anticipo < 500000) {
                $estado_id = 7;

                // Se envia notificación al productor para el cargue de evidencias
                $correo = 'anticipoProdEvidencias';
            }
            else {
                // Se envia notificación por correo a los Gerentes
                $correo = 'anticipoProdRevisionGerencia';
            }

            $this->queriedAnticipo->observaciones_revision_lider = $this->observaciones_revision_lider;
        }
        // RECHAZO REVISIÓN LIDER DE PRODUCCIÓN
        elseif ($estado_id == 11) {
            $this->validate([
                'rechazo_revision_lider' => 'required|string|max:1000',
            ]);

            $this->queriedAnticipo->rechazo_revision_lider = $this->rechazo_revision_lider;

            // Se envia notificación al productor para gestionar el rechazo
            $correo = 'anticipoProdRechazoLiderProd';
        }
        // RECHAZO REVISIÓN GERENCIA
        elseif ($estado_id == 12) {
            $this->validate([
                'rechazo_revision_gerencia' => 'required|string|max:1000',
            ]);

            $this->queriedAnticipo->rechazo_revision_gerencia = $this->rechazo_revision_gerencia;

            // Se envia notificación al productor para gestionar el rechazo
            $correo = 'anticipoProdRechazoGerencia';

            $redirect_route = 'lista-anticipos-admin';
        }

        $this->queriedAnticipo->estado_id = $estado_id;
        $this->queriedAnticipo->update();
        if ($correo) {
            $this->{$correo}($this->queriedAnticipo);
        }

        return redirect()->route($redirect_route)->with('success', 'Información guardada correctamente.');
    }

    /**
     * El líder de producción revisa las evidencias (estado 10): aprobadas → Aprobado, pendiente de causar (1),
     * y Contabilidad recibe el aviso; devueltas → Cargue de evidencias (7) con el motivo para el productor.
     * (Este paso no existía: los anticipos se quedaban en revisión de evidencias para siempre.)
     */
    public function revisionEvidencias($aprobar) {
        if ((int) Auth::user()->rol !== 6) {
            $this->addError('items-error', 'No tienes permisos para realizar esta acción.');
            return back();
        }
        if ((int) $this->queriedAnticipo->fresh()->estado_id !== 10) {
            $this->addError('items-error', 'El anticipo no está en revisión de evidencias.');
            return back();
        }
        if ($aprobar) {
            $this->validate(['observaciones_revision_evidencias' => 'nullable|string|max:1000']);
            $this->queriedAnticipo->observaciones_revision_evidencias = $this->observaciones_revision_evidencias;
            $this->queriedAnticipo->fecha_aprobacion = now();
            $this->queriedAnticipo->estado_id = 1;
            $this->queriedAnticipo->update();
            $this->anticipoProdEvidenciasAprobadas($this->queriedAnticipo);
        } else {
            $this->validate(['rechazo_revision_evidencias' => 'required|string|max:1000']);
            $this->queriedAnticipo->rechazo_revision_evidencias = $this->rechazo_revision_evidencias;
            $this->queriedAnticipo->estado_id = 7;
            $this->queriedAnticipo->update();
            $this->anticipoProdRechazoEvidencias($this->queriedAnticipo);
        }
        return redirect()->route('lista-anticipos-lid')->with('success', 'Información guardada correctamente.');
    }

    public function borrarFirmaProductor() {
        // Solo deja de reutilizar la firma guardada para firmar de nuevo. Antes borraba el archivo de la ruta que
        // mandara el navegador (cualquier archivo de storage) y dejaba sin firma a los anticipos anteriores.
        $this->firma_productor = null;
    }

    /*
        * EVIDENCIAS
    */
    // Solo el productor dueño gestiona evidencias: al cargarlas (7) o cuando Contabilidad las devolvió (13)
    private function puedeGestionarEvidencias() {
        return $this->queriedAnticipo
            && Auth::user()->rol == 7
            && $this->queriedAnticipo->productor_id == Auth::user()->id
            && in_array((int) $this->queriedAnticipo->estado_id, [7, 13], true);
    }

    public function newEvidencia() {
        if (!$this->puedeGestionarEvidencias()) {
            $this->addError('evidencias-error', 'No tienes permisos para cargar evidencias en este anticipo.');
            return back();
        }

        $this->validate([
            'item_evidencia' => ['required', \Illuminate\Validation\Rule::in($this->queriedAnticipo->anticipoItems->pluck('item_id')->all())],
            'fecha_evidencia' => 'required|date',
            'foto_evidencia' => 'required|file|mimes:jpg,jpeg,png|max:10000',
            'observacion_evidencia' => 'required|string|max:255'
        ]);

        $evidencia = EvidenciaAnticipo::create([
            'anticipo_id' => $this->queriedAnticipo->id,
            'item_id' => $this->item_evidencia,
            'fecha_evidencia' => $this->fecha_evidencia,
            'foto_evidencia' => $this->foto_evidencia->store('public/evidencias_anticipos'),
            'observacion_evidencia' => $this->observacion_evidencia,
        ]);

        $this->evidencias->push($evidencia);
        $this->resetFields(['item_evidencia', 'fecha_evidencia', 'foto_evidencia', 'observacion_evidencia']);
    }

    public function deleteEvidencia($itemId) {
        $item_evidencia_anticipo = EvidenciaAnticipo::find($itemId);

        // Solo evidencias del anticipo propio, y solo en estado de cargue
        if (!$item_evidencia_anticipo
            || !$this->puedeGestionarEvidencias()
            || $item_evidencia_anticipo->anticipo_id != $this->queriedAnticipo->id) {
            $this->addError('evidencias-error', 'No tienes permisos para eliminar esta evidencia.');
            return back();
        }

        Storage::delete($item_evidencia_anticipo->foto_evidencia);

        $item_evidencia_anticipo->delete();
        $this->evidencias = $this->evidencias->reject(function ($e) use ($itemId) {
            return $e->id === $itemId;
        });
    }

    public function enviarEvidencias() {
        if (!$this->puedeGestionarEvidencias()) {
            $this->addError('evidencias-error', 'No tienes permisos para enviar evidencias de este anticipo.');
            return back();
        }

        // Al menos una evidencia por cada ítem del anticipo (antes bastaba con el número de fotos)
        $conEvidencia = EvidenciaAnticipo::where('anticipo_id', $this->queriedAnticipo->id)->pluck('item_id')->map(function ($id) { return (int) $id; })->unique();
        $faltan = $this->queriedAnticipo->anticipoItems->pluck('item_id')->map(function ($id) { return (int) $id; })->diff($conEvidencia);
        if ($faltan->isNotEmpty()) {
            $this->addError('evidencias-error', 'Falta la evidencia de '.$faltan->count().' ítem(s) del anticipo.');
            return back();
        }

        // Cambiamos el estado a estado_id = 10 (Revisión evidencias lider)
        $this->queriedAnticipo->update([
            'estado_id' => 10
        ]);

        return redirect()->route('lista-anticipos-prod')->with('success', 'Evidencias enviadas correctamente.');
    }

    public function resetFields($fields){
        foreach ($fields as $field){
            $this->$field = '';
        }
    }
}

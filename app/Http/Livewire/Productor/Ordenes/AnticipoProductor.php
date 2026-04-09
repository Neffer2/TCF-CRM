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
        }

        $this->getData();
    }

    public function getData() {
        // Obtenemos el listado de todos los items de los anticipos que aun no se han cerrado
        $this->items_anticipos_general = ItemAnticipo::whereHas('anticipo', function($query) {
            $query->where('estado_id', '!=',  10);
        })->get();

        // Consultamos si el productor ya tiene una firma guardada
        $anticipos_productor = ModelAnticipo::where('productor_id', Auth::user()->id)->first();
        $this->firma_productor = $anticipos_productor ? $anticipos_productor->firma_productor : '';

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

            // Si el estado_id es 7 (Evidencias), obtenemos las evidencias guardadas
            if ($this->queriedAnticipo->estado_id == 7) {
                $this->evidencias = EvidenciaAnticipo::where([
                    ['anticipo_id', $this->queriedAnticipo->id],
                ])->get();
            }
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

        /* LIMITES: LOS LIMITES OMITEN LOS ITEMS DEL ANTICIPO ACTUAL SI ES ACTUALIZACIÓN */
        if ($this->queriedAnticipo){
            $this->limiteCantidad = ( ( $item_info->cantidad * $item_info->dia * $item_info->otros ) - $item_info->consumidos_anticipo()->where('anticipo_id', '!=', $this->queriedAnticipo->id)->get()->sum('cant') );
            $this->limiteValorTotal = ( $item_info->v_total - $item_info->consumidos_anticipo()->where('anticipo_id', '!=', $this->queriedAnticipo->id)->get()->sum('vanticipo') );
        }
        else {
            $this->limiteCantidad = ( ( $item_info->cantidad * $item_info->dia * $item_info->otros ) - ( $item_info->consumidos()->get()->sum('cant_oc') + $item_info->consumidos_anticipo()->get()->sum('cant') ) );
            $this->limiteValorTotal = ( $item_info->v_total - ( $item_info->consumidos()->get()->sum('vtotal_oc') + $item_info->consumidos_anticipo()->get()->sum('vanticipo') ) );
        }

        $this->limiteDias = $item_info->dia;
        $this->limiteOtros = $item_info->otros;
        $this->limiteValorUnitario = $item_info->v_unitario;
    }

    // Agrega un nuevo item al anticipo
    public function newItem() {
        $this->validate([
            'item_presupuesto' => 'required',
            'cantidad' => 'required|numeric|min: 1|max:'.$this->limiteCantidad,
            'dias' => 'required|numeric|min: 1|max:'.$this->limiteDias,
            'otros' => 'required|numeric|min: 1|max:'.$this->limiteOtros,
            'valor_unitario' => 'required|min: 1numeric|max:'.$this->limiteValorUnitario,
            'valor_total' => 'required|numeric|min: 1|max:'.$this->limiteValorTotal,
            'valor_anticipo' => 'required|numeric|min: 1'
        ]);

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
        $this->validate([
            'item_presupuesto' => 'required',
            'cantidad' => 'required|numeric|min: 1',
            'dias' => 'required|numeric|min: 1',
            'otros' => 'required|numeric|min: 1',
            'valor_unitario' => 'required|min: 1',
            'valor_total' => 'required|numeric|min: 1',
            'valor_anticipo' => 'required|numeric|min: 1'
        ]);

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
                return Str::contains($item->proveedor, 's:1:"3"') && $item->disponible == 1;
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
        if ($this->items->count() <= 0){
            $this->addError('items-error', 'No se han agregado items al anticipo.');
            return back();
        }

        $firma = "public/firmas_productores/".Auth::user()->id.".png";

        // Si ya existe una firma guardada, usamos la ruta de esa firma, de lo contrario guardamos la nueva firma
        if ($this->firma_productor) {
            $firma = $this->firma_productor;
        }
        else {
            // Procesa la imagen de la firma recibida en base64
            $data_uri = $data;
            $encoded_image = explode(",", $data_uri)[1];
            $decoded_image = base64_decode($encoded_image);

            // Guarda la firma como archivo PNG en storage
            file_put_contents("storage" . str_replace("public", "", $firma), $decoded_image);
        }

        // Creamos el registro en la tabla "anticipos"
        $anticipo = ModelAnticipo::create([
            'oc_id' => null,
            'presupuesto_id' => $this->centro_costo,
            'porcentaje_anticipo' => 0,
            'total_anticipo' => $this->total_anticipo,
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
        if ($this->items->count() <= 0){
            $this->addError('items-error', 'No se han agregado items al anticipo.');
            return back();
        }

        // Actualizamos el estado del anticipo a estado_id = 8 (Revisión lider de producción)
        $this->queriedAnticipo->update([
            'estado_id' => 8
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

        // REVISIÓN GERENCIA APROBADA
        if ($estado_id == 7) {
            $this->validate([
                'observaciones_revision_gerencia' => 'required|string|max:1000'
            ]);

            $this->queriedAnticipo->observaciones_revision_gerencia = $this->observaciones_revision_gerencia;
            $redirect_route = 'lista-anticipos-admin';

            // Se envia notificación al productor para el cargue de evidencias
            $this->anticipoProdEvidencias($this->queriedAnticipo);
        }
        // REVISIÓN LIDER PRODUCCIÓN APROBADA
        elseif ($estado_id == 9) {
            $this->validate([
                'observaciones_revision_lider' => 'required|string|max:1000'
            ]);

            // SI EL VALOR TOTAL DEL ANTICIPO ES MENOR A 5.000.000, SE CAMBIA A ESTADO EVIDENCIAS ($estado_id = 7),
            // DE LO CONTRARIO, SE ENVIA A REVISIÓN DE GERENCIA (estado_id = 9)
            if ($this->queriedAnticipo->total_anticipo < 5000000) {
                $estado_id = 7;

                // Se envia notificación al productor para el cargue de evidencias
                $this->anticipoProdEvidencias($this->queriedAnticipo);
            }
            else {
                // Se envia notificación por correo a los Gerentes
                $this->anticipoProdRevisionGerencia($this->queriedAnticipo);
            }

            $this->queriedAnticipo->observaciones_revision_lider = $this->observaciones_revision_lider;
        }
        // RECHAZO REVISIÓN LIDER DE PRODUCCIÓN
        elseif ($estado_id == 11) {
            $this->validate([
                'rechazo_revision_lider' => 'required|string|max:1000',
            ]);

            // Se envia notificación al productor para gestionar el rechazo
            $this->anticipoProdRechazoLiderProd($this->queriedAnticipo);

            $this->queriedAnticipo->rechazo_revision_lider = $this->rechazo_revision_lider;
        }
        // RECHAZO REVISIÓN GERENCIA
        elseif ($estado_id == 12) {
            $this->validate([
                'rechazo_revision_gerencia' => 'required|string|max:1000',
            ]);

            $this->queriedAnticipo->rechazo_revision_gerencia = $this->rechazo_revision_gerencia;

            // Se envia notificación al productor para gestionar el rechazo
            $this->anticipoProdRechazoGerencia($this->queriedAnticipo);

            $redirect_route = 'lista-anticipos-admin';
        }

        $this->queriedAnticipo->estado_id = $estado_id;
        $this->queriedAnticipo->update();

        return redirect()->route($redirect_route)->with('success', 'Información guardada correctamente.');
    }

    public function borrarFirmaProductor() {
        Storage::delete($this->firma_productor);
        $this->firma_productor = null;
    }

    /*
        * EVIDENCIAS
    */
    public function newEvidencia() {
        $this->validate([
            'item_evidencia' => 'required',
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
        Storage::delete($item_evidencia_anticipo->foto_evidencia);

        $item_evidencia_anticipo->delete();
        $this->evidencias = $this->evidencias->reject(function ($e) use ($itemId) {
            return $e->id === $itemId;
        });
    }

    public function enviarEvidencias() {
        if ($this->evidencias->count() < $this->items->count()) {
            $this->addError('evidencias-error', 'No se han cargado evidencias.');
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

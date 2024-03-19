<?php

namespace App\Http\Livewire\Com\Presupuesto; 

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
use App\Models\PresupuestoProyecto; 
use App\Traits\Email;

class Presupuesto extends Component   
{ 
    use Email; 

    // Models
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

    public $imprevistos = 0;
    public $administracion = 0;
    public $fee = 0;

    public $centroCostos;
    public $justificacion;
    public $justificacion_compras;

    // Useful vars
    public $presupuesto;
    public $items = [];
    public $presupuesto_id;
    public $ciudades = []; 
    public $meses = [];
    public $categorias_proveedor = [];
    public $proveedores = [];
    public $tarifario = [];
    public $selected_item;
    public $rentabilidadView = false;
    public $estadoValidator;
    public $cod_cc;

    // metricas 
    public $margenGeneral = 0;
    public $costosProyecto = 0; 
    public $ventaProyecto = 0;
    public $margenProyecto = 0;
    public $margenBruto = 0;

    // Contacto
    public $nombre;
    public $cliente;
    public $nomProyecto;
    public $ciudadContacto;

    // globals
    public $id_gestion; 
 
    public function render()
    {
        return view('livewire.com.presupuesto.presupuesto');
    }
 
    public function mount(){ 
        $validator = PresupuestoProyecto::where('id_gestion', $this->id_gestion)->first();
        if (is_null($validator)){
            $presupuesto = new PresupuestoProyecto;
            $presupuesto->id_gestion = $this->id_gestion; 
            $presupuesto->cod_cot = $this->getLatestCodCot() + 1; 
            $presupuesto->save();
            $this->presupuesto = $presupuesto;

            $this->presupuesto_id = $presupuesto->id;
            $this->estadoValidator = $presupuesto->estado_id;
            $this->cod_cc = $presupuesto->cod_cc;
        }else { 
            $this->presupuesto_id = $validator->id;
            $this->estadoValidator = $validator->estado_id;
            $this->cod_cc = $validator->cod_cc; 
            $this->justificacion = $validator->justificacion;
            $this->justificacion_compras = $validator->justificacion_compras;
            $this->presupuesto = $validator;
        }


        // Valida si es actualización. 
        if ($this->cod_cc){
            // $this->showJustificacion = true; 
        }

        $this->refresh();
        $this->getCiudades();
        $this->getProveedores();
        $this->getMeses();
        $this->getTarifario();
    }

    public function getLatestCodCot(){
        $results = PresupuestoProyecto::select('cod_cot')->orderBy('id', 'desc')->limit(1)->first();
        if (is_null($results)){
            return 10000;
        }
        return $results->cod_cot;
    }

    public function new_item(){       
        $presto = PresupuestoProyecto::where('id_gestion', $this->id_gestion)->first();
        
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
            'utilidad' => ['required', 'numeric'],
            'mes' => ['required'], 
            'dias' => ['required'],
            'ciudad' => ['required'],
            
            'imprevistos' => ['required', 'numeric'],
            'administracion' => ['required', 'numeric'],
            'fee' => ['required', 'numeric'],
        ]);

        if ($this->presupuesto->gestion->claro){
            $this->validate([
                'valor_total_cliente' => ['numeric', 'required']
            ]);
        }
         
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

        // Indica actualiazcion.
        if ($presto->cod_cc){ 
            $item->actualizado = true;
            $this->setEnEdicion($presto);
        }

        $item->v_unitario_cot = ($this->utilidad > 0) ? $this->valor_unitario / $this->utilidad : 0;
        $item->v_total_cot = ($this->utilidad > 0) ? $this->cantidad * $this->dia * $this->otros * $item->v_unitario_cot : 0;
        $item->rentabilidad = ($this->utilidad > 0) ? $item->v_total_cot - $item->v_total : 0;
        $item->save();

        $this->refresh();
        $this->limpiar();
    }

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

    public function getItems(){
        $this->items = ItemPresupuesto::where('presupuesto_id', $this->presupuesto_id)->get();
    }

    public function getMetricas(){
        $this->getInfoFacturas();   
        (!$this->margenGeneral = ItemPresupuesto::where('presupuesto_id', $this->presupuesto_id)->where('evento', 0)->where('margen_utilidad', '>', 0)->avg('margen_utilidad')) && $this->margenGeneral = 0;
        $this->ventaProyecto = ItemPresupuesto::where('presupuesto_id', $this->presupuesto_id)->where('evento', 0)->sum('v_total_cot');
        $this->ventaProyecto += ($this->ventaProyecto * ($this->imprevistos/100)) + ($this->ventaProyecto * ($this->administracion/100)) + ($this->ventaProyecto * ($this->fee/100));
        $this->costosProyecto = ItemPresupuesto::where('presupuesto_id', $this->presupuesto_id)->where('evento', 0)->sum('v_total');
        if ($this->ventaProyecto > 0){ $this->margenProyecto =  (($this->ventaProyecto - $this->costosProyecto)/$this->ventaProyecto)*100;}
        $this->margenBruto = $this->ventaProyecto - $this->costosProyecto;

        $presto = PresupuestoProyecto::where('id_gestion', $this->id_gestion)->first();
        $presto->margen_general = $this->margenGeneral;
        $presto->venta_proy = $this->ventaProyecto;
        $presto->costos_proy = $this->costosProyecto;
        $presto->margen_proy = $this->margenProyecto;
        $presto->margen_bruto = $this->margenBruto;
        $presto->update();

        $this->centroCostos = $presto->cod_cc;  
        $this->imprevistos = $presto->imprevistos;  
        $this->administracion = $presto->administracion;   
        $this->fee = $presto->fee;  
        $this->tiempoFactura = $presto->tiempo_factura;
        $this->notas = $presto->notas; 
    }

    public function getInfoFacturas(){ 
        $presto = PresupuestoProyecto::where('id_gestion', $this->id_gestion)->first();
        $this->imprevistos = $presto->imprevistos;
        $this->administracion = $presto->administracion;
        $this->fee = $presto->fee;
        $this->tiempoFactura = $presto->tiempo_factura;
        $this->notas = $presto->notas;
    }

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
 
    public function getCiudades(){
        $this->ciudades = app('ciudades');
    }

    public function getProveedores(){
        $this->categorias_proveedor = CategoriaProveedor::select('id', 'description')->orderBy('id','desc')->get();
        $this->proveedores = Proveedor::select('id', 'tercero')->get();
    }

    public function getMeses(){
        $año = Año::select('id', 'description')->where('description', date('Y'))->first();
        $this->meses = Mes::select('id', 'description')->where('ano_id', $año->id)->get();
    }

    public function getTarifario(){
        $this->tarifario = Tarifario::select('id', 'concepto', 'caso', 'v_unidad')->get();
    }
  
    public function changeDisponibilidad($id){
        $item = ItemPresupuesto::find($id);
        $item->disponible = !$item->disponible;
        $item->update();
        $this->refresh();
    }

    public function deleteItem($id){
        ItemPresupuesto::destroy($id);        
        $this->refresh();
    }

    public function refresh(){
        $this->getMetricas();
        $this->getItems();
    }

    public function getDataEdit($id){
        $this->selected_item = []; 
        foreach ($this->items as $item) {
            if ($item->id == $id){ $this->selected_item = $item; }  
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

    public function actionEdit(){
        $presto = PresupuestoProyecto::where('id_gestion', $this->id_gestion)->first();

        if (is_null($this->selected_item)){
            return redirect()->back()->withErrors('Ningún elemento seleccionado')->withInput();
        }

        if ($this->selected_item->evento){
            $this->validate([
                'descripcion' => ['required'], 
            ]);
    
            $item = ItemPresupuesto::find($this->selected_item->id);
            $item->cod = 0;
            $item->presupuesto_id = $this->presupuesto_id;
            $item->evento = 1;
            $item->cantidad = 0;
            $item->dia = 0;
            $item->otros = 0;
            $item->descripcion = $this->descripcion;
            $item->v_unitario = 0;
            $item->v_total = 0;
            $item->proveedor = [];
            $item->margen_utilidad = 0;
            $item->mes = 1;
            $item->dias = 0;
            $item->ciudad = 0;
            $item->update(); 
        }else{
            $this->validate([
                'cod' => ['required'], 
                'dia' => ['required'],
                'otros' => ['required'],
                'descripcion' => ['required'],
                // 'proveedor' => ['required', new SameCategory],
                'proveedor' => ['required'],
                'utilidad' => ['required'],
                'mes' => ['required'],
                'dias' => ['required'],
                'ciudad' => ['required'] 
            ]); 

            if ($this->presupuesto->gestion->claro){
                $this->validate([
                    'valor_total_cliente' => ['numeric', 'required']
                ]);
            }
    
            $item = ItemPresupuesto::find($this->selected_item->id);

            $this->validate([
                'cantidad' => ['required', (new PrestoConsumido($item))],
                'valor_total' => ['required', (new PrestoConsumido($item))],
            ]);

            // Indica actualiazcion
            if ($presto->cod_cc && ($this->valor_total > $item->v_total)){
                $item->actualizado = true;
                $this->setEnEdicion($presto);
            }

            $item->cod = $this->cod;
            $item->presupuesto_id = $this->presupuesto_id;
            $item->cantidad = $this->cantidad;
            $item->dia = $this->dia; 
            $item->otros = $this->otros;
            $item->descripcion = $this->descripcion;
            $item->v_unitario = $this->valor_unitario;
            $item->v_total = $this->valor_total;
            if ($this->presupuesto->gestion->claro){
                $item->v_total_cliente = $this->valor_total_cliente;
            }
            $item->proveedor = serialize($this->proveedor);
            $item->margen_utilidad = $this->utilidad;
            $item->mes = $this->mes;  
            $item->dias = $this->dias;
            $item->ciudad = $this->ciudad;
            
            $item->v_unitario_cot = ($this->utilidad > 0) ? $this->valor_unitario / $this->utilidad : 0;
            $item->v_total_cot = ($this->utilidad > 0) ? $this->cantidad * $this->dia * $this->otros * $item->v_unitario_cot : 0;
            $item->rentabilidad = ($this->utilidad > 0) ? $item->v_total_cot - $item->v_total : 0;
            $item->update();
        }
 
        $this->refresh();  
        $this->limpiar(); 
    } 
 
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
   
    // Envía a probacion 
    public function aprobacion(){
        // Valída si es actualización
        if ($this->cod_cc){
            $this->validate([
                'justificacion' => ['required', 'string', 'max:254']
            ]);
        }

        $presto = PresupuestoProyecto::where('id_gestion', $this->id_gestion)->first();
        $presto->estado_id = 2;
        $presto->justificacion = $this->justificacion;
        $presto->update(); 
        $this->estadoValidator = $presto->estado_id;

        // EMAIL
        $this->presupuestoAprobacion($presto, Auth::user()); 
        return redirect()->route('presupuesto', $this->id_gestion); 
    }
    
    // Convierte en editable
    public function setEnEdicion($presto){
        if ($presto->estado_id != 3){
            $presto->estado_id = 3;
            $presto->update();
        } 
    }

    public function updateCentro(){
        if (!$this->presupuesto->cod_cc){ 
            $this->validate([
                'centroCostos' => ['required', 'string', new CentroCostos] 
            ]);
        }else {
            $this->validate([
                'centroCostos' => ['required', 'string'] 
            ]);
        }

        $item = PresupuestoProyecto::where('id_gestion', $this->id_gestion)->first();
        
        if (is_null($item->cod_cc)){
            $gestion = GestionComercial::find($this->id_gestion);
            $gestion->id_estado = 4;
            $gestion->update();
        }
        
        // Default indicacion actualiazcion
        $itemsPresupuesot = ItemPresupuesto::where('presupuesto_id', $item->id)->get()->map(function ($item){
            $item->actualizado = false;
            $item->update();
        });

        $item->cod_cc = $this->centroCostos;
        $item->fecha_cc = date("Y-m-d");
        $item->estado_id = 1;    
        $item->justificacion_compras = null;
        $item->justificacion = null;
        $item->update();


        // Re-calcula los valores de la base y gestion comercial
        $this->reCalculate($item);
        
        // EMAIL
        $this->presupuestoAprobado($item->gestion->comercial, $item->gestion, null, $item->cod_cc);

        return redirect()->route('presupuesto-proyecto')->with('success', 'Centro de costos asignado');  
    }

    public function reCalculate($presupuesto){
        $prestosCom = [];        
        
        // Update Gestion
        $presupuesto->gestion->presto_cot = $presupuesto->venta_proy;
        $presupuesto->gestion->update();

        // Toma el presupuesto y id del usuario creador de la gestión
        array_push($prestosCom, [
            'comercial_id' => $presupuesto->gestion->id_user,
            'presupuesto' => ($presupuesto->gestion->presto_cot * $presupuesto->gestion->porcentaje)/100
        ]);

        // Toma el presupuesto y id de los usuarios partiipantes en la gestion
        $i = 2;
        while($i < 5){
            array_push($prestosCom, [   
                'comercial_id' => $presupuesto->gestion->{'comercial_'.$i},
                'presupuesto' => ($presupuesto->gestion->presto_cot * $presupuesto->gestion->{'porcentaje_'.$i})/100, 
            ]);
            $i++;
        }

        // Update Base
        foreach ($presupuesto->gestion->baseComercial as $key => $base){
            if ($base->id_user == $prestosCom[$key]['comercial_id']){
                $base->valor_original = $presupuesto->venta_proy;
                $base->valor_proyecto = $prestosCom[$key]['presupuesto'];
                $base->update();
            }
        }
    }

    public function rechazar(){
        $this->validate([
            'justificacion_compras' => ['required', 'string']
        ]);
        
        $presupuesto = PresupuestoProyecto::where('id_gestion', $this->id_gestion)->first();
        $presupuesto->justificacion_compras = $this->justificacion_compras;
        $presupuesto->estado_id = 3;
        $presupuesto->update(); 
        
        // EMAIL
        $this->presupuestoRechazado($presupuesto->gestion->comercial, $presupuesto->gestion, $presupuesto->justificacion_compras, $presupuesto->cod_cc);

        return redirect()->route('presupuesto-proyecto')->with('success', 'Cambios guardados exitosamente');  
    }

    // VALIDATIONS
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
            'proveedor' => ['required', new SameCategory]
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

    public function getValorTotal(){
        if (!is_null($this->cantidad) && !is_null($this->dia) && !is_null($this->otros) && !is_null($this->valor_unitario)){
            $this->valor_total = $this->cantidad * $this->dia * $this->otros * $this->valor_unitario;
        }

        if ($this->valor_total_cliente != 0){
            $this->getUtilidad();
        }
    }

    public function getUtilidad(){
        if ($this->valor_total_cliente > 0){
            $this->utilidad = $this->valor_total / $this->valor_total_cliente;
        }
        else {
            $this->utilidad = 0;
        }
    }

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
    }

    public function toggelRentabilidad(){
        $this->rentabilidadView = !$this->rentabilidadView;
    }
    // ----------- 
} 
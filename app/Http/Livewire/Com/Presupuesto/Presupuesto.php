<?php

namespace App\Http\Livewire\Com\Presupuesto; 

use Livewire\Component;
use Illuminate\Validation\Rules;
use Illuminate\Support\Facades\Auth;
use App\Models\GestionComercial;
use App\Models\Mes;
use App\Models\Año;
use App\Models\ItemPresupuesto;
use App\Models\Tarifario;  
use App\Models\PresupuestoProyecto; 
use App\Traits\Hablame;

class Presupuesto extends Component 
{
    use Hablame;

    // Models
    public $cod; 
    public $concepto; 

    public $cantidad;
    public $dia;
    public $otros; 
    public $descripcion;
    public $valor_unitario = 0;
    public $valor_total = 0;
    public $proveedor;
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

    // Useful vars
    public $items = [];
    public $presupuesto_id;
    public $ciudades = [];
    public $meses = [];
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
            $this->presupuesto_id = $presupuesto->id;
            $this->estadoValidator = $presupuesto->estado_id;
            $this->cod_cc = $presupuesto->cod_cc;
        }else { 
            $this->presupuesto_id = $validator->id;
            $this->estadoValidator = $validator->estado_id;
            $this->cod_cc = $validator->cod_cc;
        }

        $this->refresh();
        $this->getContacto();
        $this->getCiudades();
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
        $this->validate([
            'cod' => ['required'],
            'cantidad' => ['required'],
            'dia' => ['required'],
            'otros' => ['required'],
            'descripcion' => ['required'],
            'valor_unitario' => ['required'],
            'valor_total' => ['required'],
            'proveedor' => ['required'],
            'utilidad' => ['required', 'numeric', 'max:2'],
            'mes' => ['required'], 
            'dias' => ['required'],
            'ciudad' => ['required'],
            
            'imprevistos' => ['required', 'numeric'],
            'administracion' => ['required', 'numeric'],
            'fee' => ['required', 'numeric'],
        ]);
         
        $item = new ItemPresupuesto;
        $item->cod = $this->cod; 
        $item->presupuesto_id = $this->presupuesto_id;
        $item->cantidad = $this->cantidad;
        $item->dia = $this->dia;
        $item->otros = $this->otros;

        $item->descripcion = $this->descripcion;
        $item->v_unitario = $this->valor_unitario;
        $item->v_total = $this->valor_total;
        $item->proveedor = $this->proveedor;
        $item->margen_utilidad = $this->utilidad;

        $item->mes = $this->mes;
        $item->dias = $this->dias;
        $item->ciudad = $this->ciudad; 

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

    public function getMeses(){
        $año = Año::select('id', 'description')->where('description', date('Y'))->first();
        $this->meses = Mes::select('id', 'description')->where('ano_id', $año->id)->get();
    }

    public function getTarifario(){
        $this->tarifario = Tarifario::select('id', 'concepto', 'caso', 'v_unidad')->get();
    }

    public function deleteItem($id){
        ItemPresupuesto::destroy($id);        
        $this->refresh();
    }

    public function refresh(){
        $this->getMetricas();
        $this->getItems();
    }

    public function getContacto(){
        $contacto = GestionComercial::where('id', $this->id_gestion)->first();
        $this->nombre = $contacto->contacto->nombre." ".$contacto->apellido;
        $this->cliente = $contacto->contacto->empresa;
        $this->nomProyecto = $contacto->nom_proyecto_cot;
        $this->ciudadContacto = $contacto->contacto->ciudad;
    }

    public function getDataEdit($id){
        $this->selected_item = []; 
        foreach ($this->items as $key => $item) {
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
        $this->proveedor = $this->selected_item->proveedor;
        $this->utilidad = $this->selected_item->margen_utilidad;
        $this->mes = $this->selected_item->mes;
        $this->dias = $this->selected_item->dias;
        $this->ciudad = $this->selected_item->ciudad;
    }

    public function actionEdit(){
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
            $item->proveedor = 0;
            $item->margen_utilidad = 0;
            $item->mes = 0;
            $item->dias = 0;
            $item->ciudad = 0;
            $item->update();
        }else{
            $this->validate([
                'cod' => ['required'], 
                'dia' => ['required'],
                'otros' => ['required'],
                'descripcion' => ['required'],
                'valor_unitario' => ['required'],
                'valor_total' => ['required'],
                'proveedor' => ['required'],
                'utilidad' => ['required'],
                'mes' => ['required'],
                'dias' => ['required'],
                'ciudad' => ['required'] 
            ]);
    
            $item = ItemPresupuesto::find($this->selected_item->id);
            $item->cod = $this->cod;
            $item->presupuesto_id = $this->presupuesto_id;
            $item->cantidad = $this->cantidad;
            $item->dia = $this->dia; 
            $item->otros = $this->otros;
            $item->descripcion = $this->descripcion;
            $item->v_unitario = $this->valor_unitario;
            $item->v_total = $this->valor_total;
            $item->proveedor = $this->proveedor;
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
        return redirect()->route('cotizacion', ['prespuesto' => $this->id_gestion, 'nom_proyecto' => $this->nomProyecto, 'tipo' => 1]);
    }

    public function internoPdf(){  
        return redirect()->route('cotizacion', ['prespuesto' => $this->id_gestion, 'nom_proyecto' => $this->nomProyecto, 'tipo' => 0]);
    }

    public function cotizacionExcel(){  
        return redirect()->route('cotizacionExcel', ['prespuesto' => $this->id_gestion, 'nom_proyecto' => $this->nomProyecto, 'tipo' => 1]);
    }

    public function internoExcel(){  
        return redirect()->route('cotizacionExcel', ['prespuesto' => $this->id_gestion, 'nom_proyecto' => $this->nomProyecto, 'tipo' => 0]);
    }

    // Envía a probacion
    public function aprobacion(){
        $presto = PresupuestoProyecto::where('id_gestion', $this->id_gestion)->first();
        // $presto->estado_id = 2;
        // $presto->update();
        // $this->estadoValidator = $presto->estado_id;
        $this->presupuestoAprobacion($presto->margen_proy, Auth::user()->name);
        return redirect()->route('presupuesto', $this->id_gestion); 
    }
     

    public function updateCentro(){
        $this->validate([
            'centroCostos' => ['required', 'string']
        ]);
        $item = PresupuestoProyecto::where('id_gestion', $this->id_gestion)->first();

        if (is_null($item->cod_cc)){
            $gestion = GestionComercial::find($this->id_gestion);
            $gestion->id_estado = 4;
            $gestion->update();
        }

        $item->cod_cc = $this->centroCostos;
        $item->fecha_cc = date("Y-m-d");
        $item->estado_id = 1;
        $item->update();
        return redirect()->route('presupuesto-proyecto')->with('success', 'Centro de costos asignado');  
    }

    // VALIDATIONS
    public function updatedCod(){
        $this->cod = trim($this->cod);
        $this->validate([
            'cod' => ['required']
        ]);

        $this->setDataTarifario($this->cod);
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

    public function updatedProveedor(){
        $this->proveedor = trim($this->proveedor);
        $this->validate([
            'proveedor' => ['required']
        ]);
    }

    public function updatedUtilidad(){
        $this->utilidad = trim($this->utilidad);
        $this->utilidad = str_replace(",",'', $this->utilidad);
        $this->validate([
            'utilidad' => ['required', 'numeric', 'max:2']
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
        if (Auth::user()->rol == 1){
            $this->validate([
                'centroCostos' => ['required', 'string']
            ]);
        }
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
        $this->proveedor = "";
        $this->utilidad = "";

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
<?php

namespace App\Http\Livewire\Productor\Ordenes;

use Livewire\Component;
use App\Models\EstadoOrdenesCompra;
use App\Models\OrdenCompra;
use App\Models\OcItem;
use Livewire\WithFileUploads;
 
class Juridica extends Component
{    
    use WithFileUploads; 

    // Models
    public $item, $desc, $cant = 0, $vUnit = 0, $vTotal = 0, $dias;
    public $proveedor, $email, $contacto, $tel, $file_cot;

    // Filled
    public $presupuesto, $orden_compra;
  
    // Useful vars 
    public $ocItems = []; 
    public $selectedItem;
    public $maxCant; 
    public $maxValor;
    public $items = []; 

    public $edit = false;

    public function render()
    {
        return view('livewire.productor.ordenes.juridica');
    }

    public function mount (){
        if ($this->orden_compra){
            $this->getItems();
        }
    }

    public function newItem(){    
        $this->validate([
            'item' => 'required',
            'desc' => 'required',
            'cant' => "required|numeric|max:$this->maxCant",
            'dias' => "required|numeric",
            'vUnit' => "required|numeric|max:$this->maxValor",
            'vTotal' => 'required|numeric', 
        ]);
            
        $this->getVTotal();  

        if (is_null($this->selectedItem)){
            // Valida item repetido
            if (!$this->validateItems($this->item)){ 
                $this->resetFields();
                $this->addError('customError', 'Este item ya fué registrado.');
                return redirect()->back();
            }

            array_push($this->ocItems, [
                'id' => count($this->ocItems),
                'item' => $this->item, // $this->item contiene el id del item en DB
                'displayItem' => $this->getDisplayItem($this->item),
                'desc' => $this->desc,
                'cant' => $this->cant,
                'dias' => $this->dias,
                'vUnit' => $this->vUnit,
                'vTotal' => $this->vTotal
            ]);
        }else {
            $this->ocItems[$this->selectedItem]['displayItem'] = $this->getDisplayItem($this->item);
            $this->ocItems[$this->selectedItem]['desc'] = $this->desc;
            $this->ocItems[$this->selectedItem]['cant'] = $this->cant;
            $this->ocItems[$this->selectedItem]['dias'] = $this->dias;
            $this->ocItems[$this->selectedItem]['vUnit'] = $this->vUnit;
            $this->ocItems[$this->selectedItem]['vTotal'] = $this->vTotal;
        }
        $this->resetFields();
    }

    public function delete($id){ 
        unset($this->ocItems[$id]);
        $this->resetFields();
    } 

    public function getSelectedItem($id){
        $this->selectedItem = $this->ocItems[$id]['id']; //Guarda la poscición en el arreglo

        $this->item = $this->ocItems[$id]['item']; // El select está con el ID de la DB.
        $this->desc = $this->ocItems[$id]['desc'];
        $this->cant = $this->ocItems[$id]['cant'];
        $this->dias = $this->ocItems[$id]['dias'];
        $this->vUnit = $this->ocItems[$id]['vUnit'];
        $this->vTotal = $this->ocItems[$id]['vTotal'];

        $this->presupuesto->presupuestoItems->map(function ($item){
            if ($this->item == $item->id){
                $this->maxCant = $item->cantidad;
                $this->maxValor = $item->v_unitario;
            }
        })->first();
    }

    public function validateItems($itemDB){
        $validator = true;
        foreach ($this->ocItems as $key => $item) {
            if ($item['item'] == $itemDB){
                return false;
            }
        }
        return $validator;
    }

    // Trae y muestra la orden de compra de la base de datos (si ya está creada).
    public function getItems(){
        $this->proveedor = $this->orden_compra->proveedor;
        $this->email = $this->orden_compra->email_prov;
        $this->contacto = $this->orden_compra->contacto_prov;
        $this->tel = $this->orden_compra->telefono_prov;
        $this->file_cot = $this->orden_compra->archivo_cot;

        foreach ($this->orden_compra->ordenItems as $item){
            array_push($this->ocItems, [
                'id' => count($this->ocItems),
                'item' => $item->item_id, // $this->item contiene el id del item en DB
                'displayItem' => $this->getDisplayItem($item->item_id),
                'desc' => $item->desc_oc,
                'cant' => $item->cant_oc,
                'dias' => $item->dias_oc,
                'vUnit' => $item->vunit_oc,
                'vTotal' => $item->vtotal_oc
            ]);
        }
    }

    // Obtiene el item que reconocen los productores
    public function getDisplayItem($id){
        foreach ($this->presupuesto->presupuestoItems as $key => $item) {
            if ($id == $item->id){
                return $key+1;
            }
        }
    }

    // Valida si el item tiene aún disponibilidades en la base de datos.
    public function getVTotal(){
        $this->vUnit = trim($this->vUnit);
        $this->vUnit = str_replace(",",'', $this->vUnit);

        $this->validate([
            'cant' => 'required|numeric',
            'vUnit' => 'required|numeric'
        ]);

        $this->vTotal = $this->cant * $this->vUnit * $this->dias;
    }

    public function enviarAprobacion(){
        $this->validate([ 
            'proveedor' => 'required|string|max:200',
            'email' => 'required|email|max:200',
            'contacto' => 'required|string|max:200',
            'tel' => 'required|numeric',
            'file_cot' => 'required|file|mimes:pdf,xls,xlsx|max:10240'
        ]);
        
        if (count($this->ocItems) == 0){
            $this->addError('customError', 'No puedes enviar una orden de compra vacía.');
            return redirect()->back();
        } 

        // Si la orden está creada, entonces edita
        if ($this->orden_compra){           
            $this->orden_compra->estado_id = 2;
            $this->orden_compra->proveedor = $this->proveedor;
            $this->orden_compra->email_prov = $this->email;
            $this->orden_compra->contacto_prov = $this->contacto;
            $this->orden_compra->telefono_prov = $this->tel;
            $this->orden_compra->archivo_cot = $this->file_cot->store('public/ordenes_juridicas'); 
            $this->orden_compra->update();

            $this->deleteItems($this->orden_compra->id);
            $this->storeItems($this->orden_compra->id);
        }else{
            $orden = new OrdenCompra;
            $orden->tipo_oc = 1; 
            $orden->presupuesto_id = $this->presupuesto->id;
    
            $orden->proveedor = $this->proveedor;
            $orden->email_prov = $this->email;
            $orden->contacto_prov = $this->contacto;
            $orden->telefono_prov = $this->tel;
            $orden->archivo_cot = $this->file_cot->store('public/ordenes_juridicas'); 
            $orden->save();

            $this->storeItems($orden->id);        
        }

        $this->resetFields();
        $this->resetOcInfo();
        $this->emit('ordenCreada');
        return redirect()->back()->with('success', 'Orden de compra enviada a aprobación.');
    }

    public function storeItems($orden_id){
        foreach ($this->ocItems as $item) {  
            $itemsOrden = new OcItem;
            $itemsOrden->oc_id = $orden_id;
            $itemsOrden->item_id = $item['item'];
            $itemsOrden->display_item = $item['displayItem'];
            $itemsOrden->desc_oc = $item['desc'];
            $itemsOrden->cant_oc = $item['cant'];
            $itemsOrden->dias_oc = $item['dias'];
            $itemsOrden->vunit_oc = $item['vUnit'];
            $itemsOrden->vtotal_oc = $item['vTotal'];
            $itemsOrden->save();
        }         
    }

    // Elimina los items de la orden de compra en la DB.
    public function deleteItems($orden_id){
        $items = OcItem::where('oc_id', $orden_id)->get();

        $items->map(function ($item){
            $item->delete();
        });
    }
    
    public function cambioEstado($estado){
        $this->orden_compra->estado_id = $estado;
        $this->orden_compra->update();
        
        if ($this->orden_compra->estado_id == 1){
            return redirect()->route('ordenes-compra')->with('success', 'Orden de compra APROBADA.');
        }else{
            return redirect()->route('ordenes-compra')->with('success', 'Orden de compra RECHAZADA.');
        }
    }

    /* UPDATES */
    public function updatedItem(){
        $this->validate([
            'item' => 'required'
        ]);
        
        $dbItem = $this->presupuesto->presupuestoItems->find($this->item);

        $contCant = 0;
        foreach ($dbItem->ordenes_compra as $item) {
            $contCant += $item->cant_oc;
        }

        $this->cant = ($dbItem->cantidad - $contCant);

        if ($this->cant == 0){
            $this->addError('customError', 'Éste item ya fué consumido.');
            $this->resetFields();
            return redirect()->back();
        } 

        $this->desc = $dbItem->descripcion;
        $this->cant = $this->cant;
        $this->vUnit = $dbItem->v_unitario;
        $this->dias = $dbItem->dia;
        $this->maxCant = $this->cant;
        $this->maxValor = $this->vUnit;
        $this->getVTotal();
    }

    public function updatedDesc(){
        $this->validate([
            'desc' => 'required'
        ]);
    }

    public function updatedCant(){
        $this->validate([
            'cant' => "required|numeric|max:$this->maxCant",
        ]);

        if ($this->cant < 0){
            $this->cant = 0;
        }

        $this->getVTotal();
    }
    
    public function updatedVUnit(){
        $this->vUnit = trim($this->vUnit);
        $this->vUnit = str_replace(",",'', $this->vUnit);

        $this->validate([
            'vUnit' => "required|numeric|max:$this->maxValor"
        ]);

        if ($this->vUnit < 0){
            $this->vUnit = 0;
        }

        $this->getVTotal();
    }

    public function updatedVTotal(){
        $this->validate([
            'vTotal' => 'required'
        ]);
    }

    public function updatedProveedor(){
        $this->validate([
            'proveedor' => 'required|string|max:200',
        ]);
    }

    public function updatedEmail(){
        $this->validate([
            'email' => 'required|email|max:200',
        ]);
    }

    public function updatedContacto(){
        $this->validate([
            'contacto' => 'required|string|max:200',
        ]);
    }

    public function updatedTel(){
        $this->validate([
            'tel' => 'required|numeric',
        ]);
    }

    public function updatedFile_cot(){
        $this->validate([
            'file_cot' => 'required|file|mimes:pdf,xls,xlsx|max:10240'
        ]);
    }
    /*****/
    
    public function resetFields(){
        $this->item = "";
        $this->desc = "";
        $this->cant = 0;
        $this->dias = 0;
        $this->vUnit = 0;
        $this->vTotal = 0;

        $this->selectedItem = null;
    }

    public function resetOcInfo(){
        $this->proveedor = "";
        $this->email = "";
        $this->contacto = "";
        $this->tel = "";
        $this->file_cot = null;

        $this->ocItems = [];
    }
}
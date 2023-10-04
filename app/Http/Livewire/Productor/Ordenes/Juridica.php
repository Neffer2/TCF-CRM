<?php

namespace App\Http\Livewire\Productor\Ordenes;

use Livewire\Component;
use App\Models\EstadoOrdenesCompra;
use App\Models\OrdenCompra;
use App\Models\OcItem;

class Juridica extends Component
{    
    // Models
    public $item, $desc, $cant = 0, $vUnit = 0, $vTotal = 0;
    public $proveedor, $email, $contacto, $tel;

    // Filled
    public $presupuesto;

    // Useful vars 
    public $ocItems = []; 
    public $selectedItem;
    public $maxCant; 
    public $maxValor;
    public $items = []; 

    public function render()
    {
        return view('livewire.productor.ordenes.juridica');
    }

    public function newItem(){
        $this->validate([
            'item' => 'required',
            'desc' => 'required',
            'cant' => "required|numeric|max:$this->maxCant",
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
                'vUnit' => $this->vUnit,
                'vTotal' => $this->vTotal
            ]);
        }else {
            $this->ocItems[$this->selectedItem]['displayItem'] = $this->getDisplayItem($this->item);
            $this->ocItems[$this->selectedItem]['desc'] = $this->desc;
            $this->ocItems[$this->selectedItem]['cant'] = $this->cant;
            $this->ocItems[$this->selectedItem]['vUnit'] = $this->vUnit;
            $this->ocItems[$this->selectedItem]['vTotal'] = $this->vTotal;
        }

        $this->resetFields();
    }

    public function delete($id){
        unset($this->ocItems[$id]);
        $this->resetFields();
    }

    // Obtiene el item que reconocen los productores
    public function getDisplayItem($id){
        foreach ($this->presupuesto->presupuestoItems as $key => $item) {
            if ($id == $item->id){
                return $key+1;
            }
        }
    }

    public function getSelectedItem($id){    
        $this->selectedItem = $this->ocItems[$id]['id']; //Guarda la poscición en el arreglo

        $this->item = $this->ocItems[$id]['item']; // El select está con el ID de la DB.
        $this->desc = $this->ocItems[$id]['desc'];
        $this->cant = $this->ocItems[$id]['cant'];
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

    public function getVTotal(){
        $this->vUnit = trim($this->vUnit);
        $this->vUnit = str_replace(",",'', $this->vUnit);

        $this->validate([
            'cant' => 'required|numeric',
            'vUnit' => 'required|numeric'
        ]);

        $this->vTotal = $this->cant * $this->vUnit;
    }

    public function enviarAprobacion(){
        $this->validate([ 
            'proveedor' => 'required|string|max:200',
            'email' => 'required|email|max:200',
            'contacto' => 'required|string|max:200',
            'tel' => 'required|numeric|digits:10'
        ]);

        if (count($this->ocItems) == 0){
            $this->addError('customError', 'No puedes enviar una orden de compra vacía.');
            return redirect()->back();
        }

        $orden = new OrdenCompra;
        $orden->tipo_oc = 1; 
        $orden->estado_id = 1;
        $orden->presto_id = $this->presupuesto->id;

        $orden->proveedor = $this->proveedor;
        $orden->email_prov = $this->email;
        $orden->contacto_prov = $this->contacto;
        $orden->telefono_prov = $this->tel;
        $orden->archivo_cot = "HOLA MUNDI";
        $orden->save();

        foreach ($this->ocItems as $key => $item) {
            $itemsOrden = new OcItem;
            $itemsOrden->oc_id = $orden->id;
            $itemsOrden->item_id = $item['item'];
            $itemsOrden->desc_oc = $item['desc'];
            $itemsOrden->cant_oc = $item['cant'];
            $itemsOrden->vunit_oc = $item['vUnit'];
            $itemsOrden->vtotal_oc = $item['vTotal'];
            $itemsOrden->save();
        }        

        $this->resetFields();
        $this->resetOcInfo();
        return redirect()->back()->with('success', 'Orden de compra creada y enviada a aprobación.');
    }
    
    /* UPDATES */
    public function updatedItem(){
        $this->validate([
            'item' => 'required'
        ]);

        $this->presupuesto->presupuestoItems->map(function ($item){
            if ($this->item == $item->id){
                $this->desc = $item->descripcion;
                $this->cant = $item->cantidad;
                $this->vUnit = $item->v_unitario;
                $this->maxCant = $this->cant;
                $this->maxValor = $this->vUnit;
            }
        })->first();

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
            'tel' => 'required|numeric|digits:10',
        ]);
    }
    /*****/
    
    public function resetFields(){
        $this->item = "";
        $this->desc = "";
        $this->cant = 0;
        $this->vUnit = 0;
        $this->vTotal = 0;

        $this->selectedItem = null;
    }

    public function resetOcInfo(){
        $this->proveedor = "";
        $this->email = "";
        $this->contacto = "";
        $this->tel = "";

        $this->ocItems = [];
    }
}

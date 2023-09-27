<?php

namespace App\Http\Livewire\Productor\Ordenes;

use Livewire\Component;

class Juridica extends Component
{   
    // Models
    public $item, $desc, $cant = 0, $vUnit = 0, $vTotal = 0;

    // Filled
    public $presupuesto;

    // Useful vars
    public $ocItems = [];
    public $selectedItem;
    public $maxCant = 100;

    public function render()
    {
        return view('livewire.productor.ordenes.juridica');
    }

    public function mount(){
        
    }

    public function newItem(){
        $this->validate([
            'item' => 'required',
            'desc' => 'required',
            'cant' => 'required|numeric', 
            'vUnit' => 'required|numeric',
            'vTotal' => 'required|numeric',
        ]);

        $this->getVTotal(); 

        if (is_null($this->selectedItem)){
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
    }

    public function getDisplayItem($id){
        foreach ($this->presupuesto->presupuestoItems as $key => $item) {
            if ($id == $item->id){
                return $key+1;
            }
        }
    }

    public function getSelectedItem($id){
        $this->selectedItem = $this->ocItems[$id]['id'];
        
        $this->item = $this->ocItems[$id]['item']; // El select está con el ID de la DB.
        $this->desc = $this->ocItems[$id]['desc'];
        $this->cant = $this->ocItems[$id]['cant'];
        $this->vUnit = $this->ocItems[$id]['vUnit'];
        $this->vTotal = $this->ocItems[$id]['vTotal'];
    }

    public function updatedItem(){
        $this->validate([
            'item' => 'required'
        ]);

        $this->presupuesto->presupuestoItems->map(function ($item){
            if ($this->item == $item->id){
                $this->desc = $item->descripcion;
                $this->cant = $item->cantidad;
                $this->maxCant = $this->cant;
            }
        })->first();
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

        $this->getVTotal();
    }
    
    public function updatedVUnit(){
        $this->vUnit = trim($this->vUnit);
        $this->vUnit = str_replace(",",'', $this->vUnit);

        $this->validate([
            'vUnit' => 'required|numeric',
        ]);

        $this->getVTotal();
    }

    public function updatedVTotal(){
        $this->validate([
            'vTotal' => 'required'
        ]);
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

    public function resetFields(){
        $this->item = "";
        $this->desc = "";
        $this->cant = 0;
        $this->vUnit = 0;
        $this->vTotal = 0;

        $this->selectedItem = null;
    }
}

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
                'item' => $this->item,
                'desc' => $this->desc,
                'cant' => $this->cant,
                'vUnit' => $this->vUnit,
                'vTotal' => $this->vTotal
            ]);
        }else {
            dd("Edit");
        }

        $this->resetFields();
    }

    public function delete($id){
        unset($this->ocItems[$id]);
    }

    public function getItem($id){
        $this->selectedItem = $this->ocItems[$id]['id'];

        $this->item = $this->ocItems[$id]['item'];
        $this->desc = $this->ocItems[$id]['desc'];
        $this->cant = $this->ocItems[$id]['cant'];
        $this->vUnit = $this->ocItems[$id]['vUnit'];
        $this->vTotal = $this->ocItems[$id]['vTotal'];
    }

    public function updatedItem(){
        $this->validate([
            'item' => 'required'
        ]);
    }

    public function updatedDesc(){
        $this->validate([
            'desc' => 'required'
        ]);
    }

    public function updatedCant(){
        $this->validate([
            'cant' => 'required|numeric'
        ]);

        $this->getVTotal();
    }
    
    public function updatedVUnit(){
        $this->validate([
            'vUnit' => 'required|numeric'
        ]);

        $this->getVTotal();
    }

    public function updatedVTotal(){
        $this->validate([
            'vTotal' => 'required|numeric'
        ]);
    }

    public function getVTotal(){
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
    }
}

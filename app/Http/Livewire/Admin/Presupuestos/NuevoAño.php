<?php

namespace App\Http\Livewire\Admin\Presupuestos;

use Livewire\Component;
use Illuminate\Validation\Rules;
use App\Models\Año;
use App\Models\Mes;

class NuevoAño extends Component
{
    // models 
    public $description;

    // Help vars
    public $enableSubmit = false;

    public function render()
    {
        return view('livewire.admin.presupuestos.nuevo-año');
    }   

    public function updatedDescription(){
        $currentYear = date('Y');
        $nextYear = date('Y', strtotime('+1 years'));

        $this->validate(['description' => ['required', 'unique:anos', 'numeric', "min:$currentYear", "max:$nextYear"]]);
        $this->enableSubmit = true;
    }

    public function nuevo_año (){ 
        $currentYear = date('Y');
        $nextYear = date('Y', strtotime('+1 years'));
        $meses = ['Enero', 'Febrero', 'Marzo', 'Abril', 'Mayo', 'Junio', 'Julio', 'Agosto', 'Septiembre', 'Octubre', 'Noviembre', 'Diciembre'];
        $identifiers = ['1', '2', '3', '4', '5', '6', '7', '8', '9', '10', '11', '12'];

        $this->validate(['description' => ['required', 'unique:anos', 'numeric', "min:$currentYear", "max:$nextYear"]]);
         
        Año::create([
            'description' => $this->description 
        ]); 

        $created_year = Año::select('id')->where('description', "$this->description")->first();
        foreach ($meses as $key =>$mes){
            Mes::create([
                'ano_id' => $created_year->id, 
                'identifier' => $identifiers[$key],
                'description' => $mes
            ]);
        }

        $this->description = "";
        $this->emit('refresh'); 
        return redirect()->back()->with('success', '¡Año generado exitosamente!'); 
    }  
} 

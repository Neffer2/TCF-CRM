<?php

namespace App\Http\Livewire\Admin\Presupuestos;

use Livewire\Component;
use Illuminate\Validation\Rules;
use App\models\Año;
use App\models\Mes;

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

        $this->validate(['description' => ['required', 'unique:anos', 'numeric', "min:$currentYear", "max:$nextYear"]]);
         
        Año::create([
            'description' => $this->description
        ]); 

        $created_year = Año::select('id')->where('description', "$this->description")->first();
        foreach ($meses as $mes){
            Mes::create([
                'ano_id' => $created_year->id,
                'description' => $mes
            ]);
        }

        $this->description = "";
        $this->emit('refresh'); 
        return redirect()->back()->with('success', '¡Año generado exitosamente!'); 
    }  
} 

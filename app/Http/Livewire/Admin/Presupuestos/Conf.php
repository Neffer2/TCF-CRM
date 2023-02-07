<?php

namespace App\Http\Livewire\Admin\Presupuestos;

use Livewire\Component;
use App\Models\Año;
use App\Models\Mes;
use Illuminate\Validation\Rules;
use Illuminate\Support\Collection;

class Conf extends Component
{   
    // help vars
    public $años = '';  
    public $storedAñoData;  

    // models
    public $añoModel;
    
    public $eneroIn; 
    public $eneroFin;
    public $febreroIn;
    public $febreroFin;
    public $marzoIn;
    public $marzoFin;
    public $abrilIn;
    public $abrilFin;
    public $mayoIn;
    public $mayoFin;
    public $junioIn;
    public $junioFin;
    public $julioIn;
    public $julioFin;
    public $agostoIn;
    public $agostoFin;
    public $septiembreIn;
    public $septiembreFin;
    public $octubreIn;
    public $octubreFin;
    public $noviembreIn;
    public $noviembreFin; 
    public $diciembreIn;
    public $diciembreFin;

    // listeners
    protected $listeners = ['refresh' => 'mount'];

    public function render()
    {
        return view('livewire.admin.presupuestos.conf');
    }

    public function mount(){
        $this->getAños();
    }

    public function getAños(){
        $this->años = Año::select('id', 'description')->get();
    }

    public function updatedAñomodel(){
        $this->storedAñoData = Mes::where('ano_id', $this->añoModel)->get();

        if (!$this->storedAñoData->isEmpty()){ 
            $this->eneroIn = $this->storedAñoData[0]->f_inicio;
            $this->eneroFin = $this->storedAñoData[0]->f_fin;

            $this->febreroIn = $this->storedAñoData[1]->f_inicio;
            $this->febreroFin = $this->storedAñoData[1]->f_fin;

            $this->marzoIn = $this->storedAñoData[2]->f_inicio;
            $this->marzoFin = $this->storedAñoData[2]->f_fin;

            $this->abrilIn = $this->storedAñoData[3]->f_inicio;
            $this->abrilFin = $this->storedAñoData[3]->f_fin;

            $this->mayoIn = $this->storedAñoData[4]->f_inicio;
            $this->mayoFin = $this->storedAñoData[4]->f_fin;

            $this->junioIn = $this->storedAñoData[5]->f_inicio;
            $this->junioFin = $this->storedAñoData[5]->f_fin;

            $this->julioIn = $this->storedAñoData[6]->f_inicio;
            $this->julioFin = $this->storedAñoData[6]->f_fin;

            $this->agostoIn = $this->storedAñoData[7]->f_inicio;
            $this->agostoFin = $this->storedAñoData[7]->f_fin;

            $this->septiembreIn = $this->storedAñoData[8]->f_inicio;
            $this->septiembreFin = $this->storedAñoData[8]->f_fin;

            $this->octubreIn = $this->storedAñoData[9]->f_inicio;
            $this->octubreFin = $this->storedAñoData[9]->f_fin;

            $this->noviembreIn = $this->storedAñoData[10]->f_inicio;
            $this->noviembreFin = $this->storedAñoData[10]->f_fin;

            $this->diciembreIn = $this->storedAñoData[11]->f_inicio;
            $this->diciembreFin = $this->storedAñoData[11]->f_fin;
        }else{
            $this->storedAñoData = false;
        }
    }

    public function updateMeses (){

        $this->validate([ 
            'eneroIn' => ['required', 'date'],
            'eneroFin' => ['required', 'date'],
            'febreroIn' => ['required', 'date'],
            'febreroFin' => ['required', 'date'],
            'marzoIn' => ['required', 'date'],
            'marzoFin' => ['required', 'date'],
            'abrilIn' => ['required', 'date'],
            'abrilFin' => ['required', 'date'],
            'mayoIn' => ['required', 'date'],
            'mayoFin' => ['required', 'date'],
            'junioIn' => ['required', 'date'],
            'junioFin' => ['required', 'date'],
            'junioFin' => ['required', 'date'],
            'julioIn' => ['required', 'date'],
            'julioFin' => ['required', 'date'],
            'agostoIn' => ['required', 'date'],
            'agostoFin' => ['required', 'date'],
            'septiembreIn' => ['required', 'date'],
            'septiembreFin' => ['required', 'date'],
            'octubreIn' => ['required', 'date'],
            'octubreFin' => ['required', 'date'],
            'noviembreIn' => ['required', 'date'],
            'noviembreFin' => ['required', 'date'],
            'diciembreIn' => ['required', 'date'],
            'diciembreFin' => ['required', 'date']
        ]);

        $dates = [ 
            $this->eneroIn, 
            $this->eneroFin, 
            $this->febreroIn, 
            $this->febreroFin, 
            $this->marzoIn, 
            $this->marzoFin, 
            $this->abrilIn, 
            $this->abrilFin, 
            $this->mayoIn,
            $this->mayoFin,
            $this->junioIn,
            $this->junioFin,
            $this->julioIn,
            $this->julioFin,
            $this->agostoIn,
            $this->agostoFin,
            $this->septiembreIn,
            $this->septiembreFin,
            $this->octubreIn,
            $this->octubreFin,
            $this->noviembreIn,
            $this->noviembreFin,
            $this->diciembreIn,
            $this->diciembreFin,
        ];

        
        $meses = Mes::where('ano_id', $this->añoModel)->get();
        
        $key = 0;
        foreach ($meses as $mes){
            $mes->f_inicio = $dates[$key];
            $mes->f_fin = $dates[$key+1];
            $mes->update();
            $key += 2;
        }

        return redirect()->back()->with('success', '¡Año generado exitosamente!'); 
    }
}

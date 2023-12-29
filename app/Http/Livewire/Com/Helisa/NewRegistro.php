<?php

namespace App\Http\Livewire\Com\Helisa;

use Livewire\Component;
use App\Models\User;
use App\Models\Año;
use App\Models\Mes; 
use App\Models\Cuenta; 
use App\Models\Helisa;
use Illuminate\Validation\Rules;
use Illuminate\Support\Facades\Auth;

class NewRegistro extends Component
{   
    // MODELS
    public $fecha = ""; 
    public $tipo_doc = "";  
    public $num_doc = "";  
    public $concepto = null; 
    public $identidad = "";  
    public $nom_tercero = ""; 
    public $centro = ""; 
    public $nom_centro_costo = "";
    public $debito = null;
    public $credito = null;
    public $id_cuenta = "";
    public $mes = null;
    public $año = null;

    // porcentajes
    public $comercial0;
    public $comercial1;
    public $comercial2;
    public $comercial3;

    public $porcentaje0 = 100;
    public $porcentaje1;
    public $porcentaje2;
    public $porcentaje3;

    public $base_factura0;
    public $base_factura1;
    public $base_factura2;
    public $base_factura3;

    public $comision0;
    public $comision1;
    public $comision2;
    public $comision3;

    //USEFUL VARS
    public $cuentas = []; 
    public $años = []; 
    public $meses = []; 
    public $participaciones = 1;
    public $testigoPorcentaje;
    public $comerciales = [];

    public function render()
    {   
        return view('livewire.com.helisa.new-registro');
    }

    public function mount(){
        $this->getAños();
        $this->getMeses();
        $this->getCuentas();
        $this->getComerciales();

        $this->comercial0 = Auth::id();
    }
 
    public function getAños(){
        $this->años = Año::select('id','description')->get();
    }

    public function getMeses(){
        $this->meses = Mes::select('id','description')->where('id', '<', 13)->get();
    }

    public function getCuentas(){
        $this->cuentas = Cuenta::select('id', 'description')->get();
    }

    public function getComerciales(){
        $this->comerciales = User::select('id', 'name')->where('rol', 2)->get();
    }

    public function updatedFecha(){
        $this->validate(['fecha' => ['required']]); 
    }
    
    public function updatedTipoDoc(){
        $this->validate(['tipo_doc' => ['required', 'string']]); 
    }

    public function updatedNumDoc(){
        $this->validate(['num_doc' => ['required', 'string']]); 
    }

    public function updatedIdentidad(){
        $this->validate(['identidad' => ['required', 'string']]); 
    }

    public function updatedNomTercero(){
        $this->validate(['nom_tercero' => ['required', 'string']]); 
    }

    public function updatedCentro(){
        $this->validate(['centro' => ['required', 'string']]); 
    } 

    public function updatedNomCentroCosto(){ 
        $this->validate(['nom_centro_costo' => ['required', 'string']]); 
    }

    public function updatedDebito(){
        $this->credito = 0;
        $this->debito = str_replace(",",'', $this->debito);
        $this->validate(['debito' => ['required', 'numeric', 'min:1']]); 
        $this->debito = ($this->debito * -1);

        $this->getValor();
        $this->getTotalPorcentaje();
    }
    
    public function updatedCredito(){
        $this->debito = 0;
        $this->credito = str_replace(",",'', $this->credito);
        $this->validate(['credito' => ['required', 'numeric', 'min:1']]); 

        $this->getValor();
        $this->getTotalPorcentaje();
    }

    public function updatedMes(){
        $this->validate(['mes' => ['required', 'string']]); 
    }

    public function updatedAño(){
        $this->validate(['año' => ['required', 'string']]);  
    }

    public function updatedComision(){ 
        $this->validate(['comision' => ['required', 'numeric']]); 
    }

        /****** PARTICIPACIONES ******/
        public function updatedParticipaciones(){
            if ($this->participaciones >= 4){$this->participaciones = 4;}
            if ($this->participaciones <= 0){$this->participaciones = 1;}
    
            $this->validate([
                'participaciones' => 'required|numeric|min:1|max:4'
            ]);
    
            $this->getPorcentaje();
            $this->getValor();
            $this->getTotalPorcentaje();
            $this->updatedTestigoPorcentaje();
            $this->getComision();
        }
    
        public function updatedComercial0(){
            $this->validate([
                'comercial0' => 'required|numeric'
            ]);

            $this->comercial0 = Auth::id();
        }
    
        public function updatedComercial1(){
            $this->validate([
                'comercial1' => 'required|numeric'
            ]);
        }
    
        public function updatedComercial2(){
            $this->validate([
                'comercial2' => 'required|numeric'
            ]);
        }
    
        public function updatedComercial3(){
            $this->validate([
                'comercial3' => 'required|numeric'
            ]);
        }
    
        public function updatedPorcentaje0(){
            if ($this->porcentaje0 >= 100){$this->porcentaje0 = 100;}
            if ($this->porcentaje0 <= 0){$this->porcentaje0 = 1;}
    
            $this->validate([
                'porcentaje0' => 'required|numeric|min: 1|max: 100'
            ]);
    
            $this->getValor();
            $this->getTotalPorcentaje();
            $this->updatedTestigoPorcentaje();
        }
    
        public function updatedPorcentaje1(){
            if ($this->porcentaje1 >= 100){$this->porcentaje1 = 100;}
            if ($this->porcentaje1 <= 0){$this->porcentaje1 = 1;}
    
            $this->validate([
                'porcentaje1' => 'required|numeric|min: 1|max: 100'
            ]);
    
            $this->getValor();
            $this->getTotalPorcentaje();
            $this->updatedTestigoPorcentaje();
        }
    
        public function updatedPorcentaje2(){
            if ($this->porcentaje2 >= 100){$this->porcentaje2 = 100;}
            if ($this->porcentaje2 <= 0){$this->porcentaje2 = 1;}
    
            $this->validate([
                'porcentaje2' => 'required|numeric|min: 1|max: 100'
            ]);
    
            $this->getValor();
            $this->getTotalPorcentaje();
            $this->updatedTestigoPorcentaje();
        }
    
        public function updatedPorcentaje3(){
            if ($this->porcentaje3 >= 100){$this->porcentaje3 = 100;}
            if ($this->porcentaje3 <= 0){$this->porcentaje3 = 1;}
    
            $this->validate([
                'porcentaje3' => 'required|numeric|min: 1|max: 100'
            ]);
    
            $this->getValor();
            $this->getTotalPorcentaje();
            $this->updatedTestigoPorcentaje();
        }
    
        public function updatedBaseFactura0(){
            $this->validate([
                'base_factura0' => 'required|numeric'
            ]);
        }
    
        public function updatedBaseFactura1(){
            $this->validate([
                'base_factura1' => 'required|numeric'
            ]);
        }
    
        public function updatedBaseFactura2(){
            $this->validate([
                'base_factura2' => 'required|numeric'
            ]); 
        }
    
        public function updatedBaseFactura3(){
            $this->validate([
                'base_factura3' => 'required|numeric'
            ]);
        }
        
        public function updateComision0(){
            $this->validate([
                'comision0' => 'required|numeric'
            ]);
        }

        public function updateComision1(){
            $this->validate([
                'comision1' => 'required|numeric'
            ]);
        }

        public function updateComision2(){
            $this->validate([
                'comision2' => 'required|numeric'
            ]);
        }

        public function updateComision3(){
            $this->validate([
                'comision3' => 'required|numeric'
            ]);
        }

        public function updatedTestigoPorcentaje(){
            $this->validate([
                'testigoPorcentaje' => 'required|numeric|min:100|max:100'
            ]);
        }
    
        public function getTotalPorcentaje(){
            $i = 0;
            $this->testigoPorcentaje = 0;
            while($i < $this->participaciones){            
                $this->testigoPorcentaje += $this->{'porcentaje'.$i}; 
                $i++;
            }
        }
    
        public function getValor(){
            $i = 0;
            while($i < $this->participaciones){     
                if ($this->debito == 0){
                    $this->{'base_factura'.$i} = $this->credito * ($this->{'porcentaje'.$i} / 100);
                }elseif($this->credito == 0){
                    $this->{'base_factura'.$i} = $this->debito * ($this->{'porcentaje'.$i} / 100);
                }
                $i++;
            }

            $this->getComision();
        }
    
        public function getPorcentaje(){
            $i = 0;
            while($i < $this->participaciones){     
                $this->{'porcentaje'.$i} = 100/$this->participaciones;
                $i++;
            }
 
            $this->getComision();
        }

        public function getComision(){
            $i = 0;
            while($i < $this->participaciones){     
                $this->{'comision'.$i} = ($this->{'base_factura'.$i} * (0.02));
                $i++;
            }
        }
        /************/

    public function store(){  
        $this->validate([
            'fecha' => 'required',
            'tipo_doc' => 'required|string',
            'num_doc' => 'required|string',
            'identidad' => 'required|string',
            'nom_tercero' => 'required|string',
            'centro' => 'required|string',
            'nom_centro_costo' => 'required|string',
            'debito' => 'required|numeric',
            'credito' => 'required|numeric',
            'id_cuenta' => 'numeric',
            'mes' => 'required|string',
            'año' => 'required|string',

            // PARTICIPACIONES 
            'testigoPorcentaje' => 'required|numeric|min:100|max:100',
            'participaciones' => 'required|numeric|min:1|max:4',
            'porcentaje0' => 'required|numeric|min: 1|max: 100',
            'porcentaje1' => 'nullable|numeric|min: 1|max: 100',
            'porcentaje2' => 'nullable|numeric|min: 1|max: 100',
            'porcentaje3' => 'nullable|numeric|min: 1|max: 100',

            'comercial0' => 'required|numeric',
            'comercial1' => 'nullable|numeric',
            'comercial2' => 'nullable|numeric',
            'comercial3' => 'nullable|numeric',

            'base_factura0' => 'required|numeric',
            'base_factura1' => 'nullable|numeric',
            'base_factura2' => 'nullable|numeric',
            'base_factura3' => 'nullable|numeric',
        ]);
        
        $i  = 0;
        while($i < $this->participaciones){
            $helisa = new Helisa;
            $helisa->fecha = $this->fecha;
            $helisa->tipo_doc = $this->tipo_doc;
            $helisa->num_doc = $this->num_doc;
            $helisa->identidad = $this->identidad;
            // Concepto no va, pero no lo saco de la db por tiempo
            $helisa->concepto = $this->concepto;
            $helisa->nom_tercero = $this->nom_tercero;   
            $helisa->centro = $this->centro;
            $helisa->nom_centro_costo = $this->nom_centro_costo;
            $helisa->debito = str_replace(",",'', $this->debito);
            $helisa->credito = str_replace(",",'', $this->credito);
            $helisa->comercial = $this->{'comercial'.$i};
            $helisa->porcentaje = $this->{'porcentaje'.$i};
            $helisa->base_factura = str_replace(",",'', $this->{'base_factura'.$i});
            $helisa->participacion = $this->participaciones;

            if ($this->id_cuenta){ 
                $helisa->id_cuenta = $this->id_cuenta;
            }

            $helisa->mes = $this->mes;
            $helisa->año = $this->año;
            $helisa->comision = str_replace(",",'', $this->{'comision'.$i});
            $helisa->save(); 
            $i++;
        }
        
        return redirect()->route('gestion-helisa')->with('success', 'Registro creado exitosamente!');
    }
}

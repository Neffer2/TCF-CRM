<?php

namespace App\Http\Livewire\Com\Base;

use Livewire\Component;
use App\Models\Base_comercial;
use App\Models\EstadoCuenta;
use App\Models\Cuenta;
use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Livewire\WithFileUploads;
  
class Edit extends Component
{   
    use WithFileUploads;

    // Models
    public $nom_cliente;
    public $nom_proyecto;
    public $CC;
    public $valor; 
    public $estado;
    public $cuenta;
    public $cotizacion_file_actualizacion;

    // porcentajes
    public $comercial0;
    public $comercial1;
    public $comercial2;
    public $comercial3;

    public $porcentaje0 = 100;
    public $porcentaje1;
    public $porcentaje2;
    public $porcentaje3;

    public $valor0;
    public $valor1;
    public $valor2;
    public $valor3; 

    // Useful vars
    public $estados;
    public $cuentas; 
    public $comerciales = [];
    public $participaciones = 1;
    public $stored;
    public $id_gestion;
    public $valor_guardado = 0;

    public $proyecto_id;

 
    public function render()
    {
        return view('livewire.com.base.edit');
    }

    public function mount($proyecto_id){
        $stored = Base_comercial::select('nom_cliente', 'nom_proyecto', 'cod_cc', 'valor_original', 'valor_proyecto', 'id_estado', 'id_cuenta', 'id_gestion', 'cotizacion_file_actualizacion')->where('id', $proyecto_id)->first();
        $Proyectos = Base_comercial::select('nom_cliente', 'nom_proyecto', 'cod_cc', 'valor_original', 'valor_proyecto', 'id_estado', 'id_cuenta', 'id_gestion', 'porcentaje', 'id_user')->where('id_gestion', $stored->id_gestion)->get();

        $this->id_gestion = $stored->id_gestion;        
        $this->nom_cliente = $stored->nom_cliente;
        $this->nom_proyecto = $stored->nom_proyecto;
        $this->CC = $stored->cod_cc;
        $this->valor = $stored->valor_original;
        $this->valor_guardado = $stored->valor_original;
        $this->estado = $stored->id_estado;
        $this->cuenta = $stored->id_cuenta;

        // Necesario por si alguna base comercial no tiene gestion
        if ($stored->gestion){
            $this->participaciones = $stored->gestion->participaciones;    
        }

        foreach ($Proyectos as $key => $proyecto) {
            $this->{'comercial'.$key} = $proyecto->id_user;
        }

        foreach ($Proyectos as $key => $proyecto) {
            $this->{'porcentaje'.$key} = $proyecto->porcentaje;
        }

        $this->getValor();
        $this->getTotalPorcentaje();

        $this->getEstados();
        $this->getCuentas();
        $this->getComerciales();
    }

    public function updatedValor(){
        $this->validate([
            'valor' => 'numeric'
        ]); 

        $this->getValor();
        $this->getTotalPorcentaje();
    }

    public function updatedNomCliente(){
        $this->validate([
            'nom_cliente' => 'string|min:0'
        ]); 
    }

    public function updatedCC(){
        $this->validate([
            'nom_cliente' => 'string|min:0'
        ]); 
    }

    public function updatedNomProyecto(){
        $this->validate([
            'nom_proyecto' => 'string|min:0'
        ]); 
    }

    public function updatedEstado(){
        $this->validate([
            'estado' => 'numeric',
        ]); 
    }

    public function updatedCuenta(){
        $this->validate([
            'cuenta' => 'numeric'            
        ]); 
    }

    public function CotizacionFileActualizacion(){
        $this->validate([
            'cotizacion_file_actualizacion' => 'nullable|max:1024'         
        ]); 
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
        }
    
        public function updatedComercial0(){
            $this->validate([
                'comercial0' => 'required|numeric'
            ]);
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
    
        public function updatedValor0(){
            $this->validate([
                'valor0' => 'required|numeric'
            ]);
        }
    
        public function updatedValor1(){
            $this->validate([
                'valor1' => 'required|numeric'
            ]);
        }
    
        public function updatedValor2(){
            $this->validate([
                'valor2' => 'required|numeric'
            ]); 
        }
    
        public function updatedValor3(){
            $this->validate([
                'valor3' => 'required|numeric'
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
                $this->{'valor'.$i} = $this->valor * ($this->{'porcentaje'.$i} / 100);
                $i++;
            }
        }
    
        public function getPorcentaje(){
            $i = 0;
            while($i < $this->participaciones){     
                $this->{'porcentaje'.$i} = 100/$this->participaciones;
                $i++;
            }
        }
        /************/

    public function getEstados(){
        $this->estados = EstadoCuenta::select('id', 'description')->get();
    }

    public function getCuentas(){
        $this->cuentas = Cuenta::select('id', 'description')->get();
    }

    public function getComerciales(){
        $this->comerciales = User::select('id', 'name')->where('rol', 2)->get();
    }
    

    public function update_proyecto(){
        // $proyecto = Base_comercial::where('id', $this->proyecto_id)->first();

        $this->validate([
            'nom_cliente' => 'string|min:0',
            'nom_proyecto' => 'string|min:0',
            'CC' => 'string|min:0',
            'valor' => 'numeric', 
            'estado' => 'numeric',
            'cuenta' => 'numeric',
            'cotizacion_file_actualizacion' => 'nullable|max:1024',

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

            'valor0' => 'required|numeric',
            'valor1' => 'nullable|numeric',
            'valor2' => 'nullable|numeric',
            'valor3' => 'nullable|numeric',
        ]);

        $i  = 0;
        while($i < $this->participaciones){
            $proyecto = Base_comercial::where('id_gestion', $this->id_gestion)->where('id_user', $this->{'comercial'.$i})->first();

            if ($this->nom_cliente){
                $proyecto->nom_cliente = $this->nom_cliente;
            }
    
            if ($this->nom_proyecto){
                $proyecto->nom_proyecto = $this->nom_proyecto;
            }
    
            if ($this->CC){
                $proyecto->cod_cc = $this->CC;
            }
    
            if ($this->valor){
                $proyecto->valor_original = $this->valor;
            }
            
            if ($this->estado){
                $proyecto->id_estado = $this->estado;
            }
    
            if ($this->cuenta){ 
                $proyecto->id_cuenta = $this->cuenta;
            } 

            if ($this->cuenta){
                $proyecto->id_cuenta = $this->cuenta;
            }

            if ($this->cotizacion_file_actualizacion){
                $proyecto->cotizacion_file_actualizacion = $this->cotizacion_file_actualizacion->store('cotizaciones');
            }

            $proyecto->valor_proyecto = $this->{'valor'.$i};
            $proyecto->porcentaje = $this->{'porcentaje'.$i};

            $proyecto->id_asistente = Auth::user()->id;
            $proyecto->update();
            $i++;
        }

        return redirect()->route('dashboard-base')->with('success', 'Proyecto actualizado exitosamente.');
    }   
}
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
    // public $concepto = null; 
    public $identidad = "";  
    public $nom_tercero = ""; 
    public $centro = ""; 
    public $nom_centro_costo = "";
    public $debito = null;
    public $credito = null;
    public $id_cuenta = "";
    public $participacion = 0;
    public $base_factura = null;
    public $mes = null;
    public $año = null;
    public $comision = null;
    public $porcentaje;

    //USEFUL VARS
    public $cuentas = []; 
    public $años = []; 
    public $meses = []; 

    public function render()
    {   
        $this->getAños();
        $this->getMeses();
        $this->getCuentas();
        return view('livewire.com.helisa.new-registro');
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

    public function updatedFecha(){
        $this->validate(['fecha' => ['required']]); 
    }
    
    public function updatedTipoDoc(){
        $this->validate(['tipo_doc' => ['required', 'string']]); 
    }

    public function updatedNumDoc(){
        $this->validate(['num_doc' => ['required', 'string']]); 
    }

    // public function updatedConcepto(){
    //     $this->validate(['concepto' => ['required', 'string']]); 
    // }

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
        $this->validate(['debito' => ['required', 'numeric', 'min:1']]); 
        $this->debito = ($this->debito * -1);
        $this->baseComercial($this->debito);
    }
    
    public function updatedCredito(){
        $this->debito = 0;
        $this->validate(['credito' => ['required', 'numeric', 'min:1']]); 
        $this->baseComercial($this->credito);
    }

    public function updatedPorcentaje(){
        $this->validate(['porcentaje' => ['numeric', 'max:100', 'min:1']]); 
        if ($this->credito == 0){
            $this->baseComercial($this->debito);
        }elseif ($this->debito == 0){
            $this->baseComercial($this->credito);
        }
    }

    public function updatedParticipacion(){
        $this->validate(['participacion' => ['required', 'numeric']]); 
    }

    public function updatedBaseFactura(){
        $this->validate(['base_factura' => ['required', 'numeric']]); 
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

    public function baseComercial($value){
        if ($this->porcentaje > 0 && $value != 0){
            $this->base_factura = ($value * $this->porcentaje)/100;
        }

        $this->getComision();
    }

    public function getComision(){
        $this->comision = ($this->base_factura * (0.02));
    }

    public function store(){
        $this->validate([
            'fecha' => ['required'],
            'tipo_doc' => ['required', 'string'],
            'num_doc' => ['required', 'string'],
            // 'concepto' => ['required', 'string'],
            'identidad' => ['required', 'string'],
            'nom_tercero' => ['required', 'string'],
            'centro' => ['required', 'string'],
            'nom_centro_costo' => ['required', 'string'],
            'debito' => ['required', 'numeric'],
            'credito' => ['required', 'numeric'], 
            'porcentaje' => ['numeric', 'max:100', 'min:1'],
            'id_cuenta' => ['numeric'],
            'participacion' => ['required', 'numeric'],
            'base_factura' => ['required', 'numeric'],
            'mes' => ['required', 'string'],
            'año' => ['required', 'string'],
            'comision' => ['required', 'numeric']
        ]);
        
        $helisa = new Helisa;
        $helisa->fecha = $this->fecha;
        $helisa->tipo_doc = $this->tipo_doc;
        $helisa->num_doc = $this->num_doc;
        // $helisa->concepto = $this->concepto; 
        $helisa->identidad = $this->identidad;
        $helisa->nom_tercero = $this->nom_tercero;  
        $helisa->centro = $this->centro;
        $helisa->nom_centro_costo = $this->nom_centro_costo;
        $helisa->debito = $this->debito;
        $helisa->credito = $this->credito;
        $helisa->comercial = Auth::user()->id;
        $helisa->base_factura = $this->base_factura;
        $helisa->porcentaje = $this->porcentaje;

        if ($this->id_cuenta){ 
            $helisa->id_cuenta = $this->id_cuenta;
        }

        $helisa->participacion = $this->participacion;
        $helisa->base_factura = $this->base_factura;
        $helisa->mes = $this->mes;
        $helisa->año = $this->año;
        $helisa->comision = $this->comision;
        $helisa->save(); 

        return redirect()->route('gestion-helisa')->with('success', 'Registro creado exitosamente!');
    }
}
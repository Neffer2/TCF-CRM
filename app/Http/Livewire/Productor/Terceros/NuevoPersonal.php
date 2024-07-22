<?php

namespace App\Http\Livewire\Productor\Terceros;

use Livewire\Component;
use App\Models\Tercero;
use App\Models\EstadoTercero;
use Livewire\WithFileUploads;
use Illuminate\Support\Facades\Auth;

class NuevoPersonal extends Component
{
    use WithFileUploads;
    // Auth::check()
    /*
        * This component is used to register new personal
        * If thi component have a $tercero, it means that we are going to edit it
    */

    // Models
    public $nombre, $apellido, $cedula, $correo, $telefono, $ciudad, $banco, $rut, $cert_bancaria, $firma, $terminos, $estado = 1;

    // Useful vars 
    public $estados, $ciudades, $deleteConfirm = false;

    // Filled
    public $tercero;

    public function render()
    {
        return view('livewire.productor.terceros.nuevo-personal');
    }

    public function mount(){
        $this->ciudades = app('ciudades');
        $this->estados = EstadoTercero::all();

        if ($this->tercero){$this->fillForm();}
    }

    public function nuevoPersonal(){
        $this->validate([
            'nombre' => 'required|max:255',
            'apellido' => 'required|max:255',
            'cedula' => 'required|numeric|unique:terceros',
            'correo' => 'required|email|unique:terceros',
            'telefono' => 'required|numeric|unique:terceros',
            'ciudad' => 'required|string',            
            'estado' => 'required|numeric|max:1',            
        ]);

        $tercero = new Tercero();
        $tercero->nombre = $this->nombre;
        $tercero->apellido = $this->apellido;
        $tercero->cedula = trim($this->cedula);
        $tercero->correo = trim($this->correo);
        $tercero->telefono = trim($this->telefono);
        $tercero->ciudad = $this->ciudad;
        $tercero->estado = trim($this->estado);

        if($this->banco){
            $this->validate(['banco' => 'string|max:255']);            
            $tercero->banco = $this->banco;
        }

        if($this->rut){
            $this->validate(['rut' => 'file|mimes:pdf,xls,xlsx|max:10000']);             
            $tercero->rut = $this->rut->store('public/ruts'); 
        } 

        if($this->cert_bancaria){
            $this->validate(['cert_bancaria' => 'file|mimes:pdf,xls,xlsx|max:10000']);             
            $tercero->cert_bancaria = $this->cert_bancaria->store('public/cert_bancarias'); 
        }

        $tercero->save();
        $this->emit('terceroRegistrado');

        $this->reset_fields([
            'nombre',
            'apellido',
            'cedula',
            'correo',
            'telefono',
            'ciudad',
            'estado',
            'banco',
            'rut',
            'cert_bancaria',
        ]);
        return redirect()->back()->with('success', 'Personal registrado con éxito.');
    } 

    public function actualizarPersonal(){
        $this->validate([
            'nombre' => 'required|max:255',
            'apellido' => 'required|max:255',
            'cedula' => 'required|numeric',
            'correo' => 'required|email',
            'telefono' => 'required|numeric',
            'ciudad' => 'required|string',            
            'estado' => 'required|numeric|max:1',            
        ]);

        $tercero = $this->tercero;
        $tercero->nombre = $this->nombre;
        $tercero->apellido = $this->apellido;
        $tercero->cedula = trim($this->cedula);
        $tercero->correo = trim($this->correo);
        $tercero->telefono = trim($this->telefono);
        $tercero->ciudad = $this->ciudad;
        $tercero->estado = trim($this->estado);

        if($this->banco){
            $this->validate(['banco' => 'string|max:255']);            
            $tercero->banco = $this->banco;
        }

        if($this->rut){
            $this->validate(['rut' => 'file|mimes:pdf,xls,xlsx|max:10000']);             
            $tercero->rut = $this->rut->store('public/ruts'); 
        } 

        if($this->cert_bancaria){
            $this->validate(['cert_bancaria' => 'file|mimes:pdf,xls,xlsx|max:10000']);             
            $tercero->cert_bancaria = $this->cert_bancaria->store('public/cert_bancarias'); 
        }

        $tercero->update(); 
        $this->emit('terceroRegistrado');

        $this->reset_fields([
            'nombre',
            'apellido',
            'cedula',
            'correo',
            'telefono',
            'ciudad',
            'estado',
            'banco',
            'rut',
            'cert_bancaria',
        ]);

        return redirect()->route('personal')->with('success', 'Cambios guardados con éxito.');
    }

    public function deletePersonal(){
        $this->tercero->delete();
        $this->emit('terceroRegistrado');
        return redirect()->route('personal')->with('success', 'Personal eliminado con éxito.');
    }  

    public function fillForm(){
        $this->nombre = $this->tercero->nombre;
        $this->apellido = $this->tercero->apellido;
        $this->cedula = $this->tercero->cedula;
        $this->correo = $this->tercero->correo;
        $this->telefono = $this->tercero->telefono;
        $this->ciudad = $this->tercero->ciudad;
        $this->estado = $this->tercero->estado;
        $this->banco = $this->tercero->banco;
        // $this->rut = $this->tercero->rut;
        // $this->cert_bancaria = $this->tercero->cert_bancaria;
    }

    public function toggelConfirm(){
        $this->deleteConfirm = !$this->deleteConfirm;
    }

    /*
        * Reset de los campos
        * @param array $fields
    */
    public function reset_fields($fields = null){
        foreach($fields as $field){
            $this->$field = '';
        }
    }
}
  
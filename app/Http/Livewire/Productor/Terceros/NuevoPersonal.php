<?php

namespace App\Http\Livewire\Productor\Terceros;

use Livewire\Component;
use App\Models\Tercero;

class NuevoPersonal extends Component
{
    // Modesl
    public $nombre, $apellido, $cedula, $correo, $telefono, $ciudad, $banco, $rut, $cert_bancaria, $firma, $terminos, $estado;

    public function render()
    {
        return view('livewire.productor.terceros.nuevo-personal');
    }

    public function nuevoPersonal(){
        $this->validate([
            'nombre' => 'required|max:255',
            'apellido' => 'required|max:255',
            'cedula' => 'required|numeric|unique:terceros',
            'correo' => 'required|email|unique:terceros',
            'telefono' => 'required|numeric|unique:terceros',
            'ciudad' => 'required|alpha',            
            'estado' => 'required|numeric|max:1',            
        ]);

        $tercero = new Tercero();
        $tercero->nombre = $this->nombre;
        $tercero->apellido = $this->apellido;
        $tercero->cedula = $this->cedula;
        $tercero->correo = $this->correo;
        $tercero->telefono = $this->telefono;
        $tercero->ciudad = $this->ciudad;
        $tercero->estado = $this->estado;
        $tercero->save();

        return redirect()->back();
    } 

    // public function (){
    //     $this->estado = 1;
    // }
}
  
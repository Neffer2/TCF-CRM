<?php

namespace App\Http\Livewire\Admin\Produccion\Proveedores;

use Livewire\Component;
use App\Models\Proveedor;
use App\Models\CategoriaProveedor;

class Proveedores extends Component
{   
    // Models 
    public $contacto, $categoria, $ciudad, $estado;

    // Usefull vars
    public $categorias = [], $ciudades = [];

    // Listeners
    protected $listeners = ['refreshProveedores' => 'render'];

    public function render() 
    {   
        $filtros = [];

        if ($this->contacto){
            array_push($filtros, ['contacto', 'LIKE', "%{$this->contacto}%"]);
        }

        if ($this->categoria){
            array_push($filtros, ['categoria_id', $this->categoria]);
        }

        if ($this->ciudad){
            array_push($filtros, ['ciudad', $this->ciudad]);
        }

        if ($this->estado){
            array_push($filtros, ['estado', $this->estado]);
        }

        $proveedores = Proveedor::where($filtros)->orderBy('created_at', 'desc')->paginate(15);
        return view('livewire.admin.produccion.proveedores.proveedores', ['proveedores' => $proveedores]);
    }

    public function mount (){
        $this->getCategorias();
        $this->getCiudades();
    }

    public function getCategorias(){
        $this->categorias = CategoriaProveedor::all();
    }

    public function getCiudades(){
        $this->ciudades = app('ciudades'); 
    }

    public function deleteProveedor($proveedor_id){
        $proveedor = Proveedor::find($proveedor_id);
        $proveedor->delete();

        $this->render();
        return redirect()->back()->with('success', '¡Proveedor eliminado con éxito!');
    }
}

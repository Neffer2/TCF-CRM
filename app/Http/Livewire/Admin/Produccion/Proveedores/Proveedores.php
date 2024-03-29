<?php

namespace App\Http\Livewire\Admin\Produccion\Proveedores;

use Livewire\Component;
use App\Models\Proveedor;
use App\Models\CategoriaProveedor;
use Livewire\WithPagination; 

class Proveedores extends Component
{   
    use WithPagination; 
    protected $paginationTheme = 'bootstrap'; 

    // Models 
    public $contacto, $tercero, $categoria, $ciudad, $estado;

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

        if ($this->tercero){
            array_push($filtros, ['tercero', 'LIKE', "%{$this->tercero}%"]);
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
  
        $proveedores = Proveedor::where($filtros)->orderBy('tercero', 'asc')->paginate(15);
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

<?php

namespace App\Http\Livewire\Admin\Produccion\Proveedores;

use Livewire\Component; 
use App\Models\CategoriaProveedor;
use App\Models\Proveedor;

class NuevoProveedor extends Component  
{   
    // Models 
    public $categoria, $tercero, $tipo, $tipo_documento, $documento, $dv, $servicio, $anticipo, $contacto, $celular, $fijo, $correo, $web,
            $direccion, $departamento, $ciudad, $plazo, $observaciones, $estado, $nueva_categoria;

    // Useful vars 
    public $categorias = [], $ciudades = [], $departamentos = [];

    // Filled
    public $proveedor_id;
 
    public function render() 
    {   
        return view('livewire.admin.produccion.proveedores.nuevo-proveedor');
    }

    public function mount(){
        if ($this->proveedor_id){
            $this->getData();
        }

        $this->getCategorias();
        $this->getCiudades();
        $this->getDepartamentos();
    }

    public function getData(){
        $proveedor = Proveedor::find($this->proveedor_id);

        $this->categoria = $proveedor->categoria_id;
        $this->tercero = $proveedor->tercero;
        $this->tipo = $proveedor->tipo;
        $this->tipo_documento = $proveedor->tipo_doc;
        $this->documento = $proveedor->documento;
        $this->dv = $proveedor->dv;
        $this->servicio = $proveedor->servicio;
        $this->anticipo = $proveedor->anticipo;
        $this->contacto = $proveedor->contacto;
        $this->celular = $proveedor->celular;
        $this->fijo = $proveedor->fijo;
        $this->correo = $proveedor->correo;
        $this->web = $proveedor->web;
        $this->direccion = $proveedor->direccion;
        $this->departamento = $proveedor->departamento;
        $this->ciudad = $proveedor->ciudad;
        $this->plazo = $proveedor->plazo;
        $this->observaciones = $proveedor->observaciones;
        $this->estado = $proveedor->estado; 
    }

    /* CATEGORIAS */
    public function getCategorias(){
        $this->categorias = CategoriaProveedor::all();
        $this->nueva_categoria = "";
    }

    public function getCiudades(){
        $this->ciudades = app('ciudades'); 
    }

    public function getDepartamentos(){
        $this->departamentos = app('departamentos'); 
    }

    public function newCategoria(){
        $this->validate([
            'nueva_categoria' => 'required|string|max:50'
        ]);

        $categoria = new CategoriaProveedor;
        $categoria->description = $this->nueva_categoria;
        $categoria->save();

        $this->getCategorias();
        return redirect()->back()->with('success', '¡Nueva categoría creada con éxito!');
    }
    /* ** */  
    
    public function store(){
        if ($this->proveedor_id){
            return $this->update();
        }

        $this->validate([
            'categoria' => 'required|numeric|max:200',
            'tercero' => 'required|string|max:200',
            'tipo' => 'required|string|max:200',
            'tipo_documento' => 'required|string|max:200',
            'documento' => 'required|unique:proveedores|numeric',
            'dv' => 'required|numeric',
            'servicio' => 'required|string|max:200',
            'anticipo' => 'required|numeric',
            'contacto' => 'required|string',
            'celular' => 'required|numeric',
            'correo' => 'required|unique:proveedores|email|max:200',
            'fijo' => 'nullable|string',
            'web' => 'nullable|string|max:200',
            'direccion' => 'nullable|string|max:200',
            'departamento' => 'required|string|max:200',
            'ciudad' => 'required|string|max:200',
            'observaciones' => 'nullable|string|max:1000',
            'estado' => 'required|string|max:200',
            'plazo' => 'required|nullable|string|max:200'
        ]);        
        
        $proveedor = new Proveedor;
        $proveedor->categoria_id = $this->categoria;
        $proveedor->tercero = $this->tercero;
        $proveedor->tipo = $this->tipo;
        $proveedor->tipo_doc = $this->tipo_documento;
        $proveedor->documento = $this->documento;            
        $proveedor->dv = $this->dv;
        $proveedor->servicio = $this->servicio;
        $proveedor->anticipo = $this->anticipo;
        $proveedor->contacto = $this->contacto;
        $proveedor->celular = $this->celular;
        $proveedor->fijo = $this->fijo;
        $proveedor->correo = $this->correo;
        $proveedor->web = $this->web;
        $proveedor->direccion = $this->direccion;
        $proveedor->departamento = $this->departamento;
        $proveedor->ciudad = $this->ciudad;
        $proveedor->plazo = $this->plazo;
        $proveedor->observaciones = $this->observaciones;
        $proveedor->estado = $this->estado; 

        $proveedor->save();

        $this->limpiar();
        return redirect()->back()->with('success', '¡Nuevo proveedor creado con éxito!');
    }

    public function update(){
        $this->validate([
            'categoria' => 'required|numeric|max:200',
            'tercero' => 'required|string|max:200',
            'tipo' => 'required|string|max:200',
            'tipo_documento' => 'required|string|max:200',
            'dv' => 'required|numeric',
            'servicio' => 'required|string|max:200',
            'anticipo' => 'required|numeric',
            'contacto' => 'required|string',
            'celular' => 'required|numeric',
            'fijo' => 'nullable|string',
            'web' => 'nullable|string|max:200',
            'direccion' => 'nullable|string|max:200',
            'departamento' => 'required|string|max:200',
            'ciudad' => 'required|string|max:200',
            'observaciones' => 'nullable|string|max:1000',
            'estado' => 'required|string|max:200',
            'plazo' => 'nullable|string|max:200'
        ]);

        $proveedor = Proveedor::find($this->proveedor_id);
        $proveedor->categoria_id = $this->categoria;
        $proveedor->tercero = $this->tercero;
        $proveedor->tipo = $this->tipo;
        $proveedor->tipo_doc = $this->tipo_documento;

        if ($this->documento != $proveedor->documento){
            $this->validate([
                'documento' => 'required|unique:proveedores|numeric'
            ]);
            $proveedor->documento = $this->documento;            
        }

        $proveedor->dv = $this->dv;
        $proveedor->servicio = $this->servicio;
        $proveedor->anticipo = $this->anticipo;
        $proveedor->contacto = $this->contacto;
        $proveedor->celular = $this->celular;
        $proveedor->fijo = $this->fijo;

        if ($this->correo != $proveedor->correo){
            $this->validate([
                'correo' => 'required|unique:proveedores|email|max:200'
            ]);
            $proveedor->correo = $this->correo;
        }

        $proveedor->web = $this->web;
        $proveedor->direccion = $this->direccion;
        $proveedor->departamento = $this->departamento;
        $proveedor->ciudad = $this->ciudad;
        $proveedor->plazo = $this->plazo;
        $proveedor->observaciones = $this->observaciones;
        $proveedor->estado = $this->estado; 
        $proveedor->update();

        $this->limpiar();
        return redirect()->back()->with('success', '¡Cambios guardados con éxito!');
    }

    public function limpiar(){
        $this->categoria = "";
        $this->tercero = "";
        $this->tipo = "";
        $this->tipo_documento = "";
        $this->documento = "";
        $this->dv = "";
        $this->servicio = "";
        $this->anticipo = "";
        $this->contacto = "";
        $this->celular = "";
        $this->fijo = "";
        $this->correo = "";
        $this->web = "";
        $this->direccion = "";
        $this->departamento = "";
        $this->ciudad = "";
        $this->plazo = "";
        $this->observaciones = "";
        $this->estado = "";

        $this->emit('refreshProveedores');
        $this->mount();
    }
    
    /* UPDATES */
    public function updatedCategoria(){
        $this->validate([
            'categoria' => 'required|numeric|max:200'
        ]);
    }

    public function updatedTercero(){
        $this->validate([
            'tercero' => 'required|string|max:200'
        ]);
    }

    public function updatedTipo(){
        $this->validate([
            'tipo' => 'required|string|max:200'
        ]);
    }

    public function updatedTipoDocumento(){
        $this->validate([
            'tipo_documento' => 'required|string|max:200'
        ]);

    }
    
    public function updatedDocumento(){
        $this->validate([
            'documento' => 'required|unique:proveedores|numeric',
        ]);
    }

    public function updatedDv(){
        $this->validate([
            'dv' => 'required|numeric'
        ]);
    }

    public function updatedServicio(){
        $this->validate([
            'servicio' => 'required|string|max:200'
        ]);    
    }

    public function updatedAnticipo(){
        $this->validate([
            'anticipo' => 'required|numeric'
        ]);        
    }

    public function updatedContacto(){
        $this->validate([
            'contacto' => 'required|string'
        ]);
    }

    public function updatedCelular(){
        $this->validate([
            'celular' => 'required|numeric'
        ]);
    }

    public function updatedFijo(){
        $this->validate([
            'fijo' => 'nullable|numeric'
        ]);
    }

    public function updatedCorreo(){
        $this->validate([
            'correo' => 'required|unique:proveedores|email|max:200'
        ]);
    }

    public function updatedWeb(){
        $this->validate([
            'web' => 'nullable|string|max:200'
        ]);
    }

    public function updatedDireccion(){
        $this->validate([
            'direccion' => 'nullable|string|max:200' 
        ]);
    }

    public function updatedDepartamento(){
        $this->validate([
            'departamento' => 'required|string|max:200'
        ]);
    }

    public function updatedCiudad(){
        $this->validate([
            'ciudad' => 'required|string|max:200'
        ]);
    }

    public function updatedObservaciones(){
        $this->validate([
            'observaciones' => 'nullable|string|max:1000'
        ]);
    }

    public function updatedEstado(){
        $this->validate([
            'estado' => 'required|string|max:200'
        ]);
    }

    public function updatedPlazo(){
        $this->validate([
            'plazo' => 'required|nullable|string|max:200'
        ]);
    }
    /* ** */
}

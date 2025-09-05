<?php

namespace App\Http\Livewire\Admin\Produccion\Proveedores;

use Livewire\Component;
use App\Models\CategoriaProveedor;
use App\Models\Proveedor;

/**
 * Componente Livewire para crear y editar proveedores
 * Maneja el formulario completo de proveedores con validación en tiempo real
 */
class NuevoProveedor extends Component 
{
    // PROPIEDADES DEL MODELO PROVEEDOR
    public $categoria;              // ID de la categoría del proveedor
    public $tercero;                // Nombre o razón social del tercero
    public $tipo;                   // Tipo de proveedor (Persona natural/jurídica)
    public $tipo_documento;         // Tipo de documento de identificación
    public $documento;              // Número de documento único
    public $dv;                     // Dígito de verificación
    public $servicio;               // Descripción del servicio que presta
    public $anticipo;               // Porcentaje de anticipo requerido

    // INFORMACIÓN DE CONTACTO
    public $contacto;               // Nombre de la persona de contacto
    public $celular;                // Número de celular
    public $fijo;                   // Teléfono fijo (opcional)
    public $correo;                 // Correo electrónico único
    public $web;                    // Sitio web (opcional)

    // INFORMACIÓN DE UBICACIÓN
    public $direccion;              // Dirección física (opcional)
    public $departamento;           // Departamento donde se ubica
    public $ciudad;                 // Ciudad donde se ubica

    // INFORMACIÓN ADICIONAL
    public $plazo;                  // Plazo de pago acordado
    public $observaciones;          // Observaciones generales (opcional)
    public $estado;                 // Estado del proveedor (activo/inactivo)
    public $nueva_categoria;        // Campo para crear nueva categoría

    // COLECCIONES PARA OPCIONES DEL FORMULARIO
    public $categorias = [];        // Lista de categorías disponibles
    public $ciudades = [];          // Lista de ciudades
    public $departamentos = [];     // Lista de departamentos

    // PROPIEDADES DE CONTROL
    public $proveedor_id;           // ID del proveedor cuando se está editando

    /** 
     * Renderiza la vista del componente
     * @return \Illuminate\View\View
     */
    public function render()
    {
        return view('livewire.admin.produccion.proveedores.nuevo-proveedor');
    }

    /**
     * Inicializa el componente cuando se monta
     * Si hay un proveedor_id, carga los datos para edición
     * Siempre carga las listas de opciones para el formulario
     */
    public function mount(){
        if ($this->proveedor_id){
            $this->getData();
        }

        $this->getCategorias();
        $this->getCiudades();
        $this->getDepartamentos();
    }

    /**
     * Carga los datos del proveedor existente para edición
     * Mapea todos los campos del modelo a las propiedades del componente
     */
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

    // ========== GESTIÓN DE CATEGORÍAS ==========

    /**
     * Obtiene todas las categorías de proveedores disponibles
     * Reinicia el campo de nueva categoría
     */
    public function getCategorias(){
        $this->categorias = CategoriaProveedor::all();
        $this->nueva_categoria = "";
    }

    /**
     * Obtiene la lista de ciudades desde el contenedor de servicios
     */
    public function getCiudades(){
        $this->ciudades = app('ciudades');
    }

    /**
     * Obtiene la lista de departamentos desde el contenedor de servicios
     */
    public function getDepartamentos(){
        $this->departamentos = app('departamentos');
    }

    /**
     * Crea una nueva categoría de proveedor
     * Valida el nombre y la guarda en la base de datos
     * @return \Illuminate\Http\RedirectResponse
     */
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
      // ========== CRUD PRINCIPAL ==========

    /**
     * Almacena un nuevo proveedor o actualiza uno existente
     * Redirige al método update() si existe proveedor_id
     * @return \Illuminate\Http\RedirectResponse
     */
    public function store(){
        if ($this->proveedor_id){
            return $this->update();
        }

        // Validación completa para nuevo proveedor
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

        // Creación del nuevo proveedor con todos los datos
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

    /**
     * Actualiza un proveedor existente
     * Valida campos únicos solo si han cambiado para evitar conflictos
     * @return \Illuminate\Http\RedirectResponse
     */
    public function update(){
        // Validación base sin campos únicos
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

        // Validación de documento único solo si cambió
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

        // Validación de correo único solo si cambió
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

    /**
     * Limpia todos los campos del formulario
     * Emite evento para refrescar la lista de proveedores
     * Reinicializa las opciones del formulario
     */
    public function limpiar(){
        // Limpieza de campos principales del proveedor
        $this->categoria = "";
        $this->tercero = "";
        $this->tipo = "";
        $this->tipo_documento = "";
        $this->documento = "";
        $this->dv = "";
        $this->servicio = "";
        $this->anticipo = "";

        // Limpieza de campos de contacto
        $this->contacto = "";
        $this->celular = "";
        $this->fijo = "";
        $this->correo = "";
        $this->web = "";

        // Limpieza de campos de ubicación y adicionales
        $this->direccion = "";
        $this->departamento = "";
        $this->ciudad = "";
        $this->plazo = "";
        $this->observaciones = "";
        $this->estado = "";

        // Emite evento para actualizar lista de proveedores en otros componentes
        $this->emit('refreshProveedores');
        $this->mount();
    }

    // ========== VALIDACIONES EN TIEMPO REAL ==========

    /**
     * Valida la categoría cuando se actualiza
     */
    public function updatedCategoria(){
        $this->validate([
            'categoria' => 'required|numeric|max:200'
        ]);
    }

    /**
     * Valida el nombre del tercero cuando se actualiza
     */
    public function updatedTercero(){
        $this->validate([
            'tercero' => 'required|string|max:200'
        ]);
    }

    /**
     * Valida el tipo de proveedor cuando se actualiza
     */
    public function updatedTipo(){
        $this->validate([
            'tipo' => 'required|string|max:200'
        ]);
    }

    /**
     * Valida el tipo de documento cuando se actualiza
     */
    public function updatedTipoDocumento(){
        $this->validate([
            'tipo_documento' => 'required|string|max:200'
        ]);
    }

    /**
     * Valida el número de documento cuando se actualiza
     * Verifica que sea único en la base de datos
     */
    public function updatedDocumento(){
        $this->validate([
            'documento' => 'required|unique:proveedores|numeric',
        ]);
    }

    /**
     * Valida el dígito de verificación cuando se actualiza
     */
    public function updatedDv(){
        $this->validate([
            'dv' => 'required|numeric'
        ]);
    }

    /**
     * Valida la descripción del servicio cuando se actualiza
     */
    public function updatedServicio(){
        $this->validate([
            'servicio' => 'required|string|max:200'
        ]);
    }

    /**
     * Valida el porcentaje de anticipo cuando se actualiza
     */
    public function updatedAnticipo(){
        $this->validate([
            'anticipo' => 'required|numeric'
        ]);
    }

    /**
     * Valida el nombre del contacto cuando se actualiza
     */
    public function updatedContacto(){
        $this->validate([
            'contacto' => 'required|string'
        ]);
    }

    /**
     * Valida el número de celular cuando se actualiza
     */
    public function updatedCelular(){
        $this->validate([
            'celular' => 'required|numeric'
        ]);
    }

    /**
     * Valida el teléfono fijo cuando se actualiza (opcional)
     */
    public function updatedFijo(){
        $this->validate([
            'fijo' => 'nullable|numeric'
        ]);
    }

    /**
     * Valida el correo electrónico cuando se actualiza
     * Verifica formato de email y unicidad en la base de datos
     */
    public function updatedCorreo(){
        $this->validate([
            'correo' => 'required|unique:proveedores|email|max:200'
        ]);
    }

    /**
     * Valida el sitio web cuando se actualiza (opcional)
     */
    public function updatedWeb(){
        $this->validate([
            'web' => 'nullable|string|max:200'
        ]);
    }

    /**
     * Valida la dirección cuando se actualiza (opcional)
     */
    public function updatedDireccion(){
        $this->validate([
            'direccion' => 'nullable|string|max:200'
        ]);
    }

    /**
     * Valida la selección de departamento cuando se actualiza
     */
    public function updatedDepartamento(){
        $this->validate([
            'departamento' => 'required|string|max:200'
        ]);
    }

    /**
     * Valida la selección de ciudad cuando se actualiza
     */
    public function updatedCiudad(){
        $this->validate([
            'ciudad' => 'required|string|max:200'
        ]);
    }

    /**
     * Valida las observaciones cuando se actualizan (opcional)
     */
    public function updatedObservaciones(){
        $this->validate([
            'observaciones' => 'nullable|string|max:1000'
        ]);
    }

    /**
     * Valida el estado del proveedor cuando se actualiza
     */
    public function updatedEstado(){
        $this->validate([
            'estado' => 'required|string|max:200'
        ]);
    }

    /**
     * Valida el plazo de pago cuando se actualiza
     */
    public function updatedPlazo(){
        $this->validate([
            'plazo' => 'required|nullable|string|max:200'
        ]);
    }
    /* ** */
}

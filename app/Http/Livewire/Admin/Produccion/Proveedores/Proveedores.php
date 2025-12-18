<?php

namespace App\Http\Livewire\Admin\Produccion\Proveedores;

use Livewire\Component;
use App\Models\Proveedor;
use App\Models\CategoriaProveedor;
use Livewire\WithPagination;

/**
 * Componente Livewire para listar y gestionar proveedores
 * Permite filtrar, paginar y eliminar proveedores con múltiples criterios de búsqueda
 */
class Proveedores extends Component
{
    use WithPagination;
    protected $paginationTheme = 'bootstrap';

    // PROPIEDADES DE FILTROS DE BÚSQUEDA
    public $contacto;               // Filtro por nombre del contacto (búsqueda parcial)
    public $tercero;                // Filtro por nombre o razón social (búsqueda parcial)
    public $documento;              // Filtro por documento (búsqueda parcial)
    public $categoria;              // Filtro por ID de categoría del proveedor
    public $ciudad;                 // Filtro por ciudad del proveedor
    public $estado;                 // Filtro por estado del proveedor (activo/inactivo)

    // COLECCIONES PARA OPCIONES DE FILTROS
    public $categorias = [];        // Lista de categorías disponibles para filtrar
    public $ciudades = [];          // Lista de ciudades disponibles para filtrar

    // EVENTOS LIVEWIRE
    protected $listeners = ['refreshProveedores' => 'render']; // Escucha evento para refrescar la lista

    /**
     * Renderiza la vista del componente con los proveedores filtrados
     * Construye dinámicamente los filtros y aplica paginación
     * @return \Illuminate\View\View
     */
    public function render()
    {
        // Array para almacenar todos los filtros aplicados
        $filtros = [];

        // Filtro por contacto: búsqueda parcial usando LIKE
        if ($this->contacto){
            array_push($filtros, ['contacto', 'LIKE', "%{$this->contacto}%"]);
        }

        // Filtro por tercero: búsqueda parcial en nombre o razón social
        if ($this->tercero){
            array_push($filtros, ['tercero', 'LIKE', "%{$this->tercero}%"]);
        }

        // Filtro por categoría: búsqueda exacta por ID
        if ($this->categoria){
            array_push($filtros, ['categoria_id', $this->categoria]);
        }

        // Filtro por ciudad: búsqueda exacta
        if ($this->ciudad){
            array_push($filtros, ['ciudad', $this->ciudad]);
        }

        if ($this->documento){
            array_push($filtros, ['documento', 'LIKE', "%{$this->documento}%"]);
        }

        // Filtro por estado: búsqueda exacta
        if ($this->estado){
            array_push($filtros, ['estado', $this->estado]);
        }

        // Consulta con filtros aplicados, ordenada alfabéticamente y paginada
        $proveedores = Proveedor::where($filtros)->orderBy('tercero', 'asc')->paginate(15);
        return view('livewire.admin.produccion.proveedores.proveedores', ['proveedores' => $proveedores]);
    }

    /**
     * Inicializa el componente cuando se monta
     * Carga las opciones necesarias para los filtros del formulario
     */
    public function mount (){
        $this->getCategorias();
        $this->getCiudades();
    }

    /**
     * Obtiene todas las categorías de proveedores disponibles
     * Carga la lista para el filtro de categorías
     */
    public function getCategorias(){
        $this->categorias = CategoriaProveedor::all();
    }

    /**
     * Obtiene la lista de ciudades desde el contenedor de servicios
     * Carga las opciones para el filtro de ciudades
     */
    public function getCiudades(){
        $this->ciudades = app('ciudades');
    }

    /**
     * Elimina un proveedor específico de la base de datos
     * Refresca la vista y muestra mensaje de confirmación
     * @param int $proveedor_id ID del proveedor a eliminar
     * @return \Illuminate\Http\RedirectResponse
     */
    public function deleteProveedor($proveedor_id){
        $proveedor = Proveedor::find($proveedor_id);
        $proveedor->delete();

        // Refresca la lista después de eliminar
        $this->render();
        return redirect()->back()->with('success', '¡Proveedor eliminado con éxito!');
    }
}

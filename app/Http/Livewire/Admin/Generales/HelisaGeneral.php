<?php

namespace App\Http\Livewire\Admin\Generales;

use Livewire\Component;
use App\Models\Helisa;
use App\Models\Año;
use App\Models\User;
use Livewire\WithPagination;

/**
 * Componente Livewire para mostrar y filtrar los registros de Helisa
 * Permite filtrar por comercial, centro de costos y año
 * También incluye funcionalidad de exportación mediante redirección
 */
class HelisaGeneral extends Component
{
    // Trait para manejar la paginación de los resultados
    use WithPagination;
    protected $paginationTheme = 'bootstrap';

    // Propiedades para los filtros del formulario
    public $comercial;     // ID del comercial/usuario seleccionado
    public $centro;        // Código de centro de costos para filtrar
    public $año;           // ID del año seleccionado

    // Arrays para almacenar las opciones de los filtros
    public $comerciales = []; // Lista de comerciales/usuarios disponibles
    public $años = [];        // Lista de años disponibles
    public $yearInfo;         // Objeto completo del año con sus meses

    /**
     * Método principal que renderiza el componente con los registros de Helisa filtrados
     * Aplica filtros de búsqueda y retorna la vista con los datos paginados
     */
    public function render()
    {
        // Array para almacenar todos los filtros aplicados
        $filtros = [];

        // NOTA: Hay un error en la condición original, debería ser $this->centro en lugar de $filtros
        // Filtro por código de centro de costos (búsqueda parcial)
        if($this->centro){
            array_push($filtros, ['centro', 'LIKE', "%$this->centro%"]);
        }

        // Filtro por año: busca registros entre el primer y último día del año
        if($this->año){
            array_push($filtros, ['fecha', '>=', $this->yearInfo->meses->first()->f_inicio]);
            array_push($filtros, ['fecha', '<=', $this->yearInfo->meses->last()->f_fin]);
        }

        // Filtro por comercial/usuario específico
        if($this->comercial){
            array_push($filtros, ['comercial', $this->comercial]);
        }

        // Obtiene los registros de Helisa aplicando todos los filtros con paginación
        $registros_helisa = Helisa::where($filtros)->paginate(15);

        return view('livewire.admin.generales.helisa-general', ['registros_helisa' => $registros_helisa]);
    }

    /**
     * Método que se ejecuta al inicializar el componente
     * Carga los datos iniciales necesarios para los filtros
     */
    public function mount (){
        $this->getComerciales();
        $this->getAños();
    }

    /**
     * Carga la lista de comerciales (usuarios con rol = 2)
     * Los comerciales son usuarios que pueden gestionar registros de Helisa
     */
    public function getComerciales(){
        $this->comerciales = User::where('rol', 2)->get();
    }

    /**
     * Carga la lista de años disponibles y establece el año más reciente por defecto
     * Automáticamente selecciona el año más reciente
     */
    public function getAños(){
        $this->años = Año::all();

        // Establece el año más reciente como selección por defecto
        $añoMasReciente = $this->años->sortByDesc('description')->first();
        if ($añoMasReciente) {
            $this->año = $añoMasReciente->id;
            $this->updatedAño();
        }
    }

    /**
     * Método que se ejecuta cuando se actualiza la propiedad 'año'
     * Valida el año seleccionado y carga la información completa del año
     */
    public function updatedAño(){
        $this->validate([
            'año' => 'required'
        ]);

        // Carga la información completa del año incluyendo sus meses
        $this->yearInfo = Año::find($this->año);
    }

    /**
     * Prepara y ejecuta la exportación de datos de Helisa
     * Redirige a una ruta específica de exportación con los parámetros de filtro
     * @return \Illuminate\Http\RedirectResponse
     */
    public function exportar(){
        // Prepara el parámetro de comercial: usa el seleccionado o 'none' si no hay selección
        if ($this->comercial){
            $comercial = $this->comercial;
        }else {
            $comercial = 'none';
        }

        // Prepara el parámetro de centro: usa el ingresado o 'none' si está vacío
        if ($this->centro){
            $centro = $this->centro;
        }else{
            $centro = 'none';
        }

        // Redirige a la ruta de exportación con los parámetros de filtro
        return redirect()->route('export-helisa', ['comercial' => $comercial, 'centro' => $centro]);
    }
}

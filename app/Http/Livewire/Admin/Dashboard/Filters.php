<?php

namespace App\Http\Livewire\Admin\Dashboard;

use Livewire\Component;
use App\Models\Año;
use App\Models\Mes;
use App\Models\User;

/**
 * Componente Livewire para gestionar los filtros del dashboard administrativo
 *
 * Este componente maneja la lógica de filtrado por año, mes y comercial
 * en el dashboard del administrador, emitiendo eventos cuando cambian los filtros
 */
class Filters extends Component
{
    /*
    |--------------------------------------------------------------------------
    | Filters Component
    |--------------------------------------------------------------------------
    | Este componente Livewire es responsable de gestionar las acciones
    | de la vista admin/dashboard/filters y coordinar los filtros del dashboard.
    */

    // Propiedades públicas para los filtros seleccionados
    public $mes;        // ID del mes seleccionado
    public $comercial;  // ID del comercial seleccionado
    public $año;        // ID del año seleccionado

    // Arrays que almacenan las opciones disponibles para cada filtro
    public $StdMes = [];        // Lista de meses disponibles según el año seleccionado
    public $StdComercial = [];  // Lista de comerciales disponibles
    public $StdAño = [];        // Lista de años disponibles

    /**
     * Renderiza la vista del componente
     *
     * @return \Illuminate\View\View
     */
    public function render()
    {
        return view('livewire.admin.dashboard.filters');
    }

    /**
     * Método que se ejecuta al inicializar el componente
     *
     * Carga la lista inicial de años disponibles y ejecuta getFilters()
     * para inicializar el estado de los filtros
     */
    public function mount(){
        // Obtiene todos los años disponibles de la base de datos
        $this->StdAño = Año::select('id', 'description')->get();

        // Inicializa los filtros dependientes
        $this->getFilters();
    }

    /**
     * Listener que se ejecuta cuando cambia el año seleccionado
     *
     * Actualiza los filtros dependientes (meses y comerciales) y
     * emite señales a otros componentes
     */
    public function updatedAño(){
        $this->getFilters();  // Actualiza meses y comerciales disponibles
        $this->signals();     // Notifica el cambio a otros componentes
    }

    /**
     * Listener que se ejecuta cuando cambia el mes seleccionado
     *
     * Emite señales a otros componentes del dashboard
     */
    public function updatedMes(){
        $this->signals();
    }

    /**
     * Listener que se ejecuta cuando cambia el comercial seleccionado
     *
     * Emite señales a otros componentes del dashboard
     */
    public function updatedComercial(){
        $this->signals();
    }

    /**
     * Emite eventos Livewire con los filtros actuales a otros componentes
     *
     * Envía la descripción del año (no el ID) junto con el mes y comercial
     * a los componentes Block1 y Block2 para que actualicen sus datos
     */
    public function signals (){
        // Solo emite señales si hay un año seleccionado
        if ($this->año){
            // Obtiene la descripción del año seleccionado (no el ID)
            $año_desc = Año::select('description')->where('id', $this->año)->first();

            // Emite eventos a los componentes Block1 y Block2 con los filtros actuales
            // Nota: el año se envía como descripción, mes y comercial como IDs
            $this->emit('Block1', [
                'año' => $año_desc->description,
                'mes' => $this->mes,
                'comercial' => $this->comercial
            ]);
            $this->emit('Block2', [
                'año' => $año_desc->description,
                'mes' => $this->mes,
                'comercial' => $this->comercial
            ]);
        }
    }

    /**
     * Actualiza las opciones disponibles de los filtros dependientes
     *
     * Si hay un año seleccionado, carga los meses de ese año y la lista de comerciales.
     * Si no hay año seleccionado, limpia los filtros dependientes y resetea los componentes.
     */
    public function getFilters (){
        if ($this->año){
            // Si hay año seleccionado, carga los meses correspondientes a ese año
            $this->StdMes = Mes::select('id', 'description')
                                ->where('ano_id', $this->año)
                                ->get();

            // Carga todos los usuarios con rol de comercial (rol = 2)
            $this->StdComercial = User::select('id', 'name')
                                      ->where('rol', 2)
                                      ->get();
        } else {
            // Si no hay año seleccionado, limpia las opciones dependientes
            $this->StdMes = [];
            $this->StdComercial = [];

            // Emite eventos vacíos para resetear los componentes Block1 y Block2
            $this->emit('Block1');
            $this->emit('Block2');
        }
    }
}

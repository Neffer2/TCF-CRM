<?php

namespace App\Http\Livewire\Admin\Presupuestos;

use Livewire\Component;
use App\Models\User;
use App\Models\Presupuesto;
use App\Models\Año;

/**
 * Componente Livewire para gestionar presupuestos
 * Permite filtrar presupuestos por comercial y año
 */
class Presupuestos extends Component
{
    // Propiedades del modelo - valores seleccionados por el usuario
    public $comercial; // ID del comercial seleccionado
    public $año; // ID del año seleccionado

    // Variables útiles para almacenar datos
    public $comerciales; // Lista de comerciales disponibles
    public $presupuestos = []; // Presupuestos filtrados
    public $años = []; // Lista de años disponibles

    // Listeners para eventos de Livewire
    protected $listeners = ['refresh' => 'mount'];

    /**
     * Renderiza la vista del componente
     * @return \Illuminate\View\View
     */
    public function render()
    {
        return view('livewire.admin.presupuestos.presupuestos');
    }

    /**
     * Se ejecuta cuando cambia el valor del comercial seleccionado
     * Actualiza la lista de presupuestos según el comercial elegido
     */
    public function updatedComercial (){
        $this->getPresupuesto($this->comercial);
    }

    /**
     * Se ejecuta cuando cambia el valor del año seleccionado
     * Actualiza la lista de presupuestos según el año elegido
     */
    public function updatedAño (){
        $this->getPresupuesto(null, $this->año);
    }

    /**
     * Obtiene los presupuestos filtrados por comercial y/o año
     * @param int|null $comercial ID del comercial a filtrar
     * @param int|null $año ID del año a filtrar
     * @return \Illuminate\Database\Eloquent\Collection
     */
    public function getPresupuesto ($comercial = null, $año = null){

        // Si se han seleccionado tanto comercial como año, filtra por ambos
        if ($this->comercial && $this->año){
            return $this->presupuestos = Presupuesto::select('ano_id', 'mes_id', 'valor', 'id_user')
                                                ->where('id_user', $this->comercial)
                                                ->where('ano_id', $this->año)
                                                ->get();
        }

        // Si solo se ha seleccionado comercial, filtra solo por comercial
        if ($comercial){
            return $this->presupuestos = Presupuesto::select('mes_id', 'valor', 'id_user')->where('id_user', $this->comercial)->get();
        }
    }

    /**
     * Método de inicialización del componente
     * Se ejecuta cuando el componente es montado
     */
    public function mount (){
        $this->getComerciales();
        $this->getAños();
    }

    /**
     * Obtiene la lista de comerciales disponibles
     * Solo usuarios con rol = 2 (comerciales)
     */
    public function getComerciales(){
        $this->comerciales = User::select('id', 'name')->where('rol', 2)->get();
    }

    /**
     * Obtiene la lista de años disponibles
     */
    public function getAños (){
        $this->años = Año::select('id', 'description')->get();
    }
}

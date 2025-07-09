<?php

namespace App\Http\Livewire\Productor\Terceros;

use Livewire\Component;
use App\Models\Tercero;
use App\Models\EstadoTercero;
use Livewire\WithPagination;

class Personal extends Component
{
    use WithPagination; // Habilita la paginación de Livewire

    protected $paginationTheme = 'bootstrap'; // Usa el tema bootstrap para la paginación

    // Modelos para los filtros de búsqueda
    public $cedula, $nombre, $estado, $telefono;

    // Variable para almacenar los estados posibles de los terceros
    public $estados;

    // Listener para recargar la tabla cuando se registre un tercero
    protected $listeners = ['terceroRegistrado' => 'render'];

    // Renderiza la vista principal del componente
    public function render()
    {
        $filtros = []; // Arreglo de filtros para la consulta

        // Si se selecciona un estado, lo agrega a los filtros
        if ($this->estado){
            array_push($filtros, ['estado', '=', $this->estado]);
        }

        // Si se ingresa una cédula, la agrega a los filtros
        if ($this->cedula){
            array_push($filtros, ['cedula', 'like', '%' . $this->cedula . '%']);
        }

        // Si se ingresa un nombre, lo agrega a los filtros
        if ($this->nombre){
            array_push($filtros, ['nombre', 'like', '%' . $this->nombre . '%']);
        }

        // Si se ingresa un teléfono, lo agrega a los filtros
        if ($this->telefono){
            array_push($filtros, ['telefono', 'like', '%' . $this->telefono . '%']);
        }

        // Consulta los terceros aplicando los filtros y paginando los resultados
        $terceros = Tercero::where($filtros)->paginate(15);

        // Retorna la vista con los terceros filtrados y paginados
        return view('livewire.productor.terceros.personal', ['terceros' => $terceros]);
    }

    // Método que se ejecuta al montar el componente, carga los estados posibles
    public function mount(){
        $this->estados = EstadoTercero::select('id', 'descripcion')->get();
    }
}

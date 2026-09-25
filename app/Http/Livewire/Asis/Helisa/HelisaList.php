<?php

namespace App\Http\Livewire\Asis\Helisa;
 
use Livewire\Component;
use App\Models\Helisa;
use App\Models\User;
use App\Models\Año;
use App\Models\Mes;
use App\Models\Cuenta;
use App\Models\Asistente;
use Illuminate\Support\Facades\Auth;
use Livewire\WithPagination;

class HelisaList extends Component 
{
    use WithPagination;
    protected $paginationTheme = 'bootstrap';

    //USEFUL VARS
    public $comerciales = [], $cuentas = [], $años = [], $meses = [];

    public function render()
    {
        // Paginado y con sus relaciones: antes traía todos los registros del comercial (cientos) y montaba
        // un componente de edición por cada fila, lo que colgaba la página.
        $comercialId = optional(Asistente::where('asistente_id', Auth::id())->first())->comercial_id;
        $list = Helisa::with(['comercial_user', 'cuenta'])->where('comercial', $comercialId)
            ->orderByDesc('fecha')->orderByDesc('id')->paginate(25);
        return view('livewire.asis.helisa.helisa-list', ['list' => $list]);
    }

    public function getComerciales(){
        $this->comerciales = User::select('id', 'name')->where('rol', 2)->get();
    }

    public function getAños(){
        $this->años = Año::select('id','description')->get();
    }
 
    public function getMeses(){
        $this->meses = Mes::select('id','description')->where('id', '<', 13)->get();
    }

    public function getCuentas(){
        $this->cuentas = Cuenta::select('id', 'description')->get();
    }

    public function mount(){
        $this->getComerciales(); 
        $this->getAños();
        $this->getMeses();
        $this->getCuentas(); 
    }
}

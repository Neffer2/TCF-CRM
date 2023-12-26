<?php

namespace App\Http\Livewire\Admin\Generales;

use Livewire\Component;
use Livewire\WithPagination;
use App\Models\Base_comercial;
use App\Models\User;

class BaseComercialGeneral extends Component
{
    use WithPagination; 
    protected $paginationTheme = 'bootstrap';

    // Models
    public $comercial, $centro;

    // Useful vars
    public $comerciales = [], $filtros;

    public function render()
    {
        $this->filtros = [];
        
        if($this->centro){
            array_push($this->filtros, ['cod_cc', 'LIKE', "%$this->centro%"]);
        }
        
        if($this->comercial){
            array_push($this->filtros, ['id_user', $this->comercial]);
        }

        $baseComerciales = Base_comercial::where($this->filtros)->paginate(15);
        return view('livewire.admin.generales.base-comercial-general', ['baseComerciales' => $baseComerciales]);
    }

    public function mount(){
        $this->getComerciales();
    }

    public function getComerciales(){ 
        $this->comerciales = User::where('rol', 2)->get();
    }
}
 
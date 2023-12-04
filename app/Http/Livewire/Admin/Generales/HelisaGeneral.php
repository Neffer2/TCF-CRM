<?php

namespace App\Http\Livewire\Admin\Generales;

use Livewire\Component;
use App\Models\Helisa;
use App\Models\User;
use Livewire\WithPagination;
use Maatwebsite\Excel\Facades\Excel;
use Maatwebsite\Excel\Concerns\FromCollection;
 
class HelisaGeneral extends Component
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
            array_push($this->filtros, ['centro', 'LIKE', "%$this->centro%"]);
        }

        if($this->comercial){
            array_push($this->filtros, ['comercial', $this->comercial]);
        }

        $registros_helisa = Helisa::where($this->filtros)->paginate(15);
        return view('livewire.admin.generales.helisa-general', ['registros_helisa' => $registros_helisa]);
    } 

    public function mount (){ 
        $this->getComerciales();
    } 

    public function getComerciales(){ 
        $this->comerciales = User::where('rol', 2)->get();
    }

    public function exportar(){
        if ($this->comercial){
            $comercial = $this->comercial; 
        }else {
            $comercial = 'none';
        }

        if ($this->centro){
            $centro = $this->centro;
        }else{
            $centro = 'none';
        }
 
        return redirect()->route('export-helisa', ['comercial' => $comercial, 'centro' => $centro]); 
    }
}

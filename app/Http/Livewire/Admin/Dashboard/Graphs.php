<?php

namespace App\Http\Livewire\Admin\Dashboard;

use Livewire\Component;
use App\Models\Base_comercial;
use Illuminate\Support\Facades\DB;

class Graphs extends Component
{
    //Models
        //Graph1
        public $labelsGraph1 = [];
        public $dataGraph1 = [];

    protected $listeners = ['Graphs' => 'graph1'];

    public function render()
    {
        return view('livewire.admin.dashboard.graphs');
    }
 
    public function graph1 ($filters){
        $estados_count = DB::table('base_comerciales')
                ->select('id_estado', DB::raw('COUNT(id_estado) AS count_estados'))
                ->groupBy('id_estado')
                ->havingRaw('COUNT(id_estado) > 1')
                ->get();
             
        foreach ($estados_count as $estado) {
            array_push($this->dataGraph1, $estado->count_estados);
        }
    }
}   

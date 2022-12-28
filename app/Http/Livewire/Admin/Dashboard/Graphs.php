<?php

namespace App\Http\Livewire\Admin\Dashboard;

use Livewire\Component;
use App\Models\Base_comercial;
use Illuminate\Support\Facades\DB;

class Graphs extends Component
{
    //Models

    protected $listeners = ['Graphs' => 'graph1'];

    public function render()
    {
        return view('livewire.admin.dashboard.graphs');
    }

    public function graph1 ($filters){
        $orders = DB::table('base_comerciales')
                ->select('id_estado', DB::raw('COUNT(id_estado)'))
                ->groupBy('id_estado')
                ->havingRaw('COUNT(id_estado) > 1')
                ->get();
        dd($orders);
    }
}   

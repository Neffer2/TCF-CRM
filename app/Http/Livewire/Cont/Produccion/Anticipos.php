<?php

namespace App\Http\Livewire\Cont\Produccion;

use Livewire\Component;
use App\Models\EstadoOrdenesCompra;   
use App\Models\OrdenCompra;
use Livewire\WithPagination;  

class Anticipos extends Component
{   
    // Models
    public $fecha = 'desc', $estado = 1, $cod_cc;

    use WithPagination;  
    protected $paginationTheme = 'bootstrap';   

    public function render()
    {
        /*  ESTADOS
            * 1: Pendiente.
            * 2: Pagado.
        */
        if ($this->estado == 1){ 
            if ($this->cod_cc){
                // WhereHas busca dentro de la colleccion el campo 'proveedor'
                $ordenes = OrdenCompra::with('proveedor')->with('presupuesto') 
                    ->whereHas('proveedor', function ($proveedor){ 
                        $proveedor->where('anticipo', '>', 0);
                    })
                    ->whereHas('presupuesto', function ($presto) { 
                        $presto->where('cod_cc', 'LIKE', "%$this->cod_cc%");
                    })
                ->where('estado_id', 1)->whereNull('archivo_comprobante_pago')->whereNull('cod_causal')->orderBy('created_at', $this->fecha)->paginate(15); 
            }else {
                $ordenes = OrdenCompra::with('proveedor')
                    ->whereHas('proveedor', function ($proveedor){ 
                        $proveedor->where('anticipo', '>', 0);
                    })
                ->where('estado_id', 1)->whereNull('archivo_comprobante_pago')->whereNull('cod_causal')->orderBy('created_at', $this->fecha)->paginate(15); 
            }
        }else {
            // if ($this->cod_cc){
            //     $ordenes = OrdenCompra::with('proveedor')->with('presupuesto')
            //         ->whereHas('proveedor', function ($proveedor){ 
            //             $proveedor->where('anticipo', '>', 0);
            //         })
            //         ->whereHas('presupuesto', function ($presto) { 
            //             $presto->where('cod_cc', 'LIKE', "%$this->cod_cc%");
            //         })
            //     ->where('estado_id', 1)->where('archivo_comprobante_pago', '<>', 'NULL')->orderBy('created_at', $this->fecha)->paginate(15);
            // }else{
            //     $ordenes = OrdenCompra::with('proveedor')
            //         ->whereHas('proveedor', function ($proveedor){ 
            //             $proveedor->where('anticipo', '>', 0);
            //         })
            //     ->where('estado_id', 1)->where('archivo_comprobante_pago', '<>', 'NULL')->orderBy('created_at', $this->fecha)->paginate(15);
            // }
        }

        return view('livewire.cont.produccion.anticipos', ['ordenes' => $ordenes]);
    }
}
 
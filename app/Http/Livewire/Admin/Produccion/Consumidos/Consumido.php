<?php

namespace App\Http\Livewire\Admin\Produccion\Consumidos;

use App\Models\ItemPresupuesto;
use Livewire\Component;
use App\Models\PresupuestoProyecto;
use App\Models\Proveedor;

class Consumido extends Component
{
    //public temporales
    public $cod;
    public $concepto;
    public $cantidad;
    public $dia;
    public $otros;
    public $descripcion;
    public $valor_unitario = 0;
    public $valor_total = 0;
    public $valor_total_cliente = 0;
    public $proveedor = [];
    public $utilidad;
    public $tiempoFactura;
    public $notas;
    public array $consumosManuales = [];
    public $mes;
    public $dias;
    public $ciudad;


    // Useful vars
    public $presupuesto, $proveedores = [];

    // filled
    public $presupuesto_id;

    public function render()
    {
        //temporal
        $this->items = ItemPresupuesto::where('presupuesto_id', $this->presupuesto_id)
            ->orderBy('orden')
            ->get();


        return view('livewire.admin.produccion.consumidos.consumido');
    }

    public function mount(){
        $this->presupuesto = PresupuestoProyecto::find($this->presupuesto_id);
        $this->proveedores = Proveedor::select('id', 'tercero')->get();

        foreach ($this->presupuesto->presupuestoItems as $item) {
            // Carga previa desde la base de datos (requiere migrar la columna 'consumo_manual' en la tabla 'presupuesto_items')
            $this->consumosManuales[$item->id] = $item->consumo_manual ?? 0;
        }
    }

    public function getDataEdit($id){
        $this->selected_item = [];
        foreach ($this->items as $item) {
            if ($item->id == $id) {
                $this->selected_item = $item;
                break;
            }
        }
        $this->cod = $this->selected_item->cod;
        $this->presupuesto_id = $this->selected_item->presupuesto_id;
        $this->cantidad = $this->selected_item->cantidad;
        $this->dia = $this->selected_item->dia;
        $this->otros = $this->selected_item->otros;
        $this->descripcion = $this->selected_item->descripcion;
        $this->valor_unitario = $this->selected_item->v_unitario;
        $this->valor_total = $this->selected_item->v_total;
        $this->valor_total_cliente = $this->selected_item->v_total_cliente;
        $this->proveedor = (@unserialize($this->selected_item->proveedor)) ? @unserialize($this->selected_item->proveedor) : [$this->selected_item->proveedor];
        $this->utilidad = $this->selected_item->margen_utilidad;
        $this->mes = $this->selected_item->mes;
        $this->dias = $this->selected_item->dias;
        $this->ciudad = $this->selected_item->ciudad;
    }

    public function guardarConsumoManual($itemId)
    {
        $item = ItemPresupuesto::with('consumidos.OrdenCompra')->find($itemId);

        if (!$item) {
            return;
        }

        // 1. Recalcular consumos por OCs activas
        $acumTotalOc = 0;
        $contCantOc = 0;

        foreach ($item->consumidos as $consumido) {
            if ($consumido->OrdenCompra && $consumido->OrdenCompra->estado_id != 6) {
                $acumTotalOc += $consumido->vtotal_oc;
                $contCantOc += $consumido->cant_oc;
            }
        }

        // 2. REGLA 1: Verificar si el ítem ya consumió la totalidad por OCs (Fila Roja)
        $estaAgotado = count($item->consumidos) > 0 && (
            ($item->cantidad - $contCantOc <= 0) || 
            ($item->v_total - $acumTotalOc <= 0)
        );

        if ($estaAgotado) {
            // Cancelar cambio y resetear valor a 0
            $this->consumosManuales[$itemId] = 0;
            $item->update(['consumo_manual' => 0]);
            
            $this->dispatchBrowserEvent('alert', [
                'type' => 'error', 
                'message' => 'No es posible modificar este ítem porque ya se consumió en su totalidad por Órdenes de Compra.'
            ]);
            return;
        }

        // 3. REGLA 2: El valor ingresado no puede superar el disponible restante
        $maximoPermitido = max(0, $item->v_total - $acumTotalOc);
        $montoManualIngresado = (float) ($this->consumosManuales[$itemId] ?? 0);

        if ($montoManualIngresado > $maximoPermitido) {
            // Ajustar automáticamente al máximo permitido si el usuario intenta excederlo
            $montoManualIngresado = $maximoPermitido;
            $this->consumosManuales[$itemId] = $maximoPermitido;

            $this->dispatchBrowserEvent('alert', [
                'type' => 'warning', 
                'message' => 'El monto ingresado superaba el saldo disponible. Se ajustó automáticamente al tope máximo de $' . number_format($maximoPermitido)
            ]);
        }

        // Evitar números negativos
        if ($montoManualIngresado < 0) {
            $montoManualIngresado = 0;
            $this->consumosManuales[$itemId] = 0;
        }

        // 4. Guardar en Base de Datos
        $item->update([
            'consumo_manual' => $montoManualIngresado
        ]);
    }
}

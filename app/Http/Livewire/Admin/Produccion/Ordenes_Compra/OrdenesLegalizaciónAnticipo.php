<?php

namespace App\Http\Livewire\Admin\Produccion\Ordenes_Compra;

use App\Models\OrdenCompra;
use Livewire\Component;
use Livewire\WithFileUploads;

class OrdenesLegalizaciónAnticipo extends Component {

    use WithFileUploads;

    public $anticipo_id;
    public $ordenAnticipo;

    // Campos a actualizar/legalizar
    public $monto_gastado;
    public $diferencia = 0;
    public $observaciones;
    public $soporte_archivo;

    public $successMessage = null;
    public $errorMessage = null;

    protected $listeners = ['cargarAnticipoParaLegalizar'];


    public function mount($orden_id = null){

        if($orden_id) {
            $this->cargarAnticipo($orden_id);
        }

    }

    public function cargarAnticipo($id){
        $this->anticipo_id = $id;

        // Cargamos la orden incluyendo la relación ordenItems
        $this->ordenAnticipo = OrdenCompra::with([
            'naturalInfo.tercero',
            'proveedor',
            'presupuesto',
            'ordenItems'
        ])->find($id);

        if ($this->ordenAnticipo) {
            // Mapeamos el valor total sumando la columna 'vtotal_oc' de sus oc_items
            $montoTotalAnticipo = $this->ordenAnticipo->ordenItems->sum('vtotal_oc');

            // Inicializamos el monto gastado con el total de la OC por defecto
            $this->monto_gastado = $montoTotalAnticipo;
            $this->calcularDiferencia();
        }
    }

    public function updatedMontoGastado(){
        $this->calcularDiferencia();
    }

    public function calcularDiferencia(){
        if ($this->ordenAnticipo) {
            // Suma dinámica de los ítems de la OC
            $montoOriginal = $this->ordenAnticipo->ordenItems->sum('vtotal_oc');

            // Diferencia: Lo legalizado/gastado menos el anticipo entregado
            $this->diferencia = (float)$this->monto_gastado - (float)$montoOriginal;
        }
    }

    public function resetSeleccion(){
        $this->reset(['anticipo_id', 'ordenAnticipo', 'monto_gastado', 'diferencia', 'observaciones', 'soporte_archivo']);
    }

    public function guardarLegalizacion(){

        $this-> reset(['successMessage', 'errorMessage']);

        $this->validate([
            'monto_gastado' => 'required|numeric|min:0',
            'observaciones' => 'required',
        ]);

        if (!$this->ordenAnticipo){
            return;
        }

        try {
            $rutaArchivo = $this->ordenAnticipo->archivo_cot; // Mantiene el actual si no sube uno nuevo

            if ($this->soporte_archivo) {
                // Guardar en la carpeta pública del storage como lo hace tu PdfService
                // Genera una ruta similar a: public/legalizaciones/leg_105_16890000.pdf
                $nombreArchivo = 'leg_' . $this->ordenAnticipo->id . '_' . time() . '.' . $this->soporte_archivo->getClientOriginalExtension();
                $rutaArchivo = $this->soporte_archivo->storeAs('public/legalizaciones', $nombreArchivo);
            }

            // 2. Actualización directa en la tabla orden_compras (Igual que en el método cambioEstado)
            $this->ordenAnticipo->archivo_cot = $rutaArchivo;
            //$this->ordenAnticipo-> = $this->observaciones; // O la columna de observación asignada

            // Asignación de estado (ejemplo: Estado 14 o el correspondiente a Legalización/Remisión)
            $this->ordenAnticipo->estado_id = 14;

            // Guardar cambios en la BD
            $this->ordenAnticipo->update();

            $this->successMessage = 'Legalización de la orden #' . $this->ordenAnticipo->id . ' guardada con éxito.';
            $this->resetSeleccion();

        }
        catch (\Exception $e){
            $this->errorMessage = 'Ocurrió un error al guardar: ' .$e->getMessage();
        }
    }

    public function render(){

        $anticiposPendientes = collect();
        if (!$this->ordenAnticipo) {
            $anticiposPendientes = OrdenCompra::where('tipo_oc', 4)
            ->where('estado_id', 1)
            ->latest()
                ->take(10)
                ->get();
        }
        return view('livewire.admin.produccion.ordenes-compra.orden-compra-legalizacion', [
            'anticiposPendientes' => $anticiposPendientes
        ]);
    }
}

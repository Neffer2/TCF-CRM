<?php

namespace App\Http\Livewire\Admin\Produccion\Ordenes_Compra;

use App\Models\OrdenCompra;
use App\Models\User;
use Illuminate\Support\Facades\DB;
use Livewire\Component;
use Livewire\WithFileUploads;
use App\Models\OcItem;

class OrdenesLegalizaciónAnticipo extends Component {

    use WithFileUploads;

    public $anticipo_id;
    public $ordenAnticipo;

    public $itemsLegalizacion = [];
    public $soportesItems = [];

    // Campos a actualizar/legalizar
    public $totalAnticipado = 0;
    public $totalGastado = 0;
    public $diferenciaGlobal = 0;

    public $successMessage = null;
    public $errorMessage = null;

    public $monto_gastado = 0;
    public $observaciones = '';
    public $soporte_archivo = null;

    public $diferencia = 0;

    protected $listeners = ['cargarAnticipoParaLegalizar'];
    public $searchCC = '';

    protected function rulesGuardarItem($itemId)
    {
        return [
            "itemsLegalizacion.{$itemId}.monto_gastado" => 'required|numeric|min:0',
            "itemsLegalizacion.{$itemId}.observacion" => 'required|string|min:3|max:500',
        ];
    }

    protected $messages = [
        'itemsLegalizacion.*.monto_gastado.required' => 'El monto gastado es obligatorio.',
        'itemsLegalizacion.*.monto_gastado.numeric' => 'El monto debe ser un número válido.',
        'itemsLegalizacion.*.monto_gastado.min' => 'El monto no puede ser negativo.',
        'itemsLegalizacion.*.observacion.required' => 'Debes ingresar una observación o detalle del gasto.',
        'itemsLegalizacion.*.observacion.min' => 'La observación debe tener al menos 3 caracteres.',
        'soportesItems.*.required' => 'Debes adjuntar el archivo de soporte (PDF o Imagen).',
        'soportesItems.*.mimes' => 'El soporte debe ser un archivo de tipo: pdf, png, jpg, jpeg.',
        'soportesItems.*.max' => 'El soporte no puede pesar más de 5MB.',
    ];

    public function mount($orden_id = null){

        if($orden_id) {
            $this->cargarAnticipo($orden_id);
        }

    }
    public function cargarAnticipo($id){
        $this->reset(['successMessage', 'errorMessage']);
        $this->anticipo_id = $id;

        $this->ordenAnticipo = OrdenCompra::with([
            'naturalInfo.tercero',
            'presupuesto',
            'ordenItems'
        ])->find($id);

        if ($this->ordenAnticipo) {
            $this->itemsLegalizacion = [];
            $this->totalAnticipado = 0;

            foreach ($this->ordenAnticipo->ordenItems as $item) {
                $this->totalAnticipado += $item->vtotal_oc;

                // Cargamos el estado actual de legalización de cada ítem
                $this->itemsLegalizacion[$item->id] = [
                    'id' => $item->id,
                    'desc_oc' => $item->desc_oc,
                    'cant_oc' => $item->cant_oc,
                    'vtotal_oc' => $item->vtotal_oc,
                    // Si ya tenían un valor previo guardado (o por defecto el valor del ítem)
                    'monto_gastado' => $item->monto_legalizado ?? '',
                    'observacion' => $item->observacion_legalizacion ?? '',
                    'archivo_soporte' => $item->archivo_soporte ?? null,
                    'estado_item' => $item->estado_legalizacion ?? 'Pendiente',
                ];
            }

            $this->calcularTotalesGlobales();
        }
    }
    public function calcularDiferencia(){
        if ($this->ordenAnticipo) {
            // Suma dinámica de los ítems de la OC
            $montoOriginal = $this->ordenAnticipo->ordenItems->sum('vtotal_oc');

            // Diferencia: Lo legalizado/gastado menos el anticipo entregado
            $this->diferencia = (float)$this->monto_gastado - (float)$montoOriginal;
        }
    }

    public function updatedItemsLegalizacion(){
        $this->calcularTotalesGlobales();
    }

    public function calcularTotalesGlobales()
    {
        $this->totalGastado = 0;
        foreach ($this->itemsLegalizacion as $item) {
            // Convertimos cualquier coma a punto para que PHP haga la suma exacta con decimales
            $montoLimpio = str_replace(',', '.', $item['monto_gastado'] ?? 0);
            $this->totalGastado += (float)$montoLimpio;
        }

        $this->diferenciaGlobal = $this->totalGastado - $this->totalAnticipado;
    }

    public function resetSeleccion(){
        $this->reset(['anticipo_id', 'ordenAnticipo', 'monto_gastado', 'diferencia', 'observaciones', 'soporte_archivo']);
    }

    public function guardarLegalizacionItem($itemId)
    {
        $this->reset(['successMessage', 'errorMessage']);

        if (!isset($this->itemsLegalizacion[$itemId])) {
            return;
        }

        $itemData = $this->itemsLegalizacion[$itemId];

        $this->validate(
            $this->rulesGuardarItem($itemId),
            $this->messages
        );

        try {
            $ocItem = OcItem::find($itemId);

            if (!$ocItem) {
                $this->errorMessage = 'No se encontró el ítem especificado.';
                return;
            }

            // 2. Control de seguridad: Si ya está legalizado, impedir re-edición
            if ($ocItem->estado_legalizacion === 'Legalizado') {
                $this->errorMessage = 'Este ítem ya ha sido legalizado previamente y no puede ser modificado.';
                return;
            }

            // 3. Validar Soporte: Requerido si el ítem no tiene soporte guardado en BD
            $tieneSoportePrevio = !empty($ocItem->archivo_soporte);
            $tieneSoporteNuevo = isset($this->soportesItems[$itemId]);

            if (!$tieneSoportePrevio && !$tieneSoporteNuevo) {
                $this->validate([
                    "soportesItems.{$itemId}" => 'required|mimes:pdf,png,jpg,jpeg|max:5120',
                ], $this->messages);
            }

            // Manejo y guardado del archivo de soporte si viene uno nuevo
            $rutaSoporte = $ocItem->archivo_soporte;

            if ($tieneSoporteNuevo) {
                $this->validate([
                    "soportesItems.{$itemId}" => 'mimes:pdf,png,jpg,jpeg|max:5120',
                ], $this->messages);

                $file = $this->soportesItems[$itemId];
                $nombre = 'leg_item_' . $itemId . '_' . time() . '.' . $file->getClientOriginalExtension();
                $rutaSoporte = $file->storeAs('public/legalizaciones/items', $nombre);
            }

            $montoFinal = (float) str_replace(',', '.', $this->itemsLegalizacion[$itemId]['monto_gastado']);

            // 4. Guardar datos en DB
            $ocItem->monto_legalizado = $montoFinal;
            $ocItem->observacion_legalizacion = $itemData['observacion'];
            $ocItem->archivo_soporte = $rutaSoporte;
            $ocItem->estado_legalizacion = 'Legalizado';
            $ocItem->save();

            // 5. Actualizar estado local en el componente
            $this->itemsLegalizacion[$itemId]['monto_gastado'] = $ocItem->monto_legalizado;
            $this->itemsLegalizacion[$itemId]['observacion'] = $ocItem->observacion_legalizacion;
            $this->itemsLegalizacion[$itemId]['archivo_soporte'] = $ocItem->archivo_soporte;
            $this->itemsLegalizacion[$itemId]['estado_item'] = 'Legalizado';

            unset($this->soportesItems[$itemId]);

            $this->calcularTotalesGlobales();

            $this->successMessage = 'Ítem "' . $ocItem->desc_oc . '" legalizado con éxito.';

        } catch (\Illuminate\Validation\ValidationException $e) {
            throw $e;
        } catch (\Exception $e) {
            $this->errorMessage = 'Error al guardar el ítem: ' . $e->getMessage();
        }
    }

    public function render(){
        if (!$this->ordenAnticipo) {
            $query = OrdenCompra::with([
                'presupuesto.productor_info',
                'ordenItems'
            ])
                ->where('tipo_oc', 4)
                ->where('estado_id', 1);

            // Filtro dinámico por Centro de Costos
            if (!empty(trim($this->searchCC))) {
                $query->whereHas('presupuesto', function ($q) {
                    $q->where('cod_cc', 'like', '%' . trim($this->searchCC) . '%');
                });
            }

            $anticiposPendientes = $query->latest()
                ->take(10)
                ->get();
        } else {
            $anticiposPendientes = collect();
        }

        return view('livewire.admin.produccion.ordenes-compra.orden-compra-legalizacion', [
            'anticiposPendientes' => $anticiposPendientes
        ]);
    }
}

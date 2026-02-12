<?php

namespace App\Http\Livewire\Productor;

use Livewire\Component;
use Livewire\WithFileUploads;
use App\Models\OrdenCompra;
use App\Traits\Email;
use Illuminate\Support\Facades\Storage;

class Remision extends Component
{
    // Habilita la subida de archivos y el trait de envío de emails
    use WithFileUploads, Email;

    // Propiedades para el formulario
    public $remision, $observaciones;
    // Listener para ejecutar el método store cuando se emite 'store-signal'
    protected $listeners = ['store-signal' => 'store'];

    // Variable para almacenar la orden cargada
    public $storedOrden;

    // ID de la orden a trabajar
    public $orden;

    // Renderiza la vista asociada al componente
    public function render()
    {
        return view('livewire.productor.remision');
    }

    // Se ejecuta al montar el componente, carga los datos de la orden
    public function mount(){
        $this->getData();
    }

    // Guarda la remisión y la firma recibida como base64
    public function store($data){
        // Valida los campos requeridos
        $this->validate([
            'remision' => 'required|file|mimes:pdf|max:10000',
            'observaciones' => 'nullable|string'
        ]);

        // Busca la orden de compra
        $orden = OrdenCompra::find($this->orden);
        // Guarda el archivo PDF de la remisión
        $orden->archivo_remision = $this->remision->store('public/remisiones');
        // Define la ruta donde se guardará la firma
        $orden->archivo_firma = "public/firmas_produccion/$this->orden.png";
        // Guarda las observaciones
        $orden->observacion_remision = $this->observaciones;
        // Cambia el estado de la orden a "Revisión evidencias lider"
        $orden->estado_id = 10; //Revisión evidencias lider

        // Procesa la imagen de la firma recibida en base64
        $data_uri = $data;
        $encoded_image = explode(",", $data_uri)[1];
        $decoded_image = base64_decode($encoded_image);
        // Guarda la firma como archivo PNG en storage
        file_put_contents("storage/firmas_produccion/$this->orden.png", $decoded_image);

        // Actualiza la orden en la base de datos
        $orden->update();
        // $this->send($orden); // (opcional) Enviar notificación por email

        // Redirige con mensaje de éxito
        return redirect()->route('dashboard-productor')->with('success', 'Remisión firmada y enviada exitósamente.');
    }

    // Carga los datos de la orden seleccionada
    public function getData(){
        $this->storedOrden = OrdenCompra::find($this->orden);
    }

    // Valida el archivo de remisión cuando se actualiza
    public function updatedRemision(){
        $this->validate([
            'remision' => 'required|file|mimes:pdf|max:10000',
        ]);
    }

    // Valida las observaciones cuando se actualizan
    public function updatedObservaciones(){
        $this->validate([
            'observaciones' => 'nullable|string|max:1000'
        ]);
    }
}

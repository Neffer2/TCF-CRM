<?php

namespace App\Http\Livewire\Productor;

use Livewire\Component;
use Livewire\WithFileUploads; 
use App\Models\OrdenCompra;
use App\Traits\Email;
use Illuminate\Support\Facades\Storage;

class Remision extends Component
{
    use WithFileUploads, Email; 

    // Models
    public $remision, $observaciones;  
    protected $listeners = ['store-signal' => 'store'];

    // Useful vars
    public $storedOrden;

    // Filled
    public $orden; 

    public function render()
    {
        return view('livewire.productor.remision');
    }

    public function mount(){
        $this->getData();
    }
 
    public function store($data){
        $this->validate([
            'remision' => 'required|file|mimes:pdf|max:10000',
            'observaciones' => 'nullable|string'
        ]);  
 
        $orden = OrdenCompra::find($this->orden);
        $orden->archivo_remision = $this->remision->store('public/remisiones'); 
        $orden->archivo_firma = "public/firmas_produccion/$this->orden.png";
        $orden->observacion_remision = $this->observaciones; 
        $orden->estado_id = 4; //Recibido

        $data_uri = $data; 
        $encoded_image = explode(",", $data_uri)[1]; 
        $decoded_image = base64_decode($encoded_image);
        file_put_contents("storage/firmas_produccion/$this->orden.png", $decoded_image);

        $orden->update();
        // $this->send($orden);

        return redirect()->route('dashboard-productor')->with('success', 'Remisión firmada y enviada exitósamente.');
    }

    public function getData(){
        $this->storedOrden = OrdenCompra::find($this->orden);
    }

    public function updatedRemision(){
        $this->validate([
            'remision' => 'required|file|mimes:pdf|max:10000',
        ]);
    }

    public function updatedObservaciones(){
        $this->validate([
            'observaciones' => 'nullable|string|max:1000'
        ]);
    }
}

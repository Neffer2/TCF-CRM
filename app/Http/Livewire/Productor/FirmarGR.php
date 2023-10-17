<?php

namespace App\Http\Livewire\Productor;
 
use Livewire\Component;
use Livewire\WithFileUploads;
use App\Models\OrdenCompra;
use Illuminate\Support\Facades\Storage;

class FirmarGR extends Component 
{
    use WithFileUploads;

    // Models
    public $remision; 
    protected $listeners = ['store-signal' => 'store'];

    // Useful vars
    public $storedOrden;

    // Filled
    public $orden;

    public function render()
    { 
        return view('livewire.productor.firmar-g-r'); 
    }

    public function mount(){
        $this->getData();
    }

    public function store($data){
        $this->validate([
            'remision' => 'required|file|mimes:pdf,xls,xlsx|max:10240',
        ]);

        $orden = OrdenCompra::find($this->orden);
        $orden->archivo_remision = $this->remision->store('public/remisiones'); 
        $orden->archivo_firma = "public/firmas_produccion/$this->orden.png";
        $orden->estado_id = 5;

        $data_uri = $data;
        $encoded_image = explode(",", $data_uri)[1];
        $decoded_image = base64_decode($encoded_image);
        file_put_contents("storage/firmas_produccion/$this->orden.png", $decoded_image);

        $orden->update();

        return redirect()->route('dashboard-productor')->with('success', 'Good receive firmado y enviado.');
    }

    public function getData(){
        $this->storedOrden = OrdenCompra::find($this->orden);
    }
} 
  
<?php

namespace App\Http\Livewire\Productor;
 
use Livewire\Component;
use Livewire\WithFileUploads; 
use App\Models\OrdenCompra;
use App\Traits\Email;
use Illuminate\Support\Facades\Storage;
 
class FirmarGR extends Component 
{
    use WithFileUploads, Email;

    // Models
    public $remision, $gr, $email_prov;  
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
        $this->send();
        $this->getData();
    }

    public function store($data){
        $this->validate([
            'remision' => 'required|file|mimes:pdf,xls,xlsx|max:10240',
            'gr' => 'required|numeric'
        ]);

        $orden = OrdenCompra::find($this->orden);
        $orden->archivo_remision = $this->remision->store('public/remisiones'); 
        $orden->archivo_firma = "public/firmas_produccion/$this->orden.png";
        $orden->gr = $this->gr;
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
        $this->email_prov = $this->storedOrden->email_prov;
    }

    public function updatedGr(){
        $this->validate([
            'gr' => 'required|numeric'
        ]);
    }

    public function updatedEmailProv(){
        $this->validate([
            'email_prov' => 'required|email'
        ]);
    }
} 
  
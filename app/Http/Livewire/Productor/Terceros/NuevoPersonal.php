<?php

namespace App\Http\Livewire\Productor\Terceros;

use Livewire\Component;
use App\Models\Tercero;
use App\Models\EstadoTercero;
use App\Imports\TerceroImport;
use Livewire\WithFileUploads;
use Illuminate\Support\Facades\Auth;
use Maatwebsite\Excel\Facades\Excel;
use Dompdf\Dompdf;
use Illuminate\Support\Facades\View;

class NuevoPersonal extends Component
{
    use WithFileUploads;
    /*
        * This component is used to register new personal
        * If this component have a $tercero, it means that we are going to edit it
        * If this component have a $orden, it means that a third party employee is editing
    */

    // Models
    public $nombre, $apellido, $cedula, $correo, $telefono, $ciudad,
    $banco, $rut, $cert_bancaria, $firma, $terminos, $estado = 1, $terceroXlsx, $copia_cedula;

    // Useful vars
    public $estados, $ciudades, $deleteConfirm = false, $contrato;

    // Filled
    public $tercero, $orden;

    public function render()
    {
        return view('livewire.productor.terceros.nuevo-personal');
    }

    public function mount(){
        $this->ciudades = app('ciudades');
        $this->estados = EstadoTercero::all();

        if ($this->tercero){$this->fillForm();}
    }

    public function updatedterceroXlsx(){
        Excel::import(new TerceroImport, $this->terceroXlsx);
    }

    public function nuevoPersonal(){
        $this->validate([
            'nombre' => 'required|max:255',
            'apellido' => 'required|max:255',
            'cedula' => 'required|numeric|unique:terceros',
            'correo' => 'required|email|unique:terceros',
            'telefono' => 'required|numeric|unique:terceros',
            'ciudad' => 'required|string',
            // 'estado' => 'required|numeric|max:1',
        ]);

        $tercero = new Tercero();
        $tercero->nombre = $this->nombre;
        $tercero->apellido = $this->apellido;
        $tercero->cedula = trim($this->cedula);
        $tercero->correo = trim($this->correo);
        $tercero->telefono = trim($this->telefono);
        $tercero->ciudad = $this->ciudad;
        $tercero->estado = 1;

        if($this->banco){
            $this->validate(['banco' => 'string|max:255']);
            $tercero->banco = $this->banco;
        }

        if($this->rut){
            $this->validate(['rut' => 'file|mimes:pdf,xls,xlsx|max:10000']);
            $tercero->rut = $this->rut->store('public/ruts');
        }

        if($this->cert_bancaria){
            $this->validate(['cert_bancaria' => 'file|mimes:pdf,xls,xlsx|max:10000']);
            $tercero->cert_bancaria = $this->cert_bancaria->store('public/cert_bancarias');
        }

        $tercero->save();
        $this->emit('terceroRegistrado');

        $this->reset_fields([
            'nombre',
            'apellido',
            'cedula',
            'correo',
            'telefono',
            'ciudad',
            'estado',
            'banco',
            'rut',
            'cert_bancaria',
        ]);

        return redirect()->back();
    }

    public function actualizarTercero(){
        $this->validate([
            'nombre' => 'required|max:255',
            'apellido' => 'required|max:255',
            'cedula' => 'required|numeric',
            'correo' => 'required|email',
            'telefono' => 'required|numeric',
            'ciudad' => 'required|string',
            'estado' => 'required|numeric|max:1',
        ]);

        if (!Auth::check()){
            $this->validate([
                'banco' => 'required|string|max:255',
                'terminos' => 'required|accepted'
            ]);
        }

        $tercero = $this->tercero;
        $tercero->nombre = $this->nombre;
        $tercero->apellido = $this->apellido;
        $tercero->cedula = trim($this->cedula);
        $tercero->correo = trim($this->correo);
        $tercero->telefono = trim($this->telefono);
        $tercero->ciudad = $this->ciudad;
        $tercero->estado = trim($this->estado);

        if($this->banco){
            $this->validate(['banco' => 'string|max:255']);
            $tercero->banco = $this->banco;
        }

        if (!$tercero->copia_cedula && !Auth::check()){
            $this->validate(['copia_cedula' => 'required|file|mimes:pdf,xls,xlsx|max:10000']);
            $tercero->copia_cedula = $this->copia_cedula->store('public/copia_cedula');
        }elseif($this->copia_cedula){
            $this->validate(['copia_cedula' => 'file|mimes:pdf,xls,xlsx|max:10000']);
        }

        if (!$tercero->cert_bancaria && !Auth::check()){
            $this->validate(['cert_bancaria' => 'required|file|mimes:pdf,xls,xlsx|max:10000']);
            $tercero->cert_bancaria = $this->cert_bancaria->store('public/cert_bancarias');
        }elseif($this->cert_bancaria){
            $this->validate(['cert_bancaria' => 'file|mimes:pdf,xls,xlsx|max:10000']);
        }

        if (!$tercero->rut && !Auth::check()){
            $this->validate(['rut' => 'required|file|mimes:pdf,xls,xlsx|max:10000']);
            $tercero->rut = $this->rut->store('public/ruts');
        }elseif($this->rut){
            $this->validate(['rut' => 'file|mimes:pdf,xls,xlsx|max:10000']);
        }

        if (!Auth::check()){
            $this->orden->naturalInfo->terminos = $this->terminos;
            $this->orden->naturalInfo->update();
            $this->orden->estado_id = 2;
            $this->orden->update();
        }

        $tercero->update();
        $this->emit('terceroRegistrado');

        $this->reset_fields([
            'nombre',
            'apellido',
            'cedula',
            'correo',
            'telefono',
            'ciudad',
            'estado',
            'banco',
            'rut',
            'cert_bancaria',
            'terminos'
        ]);

        if (!Auth::check()){
            return redirect()->route('consulta-terceros');
        }

        return redirect()->route('personal')->with('success', 'Cambios guardados con éxito.');
    }

    public function generarContrato(){
        $this->validate([
            'nombre' => 'required|max:255',
            'apellido' => 'required|max:255',
            'cedula' => 'required|numeric',
            'correo' => 'required|email',
            'telefono' => 'required|numeric',
            'ciudad' => 'required|string',
            'estado' => 'required|numeric|max:1',
            'banco' => 'required|string|max:255',
        ]);

        if (!$this->copia_cedula && !$this->tercero->copia_cedula && !Auth::check()){
            $this->validate(['copia_cedula' => 'required|file|mimes:pdf,xls,xlsx|max:10000']);
        }

        if (!$this->cert_bancaria && !$this->tercero->cert_bancaria && !Auth::check()){
            $this->validate(['cert_bancaria' => 'required|file|mimes:pdf,xls,xlsx|max:10000']);
        }

        if (!$this->tercero->rut && !$this->tercero->rut && !Auth::check()){
            $this->validate(['rut' => 'required|file|mimes:pdf,xls,xlsx|max:10000']);
        }

        $dompdf = new Dompdf(array('enable_remote' => true));
        $html = View::make('exports.contrato', ['tercero' => $this->tercero, 'items' => $this->orden->ordenItems])->render();
        $dompdf->loadHtml($html);
        $dompdf->render();

        // Guardar el PDF en el almacenamiento temporal
        $output = $dompdf->output();
        $filePath = storage_path('app/public/contratos/contrato_' . time() . '.pdf');
        file_put_contents($filePath, $output);

        // Generar una URL pública para el archivo
        $this->contrato = asset('storage/contratos/' . basename($filePath));
    }

    public function deletePersonal(){
        $this->tercero->delete();
        $this->emit('terceroRegistrado');
        return redirect()->route('personal')->with('success', 'Personal eliminado con éxito.');
    }

    public function fillForm(){
        $this->nombre = $this->tercero->nombre;
        $this->apellido = $this->tercero->apellido;
        $this->cedula = $this->tercero->cedula;
        $this->correo = $this->tercero->correo;
        $this->telefono = $this->tercero->telefono;
        $this->ciudad = $this->tercero->ciudad;
        $this->estado = $this->tercero->estado;
        $this->banco = $this->tercero->banco;
        // $this->rut = $this->tercero->rut;
        // $this->cert_bancaria = $this->tercero->cert_bancaria;
    }

    public function toggelConfirm(){
        $this->deleteConfirm = !$this->deleteConfirm;
    }

    /*
        * Reset de los campos
        * @param array $fields
    */
    public function reset_fields($fields = null){
        foreach($fields as $field){
            $this->$field = '';
        }
    }
}

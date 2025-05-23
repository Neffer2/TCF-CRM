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
use Carbon\Carbon;
use Illuminate\Support\Facades\Storage;

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
    $banco, $rut, $cert_bancaria, $terminos, $estado = 1, $terceroXlsx, $copia_cedula, $num_rut, $servicio;

    // Useful vars
    public $estados, $ciudades, $deleteConfirm = false, $contrato, $servicios = [], $bancos = [], $min_rut = 198000;

    // Filled
    public $tercero, $orden;

    /*
        * EVIDENCIAS
    */

    // Models
    public $fechaEvidencia, $fotoEvidencia, $observacionEvidencia;

    // Useful vars
    public $evidencias = [];

    public function render()
    {
        return view('livewire.productor.terceros.nuevo-personal');
    }

    public function mount(){
        $this->ciudades = app('ciudades');
        $this->servicios = app('servicios');
        $this->bancos = app('bancos');
        $this->evidencias = collect();

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
            'servicio' => 'required|string',
            // 'estado' => 'required|numeric|max:1',
        ]);

        $tercero = new Tercero();
        $tercero->nombre = $this->nombre;
        $tercero->apellido = $this->apellido;
        $tercero->cedula = trim($this->cedula);
        $tercero->correo = trim($this->correo);
        $tercero->telefono = trim($this->telefono);
        $tercero->ciudad = $this->ciudad;
        $tercero->servicio = $this->servicio;
        $tercero->estado = 1;

        if($this->banco){
            $this->validate(['banco' => 'string|max:255']);
            $tercero->banco = $this->banco;
        }

        if($this->rut){
            $this->validate(['rut' => 'file|mimes:pdf,xls,xlsx|max:10000']);
            $tercero->rut = $this->rut->store('public/ruts');
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
            'servicio',
            'rut',
            'cert_bancaria',
        ]);

        return redirect()->back();
    }

    public function actualizarTercero(){
        if ($this->orden && $this->orden->ordenItems->sum('vtotal_oc') > $this->min_rut){
            $this->validate([
                'num_rut' => 'required|numeric'
            ]);
        }

        $this->validate([
            'nombre' => 'required|max:255',
            'apellido' => 'required|max:255',
            'cedula' => 'required|numeric',
            'correo' => 'required|email',
            'telefono' => 'required|numeric',
            'ciudad' => 'required|string',
            'servicio' => 'required|string',
            'estado' => 'required|numeric|max:1',
        ]);

        if (!Auth::check()){
            $this->validate([
                'banco' => 'required|string|max:255',
                'contrato' => 'required|string',
                'terminos' => 'required|accepted'
            ]);
        }

        $tercero = $this->tercero;
        $tercero->nombre = $this->nombre;
        $tercero->apellido = $this->apellido;
        $tercero->cedula = trim($this->cedula);
        $tercero->correo = trim($this->correo);
        $tercero->telefono = trim($this->telefono);
        if ($this->orden && $this->orden->ordenItems->sum('vtotal_oc') > $this->min_rut){ $tercero->num_rut = trim($this->num_rut); }
        $tercero->ciudad = $this->ciudad;
        $tercero->servicio = $this->servicio;
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
            $tercero->copia_cedula = $this->copia_cedula->store('public/copia_cedula');
        }

        if (!$tercero->cert_bancaria && !Auth::check()){
            $this->validate(['cert_bancaria' => 'required|file|mimes:pdf,xls,xlsx|max:10000']);
            $tercero->cert_bancaria = $this->cert_bancaria->store('public/cert_bancarias');
        }elseif($this->cert_bancaria){
            $this->validate(['cert_bancaria' => 'file|mimes:pdf,xls,xlsx|max:10000']);
            $tercero->cert_bancaria = $this->cert_bancaria->store('public/cert_bancarias');
        }

        if ($this->orden && $this->orden->ordenItems->sum('vtotal_oc') > $this->min_rut){
            //
            if (!$tercero->rut && !Auth::check()){
                $this->validate(['rut' => 'required|file|mimes:pdf,xls,xlsx|max:10000']);
                $tercero->rut = $this->rut->store('public/ruts');
            }elseif($this->rut){
                $this->validate(['rut' => 'file|mimes:pdf,xls,xlsx|max:10000']);
                $tercero->rut = $this->rut->store('public/ruts');
            }
        }

        if (!Auth::check()){
            $this->orden->naturalInfo->contrato = $this->contrato;
            $this->orden->naturalInfo->terminos = $this->terminos;
            $this->orden->naturalInfo->update();

            // La orden continúa editable, pero con contrato adjunto
            $this->orden->estado_id = 3;
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
            'num_rut',
            'cert_bancaria',
            'terminos'
        ]);

        if (!Auth::check()){
            return redirect()->route('consulta-terceros');
        }

        return redirect()->route('personal')->with('success', 'Cambios guardados con éxito.');
    }

    public function generarContrato(){
        if ($this->orden->ordenItems->sum('vtotal_oc') > $this->min_rut){
            $this->validate([
                'num_rut' => 'required|numeric'
            ]);
        }

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

        if ((!$this->copia_cedula && !$this->tercero->copia_cedula) && !Auth::check()){
            $this->validate(['copia_cedula' => 'required|file|mimes:pdf,xls,xlsx|max:10000']);
        }

        if ((!$this->cert_bancaria && !$this->tercero->cert_bancaria) && !Auth::check()){
            $this->validate(['cert_bancaria' => 'required|file|mimes:pdf,xls,xlsx|max:10000']);
        }

        if (((!$this->rut && !$this->tercero->rut) && !Auth::check()) && $this->orden->ordenItems->sum('vtotal_oc') > $this->min_rut){
            $this->validate(['rut' => 'required|file|mimes:pdf,xls,xlsx|max:10000']);
        }

        $contratoInfo = [
            'items' => $this->orden->ordenItems,
            'tercero' => $this->tercero,
            'ciudad' => $this->ciudad,
            'telefono' => $this->telefono,
            'dia' => Carbon::now()->format('d'),
            'dia_str' => $this->getNumberString(Carbon::now()->format('d')),
            'mes' => Carbon::now()->translatedFormat('F'),
            'ano' => Carbon::now()->format('Y'),
            'num_rut' => $this->num_rut
        ];

        $dompdf = new Dompdf(array('enable_remote' => true));
        $html = View::make('exports.contrato', ['contratoInfo' => $contratoInfo])->render();
        $dompdf->loadHtml($html);
        $dompdf->render();

        // Guardar el PDF en el almacenamiento
        $output = $dompdf->output();
        $filePath = storage_path('app/public/contratos/contrato_' . time() . '.pdf');
        file_put_contents($filePath, $output);

        // Generar una URL pública para el archivo
        $this->contrato = asset('storage/contratos/' . basename($filePath));
    }

    private function getNumberString($numero){
        $numerosEnTexto = [
            1 => 'Uno', 2 => 'Dos', 3 => 'Tres', 4 => 'Cuatro', 5 => 'Cinco',
            6 => 'Seis', 7 => 'Siete', 8 => 'Ocho', 9 => 'Nueve', 10 => 'Diez',
            11 => 'Once', 12 => 'Doce', 13 => 'Trece', 14 => 'Catorce', 15 => 'Quince',
            16 => 'Dieciséis', 17 => 'Diecisiete', 18 => 'Dieciocho', 19 => 'Diecinueve', 20 => 'Veinte',
            21 => 'Veintiuno', 22 => 'Veintidós', 23 => 'Veintitrés', 24 => 'Veinticuatro', 25 => 'Veinticinco',
            26 => 'Veintiséis', 27 => 'Veintisiete', 28 => 'Veintiocho', 29 => 'Veintinueve', 30 => 'Treinta',
            31 => 'Treinta y uno'
        ];

        return $numerosEnTexto[(int) $numero] ?? $numero;
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
        $this->servicio = $this->tercero->servicio;
        $this->num_rut = $this->tercero->num_rut;
        // $this->rut = $this->tercero->rut;
        // $this->cert_bancaria = $this->tercero->cert_bancaria;
    }

    public function toggelConfirm(){
        $this->deleteConfirm = !$this->deleteConfirm;
    }

    /*
        * EVIDENCIAS
    */
    public function newEvidencia(){
        $this->validate([
            'fechaEvidencia' => 'required|date',
            'fotoEvidencia' => 'required|file|mimes:jpg,jpeg,png|max:10000',
            'observacionEvidencia' => 'nullable|string|max:255'
        ]);

        $evidencia = [
            'fecha' => $this->fechaEvidencia,
            'foto' => $this->fotoEvidencia->store('public/evidencias'),
            'observacion' => $this->observacionEvidencia
        ];

        $this->evidencias->push($evidencia);
        $this->reset_fields(['fechaEvidencia', 'fotoEvidencia', 'observacionEvidencia']);
    }

    public function deleteItem($itemId){
        $filePath = $this->evidencias[$itemId]['foto'];
        Storage::delete($filePath);
        unset($this->evidencias[$itemId]);
    }

    public function saveEvidencia(){
        foreach($this->evidencias as $evidencia){
            $this->orden->evidencias()->create([
                'fecha_evidencia' => $evidencia['fecha'],
                'foto_evidencia' => $evidencia['foto'],
                'observacion_evidencia' => $evidencia['observacion'],
                'tercero_id' => $this->orden->naturalInfo->tercero_id
            ]);
        }

        $this->orden->estado_id = 2;
        $this->orden->update();

        $this->reset_fields([
            'fechaEvidencia',
            'fotoEvidencia',
            'observacionEvidencia'
        ]);

        return redirect()->route('consulta-terceros');
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

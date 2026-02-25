<?php

namespace App\Http\Livewire\Productor\Terceros;

use App\Models\Evidencia;
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
use App\Traits\SMS;
use App\Traits\Email;

class NuevoPersonal extends Component
{
    use WithFileUploads, SMS, Email;

    /*
        * Este componente se utiliza para registrar o editar personal.
        * Si tiene $tercero, es edición; si tiene $orden, es edición por parte de un tercero.
    */

    // Modelos y campos del formulario
    public $nombre, $apellido, $cedula, $correo, $telefono, $ciudad,
    $banco, $rut, $cert_bancaria, $terminos, $estado = 1, $terceroXlsx,
    $copia_cedula, $num_rut, $servicio, $art383, $check_art383,
    $planilla_aportes, $tipo_cuenta, $num_cuenta;

    // Variables auxiliares
    public $estados, $ciudades, $deleteConfirm = false, $contrato, $servicios = [], $bancos = [], $min_rut = 198000;

    // Modelos cargados
    public $tercero, $orden;

    /*
        * EVIDENCIAS
    */

    // Campos para evidencias
    public $fechaEvidencia, $fotoEvidencia, $observacionEvidencia;

    // Colección de evidencias
    public $evidencias = [];

    // Listener para ejecutar el método store cuando se emite 'evidencia-signal'
    protected $listeners = ['evidencia-signal' => 'saveEvidencia'];

    // Renderiza la vista principal del componente
    public function render()
    {
        return view('livewire.productor.terceros.nuevo-personal');
    }

    // Inicializa variables y carga catálogos
    public function mount(){
        $this->ciudades = app('ciudades');
        $this->servicios = app('servicios');
        $this->bancos = app('bancos');
        $this->evidencias = collect();

        $this->estados = EstadoTercero::all();

        if ($this->tercero) {
            $this->fillForm();
        }

        // Si el estado_id es 7 (Evidencias), obtenemos las evidencias guardadas
        if ($this->orden->estado_id == 7) {
            $this->evidencias = Evidencia::where([
                ['oc_id', $this->orden->id],
                ['tercero_id', $this->tercero->id],
            ])->get();
        }
    }

    // Importa terceros desde archivo Excel
    public function updatedterceroXlsx(){
        Excel::import(new TerceroImport, $this->terceroXlsx);
    }

    // Registra un nuevo personal
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

        // Si se diligenció banco
        if($this->banco){
            $this->validate(['banco' => 'string|max:255']);
            $tercero->banco = $this->banco;
        }

        // Si se adjuntó RUT
        if($this->rut){
            $this->validate(['rut' => 'file|mimes:pdf,xls,xlsx,jpg,bmp,png|max:10000']);
            $tercero->rut = $this->rut->store('public/ruts');
            $tercero->rut = $this->rut->store('public/ruts');
        }

        // Si se adjuntó certificación bancaria
        if($this->cert_bancaria){
            $this->validate(['cert_bancaria' => 'file|mimes:pdf,xls,xlsx,jpg,bmp,png|max:10000']);
            $tercero->cert_bancaria = $this->cert_bancaria->store('public/cert_bancarias');
        }

        $tercero->save();
        $this->emit('terceroRegistrado');

        // Limpia los campos del formulario
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

    // Actualiza un tercero existente
    public function actualizarTercero(){
        // Si el valor supera el mínimo, exige número de RUT
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
            'estado' => 'required|numeric|max:1'
        ]);

        // Si no está autenticado, exige más campos
        if (!Auth::check()){
            $this->validate([
                'banco' => 'required|string|max:255',
                'tipo_cuenta' => 'required|string|max:255',
                'num_cuenta' => 'required|string|max:255',
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
        $tercero->tipo_cuenta = $this->tipo_cuenta;
        $tercero->num_cuenta = $this->num_cuenta;

        // Banco
        if($this->banco){
            $this->validate(['banco' => 'string|max:255']);
            $tercero->banco = $this->banco;
        }

        // Copia de cédula
        if (!$tercero->copia_cedula && !Auth::check()){
            $this->validate(['copia_cedula' => 'required|file|mimes:pdf,xls,xlsx,jpg,bmp,png,jpg,bmp,png|max:10000']);
            $tercero->copia_cedula = $this->copia_cedula->store('public/copia_cedula');
        }elseif($this->copia_cedula){
            $this->validate(['copia_cedula' => 'file|mimes:pdf,xls,xlsx,jpg,bmp,png,jpg,bmp,png|max:10000']);
            $tercero->copia_cedula = $this->copia_cedula->store('public/copia_cedula');
        }

        // Certificación bancaria
        if (!$tercero->cert_bancaria && !Auth::check()){
            $this->validate(['cert_bancaria' => 'required|file|mimes:pdf,xls,xlsx,jpg,bmp,png|max:10000']);
            $tercero->cert_bancaria = $this->cert_bancaria->store('public/cert_bancarias');
        }elseif($this->cert_bancaria){
            $this->validate(['cert_bancaria' => 'file|mimes:pdf,xls,xlsx,jpg,bmp,png|max:10000']);
            $tercero->cert_bancaria = $this->cert_bancaria->store('public/cert_bancarias');
        }

        // Planilla de aportes
        if (!$tercero->planilla_aportes && !Auth::check()){
            $this->validate(['planilla_aportes' => 'nullable|file|mimes:pdf,xls,xlsx,jpg,bmp,png|max:10000']);
            if ($this->planilla_aportes){$tercero->planilla_aportes = $this->planilla_aportes->store('public/planillas_aportes');}
        }

        // RUT si aplica
        if ($this->orden && $this->orden->ordenItems->sum('vtotal_oc') > $this->min_rut){
            if (!$tercero->rut && !Auth::check()){
                $this->validate(['rut' => 'required|file|mimes:pdf,xls,xlsx,jpg,bmp,png|max:10000']);
                $tercero->rut = $this->rut->store('public/ruts');
            }elseif($this->rut){
                $this->validate(['rut' => 'file|mimes:pdf,xls,xlsx,jpg,bmp,png|max:10000']);
                $tercero->rut = $this->rut->store('public/ruts');
            }
        }

        // Si no está autenticado, actualiza contrato y términos en la orden
        if (!Auth::check()){
            $this->orden->naturalInfo->contrato = $this->contrato;
            $this->orden->naturalInfo->terminos = $this->terminos;
            $this->orden->naturalInfo->update();

            // La orden continúa editable, pero con contrato adjunto
            $this->orden->estado_id = 3;
            $this->orden->update();

            // Mail notificacion
            $this->ocNaturalFirmada($this->orden);
        }

        $tercero->update();
        $this->emit('terceroRegistrado');

        // Limpia los campos del formulario
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

    // Genera el contrato en PDF y lo almacena
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
            'num_cuenta' => 'required|string|max:255',
            'tipo_cuenta' => 'required|string|max:255'
        ]);

        // Validaciones de archivos requeridos
        if ((!$this->planilla_aportes && !$this->tercero->planilla_aportes) && !Auth::check()){
            $this->validate(['planilla_aportes' => 'nullable|file|mimes:pdf,xls,xlsx,jpg,bmp,png|max:10000']);
        }

        if ((!$this->copia_cedula && !$this->tercero->copia_cedula) && !Auth::check()){
            $this->validate(['copia_cedula' => 'required|file|mimes:pdf,xls,xlsx,jpg,bmp,png|max:10000']);
        }

        if ((!$this->cert_bancaria && !$this->tercero->cert_bancaria) && !Auth::check()){
            $this->validate(['cert_bancaria' => 'required|file|mimes:pdf,xls,xlsx,jpg,bmp,png|max:10000']);
        }

        if (((!$this->rut && !$this->tercero->rut) && !Auth::check()) && $this->orden->ordenItems->sum('vtotal_oc') > $this->min_rut){
            $this->validate(['rut' => 'required|file|mimes:pdf,xls,xlsx,jpg,bmp,png|max:10000']);
        }

        // Información para el contrato
        $contratoInfo = [
            'items' => $this->orden->ordenItems,
            'tercero' => $this->tercero,
            'ciudad' => $this->ciudad,
            'telefono' => $this->telefono,
            'correo' => $this->correo,
            'dia' => Carbon::now()->format('d'),
            'dia_str' => $this->getNumberString(Carbon::now()->format('d')),
            'mes' => Carbon::now()->translatedFormat('F'),
            'ano' => Carbon::now()->format('Y'),
            'num_rut' => $this->num_rut
        ];

        // Genera el PDF y lo guarda en storage
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

    // Convierte un número a texto (para el contrato)
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

    // Elimina un personal
    public function deletePersonal(){
        $this->tercero->delete();
        $this->emit('terceroRegistrado');
        return redirect()->route('personal')->with('success', 'Personal eliminado con éxito.');
    }

    // Llena el formulario con los datos del tercero
    public function fillForm(){
        $this->nombre = $this->tercero->nombre;
        $this->apellido = $this->tercero->apellido;
        $this->cedula = $this->tercero->cedula;
        $this->correo = $this->tercero->correo;
        $this->telefono = $this->tercero->telefono;
        $this->ciudad = $this->tercero->ciudad;
        $this->estado = $this->tercero->estado;
        $this->banco = $this->tercero->banco;
        $this->tipo_cuenta = $this->tercero->tipo_cuenta;
        $this->num_cuenta = $this->tercero->num_cuenta;
        $this->servicio = $this->tercero->servicio;
        $this->num_rut = $this->tercero->num_rut;
        $this->planilla_aportes = $this->tercero->planilla_aportes;
    }

    // Alterna la confirmación de eliminación
    public function toggelConfirm(){
        $this->deleteConfirm = !$this->deleteConfirm;
    }

    /*
        * EVIDENCIAS
    */

    // Agrega una nueva evidencia a la colección
    public function newEvidencia(){
        $this->validate([
            'fechaEvidencia' => 'required|date',
            'fotoEvidencia' => 'required|file|mimes:jpg,jpeg,png|max:10000',
            'observacionEvidencia' => 'required|string|max:255'
        ]);

        $evidencia = $this->orden->evidencias()->create([
            'fecha_evidencia' => $this->fechaEvidencia,
            'foto_evidencia' => $this->fotoEvidencia->store('public/evidencias'),
            'observacion_evidencia' => $this->observacionEvidencia,
            'tercero_id' => $this->orden->naturalInfo->tercero_id
        ]);

        $this->evidencias->push($evidencia);
        $this->reset_fields(['fechaEvidencia', 'fotoEvidencia', 'observacionEvidencia']);
    }

    // Elimina una evidencia de la colección y borra el archivo
    public function deleteEvidencia($itemId){
        $itemEvidencia = Evidencia::find($itemId);
        Storage::delete($itemEvidencia->foto_evidencia);

        $itemEvidencia->delete();
        $this->evidencias = $this->evidencias->reject(function ($e) use ($itemId) {
            return $e->id === $itemId;
        });
    }

    // Guarda todas las evidencias en la base de datos
    public function saveEvidencia($data){
        // Eliminamos la firma antigua
        Storage::delete($this->tercero->firma);

        // Procesa la imagen de la firma recibida en base64
        $data_uri = $data;
        $encoded_image = explode(",", $data_uri)[1];
        $decoded_image = base64_decode($encoded_image);

        // Guarda la firma como archivo PNG en storage
        file_put_contents("storage/firmas_terceros/".$this->tercero->cedula.".png", $decoded_image);
        $this->tercero->firma = "public/firmas_terceros/".$this->tercero->cedula.".png";
        $this->tercero->update();

        $this->orden->estado_id = 3;
        $this->orden->update();

        // Mail evidencias enviadas
        $this->ocNaturalEvidenciasEnviadas($this->orden);

        return redirect()->route('consulta-terceros');
    }

    /*
        * Limpia los campos especificados del formulario
        * @param array $fields
    */
    public function reset_fields($fields = null){
        foreach($fields as $field){
            $this->$field = '';
        }
    }
}

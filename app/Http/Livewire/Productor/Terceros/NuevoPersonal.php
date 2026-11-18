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
use App\Traits\SMS;
use App\Traits\Email;
use Illuminate\Support\Facades\DB;

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
    $planilla_aportes, $tipo_cuenta, $num_cuenta, $queriedOrden;
    public $mostrarContrato = false;

    public $firma;
    public $contratoBase64;
    public $pdfCuentaCobroBase64;

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
        
        // Valida si existe una para generar PDF 
        if ($this->orden) {
            $this->pdfCuentaCobroBase64 = $this->generarPdfCuentaCobroBase64();
            $this->generarPrevisualizacionContrato();
        }

        if ($this->tercero){$this->fillForm();}
    }

    // Importa terceros desde archivo Excel
    public function updatedterceroXlsx(){
        Excel::import(new TerceroImport, $this->terceroXlsx);
    }

    // Registra un nuevo personal
    public function nuevoPersonal()
    {
        $this->validate([
            'nombre'   => 'required|max:255',
            'apellido' => 'required|max:255',
            'cedula'   => 'required|numeric|unique:terceros',
            'correo'   => 'required|email|unique:terceros',
            'telefono' => 'required|numeric|unique:terceros',
            'ciudad'   => 'required|string',
            'servicio' => 'required|string',
        ]);

        $tercero = new Tercero();
        $tercero->nombre   = $this->nombre;
        $tercero->apellido = $this->apellido;
        $tercero->cedula   = trim($this->cedula);
        $tercero->correo   = trim($this->correo);
        $tercero->telefono = trim($this->telefono);
        $tercero->ciudad   = $this->ciudad;
        $tercero->servicio = $this->servicio;
        $tercero->estado   = 1;

        // Si se diligenció banco
        if ($this->banco) {
            $this->validate(['banco' => 'string|max:255']);
            $tercero->banco = $this->banco;
        }

        // Si se adjuntó RUT (se corrigió la duplicidad)
        if ($this->rut) {
            $this->validate(['rut' => 'file|mimes:pdf,xls,xlsx,jpg,bmp,png|max:10000']);
            $tercero->rut = $this->rut->store('public/ruts');
        }

        // Si se adjuntó certificación bancaria
        if ($this->cert_bancaria) {
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
    private function guardarArchivosTemporales($tercero)
    {
        // Copia de cédula
        if (!$tercero->copia_cedula && !Auth::check()){
            $this->validate(['copia_cedula' => 'required|file|mimes:pdf,xls,xlsx,jpg,bmp,png|max:10000']);
            $tercero->copia_cedula = $this->copia_cedula->store('public/copia_cedula');
        }elseif($this->copia_cedula){
            $this->validate(['copia_cedula' => 'file|mimes:pdf,xls,xlsx,jpg,bmp,png|max:10000']);
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
            if ($this->planilla_aportes){ $tercero->planilla_aportes = $this->planilla_aportes->store('public/planillas_aportes'); }
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
    }

    // Genera el contrato en PDF y lo almacena
    public function confirmarInformacion()
    {
        if ($this->orden->ordenItems->sum('vtotal_oc') > $this->min_rut) {
            $this->validate([
                'num_rut' => 'required|numeric'
            ]);
        }

        $this->validate([
            'nombre'      => 'required|max:255',
            'apellido'    => 'required|max:255',
            'cedula'      => 'required|numeric',
            'correo'      => 'required|email',
            'telefono'    => 'required|numeric',
            'ciudad'      => 'required|string',
            'estado'      => 'required|numeric|max:1',
            'banco'       => 'required|string|max:255',
            'num_cuenta'  => 'required|string|max:255',
            'tipo_cuenta' => 'required|string|max:255',
        ]);

        if (!Auth::check()){
            $this->validate([
                'banco'       => 'required|string|max:255',
                'tipo_cuenta' => 'required|string|max:255',
                'num_cuenta'  => 'required|string|max:255',
            ]);
        }

        $tercero = $this->tercero;
        $tercero->nombre      = $this->nombre;
        $tercero->apellido    = $this->apellido;
        $tercero->cedula      = trim($this->cedula);
        $tercero->correo      = trim($this->correo);
        $tercero->telefono    = trim($this->telefono);
        if ($this->orden && $this->orden->ordenItems->sum('vtotal_oc') > $this->min_rut){ 
            $tercero->num_rut = trim($this->num_rut); 
        }
        $tercero->ciudad      = $this->ciudad;
        $tercero->servicio    = $this->servicio;
        $tercero->estado      = trim($this->estado);
        $tercero->tipo_cuenta = $this->tipo_cuenta;
        $tercero->num_cuenta  = $this->num_cuenta;

        if($this->banco){
            $this->validate(['banco' => 'string|max:255']);
            $tercero->banco = $this->banco;
        }

        $this->guardarArchivosTemporales($tercero);
        $tercero->update();
        
        $contratoInfo = [
            'items'    => $this->orden->ordenItems,
            'tercero'  => $this->tercero,
            'ciudad'   => $this->ciudad,
            'telefono' => $this->telefono,
            'correo'   => $this->correo,
            'dia'      => Carbon::now()->format('d'),
            'dia_str'  => $this->getNumberString(Carbon::now()->format('d')),
            'mes'      => Carbon::now()->translatedFormat('F'),
            'ano'      => Carbon::now()->format('Y'),
            'num_rut'  => $this->num_rut,
            'firma'    => null, // En blanco para la lectura
        ];

        $dompdf = new Dompdf(['enable_remote' => true]);
        $html = View::make('exports.contrato', ['contratoInfo' => $contratoInfo])->render();
        $dompdf->loadHtml($html);
        $dompdf->setPaper('A4', 'portrait');
        $dompdf->render();

        // Guardamos el Base64 y activamos la vista del contrato
        $this->contratoBase64 = base64_encode($dompdf->output());
        $this->mostrarContrato = true;
    }
    public function generarContrato()
    {
        // 1. Si el valor supera el mínimo, exige número de RUT
        if ($this->orden && $this->orden->ordenItems->sum('vtotal_oc') > $this->min_rut){
            $this->validate([
                'num_rut' => 'required|numeric'
            ]);
        }

        $this->validate([
            'nombre'      => 'required|max:255',
            'apellido'    => 'required|max:255',
            'cedula'      => 'required|numeric',
            'correo'      => 'required|email',
            'telefono'    => 'required|numeric',
            'ciudad'      => 'required|string',
            'estado'      => 'required|numeric|max:1',
            'banco'       => 'required|string|max:255',
            'num_cuenta'  => 'required|string|max:255',
            'tipo_cuenta' => 'required|string|max:255',
            'terminos'    => 'required|accepted' // Validar aceptación de términos
        ]);

        // 2. Validar la firma digital
        $this->validate(
            ['firma' => 'required|string|starts_with:data:image/png;base64,|max:400000'],
            ['firma.required' => 'Debes firmar el contrato.', 'firma.starts_with' => 'La firma no es válida.']
        );

        $firmaBin = base64_decode(substr($this->firma, strlen('data:image/png;base64,')), true);
        if ($firmaBin === false || @getimagesizefromstring($firmaBin) === false) {
            $this->addError('firma', 'La firma no es una imagen válida.');
            return;
        }

        // 3. Validaciones de archivos requeridos
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

        // 4. Información completa para el contrato (incluyendo la firma y sus metadatos)
        $contratoInfo = [
            'items'       => $this->orden->ordenItems,
            'tercero'     => $this->tercero,
            'ciudad'      => $this->ciudad,
            'telefono'    => $this->telefono,
            'correo'      => $this->correo,
            'dia'         => Carbon::now()->format('d'),
            'dia_str'     => $this->getNumberString(Carbon::now()->format('d')),
            'mes'         => Carbon::now()->translatedFormat('F'),
            'ano'         => Carbon::now()->format('Y'),
            'num_rut'     => $this->num_rut,
            'firma'       => $this->firma,
            'firma_fecha' => Carbon::now()->format('Y-m-d H:i:s'),
            'firma_ip'    => request()->ip(),
            'firma_hash'  => hash('sha256', $firmaBin),
        ];

        // 5. Genera el PDF con Dompdf
        $dompdf = new Dompdf(array('enable_remote' => true));
        $html = View::make('exports.contrato', ['contratoInfo' => $contratoInfo])->render();
        $dompdf->loadHtml($html);
        $dompdf->setPaper('A4', 'portrait');
        $dompdf->render();

        // 6. Guardar el PDF en el almacenamiento físico
        $output = $dompdf->output();
        $fileName = 'contrato_' . time() . '.pdf';
        $filePath = storage_path('app/public/contratos/' . $fileName);
        file_put_contents($filePath, $output);

        // 7. Generar la URL pública completa (Ej: http://.../storage/contratos/contrato_...pdf)
        $urlPublica = asset('storage/contratos/' . $fileName);
        $this->contrato = $urlPublica;

        // 8. Actualizar datos en la base de datos si no está autenticado
        if (!Auth::check()){
            $this->orden->naturalInfo->contrato = $urlPublica; // Se guarda la URL completa en el campo contrato
            $this->orden->naturalInfo->terminos = $this->terminos;
            $this->orden->naturalInfo->update();

            $this->orden->estado_id = 3;
            $this->orden->update();

            $this->ocNaturalFirmada($this->orden);
        }

        // Limpiar firma de la propiedad y del canvas
        $this->firma = null;
        $this->dispatchBrowserEvent('firma-limpiar'); // O $this->dispatch('firma-limpiar') en Livewire v3

        session()->flash('success', 'Contrato firmado y guardado con éxito.');

        if (!Auth::check()){
            return redirect()->route('consulta-terceros');
        }

        return redirect()->route('personal')->with('success', 'Cambios guardados con éxito.');
    }

    public function generarPrevisualizacionContrato()
{
    $contratoInfo = [
        'items'    => $this->orden->ordenItems,
        'tercero'  => $this->tercero,
        'ciudad'   => $this->ciudad,
        'telefono' => $this->telefono,
        'correo'   => $this->correo,
        'dia'      => Carbon::now()->format('d'),
        'dia_str'  => $this->getNumberString(Carbon::now()->format('d')),
        'mes'      => Carbon::now()->translatedFormat('F'),
        'ano'      => Carbon::now()->format('Y'),
        'num_rut'  => $this->num_rut,
        'firma'    => null, // Sin firma en la previsualización
    ];

    $dompdf = new Dompdf(['enable_remote' => true]);
    $html = View::make('exports.contrato', ['contratoInfo' => $contratoInfo])->render();
    $dompdf->loadHtml($html);
    $dompdf->setPaper('A4', 'portrait');
    $dompdf->render();

    $this->contratoBase64 = base64_encode($dompdf->output());
}

/**
 * 2. Valida la firma, los términos, genera el PDF firmado y lo almacena
 */
    public function firmarYGuardarContrato()
    {
        // Validaciones básicas de datos y RUT si aplica
        if ($this->orden->ordenItems->sum('vtotal_oc') > $this->min_rut) {
            $this->validate(['num_rut' => 'required|numeric']);
        }

        $this->validate([
            'nombre'      => 'required|max:255',
            'apellido'    => 'required|max:255',
            'cedula'      => 'required|numeric',
            'correo'      => 'required|email',
            'telefono'    => 'required|numeric',
            'ciudad'      => 'required|string',
            'estado'      => 'required|numeric|max:1',
            'banco'       => 'required|string|max:255',
            'num_cuenta'  => 'required|string|max:255',
            'tipo_cuenta' => 'required|string|max:255',
            'terminos'    => 'required|accepted', // Validación de términos y condiciones
        ]);

        // Validar la firma
        $this->validate(
            ['firma' => 'required|string|starts_with:data:image/png;base64,|max:400000'],
            ['firma.required' => 'Debes firmar el contrato.', 'firma.starts_with' => 'La firma no es válida.']
        );

        $firmaBin = base64_decode(substr($this->firma, strlen('data:image/png;base64,')), true);
        if ($firmaBin === false || @getimagesizefromstring($firmaBin) === false) {
            $this->addError('firma', 'La firma no es una imagen válida.');
            return;
        }

        // Validaciones de archivos requeridos
        if ((!$this->planilla_aportes && !$this->tercero->planilla_aportes) && !Auth::check()) {
            $this->validate(['planilla_aportes' => 'nullable|file|mimes:pdf,xls,xlsx,jpg,bmp,png|max:10000']);
        }
        if ((!$this->copia_cedula && !$this->tercero->copia_cedula) && !Auth::check()) {
            $this->validate(['copia_cedula' => 'required|file|mimes:pdf,xls,xlsx,jpg,bmp,png|max:10000']);
        }
        if ((!$this->cert_bancaria && !$this->tercero->cert_bancaria) && !Auth::check()) {
            $this->validate(['cert_bancaria' => 'required|file|mimes:pdf,xls,xlsx,jpg,bmp,png|max:10000']);
        }
        if (((!$this->rut && !$this->tercero->rut) && !Auth::check()) && $this->orden->ordenItems->sum('vtotal_oc') > $this->min_rut) {
            $this->validate(['rut' => 'required|file|mimes:pdf,xls,xlsx,jpg,bmp,png|max:10000']);
        }

        // Información con la firma definitiva para el contrato
        $contratoInfo = [
            'items'       => $this->orden->ordenItems,
            'tercero'     => $this->tercero,
            'ciudad'      => $this->ciudad,
            'telefono'    => $this->telefono,
            'correo'      => $this->correo,
            'dia'         => Carbon::now()->format('d'),
            'dia_str'     => $this->getNumberString(Carbon::now()->format('d')),
            'mes'         => Carbon::now()->translatedFormat('F'),
            'ano'         => Carbon::now()->format('Y'),
            'num_rut'     => $this->num_rut,
            'firma'       => $this->firma,
            'firma_fecha' => Carbon::now()->format('Y-m-d H:i:s'),
            'firma_ip'    => request()->ip(),
            'firma_hash'  => hash('sha256', $firmaBin),
        ];

        // Generar PDF definitivo con Dompdf
        $dompdf = new Dompdf(['enable_remote' => true]);
        $html = View::make('exports.contrato', ['contratoInfo' => $contratoInfo])->render();
        $dompdf->loadHtml($html);
        $dompdf->setPaper('A4', 'portrait');
        $dompdf->render();

        // Guardar en storage/app/public/contratos/
        $nombreArchivo = 'contrato_' . time() . '.pdf';
        $rutaRelativa  = 'contratos/' . $nombreArchivo;
        Storage::disk('public')->put($rutaRelativa, $dompdf->output());

        // Guardar ruta en la orden o naturalInfo según corresponda en tu BD
        // Ej: $this->orden->naturalInfo->contrato = $rutaRelativa; ...

        session()->flash('success', 'Contrato firmado y guardado con éxito.');
        return redirect()->route('consulta-terceros');
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
    public function newEvidencia()
    {
        $this->validate([
            'fechaEvidencia'       => 'required|date',
            'fotoEvidencia'        => 'required|file|mimes:jpg,jpeg,png,pdf|max:10000',
            'observacionEvidencia' => 'required|string|max:255'
        ]);

        // Guarda el archivo subido en storage/app/public/evidencias
        $path = $this->fotoEvidencia->store('public/evidencias');

        // Añade el registro a la colección temporal
        $evidencia = [
            'fecha'       => $this->fechaEvidencia,
            'foto'        => $path,
            'observacion' => $this->observacionEvidencia
        ];

        $this->evidencias->push($evidencia);

        // Limpia las variables del formulario
        $this->reset_fields(['fechaEvidencia', 'fotoEvidencia', 'observacionEvidencia']);
    }

    // Elimina una evidencia de la colección y borra el archivo
    public function deleteEvidencia($itemId){
        $filePath = $this->evidencias[$itemId]['foto'];
        Storage::delete($filePath);
        unset($this->evidencias[$itemId]);
    }

    private function generarPdfCuentaCobroBase64()
    {
        if (!$this->orden) {
            throw new \Exception('No se ha seleccionado o cargado ninguna orden para generar el PDF.');
        }

            $this->orden->load([
            'naturalInfo.tercero',
            'ordenItems.itemPresupuesto.presto',
        ]);

        $items   = $this->orden->ordenItems;
        $tercero = $this->orden->naturalInfo->tercero;
        $total   = $items->sum('vtotal_oc');
        $fecha   = now()->locale('es')->translatedFormat('d \d\e F \d\e Y');

        // Renderizamos la vista limpia de exports.cuenta-cobro
        $html = View::make('exports.cuenta-cobro', [
            'orden'   => $this->orden,
            'items'   => $items,
            'tercero' => $tercero,
            'total'   => $total,
            'fecha'   => $fecha,
        ])->render();

        $dompdf = new Dompdf(['enable_remote' => true]);
        $dompdf->loadHtml($html);
        $dompdf->setPaper('A4', 'portrait');
        $dompdf->render();

        return base64_encode($dompdf->output());
    }

    public function guardarCuentaCobroConFirma()
    {
        // 1. Validar que la firma no esté vacía
        if (!$this->firma) {
            session()->flash('error', 'Por favor, dibuje su firma antes de continuar.');
            return;
        }

        // 2. Ejecutar la lógica de guardado de evidencias (lo que hacía saveEvidencia)
        DB::transaction(function () {
            foreach ($this->evidencias as $evidencia) {
                $this->orden->evidencias()->create([
                    'fecha_evidencia'       => $evidencia['fecha'],
                    'foto_evidencia'        => $evidencia['foto'],
                    'observacion_evidencia' => $evidencia['observacion'],
                    'tercero_id'            => $this->orden->naturalInfo->tercero_id
                ]);
            }

            $this->orden->estado_id = 3;
            $this->orden->save();
        });

        // Limpiar campos de evidencias
        $this->reset_fields([
            'fechaEvidencia',
            'fotoEvidencia',
            'observacionEvidencia'
        ]);

        // Enviar correo de notificación de evidencias
        $this->ocNaturalEvidenciasEnviadas($this->orden);

        // 3. Generar el PDF con la firma y guardarlo en 'cuentas_cobro/'
        $this->orden->load([
            'naturalInfo.tercero',
            'ordenItems.itemPresupuesto.presto',
        ]);

        $items   = $this->orden->ordenItems;
        $tercero = $this->orden->naturalInfo->tercero;
        $total   = $items->sum('vtotal_oc');
        $fecha   = now()->locale('es')->translatedFormat('d \d\e F \d\e Y');

        $html = View::make('exports.cuenta-cobro', [
            'orden'       => $this->orden,
            'items'       => $items,
            'tercero'     => $tercero,
            'total'       => $total,
            'fecha'       => $fecha,
            'firmaBase64' => $this->firma, 
        ])->render();

        $dompdf = new Dompdf(['enable_remote' => true]);
        $dompdf->loadHtml($html);
        $dompdf->setPaper('A4', 'portrait');
        $dompdf->render();

        $nombreArchivo = 'cuenta_cobro_' . $this->orden->id . '_' . time() . '.pdf';
        $rutaRelativa  = 'cuentas_cobro/' . $nombreArchivo;

        Storage::disk('public')->put($rutaRelativa, $dompdf->output());

        // Actualizar la ruta del archivo en el modelo
        $this->orden->archivo_cuenta_cobro = $rutaRelativa;
        $this->orden->save();

        session()->flash('success', 'Cuenta de cobro firmada y evidencias guardadas con éxito.');
        
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

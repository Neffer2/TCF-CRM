<?php

namespace App\Http\Livewire\Admin\Presupuestos;

use Livewire\Component;
use App\Models\Año;
use App\Models\Mes;
use Illuminate\Validation\Rules;
use Illuminate\Support\Collection;

/**
 * Componente Livewire para la configuración de fechas de inicio y fin de los meses
 * Permite establecer y modificar los períodos de cada mes para un año específico
 */
class Conf extends Component
{
    // Propiedades para datos auxiliares

    /**
     * Colección de años disponibles para selección
     * @var \Illuminate\Database\Eloquent\Collection|string
     */
    public $años = '';

    /**
     * Datos almacenados del año seleccionado (meses con sus fechas)
     * @var \Illuminate\Database\Eloquent\Collection|bool
     */
    public $storedAñoData;

    // Propiedades del modelo principal

    /**
     * ID del año seleccionado
     * @var int
     */
    public $añoModel;

    // Propiedades para fechas de inicio y fin de cada mes

    /**
     * Fecha de inicio del mes de enero
     * @var string
     */
    public $eneroIn;

    /**
     * Fecha de fin del mes de enero
     * @var string
     */
    public $eneroFin;

    /**
     * Fecha de inicio del mes de febrero
     * @var string
     */
    public $febreroIn;

    /**
     * Fecha de fin del mes de febrero
     * @var string
     */
    public $febreroFin;

    /**
     * Fecha de inicio del mes de marzo
     * @var string
     */
    public $marzoIn;

    /**
     * Fecha de fin del mes de marzo
     * @var string
     */
    public $marzoFin;

    /**
     * Fecha de inicio del mes de abril
     * @var string
     */
    public $abrilIn;

    /**
     * Fecha de fin del mes de abril
     * @var string
     */
    public $abrilFin;

    /**
     * Fecha de inicio del mes de mayo
     * @var string
     */
    public $mayoIn;

    /**
     * Fecha de fin del mes de mayo
     * @var string
     */
    public $mayoFin;

    /**
     * Fecha de inicio del mes de junio
     * @var string
     */
    public $junioIn;

    /**
     * Fecha de fin del mes de junio
     * @var string
     */
    public $junioFin;

    /**
     * Fecha de inicio del mes de julio
     * @var string
     */
    public $julioIn;

    /**
     * Fecha de fin del mes de julio
     * @var string
     */
    public $julioFin;

    /**
     * Fecha de inicio del mes de agosto
     * @var string
     */
    public $agostoIn;

    /**
     * Fecha de fin del mes de agosto
     * @var string
     */
    public $agostoFin;

    /**
     * Fecha de inicio del mes de septiembre
     * @var string
     */
    public $septiembreIn;

    /**
     * Fecha de fin del mes de septiembre
     * @var string
     */
    public $septiembreFin;

    /**
     * Fecha de inicio del mes de octubre
     * @var string
     */
    public $octubreIn;

    /**
     * Fecha de fin del mes de octubre
     * @var string
     */
    public $octubreFin;

    /**
     * Fecha de inicio del mes de noviembre
     * @var string
     */
    public $noviembreIn;

    /**
     * Fecha de fin del mes de noviembre
     * @var string
     */
    public $noviembreFin;

    /**
     * Fecha de inicio del mes de diciembre
     * @var string
     */
    public $diciembreIn;

    /**
     * Fecha de fin del mes de diciembre
     * @var string
     */
    public $diciembreFin;

    // Configuración de listeners para Livewire

    /**
     * Array de listeners que escuchan eventos específicos
     * @var array
     */
    protected $listeners = ['refresh' => 'mount'];

    /**
     * Renderiza la vista del componente
     *
     * @return \Illuminate\View\View
     */
    public function render()
    {
        return view('livewire.admin.presupuestos.conf');
    }

    /**
     * Método que se ejecuta al inicializar el componente
     * Carga los años disponibles para selección
     *
     * @return void
     */
    public function mount(){
        $this->getAños();
    }

    /**
     * Obtiene todos los años disponibles en el sistema
     * Carga la colección de años para mostrar en el selector
     *
     * @return void
     */
    public function getAños(){
        $this->años = Año::select('id', 'description')->get();
    }

    /**
     * Método que se ejecuta automáticamente cuando cambia el valor de $añoModel
     * Carga las fechas de los meses del año seleccionado y las asigna a las propiedades correspondientes
     * Si no existen datos para el año, establece $storedAñoData como false
     *
     * @return void
     */
    public function updatedAñomodel(){
        // Obtiene todos los meses del año seleccionado
        $this->storedAñoData = Mes::where('ano_id', $this->añoModel)->get();

        // Si existen datos de meses para el año seleccionado
        if (!$this->storedAñoData->isEmpty()){
            // Asigna las fechas de cada mes a las propiedades correspondientes
            // (asume que los meses están ordenados: enero=0, febrero=1, etc.)

            // Enero (índice 0)
            $this->eneroIn = $this->storedAñoData[0]->f_inicio;
            $this->eneroFin = $this->storedAñoData[0]->f_fin;

            // Febrero (índice 1)
            $this->febreroIn = $this->storedAñoData[1]->f_inicio;
            $this->febreroFin = $this->storedAñoData[1]->f_fin;

            // Marzo (índice 2)
            $this->marzoIn = $this->storedAñoData[2]->f_inicio;
            $this->marzoFin = $this->storedAñoData[2]->f_fin;

            // Abril (índice 3)
            $this->abrilIn = $this->storedAñoData[3]->f_inicio;
            $this->abrilFin = $this->storedAñoData[3]->f_fin;

            // Mayo (índice 4)
            $this->mayoIn = $this->storedAñoData[4]->f_inicio;
            $this->mayoFin = $this->storedAñoData[4]->f_fin;

            // Junio (índice 5)
            $this->junioIn = $this->storedAñoData[5]->f_inicio;
            $this->junioFin = $this->storedAñoData[5]->f_fin;

            // Julio (índice 6)
            $this->julioIn = $this->storedAñoData[6]->f_inicio;
            $this->julioFin = $this->storedAñoData[6]->f_fin;

            // Agosto (índice 7)
            $this->agostoIn = $this->storedAñoData[7]->f_inicio;
            $this->agostoFin = $this->storedAñoData[7]->f_fin;

            // Septiembre (índice 8)
            $this->septiembreIn = $this->storedAñoData[8]->f_inicio;
            $this->septiembreFin = $this->storedAñoData[8]->f_fin;

            // Octubre (índice 9)
            $this->octubreIn = $this->storedAñoData[9]->f_inicio;
            $this->octubreFin = $this->storedAñoData[9]->f_fin;

            // Noviembre (índice 10)
            $this->noviembreIn = $this->storedAñoData[10]->f_inicio;
            $this->noviembreFin = $this->storedAñoData[10]->f_fin;

            // Diciembre (índice 11)
            $this->diciembreIn = $this->storedAñoData[11]->f_inicio;
            $this->diciembreFin = $this->storedAñoData[11]->f_fin;
        }else{
            // Si no hay datos para el año, establece como false
            $this->storedAñoData = false;
        }
    }

    /**
     * Actualiza las fechas de inicio y fin de todos los meses del año seleccionado
     * Valida que todas las fechas sean válidas y luego las guarda en la base de datos
     *
     * @return \Illuminate\Http\RedirectResponse
     */
    public function updateMeses (){

        // Valida que todas las fechas de inicio y fin sean válidas y estén presentes
        $this->validate([
            'eneroIn' => ['required', 'date'],
            'eneroFin' => ['required', 'date'],
            'febreroIn' => ['required', 'date'],
            'febreroFin' => ['required', 'date'],
            'marzoIn' => ['required', 'date'],
            'marzoFin' => ['required', 'date'],
            'abrilIn' => ['required', 'date'],
            'abrilFin' => ['required', 'date'],
            'mayoIn' => ['required', 'date'],
            'mayoFin' => ['required', 'date'],
            'junioIn' => ['required', 'date'],
            'junioFin' => ['required', 'date'],
            'junioFin' => ['required', 'date'], // Nota: duplicado en el código original
            'julioIn' => ['required', 'date'],
            'julioFin' => ['required', 'date'],
            'agostoIn' => ['required', 'date'],
            'agostoFin' => ['required', 'date'],
            'septiembreIn' => ['required', 'date'],
            'septiembreFin' => ['required', 'date'],
            'octubreIn' => ['required', 'date'],
            'octubreFin' => ['required', 'date'],
            'noviembreIn' => ['required', 'date'],
            'noviembreFin' => ['required', 'date'],
            'diciembreIn' => ['required', 'date'],
            'diciembreFin' => ['required', 'date']
        ]);

        // Crea un array con todas las fechas en orden secuencial
        // Cada par de elementos representa: [fecha_inicio, fecha_fin] de cada mes
        $dates = [
            $this->eneroIn,      // 0 - Enero inicio
            $this->eneroFin,     // 1 - Enero fin
            $this->febreroIn,    // 2 - Febrero inicio
            $this->febreroFin,   // 3 - Febrero fin
            $this->marzoIn,      // 4 - Marzo inicio
            $this->marzoFin,     // 5 - Marzo fin
            $this->abrilIn,      // 6 - Abril inicio
            $this->abrilFin,     // 7 - Abril fin
            $this->mayoIn,       // 8 - Mayo inicio
            $this->mayoFin,      // 9 - Mayo fin
            $this->junioIn,      // 10 - Junio inicio
            $this->junioFin,     // 11 - Junio fin
            $this->julioIn,      // 12 - Julio inicio
            $this->julioFin,     // 13 - Julio fin
            $this->agostoIn,     // 14 - Agosto inicio
            $this->agostoFin,    // 15 - Agosto fin
            $this->septiembreIn, // 16 - Septiembre inicio
            $this->septiembreFin,// 17 - Septiembre fin
            $this->octubreIn,    // 18 - Octubre inicio
            $this->octubreFin,   // 19 - Octubre fin
            $this->noviembreIn,  // 20 - Noviembre inicio
            $this->noviembreFin, // 21 - Noviembre fin
            $this->diciembreIn,  // 22 - Diciembre inicio
            $this->diciembreFin, // 23 - Diciembre fin
        ];

        // Obtiene todos los meses del año seleccionado
        $meses = Mes::where('ano_id', $this->añoModel)->get();

        // Itera sobre cada mes y actualiza sus fechas
        $key = 0; // Índice para el array de fechas
        foreach ($meses as $mes){
            // Asigna fecha de inicio (índice par) y fecha de fin (índice impar)
            $mes->f_inicio = $dates[$key];     // Fecha de inicio
            $mes->f_fin = $dates[$key+1];      // Fecha de fin
            $mes->update(); // Guarda los cambios en la base de datos
            $key += 2; // Avanza al siguiente par de fechas
        }

        // Redirige con mensaje de éxito
        return redirect()->back()->with('success', '¡Año generado exitosamente!');
    }
}

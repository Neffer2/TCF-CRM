<?php

namespace App\Http\Livewire\Admin\Presupuestos;

use Livewire\Component;
use Illuminate\Validation\Rules;
use App\Models\Año;
use App\Models\Mes;

/**
 * Componente Livewire para la creación de nuevos años en el sistema
 * Permite crear un año y automáticamente genera los 12 meses asociados
 * Valida que el año esté dentro del rango permitido (año actual a siguiente año)
 */
class NuevoAño extends Component
{
    // Propiedades del modelo principal

    /**
     * Descripción del año a crear (normalmente el número del año)
     * @var string
     */
    public $description;

    // Variables auxiliares para el control del formulario

    /**
     * Controla si el botón de envío está habilitado
     * Se activa cuando la validación es exitosa
     * @var bool
     */
    public $enableSubmit = false;

    /**
     * Renderiza la vista del componente
     *
     * @return \Illuminate\View\View
     */
    public function render()
    {
        return view('livewire.admin.presupuestos.nuevo-año');
    }

    /**
     * Método que se ejecuta automáticamente cuando cambia el valor de $description
     * Valida en tiempo real que el año sea válido y único
     * Habilita el botón de envío si la validación es exitosa
     *
     * @return void
     */
    public function updatedDescription(){
        // Obtiene el año actual y el siguiente año para validación
        $currentYear = date('Y');
        $nextYear = date('Y', strtotime('+1 years'));

        // Valida que la descripción sea requerida, única, numérica y esté en el rango permitido
        $this->validate(['description' => ['required', 'unique:anos', 'numeric', "min:$currentYear", "max:$nextYear"]]);

        // Si la validación es exitosa, habilita el botón de envío
        $this->enableSubmit = true;
    }

    /**
     * Crea un nuevo año en el sistema junto con sus 12 meses correspondientes
     * Valida la información antes de crear y establece automáticamente los meses estándar
     *
     * @return \Illuminate\Http\RedirectResponse
     */
    public function nuevo_año (){
        // Define el rango de años permitidos (actual y siguiente)
        $currentYear = date('Y');
        $nextYear = date('Y', strtotime('+1 years'));

        // Define los nombres de los meses en español
        $meses = ['Enero', 'Febrero', 'Marzo', 'Abril', 'Mayo', 'Junio', 'Julio', 'Agosto', 'Septiembre', 'Octubre', 'Noviembre', 'Diciembre'];

        // Define los identificadores numéricos para cada mes (1-12)
        $identifiers = ['1', '2', '3', '4', '5', '6', '7', '8', '9', '10', '11', '12'];

        // Valida nuevamente la información antes de procesar
        $this->validate(['description' => ['required', 'unique:anos', 'numeric', "min:$currentYear", "max:$nextYear"]]);

        // Crea el nuevo año en la base de datos
        Año::create([
            'description' => $this->description
        ]);

        // Obtiene el ID del año recién creado
        $created_year = Año::select('id')->where('description', "$this->description")->first();

        // Crea los 12 meses asociados al año
        foreach ($meses as $key => $mes){
            Mes::create([
                'ano_id' => $created_year->id,           // ID del año padre
                'identifier' => $identifiers[$key],      // Identificador numérico del mes
                'description' => $mes                    // Nombre del mes en español
            ]);
        }

        // Limpia el campo después de crear exitosamente
        $this->description = "";

        // Emite evento para refrescar otros componentes que escuchen
        $this->emit('refresh');

        // Redirige con mensaje de éxito
        return redirect()->back()->with('success', '¡Año generado exitosamente!');
    }
}

<?php

namespace App\Rules;
use App\Models\PresupuestoProyecto;

use Illuminate\Contracts\Validation\Rule;

class CentroCostos implements Rule
{ 
    /**
     * Create a new rule instance.
     *
     * @return void
     */ 
    public function __construct()
    {
        
    }

    /**
     * Determine if the validation rule passes.
     *
     * @param  string  $attribute
     * @param  mixed  $value
     * @return bool
     */
    public function passes($attribute, $value)
    {
        // Se compara el código puro (sin el nombre del proyecto) y se cuenta también lo que está en la papelera:
        // un centro de costos de Helisa no se reutiliza nunca.
        $codigo = PresupuestoProyecto::codigoPuro($value);
        $q = PresupuestoProyecto::withTrashed();
        $codigo ? $q->where('centro_costos', $codigo) : $q->where('cod_cc', $value);
        return !$q->exists();
    }

    /**
     * Get the validation error message.
     *
     * @return string
     */
    public function message()
    {
        return 'Éste centro de costos ya fue asignado a otro proyecto.';
    }
}

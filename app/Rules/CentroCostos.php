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
        $centroValidator = PresupuestoProyecto::where('cod_cc', $value)->get();        
        if ($centroValidator->count() >= 1){
            return false;
        }
        return true;
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

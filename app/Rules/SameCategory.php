<?php

namespace App\Rules;
use App\Models\Proveedor;

use Illuminate\Contracts\Validation\Rule;

class SameCategory implements Rule
{
    /**
     * Create a new rule instance.
     *
     * @return void
     */
    public function __construct()
    {
        //
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
        $categorias = [];
        $proveedores = Proveedor::select('id', 'tercero', 'categoria_id')->get();
        foreach ($value as $proveedor) {
            if (is_numeric($proveedor)){
                array_push($categorias, $proveedores->find($proveedor)->categoria_id);
            }
        }
        
        $validator = array_unique($categorias);
        if (count($validator) > 1){            
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
        return 'Los proveedores no pertencen a la misma categoría.';
    }
}

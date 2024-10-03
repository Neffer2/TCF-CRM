<?php

namespace App\Rules;

use Illuminate\Contracts\Validation\Rule;

class PrestoConsumido implements Rule
{
    protected $item, $cantidadConsumido = 0, $valorTotalConsumido = 0;
    /**
     * Create a new rule instance.
     *
     * @return void
     */
    public function __construct($item)
    {
        $this->item = $item;
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
        $cantidad = ($this->item->cantidad * $this->item->dia * $this->item->otros);
        if ($attribute == 'cantidad') {
            foreach ($this->item->consumidos as $consumido){

                if ($consumido->OrdenCompra->estado_id != 6){
                    $this->cantidadConsumido += $consumido->cant_oc;
                }
            }

            if ($cantidad >= $this->cantidadConsumido){
                return true;
            }
        }elseif ($attribute == 'valor_total'){
            foreach ($this->item->consumidos as $consumido){
                if ($consumido->OrdenCompra->estado_id != 6){
                    $this->valorTotalConsumido += $consumido->vtotal_oc;
                }
            }

            if ($value >= $this->valorTotalConsumido){
                return true;
            }
        }
        return false;
    }

    /**
     * Get the validation error message.
     *
     * @return string
     */
    public function message()
    {
        return "No puedes disminuir este item porque ya tiene ordenes de compra.
        Revisa tu <a style='text-decoration: underline;' href=".route('consumido', $this->item->presto->id)." target='_blank'>consumido</a>.";
    }
}

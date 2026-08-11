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
        }elseif ($attribute == 'valor_total') {
            // 1. Suma de consumos por Órdenes de Compra válidas
            foreach ($this->item->consumidos as $consumido) {
                if ($consumido->OrdenCompra->estado_id != 6) {
                    $this->valorTotalConsumido += $consumido->vtotal_oc;
                }
            }

            // 2. Sumamos el consumo manual registrado directamente en el ítem de presupuesto
            $this->valorTotalConsumido += (float) $this->item->consumo_manual;

            // Validamos que el nuevo valor_total presupuestado sea mayor o igual al consumo total acumulado
            if ($value >= $this->valorTotalConsumido) {
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

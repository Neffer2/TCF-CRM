<?php

namespace App\Models\Concerns;

use App\Models\HistorialEstado;

/** Relación con historial_estados para los modelos cuyo estado se registra fila a fila. */
trait TieneHistorialEstados
{
    public function historialEstados()
    {
        return $this->hasMany(HistorialEstado::class, 'registro_id')->where('tabla', $this->getTable())->orderBy('id');
    }
}

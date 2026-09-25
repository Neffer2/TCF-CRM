<?php

namespace App\Models\Concerns;

use App\Models\Ciudad;

/**
 * Mantiene ciudad_id (catálogo ciudades) al día a partir del texto de la columna ciudad.
 * El texto se conserva tal cual; el enlace se calcula al guardar.
 */
trait ResuelveCiudad
{
    public static function bootResuelveCiudad()
    {
        static::saving(function ($modelo) {
            $atributos = $modelo->getAttributes();
            if (!array_key_exists('ciudad', $atributos)) { return; }
            if ($modelo->isDirty('ciudad') || (empty($atributos['ciudad_id']) && !empty($atributos['ciudad']))) {
                $ciudad = Ciudad::desdeTexto($atributos['ciudad'] ?? null);
                $modelo->ciudad_id = $ciudad ? $ciudad->id : null;
            }
        });
    }

    public function ciudadCatalogo()
    {
        return $this->belongsTo(Ciudad::class, 'ciudad_id');
    }
}
